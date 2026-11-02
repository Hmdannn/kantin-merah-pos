<?php

namespace App\Controllers;

use App\Models\KategoriModel;
use App\Models\MenuModel;

class MenuController extends BaseController
{
    protected $db;
    protected $menuModel;
    protected $kategoriModel;

    public function __construct()
    {
        helper('url');

        $this->db = \Config\Database::connect();
        $this->menuModel = new MenuModel();
        $this->kategoriModel = new KategoriModel();
    }

    /**
     * Halaman kelola menu untuk Manajerial.
     * Request dengan Accept JSON / ?format=json akan menerima daftar menu.
     */
    public function index()
    {
        if ($this->wantsJson()) {
            return $this->response->setJSON([
                'status' => true,
                'data'   => $this->menuWithFotoUrl($this->daftarMenu()),
            ]);
        }

        return view('manajerial/menu', [
            'menu'     => $this->menuWithFotoUrl($this->daftarMenu()),
            'kategori' => $this->daftarKategori(),
        ]);
    }

    public function show($id)
    {
        $menu = $this->menuModel->find($id);

        if (!$menu) {
            return $this->jsonError('Menu tidak ditemukan.', 404);
        }

        $menu['foto_url'] = $this->fotoUrl($menu['foto'] ?? null);

        return $this->response->setJSON([
            'status' => true,
            'data'   => $menu,
        ]);
    }

