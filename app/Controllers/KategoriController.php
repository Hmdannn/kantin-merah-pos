<?php

namespace App\Controllers;

use App\Models\KategoriModel;
use App\Models\MenuModel;

class KategoriController extends BaseController
{
    protected $kategoriModel;
    protected $menuModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
        $this->menuModel = new MenuModel();
    }

    /**
     * Daftar kategori beserta jumlah menu.
     * Dipakai oleh halaman kelola menu (AJAX).
     */
    public function index()
    {
        $kategori = $this->kategoriModel
            ->orderBy('nama_kategori', 'ASC')
            ->findAll();

        foreach ($kategori as &$item) {
            $item['jumlah_menu'] = $this->menuModel
                ->where('id_kategori', $item['id_kategori'])
                ->countAllResults();
        }

        unset($item);

        return $this->response->setJSON([
            'status' => true,
            'data'   => $kategori,
        ]);
    }

    public function store()
    {
        $namaKategori = trim((string) $this->request->getPost('nama_kategori'));
        $error = $this->validasiNama($namaKategori);

        if ($error !== null) {
            return $this->jsonError($error, 422, ['nama_kategori' => $error]);
        }

        $duplikat = $this->kategoriModel
            ->where('nama_kategori', $namaKategori)
            ->first();

        if ($duplikat) {
            return $this->jsonError('Nama kategori sudah digunakan.', 422, [
                'nama_kategori' => 'Nama kategori sudah digunakan.',
            ]);
        }

        $idKategori = $this->kategoriModel->insert([
            'nama_kategori' => $namaKategori,
        ]);

        if ($idKategori === false) {
            return $this->jsonError('Kategori gagal disimpan.', 500);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Kategori berhasil ditambahkan.',
            'data'    => [
                'id_kategori'   => (int) $idKategori,
                'nama_kategori' => $namaKategori,
                'jumlah_menu'   => 0,
            ],
        ]);
    }

    public function update($id)
    {
        $kategori = $this->kategoriModel->find($id);

        if (!$kategori) {
            return $this->jsonError('Kategori tidak ditemukan.', 404);
        }

        $namaKategori = trim((string) $this->request->getPost('nama_kategori'));
        $error = $this->validasiNama($namaKategori);

        if ($error !== null) {
            return $this->jsonError($error, 422, ['nama_kategori' => $error]);
        }

        $duplikat = $this->kategoriModel
            ->where('nama_kategori', $namaKategori)
            ->first();

        if ($duplikat && (int) $duplikat['id_kategori'] !== (int) $id) {
            return $this->jsonError('Nama kategori sudah digunakan.', 422, [
                'nama_kategori' => 'Nama kategori sudah digunakan.',
            ]);
        }

        $berhasil = $this->kategoriModel->update($id, [
            'nama_kategori' => $namaKategori,
        ]);

        if (!$berhasil) {
            return $this->jsonError('Kategori gagal diperbarui.', 500);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Kategori berhasil diperbarui.',
            'data'    => [
                'id_kategori'   => (int) $id,
                'nama_kategori' => $namaKategori,
            ],
        ]);
    }

    public function delete($id)
    {
        $kategori = $this->kategoriModel->find($id);

        if (!$kategori) {
            return $this->jsonError('Kategori tidak ditemukan.', 404);
        }

        $jumlahMenu = $this->menuModel
            ->where('id_kategori', $id)
            ->countAllResults();

        if ($jumlahMenu > 0) {
            return $this->jsonError(
                'Kategori masih memiliki ' . $jumlahMenu . ' menu. Pindahkan atau hapus menu terlebih dahulu.',
                409
            );
        }

        $this->kategoriModel->delete($id);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Kategori berhasil dihapus.',
        ]);
    }

    private function validasiNama(string $namaKategori): ?string
    {
        if ($namaKategori === '') {
            return 'Nama kategori wajib diisi.';
        }

        if (mb_strlen($namaKategori) > 100) {
            return 'Nama kategori maksimal 100 karakter.';
        }

        return null;
    }

    private function jsonError(string $message, int $status = 422, array $errors = [])
    {
        $payload = [
            'status'  => false,
            'message' => $message,
        ];

        if ($errors !== []) {
            $payload['errors'] = $errors;
        }

        return $this->response
            ->setStatusCode($status)
            ->setJSON($payload);
    }
}
