<?php

namespace App\Controllers;

class ManajerialController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        return view('manajerial/index');
    }

    public function laporan()
    {
        $periode = $this->request->getGet('periode') ?? 'harian';

        $tanggalMulai = null;
        $tanggalSelesai = null;

        switch ($periode) {
            case 'harian':
                $tanggalMulai = date('Y-m-d');
                $tanggalSelesai = date('Y-m-d');
                break;

            case 'mingguan':
                $tanggalMulai = date('Y-m-d', strtotime('-6 days'));
                $tanggalSelesai = date('Y-m-d');
                break;

            case 'bulanan':
                $tanggalMulai = date('Y-m-01');
                $tanggalSelesai = date('Y-m-t');
                break;

            default:
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => false,
                    'message' => 'Periode harus harian, mingguan, atau bulanan.'
                ]);
        }

        /*
         * 1. Ringkasan penjualan
         */
        $penjualan = $this->db
            ->table('pesanan')
            ->where('DATE(waktu_pesan) >=', $tanggalMulai)
            ->where('DATE(waktu_pesan) <=', $tanggalSelesai)
            ->whereIn('status', [
                'Dibayar',
                'Diproses',
                'Siap Diambil',
                'Selesai'
            ])
            ->selectSum('total_bayar')
            ->get()
            ->getRow();

        $jumlahTransaksi = $this->db
            ->table('pesanan')
            ->where('DATE(waktu_pesan) >=', $tanggalMulai)
            ->where('DATE(waktu_pesan) <=', $tanggalSelesai)
            ->whereIn('status', [
                'Dibayar',
                'Diproses',
                'Siap Diambil',
                'Selesai'
            ])
            ->countAllResults();

        /*
         * 2. Penjualan berdasarkan metode pembayaran
         */
        $pembayaran = $this->db
            ->table('pembayaran')
            ->select('metode, SUM(jumlah_diterima) AS total')
            ->where('DATE(waktu_bayar) >=', $tanggalMulai)
            ->where('DATE(waktu_bayar) <=', $tanggalSelesai)
            ->where('status_bayar', 'Berhasil')
            ->where('id_kasir IS NOT NULL', null, false)
            ->groupBy('metode')
            ->get()
            ->getResultArray();

        $totalTunai = 0;
        $totalQris = 0;

        foreach ($pembayaran as $item) {
            if ($item['metode'] === 'Tunai') {
                $totalTunai = (float) $item['total'];
            }

            if ($item['metode'] === 'QRIS') {
                $totalQris = (float) $item['total'];
            }
        }

        /*
         * 3. Menu terlaris
         */
        $menuTerlaris = $this->db
            ->table('detail_pesanan')
            ->select(
                'menu.id_menu,
                 menu.nama_menu,
                 SUM(detail_pesanan.jumlah) AS jumlah_terjual,
                 SUM(detail_pesanan.subtotal) AS total_penjualan'
            )
            ->join(
                'menu',
                'menu.id_menu = detail_pesanan.id_menu'
            )
            ->join(
                'pesanan',
                'pesanan.id_pesanan = detail_pesanan.id_pesanan'
            )
            ->where('DATE(pesanan.waktu_pesan) >=', $tanggalMulai)
            ->where('DATE(pesanan.waktu_pesan) <=', $tanggalSelesai)
            ->whereIn('pesanan.status', [
                'Dibayar',
                'Diproses',
                'Siap Diambil',
                'Selesai'
            ])
            ->groupBy('menu.id_menu')
            ->orderBy('jumlah_terjual', 'DESC')
            ->get()
            ->getResultArray();

        /*
         * 4. Jam paling ramai
         */
        $jamRamai = $this->db
            ->table('pesanan')
            ->select(
                'HOUR(waktu_pesan) AS jam,
                 COUNT(*) AS jumlah_transaksi,
                 SUM(total_bayar) AS total_penjualan'
            )
            ->where('DATE(waktu_pesan) >=', $tanggalMulai)
            ->where('DATE(waktu_pesan) <=', $tanggalSelesai)
            ->whereIn('status', [
                'Dibayar',
                'Diproses',
                'Siap Diambil',
                'Selesai'
            ])
            ->groupBy('HOUR(waktu_pesan)')
            ->orderBy('jumlah_transaksi', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => true,
            'data' => [
                'periode' => $periode,
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,

                'ringkasan' => [
                    'total_penjualan' =>
                        (float) ($penjualan->total_bayar ?? 0),

                    'jumlah_transaksi' =>
                        $jumlahTransaksi,

                    'total_tunai' =>
                        $totalTunai,

                    'total_qris' =>
                        $totalQris,
                ],

                'menu_terlaris' => $menuTerlaris,

                'jam_ramai' => $jamRamai
            ]
        ]);
    }
}