    public function store()
    {
        $namaMenu = trim((string) $this->request->getPost('nama_menu'));
        $idKategori = (int) $this->request->getPost('id_kategori');
        $harga = $this->parseHarga($this->request->getPost('harga'));
        $jumlahStok = max(0, (int) $this->request->getPost('jumlah_stok'));
        $statusStok = $this->normalisasiStatus($this->request->getPost('status_stok'), $jumlahStok);

        $errors = $this->validasiMenu($namaMenu, $idKategori, $harga, $jumlahStok);

        if ($errors !== []) {
            return $this->jsonError('Periksa kembali data menu.', 422, $errors);
        }

        $foto = null;

        [$hasilUpload, $uploadError] = $this->uploadFoto();

        if ($uploadError !== null) {
            return $this->jsonError($uploadError, 422, ['foto_file' => $uploadError]);
        }

        if ($hasilUpload !== null) {
            $foto = $hasilUpload;
        } else {
            $fotoUrl = trim((string) $this->request->getPost('foto_url'));

            if ($fotoUrl !== '') {
                if (!$this->isValidFotoUrl($fotoUrl)) {
                    return $this->jsonError('URL foto tidak valid. Gunakan alamat http:// atau https://.', 422, [
                        'foto_url' => 'URL foto tidak valid.',
                    ]);
                }

                $foto = $fotoUrl;
            }
        }

        $idMenu = $this->menuModel->insert([
            'nama_menu'   => $namaMenu,
            'id_kategori' => $idKategori,
            'harga'       => $harga,
            'jumlah_stok' => $jumlahStok,
            'status_stok' => $statusStok,
            'foto'        => $foto,
        ]);

        if ($idMenu === false) {
            $this->hapusFileFoto($foto);

            return $this->jsonError('Menu gagal disimpan.', 500);
        }

        $menu = $this->menuModel->find($idMenu);
        $menu['foto_url'] = $this->fotoUrl($menu['foto'] ?? null);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Menu berhasil ditambahkan.',
            'data'    => $menu,
        ]);
    }

    public function update($id)
    {
        $menu = $this->menuModel->find($id);

        if (!$menu) {
            return $this->jsonError('Menu tidak ditemukan.', 404);
        }

        $namaMenu = trim((string) $this->request->getPost('nama_menu'));
        $idKategori = (int) $this->request->getPost('id_kategori');
        $harga = $this->parseHarga($this->request->getPost('harga'));
        $jumlahStok = max(0, (int) $this->request->getPost('jumlah_stok'));
        $statusStok = $this->normalisasiStatus($this->request->getPost('status_stok'), $jumlahStok);

        $errors = $this->validasiMenu($namaMenu, $idKategori, $harga, $jumlahStok);

        if ($errors !== []) {
            return $this->jsonError('Periksa kembali data menu.', 422, $errors);
        }

        $fotoLama = $menu['foto'] ?? null;
        $foto = $fotoLama;
        $fotoBaru = null;
        $hapusFoto = false;

        [$hasilUpload, $uploadError] = $this->uploadFoto();

        if ($uploadError !== null) {
            return $this->jsonError($uploadError, 422, ['foto_file' => $uploadError]);
        }

        if ($hasilUpload !== null) {
            $fotoBaru = $hasilUpload;
        } else {
            $fotoUrl = trim((string) $this->request->getPost('foto_url'));

            if ($fotoUrl !== '') {
                if (!$this->isValidFotoUrl($fotoUrl)) {
                    return $this->jsonError('URL foto tidak valid. Gunakan alamat http:// atau https://.', 422, [
                        'foto_url' => 'URL foto tidak valid.',
                    ]);
                }

                $fotoBaru = $fotoUrl;
            }
        }

        if ($fotoBaru !== null) {
            $foto = $fotoBaru;
        } elseif ((string) $this->request->getPost('hapus_foto') === '1') {
            $foto = null;
            $hapusFoto = true;
        }

        $berhasil = $this->menuModel->update($id, [
            'nama_menu'   => $namaMenu,
            'id_kategori' => $idKategori,
            'harga'       => $harga,
            'jumlah_stok' => $jumlahStok,
            'status_stok' => $statusStok,
            'foto'        => $foto,
        ]);

        if (!$berhasil) {
            if ($hasilUpload !== null) {
                $this->hapusFileFoto($hasilUpload);
            }

            return $this->jsonError('Menu gagal diperbarui.', 500);
        }

        if ($fotoBaru !== null || $hapusFoto) {
            $this->hapusFileFoto($fotoLama);
        }

        $menu = $this->menuModel->find($id);
        $menu['foto_url'] = $this->fotoUrl($menu['foto'] ?? null);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Menu berhasil diperbarui.',
            'data'    => $menu,
        ]);
    }

    public function toggle($id)
    {
        $menu = $this->menuModel->find($id);

        if (!$menu) {
            return $this->jsonError('Menu tidak ditemukan.', 404);
        }

        $statusBaru = ($menu['status_stok'] === 'Tersedia') ? 'Habis' : 'Tersedia';

        $this->menuModel->update($id, [
            'status_stok' => $statusBaru,
        ]);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Status menu diubah menjadi ' . $statusBaru . '.',
            'data'    => [
                'id_menu'     => (int) $id,
                'status_stok' => $statusBaru,
            ],
        ]);
    }

    public function delete($id)
    {
        $menu = $this->menuModel->find($id);

        if (!$menu) {
            return $this->jsonError('Menu tidak ditemukan.', 404);
        }

        $sudahDipesan = $this->db
            ->table('detail_pesanan')
            ->where('id_menu', $id)
            ->countAllResults();

        if ($sudahDipesan > 0) {
            return $this->jsonError(
                'Menu ini sudah pernah dipesan sehingga tidak bisa dihapus. Ubah statusnya menjadi Habis agar riwayat pesanan tetap utuh.',
                409
            );
        }

        $this->menuModel->delete($id);
        $this->hapusFileFoto($menu['foto'] ?? null);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Menu berhasil dihapus.',
        ]);
    }

    private function daftarMenu(): array
    {
        return $this->menuModel
            ->select('menu.*, kategori.nama_kategori')
            ->join('kategori', 'kategori.id_kategori = menu.id_kategori', 'left')
            ->orderBy('kategori.nama_kategori', 'ASC')
            ->orderBy('menu.nama_menu', 'ASC')
            ->findAll();
    }

    private function daftarKategori(): array
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

        return $kategori;
    }

    private function validasiMenu(string $namaMenu, int $idKategori, float $harga, int $jumlahStok): array
    {
        $errors = [];

        if ($namaMenu === '') {
            $errors['nama_menu'] = 'Nama menu wajib diisi.';
        } elseif (mb_strlen($namaMenu) > 100) {
            $errors['nama_menu'] = 'Nama menu maksimal 100 karakter.';
        }

        if ($idKategori <= 0 || !$this->kategoriModel->find($idKategori)) {
            $errors['id_kategori'] = 'Pilih kategori yang valid.';
        }

        if ($harga < 0) {
            $errors['harga'] = 'Harga tidak boleh negatif.';
        } elseif ($harga > 99999999.99) {
            $errors['harga'] = 'Harga terlalu besar.';
        }

        if ($jumlahStok < 0) {
            $errors['jumlah_stok'] = 'Stok tidak boleh negatif.';
        }

        return $errors;
    }

    private function parseHarga($nilai): float
    {
        $teks = trim((string) $nilai);
        $teks = str_replace(' ', '', $teks);

        if (str_contains($teks, ',')) {
            $teks = str_replace('.', '', $teks);
            $teks = str_replace(',', '.', $teks);
        } elseif (preg_match('/^[1-9]\d{0,2}\.\d{3}$/', $teks)) {
            // Format ribuan Indonesia, contoh: 15.000
            $teks = str_replace('.', '', $teks);
        } elseif (substr_count($teks, '.') > 1) {
            $teks = str_replace('.', '', $teks);
        }

        return round(max(0, (float) $teks), 2);
    }

    private function normalisasiStatus($status, int $jumlahStok): string
    {
        $status = trim((string) $status);

        if ($jumlahStok <= 0) {
            return 'Habis';
        }

        return ($status === 'Habis') ? 'Habis' : 'Tersedia';
    }

    private function uploadFoto(): array
    {
        $file = $this->request->getFile('foto_file');

        if (!$file instanceof \CodeIgniter\HTTP\Files\UploadedFile) {
            return [null, null];
        }

        if ($file->getError() === UPLOAD_ERR_NO_FILE) {
            return [null, null];
        }

        if (!$file->isValid()) {
            return [null, 'Foto gagal diunggah: ' . $file->getErrorString()];
        }

        if ($file->getSize() > 2 * 1024 * 1024) {
            return [null, 'Ukuran foto maksimal 2 MB.'];
        }

        $mime = $file->getMimeType();
        $diizinkan = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];

        if (!isset($diizinkan[$mime])) {
            return [null, 'Format foto harus JPG, PNG, atau WEBP.'];
        }

        $folder = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'menu';

        if (!is_dir($folder) && !mkdir($folder, 0775, true) && !is_dir($folder)) {
            return [null, 'Folder upload foto tidak dapat dibuat.'];
        }

        $namaFile = bin2hex(random_bytes(16)) . '.' . $diizinkan[$mime];

        if (!$file->move($folder, $namaFile)) {
            return [null, 'Foto gagal disimpan ke folder upload.'];
        }

        return ['uploads/menu/' . $namaFile, null];
    }

    private function hapusFileFoto(?string $foto): void
    {
        if (!$foto) {
            return;
        }

        $foto = ltrim(str_replace('\\', '/', $foto), '/');

        if (!str_starts_with($foto, 'uploads/menu/')) {
            return;
        }

        $path = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $foto);

        if (is_file($path)) {
            @unlink($path);
        }
    }

    private function isValidFotoUrl(string $url): bool
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https'], true);
    }

    private function menuWithFotoUrl(array $menu): array
    {
        foreach ($menu as &$item) {
            $item['foto_url'] = $this->fotoUrl($item['foto'] ?? null);
        }

        unset($item);

        return $menu;
    }

    private function fotoUrl(?string $foto): ?string
    {
        if (!$foto) {
            return null;
        }

        if (preg_match('#^https?://#i', $foto)) {
            return $foto;
        }

        return base_url(ltrim(str_replace('\\', '/', $foto), '/'));
    }

    private function wantsJson(): bool
    {
        if (strtolower($this->request->getMethod()) !== 'get') {
            return true;
        }

        return $this->request->isAJAX()
            || $this->request->getGet('format') === 'json'
            || str_contains($this->request->getHeaderLine('Accept'), 'application/json');
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
