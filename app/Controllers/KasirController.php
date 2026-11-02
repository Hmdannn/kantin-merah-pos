<?php

namespace App\Controllers;

use App\Models\PesananModel;
use App\Models\DetailPesananModel;
use App\Models\PembayaranModel;
use App\Models\ShiftModel;
use App\Models\NotifikasiModel;

class KasirController extends BaseController
{
    protected $pesananModel;
    protected $detailPesananModel;
    protected $pembayaranModel;
    protected $shiftModel;
    protected $notifikasiModel;

    public function __construct()
    {
        $this->pesananModel = new PesananModel();
        $this->detailPesananModel = new DetailPesananModel();
        $this->pembayaranModel = new PembayaranModel();
        $this->shiftModel = new ShiftModel();
        $this->notifikasiModel = new NotifikasiModel();
    }

    public function index()
    {
        $idKasir = session()->get('id_user');

        // 1. Shift Aktif Kasir
        $shiftAktif = $this->shiftModel
            ->where('id_kasir', $idKasir)
            ->where('waktu_selesai', null)
            ->first();

        // 2. Semua pesanan yang masih menunggu pembayaran
        $pesananMenunggu = $this->pesananModel
            ->where('status', 'Menunggu Pembayaran')
            ->orderBy('no_antrian', 'ASC')
            ->orderBy('waktu_pesan', 'ASC')
            ->findAll();

        foreach ($pesananMenunggu as &$p) {
            $p['items'] = $this->detailPesananModel
                ->select('detail_pesanan.*, menu.nama_menu')
                ->join('menu', 'menu.id_menu = detail_pesanan.id_menu', 'left')
                ->where('detail_pesanan.id_pesanan', $p['id_pesanan'])
                ->findAll();

            $p['pembayaran'] = $this->pembayaranModel
                ->where('id_pesanan', $p['id_pesanan'])
                ->first();
        }
        unset($p);

        // Antrean tunai tetap diverifikasi kasir.
        $pesananTunai = array_values(array_filter(
            $pesananMenunggu,
            static function ($p) {
                return ($p['metode_bayar'] ?? 'Tunai') === 'Tunai';
            }
        ));

        // 3. Pesanan Siap Diambil (self service -> ditandai Selesai)
        $pesananSiap = $this->pesananModel
            ->where('status', 'Siap Diambil')
            ->orderBy('no_antrian', 'ASC')
            ->orderBy('waktu_pesan', 'ASC')
            ->findAll();

        foreach ($pesananSiap as &$p) {
            $p['items'] = $this->detailPesananModel
                ->select('detail_pesanan.*, menu.nama_menu')
                ->join('menu', 'menu.id_menu = detail_pesanan.id_menu', 'left')
                ->where('detail_pesanan.id_pesanan', $p['id_pesanan'])
                ->findAll();
        }
        unset($p);

        // 4. Transaksi baru yang sudah dibayar dan siap masuk dapur.
        $pesananBaruMasuk = $this->pesananModel
            ->select('id_pesanan, no_antrian, kode_pesanan, id_customer, nama_customer, nomor_meja, status, total_bayar, waktu_pesan, updated_at')
            ->where('status', 'Dibayar')
            ->where('waktu_pesan >=', date('Y-m-d 00:00:00'))
            ->orderBy('no_antrian', 'DESC')
            ->limit(5)
            ->findAll();

        // Tampilkan 5 transaksi terbaru dari nomor antrian terkecil
        // agar urutan baca tetap kronologis.
        usort($pesananBaruMasuk, static function ($a, $b) {
            return ((int) ($a['no_antrian'] ?? 0)) <=> ((int) ($b['no_antrian'] ?? 0));
        });

        // 5. Riwayat lengkap ada di halaman sidebar tersendiri.
        $jumlahDapurAktif = $this->pesananModel
            ->whereIn('status', ['Dibayar', 'Diproses'])
            ->where('waktu_pesan >=', date('Y-m-d 00:00:00'))
            ->countAllResults();

        return view('kasir/index', [
            'shiftAktif'        => $shiftAktif,
            'pesananTunai'      => $pesananTunai,
            'pesananSiap'       => $pesananSiap,
            'pesananBaruMasuk'  => $pesananBaruMasuk,
            'jumlahDapurAktif'  => $jumlahDapurAktif,
        ]);
    }

