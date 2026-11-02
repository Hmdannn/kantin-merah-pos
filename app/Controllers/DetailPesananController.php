<?php

namespace App\Controllers;

use App\Models\DetailPesananModel;
use App\Models\PesananModel;
use App\Models\MenuModel;

class DetailPesananController extends BaseController
{
    protected $detailModel;
    protected $pesananModel;
    protected $menuModel;

    public function __construct()
    {
        $this->detailModel = new DetailPesananModel();
        $this->pesananModel = new PesananModel();
        $this->menuModel = new MenuModel();
    }

    // Menampilkan semua item dalam satu pesanan
    public function index($idPesanan)
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

        $detail = $this->detailModel
            ->select('detail_pesanan.*, menu.nama_menu, menu.harga')
            ->join(
                'menu',
                'menu.id_menu = detail_pesanan.id_menu'
            )
            ->where('detail_pesanan.id_pesanan', $idPesanan)
            ->findAll();

        return $this->response->setJSON([
            'status' => true,
            'data'   => $detail
        ]);
    }

    // Menambahkan item ke pesanan
    public function store($idPesanan)
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

        $idMenu = $this->request->getPost('id_menu');
        $jumlah = $this->request->getPost('jumlah');
        $catatan = $this->request->getPost('catatan_khusus');

        if (!$idMenu || $jumlah === null || (int) $jumlah <= 0) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Menu dan jumlah yang valid wajib diisi.'
                ]);
        }

        $menu = $this->menuModel->find($idMenu);

        if (!$menu) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Menu tidak ditemukan.'
                ]);
        }

        if ((int) $menu['jumlah_stok'] < (int) $jumlah) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Stok menu tidak mencukupi.'
                ]);
        }

        $subtotal = (float) $menu['harga'] * (int) $jumlah;

        $this->detailModel->insert([
            'id_pesanan'     => $idPesanan,
            'id_menu'        => $idMenu,
            'jumlah'         => $jumlah,
            'catatan_khusus' => $catatan,
            'subtotal'       => $subtotal,
        ]);

        // Hitung ulang total pesanan
        $this->updateTotalPesanan($idPesanan);

        return $this->response->setJSON([
            'status'   => true,
            'message'  => 'Item berhasil ditambahkan ke pesanan.',
            'id'       => $this->detailModel->getInsertID(),
            'subtotal' => $subtotal
        ]);
    }

    // Menghapus item dari pesanan
    public function delete($idDetail)
    {
        $detail = $this->detailModel->find($idDetail);

        if (!$detail) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Detail pesanan tidak ditemukan.'
                ]);
        }

        $idPesanan = $detail['id_pesanan'];

        $this->detailModel->delete($idDetail);

        // Hitung ulang total pesanan
        $this->updateTotalPesanan($idPesanan);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Item pesanan berhasil dihapus.'
        ]);
    }

    // Menghitung ulang total pesanan
    private function updateTotalPesanan($idPesanan)
    {
        $details = $this->detailModel
            ->where('id_pesanan', $idPesanan)
            ->findAll();

        $total = 0;

        foreach ($details as $detail) {
            $total += (float) $detail['subtotal'];
        }

        $this->pesananModel->update($idPesanan, [
            'total_bayar' => $total
        ]);
    }
}