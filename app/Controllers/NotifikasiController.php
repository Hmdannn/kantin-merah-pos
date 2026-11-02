<?php

namespace App\Controllers;

use App\Models\NotifikasiModel;
use App\Models\PesananModel;

class NotifikasiController extends BaseController
{
    protected $notifikasiModel;
    protected $pesananModel;

    public function __construct()
    {
        $this->notifikasiModel = new NotifikasiModel();
        $this->pesananModel = new PesananModel();
    }

    /*
     * Melihat semua notifikasi untuk dapur.
     */
    public function index()
    {
        $notifikasi = $this->notifikasiModel
            ->select('notifikasi.*, pesanan.kode_pesanan, pesanan.waktu_pesan, pesanan.status')
            ->join(
                'pesanan',
                'pesanan.id_pesanan = notifikasi.id_pesanan'
            )
            ->orderBy('notifikasi.waktu_kirim', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'status' => true,
            'data'   => $notifikasi
        ]);
    }

    /*
     * Melihat notifikasi yang belum dibaca.
     */
    public function belumDibaca()
    {
        $notifikasi = $this->notifikasiModel
            ->select('notifikasi.*, pesanan.kode_pesanan, pesanan.waktu_pesan, pesanan.status')
            ->join(
                'pesanan',
                'pesanan.id_pesanan = notifikasi.id_pesanan'
            )
            ->where('notifikasi.status_baca', false)
            ->orderBy('notifikasi.waktu_kirim', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'status' => true,
            'data'   => $notifikasi
        ]);
    }

    /*
     * Menandai satu notifikasi sebagai sudah dibaca.
     */
    public function baca($idNotifikasi)
    {
        $notifikasi = $this->notifikasiModel->find($idNotifikasi);

        if (!$notifikasi) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Notifikasi tidak ditemukan.'
                ]);
        }

        $this->notifikasiModel->update($idNotifikasi, [
            'status_baca' => true
        ]);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Notifikasi berhasil ditandai sebagai sudah dibaca.'
        ]);
    }
}