    /**
     * Riwayat semua transaksi, 5 baris per halaman.
     */
    private function getRiwayatTransaksiData($page = 1, $perPage = 5)
    {
        $page = max(1, (int) $page);
        $perPage = max(1, (int) $perPage);

        $kolom = 'id_pesanan, no_antrian, kode_pesanan, id_customer, nama_customer, nomor_meja, metode_bayar, status, total_bayar, waktu_pesan, updated_at';

        $total = $this->pesananModel->countAllResults();

        $totalPages = max(1, (int) ceil($total / $perPage));

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $data = $this->pesananModel
            ->select($kolom)
            ->orderBy('waktu_pesan', 'DESC')
            ->orderBy('id_pesanan', 'DESC')
            ->findAll($perPage, ($page - 1) * $perPage);

        return [
            'page'       => $page,
            'per_page'   => $perPage,
            'total'      => $total,
            'total_pages' => $totalPages,
            'from'       => $total > 0 ? (($page - 1) * $perPage) + 1 : 0,
            'to'         => min($total, $page * $perPage),
            'data'       => $data,
        ];
    }

    /**
     * Halaman sidebar riwayat transaksi.
     */
    public function riwayat()
    {
        $hasil = $this->getRiwayatTransaksiData(
            $this->request->getGet('page') ?? 1
        );

        return view('kasir/riwayat', [
            'page'             => $hasil['page'],
            'perPage'          => $hasil['per_page'],
            'total'            => $hasil['total'],
            'totalPages'       => $hasil['total_pages'],
            'from'             => $hasil['from'],
            'to'               => $hasil['to'],
            'riwayatTransaksi' => $hasil['data'],
        ]);
    }

    /**
     * Endpoint polling halaman riwayat transaksi.
     */
    public function riwayatData()
    {
        $hasil = $this->getRiwayatTransaksiData(
            $this->request->getGet('page') ?? 1
        );

        return $this->response->setJSON([
            'status'      => true,
            'page'        => $hasil['page'],
            'per_page'    => $hasil['per_page'],
            'total'       => $hasil['total'],
            'total_pages' => $hasil['total_pages'],
            'from'        => $hasil['from'],
            'to'          => $hasil['to'],
            'data'        => $hasil['data'],
        ]);
    }

    /*
     * ==========================================
     * MENANDAI PESANAN SUDAH DIAMBIL
     * ==========================================
     *
     * Siap Diambil -> Selesai (self service).
     *
     * Endpoint ini khusus kasir. Sebelumnya halaman
     * kasir memanggil endpoint dapur yang hanya boleh
     * diakses role dapur, sehingga selalu gagal.
     */
    public function selesai($idPesanan)
    {
        $pesanan = $this->pesananModel->find($idPesanan);

        if (!$pesanan) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Pesanan tidak ditemukan.'
                ]);
        }

        if ($pesanan['status'] !== 'Siap Diambil') {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Pesanan harus berstatus Siap Diambil sebelum ditandai sudah diambil.'
                ]);
        }

        $this->pesananModel->update($idPesanan, [
            'status'         => 'Selesai',
            'id_user_update' => session()->get('id_user'),
        ]);

        $this->notifikasiModel->insert([
            'id_pesanan'  => $idPesanan,
            'pesan'       => 'Pesanan ' . $pesanan['kode_pesanan'] . ' telah diambil customer (self service).',
            'status_baca' => false,
            'waktu_kirim' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Pesanan telah ditandai selesai (diambil customer).',
            'data'    => [
                'id_pesanan' => $idPesanan,
                'status'     => 'Selesai',
            ]
        ]);
    }


}
