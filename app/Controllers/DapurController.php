<?php

namespace App\Controllers;

use App\Models\PesananModel;
use App\Models\DetailPesananModel;
use App\Models\NotifikasiModel;
use App\Models\MenuModel;

class DapurController extends BaseController
{
    protected $pesananModel;
    protected $detailPesananModel;
    protected $notifikasiModel;

    public function __construct()
    {
        $this->pesananModel = new PesananModel();
        $this->detailPesananModel = new DetailPesananModel();
        $this->notifikasiModel = new NotifikasiModel();
    }

    /*
     * ==========================================
     * DAFTAR PESANAN DAPUR
     * ==========================================
     *
     * Hanya menampilkan pesanan yang sudah
     * dibayar dan sedang dalam proses dapur.
     *
     * Urutan FIFO:
     * pesanan yang masuk lebih dahulu
     * ditampilkan lebih dahulu.
     */
    public function index()
    {
        $pesanan = $this->pesananModel
            ->whereIn('status', [
                'Dibayar',
                'Diproses',
                'Siap Diambil'
            ])
            ->orderBy('no_antrian', 'ASC')
            ->orderBy('waktu_pesan', 'ASC')
            ->orderBy('id_pesanan', 'ASC')
            ->findAll();

        foreach ($pesanan as &$p) {
            $p['items'] = $this->detailPesananModel
                ->select('detail_pesanan.*, menu.nama_menu')
                ->join('menu', 'menu.id_menu = detail_pesanan.id_menu', 'left')
                ->where('detail_pesanan.id_pesanan', $p['id_pesanan'])
                ->findAll();
        }

        if ($this->request->isAJAX() || $this->request->getGet('format') === 'json') {
            return $this->response->setJSON([
                'status' => true,
                'data'   => $pesanan
            ]);
        }

        $menuModel = new MenuModel();
        $menus = $menuModel->orderBy('nama_menu', 'ASC')->findAll();

        return view('dapur/index', [
            'pesanan' => $pesanan,
            'menus'   => $menus
        ]);
    }

    /*
     * ==========================================
     * DETAIL PESANAN UNTUK DAPUR
     * ==========================================
     */
    public function show($idPesanan)
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

