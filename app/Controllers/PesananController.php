<?php

namespace App\Controllers;

use App\Models\PesananModel;
use App\Models\DetailPesananModel;

class PesananController extends BaseController
{
    protected $pesananModel;
    protected $detailModel;

    public function __construct()
    {
        $this->pesananModel = new PesananModel();
        $this->detailModel = new DetailPesananModel();
    }

    // Menampilkan semua pesanan
    public function index()
    {
        $pesanan = $this->pesananModel
            ->orderBy('waktu_pesan', 'DESC')
            ->findAll();

        return $this->response->setJSON([
            'status' => true,
            'data'   => $pesanan
        ]);
    }

    // Menampilkan satu pesanan
    public function show($id)
    {
        $pesanan = $this->pesananModel->find($id);

        if (!$pesanan) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Pesanan tidak ditemukan.'
                ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'data'   => $pesanan
        ]);
    }

    // Membuat pesanan baru
    public function store()
    {
        $kodePesanan = $this->request->getPost('kode_pesanan');

        if (!$kodePesanan) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Kode pesanan wajib diisi.'
                ]);
        }

        // Pastikan kode pesanan belum digunakan
        $existing = $this->pesananModel
            ->where('kode_pesanan', $kodePesanan)
            ->first();

        if ($existing) {
            return $this->response
                ->setStatusCode(409)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Kode pesanan sudah digunakan.'
                ]);
        }

        $this->pesananModel->insert([
            'kode_pesanan'  => $kodePesanan,
            'waktu_pesan'   => date('Y-m-d H:i:s'),
            'status'        => 'Menunggu Pembayaran',
            'total_bayar'   => 0,
            'id_user_update' => null,
        ]);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Pesanan berhasil dibuat.',
            'id'      => $this->pesananModel->getInsertID()
        ]);
    }

    // Menghitung total berdasarkan detail pesanan
    public function calculateTotal($id)
    {
        $pesanan = $this->pesananModel->find($id);

        if (!$pesanan) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Pesanan tidak ditemukan.'
                ]);
        }

        $details = $this->detailModel
            ->where('id_pesanan', $id)
            ->findAll();

        $total = 0;

        foreach ($details as $detail) {
            $total += (float) $detail['subtotal'];
        }

        $this->pesananModel->update($id, [
            'total_bayar' => $total
        ]);

        return $this->response->setJSON([
            'status' => true,
            'data' => [
                'id_pesanan'  => $id,
                'total_bayar' => $total
            ]
        ]);
    }

    // Mengubah status pesanan
    public function updateStatus($id)
    {
        /*
         * ==========================================
         * CEK LOGIN
         * ==========================================
         *
         * Perubahan status melalui endpoint ini
         * hanya boleh dilakukan oleh user yang login.
         */

        if (!session()->get('logged_in')) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Silakan login terlebih dahulu.'
                ]);
        }

        /*
         * ==========================================
         * CEK ROLE
         * ==========================================
         *
         * Endpoint ini hanya dapat digunakan
         * oleh kasir, dapur, atau manajerial.
         */

        $role = session()->get('role');

        if (!in_array($role, ['kasir', 'dapur', 'manajerial'])) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Anda tidak memiliki akses untuk mengubah status pesanan.'
                ]);
        }

        /*
         * ==========================================
         * CEK PESANAN
         * ==========================================
         */

        $pesanan = $this->pesananModel->find($id);

        if (!$pesanan) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Pesanan tidak ditemukan.'
                ]);
        }

        $statusLama = $pesanan['status'];
        $statusBaru = $this->request->getPost('status');

        /*
         * ==========================================
         * ATURAN PERUBAHAN STATUS
         * ==========================================
         *
         * Setiap role hanya boleh melakukan
         * perubahan status yang sesuai dengan
         * tanggung jawabnya.
         */

        $transisiDiizinkan = false;

        /*
         * KASIR
         *
         * Kasir dapat membatalkan pesanan
         * atau menangani status pembayaran tertentu.
         */
        if ($role === 'kasir') {

            if (
                $statusLama === 'Menunggu Pembayaran'
                && in_array($statusBaru, ['Kadaluarsa', 'Dibatalkan'])
            ) {
                $transisiDiizinkan = true;
            }
        }

        /*
         * DAPUR
         *
         * Dapur menangani alur:
         *
         * Dibayar
         *    ↓
         * Diproses
         *    ↓
         * Siap Diambil
         *    ↓
         * Selesai
         *
         * Tetapi endpoint khusus DapurController
         * tetap menjadi jalur utama proses dapur.
         */
        if ($role === 'dapur') {

            if (
                ($statusLama === 'Dibayar' && $statusBaru === 'Diproses')
                ||
                ($statusLama === 'Diproses' && $statusBaru === 'Siap Diambil')
                ||
                ($statusLama === 'Siap Diambil' && $statusBaru === 'Selesai')
            ) {
                $transisiDiizinkan = true;
            }
        }

        /*
         * MANAJERIAL
         *
         * Untuk sementara manajerial tidak diberikan
         * akses mengubah status pesanan melalui
         * endpoint umum ini.
         */
        if ($role === 'manajerial') {
            $transisiDiizinkan = false;
        }

        /*
         * ==========================================
         * CEK HASIL TRANSISI
         * ==========================================
         */

        if (!$transisiDiizinkan) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Perubahan status pesanan tidak diizinkan untuk role atau status saat ini.'
                ]);
        }

        /*
         * ==========================================
         * UPDATE STATUS
         * ==========================================
         */

        $this->pesananModel->update($id, [
            'status'         => $statusBaru,
            'id_user_update' => session()->get('id_user'),
        ]);

        /*
         * ==========================================
         * TUTUP DATA PEMBAYARAN
         * ==========================================
         *
         * Jika pesanan dibatalkan atau kadaluarsa,
         * pembayaran yang masih menunggu / sudah
         * dikonfirmasi customer tidak boleh lagi
         * dianggap sebagai pendapatan.
         */
        if (in_array($statusBaru, ['Dibatalkan', 'Kadaluarsa'], true)) {
            $pembayaranModel = new \App\Models\PembayaranModel();

            $pembayaran = $pembayaranModel
                ->where('id_pesanan', $id)
                ->first();

            if ($pembayaran) {
                $pembayaranModel->update(
                    $pembayaran['id_pembayaran'],
                    [
                        'status_bayar' => $statusBaru === 'Kadaluarsa'
                            ? 'Kadaluarsa'
                            : 'Gagal',
                    ]
                );
            }
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Status pesanan berhasil diperbarui.',
            'data'    => [
                'id_pesanan' => $id,
                'status_lama' => $statusLama,
                'status_baru' => $statusBaru,
            ]
        ]);
    }
}