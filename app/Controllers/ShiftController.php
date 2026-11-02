<?php

namespace App\Controllers;

use App\Models\ShiftModel;
use App\Models\UserModel;

class ShiftController extends BaseController
{
    protected $shiftModel;
    protected $userModel;

    public function __construct()
    {
        $this->shiftModel = new ShiftModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        return view('kasir/shift');
    }

    /*
     * Membuka shift kasir
     */
    public function buka()
    {
        $idKasir = session()->get('id_user');

        if (!$idKasir) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Kasir belum login.'
                ]);
        }

        $kasir = $this->userModel->find($idKasir);

        if (!$kasir || $kasir['role'] !== 'kasir') {
            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Hanya pengguna dengan role kasir yang dapat membuka shift.'
                ]);
        }

        $shiftAktif = $this->shiftModel
            ->where('id_kasir', $idKasir)
            ->where('waktu_selesai', null)
            ->first();

        if ($shiftAktif) {
            return $this->response
                ->setStatusCode(409)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Kasir masih memiliki shift yang aktif.'
                ]);
        }

        $this->shiftModel->insert([
            'id_kasir'        => $idKasir,
            'waktu_mulai'     => date('Y-m-d H:i:s'),
            'waktu_selesai'   => null,
            'total_kas_tunai' => 0,
            'total_kas_qris'  => 0,
        ]);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Shift berhasil dibuka.',
            'data'    => [
                'id_shift'    => $this->shiftModel->getInsertID(),
                'id_kasir'    => $idKasir,
                'waktu_mulai' => date('Y-m-d H:i:s'),
            ]
        ]);
    }

    /*
     * Melihat shift aktif kasir
     */
    public function aktif()
    {
        $idKasir = session()->get('id_user');

        if (!$idKasir) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Kasir belum login.'
                ]);
        }

        $shift = $this->shiftModel
            ->where('id_kasir', $idKasir)
            ->where('waktu_selesai', null)
            ->first();

        if (!$shift) {
            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Tidak ada shift aktif.',
                'data'    => null
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'data'   => $shift
        ]);
    }

    /*
     * Menutup shift kasir
     */
    public function tutup($idShift = null)
    {
        $idKasir = session()->get('id_user');

        if (!$idKasir) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Kasir belum login.'
                ]);
        }

        if (!$idShift) {
            $idShift = $this->request->getPost('id_shift');
        }

        if (!$idShift) {
            $shiftAktif = $this->shiftModel
                ->where('id_kasir', $idKasir)
                ->where('waktu_selesai', null)
                ->first();

            if ($shiftAktif) {
                $idShift = $shiftAktif['id_shift'];
            }
        }

        if (!$idShift) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status'  => false,
                    'message' => 'ID Shift tidak ditemukan.'
                ]);
        }

        $shift = $this->shiftModel->find($idShift);

        if (!$shift) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Shift tidak ditemukan.'
                ]);
        }

        if ((int) $shift['id_kasir'] !== (int) $idKasir) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Anda tidak memiliki akses ke shift ini.'
                ]);
        }

        if ($shift['waktu_selesai'] !== null) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Shift sudah ditutup.'
                ]);
        }

        $waktuSelesai = date('Y-m-d H:i:s');

        $this->shiftModel->update($idShift, [
            'waktu_selesai' => $waktuSelesai,
        ]);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Shift berhasil ditutup.',
            'data'    => [
                'id_shift'        => $idShift,
                'waktu_mulai'     => $shift['waktu_mulai'],
                'waktu_selesai'   => $waktuSelesai,
                'total_kas_tunai' => $shift['total_kas_tunai'],
                'total_kas_qris'  => $shift['total_kas_qris'],
            ]
        ]);
    }

    /*
     * Rekap total kas shift aktif
     */
    public function total()
    {
        $idKasir = session()->get('id_user');

        $shift = $this->shiftModel
            ->where('id_kasir', $idKasir)
            ->where('waktu_selesai', null)
            ->first();

        if (!$shift) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Tidak ada shift yang sedang aktif.',
                'data'    => null
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'data'   => [
                'id_shift'        => $shift['id_shift'],
                'total_kas_tunai' => (float) $shift['total_kas_tunai'],
                'total_kas_qris'  => (float) $shift['total_kas_qris'],
                'total_keseluruhan' => (float) ($shift['total_kas_tunai'] + $shift['total_kas_qris'])
            ]
        ]);
    }

    /*
     * Riwayat shift kasir
     */
    public function riwayat()
    {
        $idKasir = session()->get('id_user');

        $riwayat = $this->shiftModel
            ->where('id_kasir', $idKasir)
            ->orderBy('waktu_mulai', 'DESC')
            ->findAll(20);

        return $this->response->setJSON([
            'status' => true,
            'data'   => $riwayat
        ]);
    }
}