        /*
         * Pesanan yang belum dibayar tidak perlu
         * ditampilkan sebagai pesanan dapur.
         */
        if (!in_array($pesanan['status'], [
            'Dibayar',
            'Diproses',
            'Siap Diambil'
        ])) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Pesanan belum dapat diproses oleh dapur.'
                ]);
        }

        $detail = $this->detailPesananModel
            ->select(
                'detail_pesanan.*, menu.nama_menu, menu.harga'
            )
            ->join(
                'menu',
                'menu.id_menu = detail_pesanan.id_menu'
            )
            ->where(
                'detail_pesanan.id_pesanan',
                $idPesanan
            )
            ->findAll();

        return $this->response->setJSON([
            'status' => true,
            'data'   => [
                'pesanan' => $pesanan,
                'detail'  => $detail
            ]
        ]);
    }

    /*
     * ==========================================
     * MULAI MEMPROSES PESANAN
     * ==========================================
     *
     * Dibayar → Diproses
     *
     * Sistem menerapkan FIFO:
     * pesanan yang lebih lama dan masih berstatus
     * Dibayar harus diproses terlebih dahulu.
     */
    public function mulaiProses($idPesanan)
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

        /*
         * Pesanan harus berstatus Dibayar.
         */
        if ($pesanan['status'] !== 'Dibayar') {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status'  => false,
                    'message' =>
                        'Pesanan harus berstatus Dibayar sebelum diproses.'
                ]);
        }

        /*
         * ==========================================
         * CEK FIFO
         * ==========================================
         */
        $pesananLebihDulu = $this->pesananModel
            ->groupStart()
                ->where('waktu_pesan <', $pesanan['waktu_pesan'])
                ->orGroupStart()
                    ->where(
                        'waktu_pesan',
                        $pesanan['waktu_pesan']
                    )
                    ->where(
                        'id_pesanan <',
                        $pesanan['id_pesanan']
                    )
                ->groupEnd()
            ->groupEnd()
            ->where('status', 'Dibayar')
            ->first();

        if ($pesananLebihDulu) {
            return $this->response
                ->setStatusCode(409)
                ->setJSON([
                    'status'  => false,
                    'message' =>
                        'Pesanan belum dapat diproses karena masih ada pesanan yang lebih dahulu dan belum diproses.',
                    'data' => [
                        'id_pesanan_sebelumnya' =>
                            $pesananLebihDulu['id_pesanan'],
                        'kode_pesanan_sebelumnya' =>
                            $pesananLebihDulu['kode_pesanan'],
                    ]
                ]);
        }

        /*
         * ==========================================
         * UBAH STATUS
         * ==========================================
         */
        $this->pesananModel->update($idPesanan, [
            'status' => 'Diproses'
        ]);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Pesanan mulai diproses oleh dapur.',
            'data'    => [
                'id_pesanan' => $idPesanan,
                'status'     => 'Diproses'
            ]
        ]);
    }

    /*
     * ==========================================
     * PESANAN SELESAI DIMASAK
     * ==========================================
     *
     * Diproses → Siap Diambil
     */
    public function siapDiambil($idPesanan)
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

        if ($pesanan['status'] !== 'Diproses') {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status'  => false,
                    'message' =>
                        'Pesanan harus berstatus Diproses sebelum menjadi Siap Diambil.'
                ]);
        }

        /*
         * Ubah status pesanan.
         */
        $this->pesananModel->update($idPesanan, [
            'status' => 'Siap Diambil'
        ]);

        /*
         * Buat notifikasi untuk pesanan.
         */
        $this->notifikasiModel->insert([
            'id_pesanan'   => $idPesanan,
            'pesan'        => 'Pesanan ' . $pesanan['kode_pesanan'] . ' sudah siap diambil. Silakan ambil makanan Anda di kasir.',
            'status_baca'  => false,
            'waktu_kirim'  => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Pesanan sudah siap diambil.',
            'data'    => [
                'id_pesanan' => $idPesanan,
                'status'     => 'Siap Diambil'
            ]
        ]);
    }

    /*
     * ==========================================
     * PESANAN SUDAH DIAMBIL
     * ==========================================
     *
     * Siap Diambil → Selesai
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
                    'message' =>
                        'Pesanan harus berstatus Siap Diambil sebelum selesai.'
                ]);
        }

        /*
         * Ubah status pesanan.
         */
        $this->pesananModel->update($idPesanan, [
            'status' => 'Selesai'
        ]);

        /*
         * Buat notifikasi bahwa pesanan telah selesai.
         */
        $this->notifikasiModel->insert([
            'id_pesanan'   => $idPesanan,
            'pesan'        => 'Pesanan ' . $pesanan['kode_pesanan'] . ' telah selesai.',
            'status_baca'  => false,
            'waktu_kirim'  => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Pesanan telah selesai.',
            'data'    => [
                'id_pesanan' => $idPesanan,
                'status'     => 'Selesai'
            ]
        ]);
    }

    /*
     * ==========================================
     * MENANDAI MENU HABIS / TERSEDIA
     * ==========================================
     * Sesuai use case Dapur di dokumen rancangan PDF
     */
    public function toggleMenuHabis($idMenu)
    {
        $menuModel = new MenuModel();
        $menu = $menuModel->find($idMenu);

        if (!$menu) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Menu tidak ditemukan.'
                ]);
        }

        $newStatus = ($menu['status_stok'] === 'Tersedia') ? 'Habis' : 'Tersedia';
        $menuModel->update($idMenu, [
            'status_stok' => $newStatus
        ]);

        return $this->response->setJSON([
            'status'      => true,
            'message'     => 'Status menu "' . $menu['nama_menu'] . '" berhasil diubah menjadi ' . $newStatus . '.',
            'status_stok' => $newStatus
        ]);
    }
}
