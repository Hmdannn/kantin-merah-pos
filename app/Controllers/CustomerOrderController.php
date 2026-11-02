<?php

namespace App\Controllers;

use App\Models\PesananModel;
use App\Models\DetailPesananModel;
use App\Models\MenuModel;
use App\Models\PembayaranModel;
use App\Models\NotifikasiModel;
use App\Models\ShiftModel;

class CustomerOrderController extends BaseController
{
    protected $pesananModel;
    protected $detailModel;
    protected $menuModel;
    protected $pembayaranModel;
    protected $notifikasiModel;
    protected $shiftModel;

    public function __construct()
    {
        $this->pesananModel    = new PesananModel();
        $this->detailModel     = new DetailPesananModel();
        $this->menuModel       = new MenuModel();
        $this->pembayaranModel = new PembayaranModel();
        $this->notifikasiModel = new NotifikasiModel();
        $this->shiftModel      = new ShiftModel();
    }

    /**
     * Cari pesanan berdasarkan kode.
     *
     * Sebelum kolom kode_pesanan diperlebar, MySQL memotong
     * kode 22 karakter menjadi 20 karakter. Untuk pesanan lama,
     * cocokkan juga 20 karakter pertama dari kode yang dikirim
     * customer agar pesanan lama tetap bisa dibuka/dibayar.
     */
    private function findPesananByKode($kodePesanan)
    {
        $pesanan = $this->pesananModel
            ->where('kode_pesanan', $kodePesanan)
            ->first();

        if ($pesanan) {
            return $pesanan;
        }

        if (strlen($kodePesanan) > 20) {
            return $this->pesananModel
                ->where('kode_pesanan', substr($kodePesanan, 0, 20))
                ->first();
        }

        return null;
    }

    public function checkout()
    {
        $payload = $this->request->getJSON(true);

        if (!is_array($payload) || empty($payload['items'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => false,
                'message' => 'Keranjang pesanan kosong.'
            ]);
        }

        $items = $payload['items'];
        $metodeBayar = $payload['metode'] ?? 'Tunai';

        if (!in_array($metodeBayar, ['Tunai', 'QRIS'], true)) {
            $metodeBayar = 'Tunai';
        }

        $namaCustomer = trim($payload['nama_customer'] ?? '');
        if ($namaCustomer === '') {
            $namaCustomer = 'Pelanggan';
        }

        $nomorMeja = trim($payload['nomor_meja'] ?? '');
        if ($nomorMeja === '') {
            $nomorMeja = 'Meja -';
        }

        $idCustomer = trim($payload['id_customer'] ?? '');
        if ($idCustomer === '') {
            $idCustomer = $nomorMeja;
        }

        $db = \Config\Database::connect();

        // Pastikan kolom identitas pemesan & metode_bayar ada di tabel pesanan
        try {
            $fields = $db->getFieldNames('pesanan');
            if (!in_array('nama_customer', $fields, true)) {
                $db->query("ALTER TABLE `pesanan` ADD `nama_customer` VARCHAR(100) NULL AFTER `kode_pesanan`");
            }
            if (!in_array('nomor_meja', $fields, true)) {
                $db->query("ALTER TABLE `pesanan` ADD `nomor_meja` VARCHAR(50) NULL AFTER `nama_customer`");
            }
            if (!in_array('id_customer', $fields, true)) {
                $db->query("ALTER TABLE `pesanan` ADD `id_customer` VARCHAR(50) NULL AFTER `nomor_meja`");
            }
            if (!in_array('metode_bayar', $fields, true)) {
                $db->query("ALTER TABLE `pesanan` ADD `metode_bayar` VARCHAR(50) NULL AFTER `total_bayar`");
            }
        } catch (\Throwable $e) {}

        $db->transBegin();

        try {
            $kodePesanan = 'ORD-' . date('YmdHis') . '-' . random_int(100, 999);

            $insertPesanan = [
                'kode_pesanan'   => $kodePesanan,
                'nama_customer'  => $namaCustomer,
                'nomor_meja'     => $nomorMeja,
                'id_customer'    => $idCustomer,
                'waktu_pesan'    => date('Y-m-d H:i:s'),
                'status'         => 'Menunggu Pembayaran',
                'total_bayar'    => 0,
                'metode_bayar'   => $metodeBayar,
                'id_user_update' => null,
            ];

            $idPesanan = $this->pesananModel->insert($insertPesanan);

            if (!$idPesanan) {
                $idPesanan = $this->pesananModel->getInsertID();
            }

            if (!$idPesanan || (int)$idPesanan <= 0) {
                throw new \Exception('Gagal membuat nomor pesanan di database.');
            }

            foreach ($items as $item) {
                if (!isset($item['id_menu']) || !isset($item['jumlah'])) {
                    throw new \Exception('Data menu tidak lengkap.');
                }

                $idMenu = (int) $item['id_menu'];
                $jumlah = (int) $item['jumlah'];

                if ($jumlah <= 0) {
                    throw new \Exception('Jumlah menu tidak valid.');
                }

                $menu = $this->menuModel->find($idMenu);

                if (!$menu) {
                    throw new \Exception('Menu dengan ID ' . $idMenu . ' tidak ditemukan.');
                }

                if ($menu['status_stok'] !== 'Tersedia' || (int) $menu['jumlah_stok'] < $jumlah) {
                    throw new \Exception('Stok menu "' . $menu['nama_menu'] . '" tidak mencukupi.');
                }

                $subtotal = (float) $menu['harga'] * $jumlah;

                $this->detailModel->insert([
                    'id_pesanan'     => $idPesanan,
                    'id_menu'        => $idMenu,
                    'jumlah'         => $jumlah,
                    'catatan_khusus' => $item['catatan_khusus'] ?? null,
                    'subtotal'       => $subtotal,
                ]);
            }

            $details = $this->detailModel
                ->where('id_pesanan', $idPesanan)
                ->findAll();

            $total = 0;
            foreach ($details as $detail) {
                $total += (float) $detail['subtotal'];
            }

            // Nomor antrian harian mengikuti urutan transaksi masuk.
            $noAntrian = $this->pesananModel->nomorAntrianUntuk(
                $idPesanan,
                $insertPesanan['waktu_pesan']
            );

            $this->pesananModel->update($idPesanan, [
                'no_antrian'  => $noAntrian,
                'total_bayar' => $total
            ]);

            $batasWaktu = date(
                'Y-m-d H:i:s',
                strtotime(
                    '+' . PesananModel::PAYMENT_TIMEOUT_MINUTES . ' minutes'
                )
            );

            // Buat record pembayaran awal status 'Menunggu' (Sesuai Class Diagram PDF)
            try {
                $this->pembayaranModel->insert([
                    'id_pesanan'      => $idPesanan,
                    'id_kasir'        => null,
                    'metode'          => $metodeBayar,
                    'status_bayar'    => 'Menunggu',
                    'batas_waktu'     => $batasWaktu,
                    'jumlah_diterima' => 0,
                    'kembalian'       => 0,
                    'waktu_bayar'     => null,
                ]);
            } catch (\Throwable $e) {}

            if ($db->transStatus() === false) {
                $db->transRollback();

                return $this->response->setStatusCode(500)->setJSON([
                    'status'  => false,
                    'message' => 'Checkout gagal diproses.'
                ]);
            }

            $db->transCommit();

            return $this->response->setJSON([
                'status'         => true,
                'message'        => 'Pesanan berhasil dibuat.',
                'id_pesanan'     => $idPesanan,
                'kode_pesanan'   => $kodePesanan,
                'nama_customer'  => $namaCustomer,
                'nomor_meja'     => $nomorMeja,
                'id_customer'    => $idCustomer,
                'total_bayar'    => $total,
                'status_pesanan' => 'Menunggu Pembayaran',
                'metode_bayar'   => $metodeBayar,
                'no_antrian'     => $noAntrian,
                'batas_waktu'    => $batasWaktu,
                'data'           => [
                    'id_pesanan'    => $idPesanan,
                    'kode_pesanan'  => $kodePesanan,
                    'nama_customer' => $namaCustomer,
                    'nomor_meja'    => $nomorMeja,
                    'id_customer'   => $idCustomer,
                    'total_bayar'   => $total,
                    'status'        => 'Menunggu Pembayaran',
                    'metode_bayar'  => $metodeBayar,
                    'no_antrian'    => $noAntrian,
                    'batas_waktu'   => $batasWaktu,
                ]
            ]);

        } catch (\Throwable $e) {
            $db->transRollback();

            return $this->response->setStatusCode(400)->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function status($kodePesanan)
    {
        $pesanan = $this->findPesananByKode($kodePesanan);

        if (!$pesanan) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => false,
                'message' => 'Pesanan tidak ditemukan.'
            ]);
        }

        $detail = $this->detailModel
            ->select('
                detail_pesanan.*,
                menu.nama_menu,
                menu.harga
            ')
            ->join(
                'menu',
                'menu.id_menu = detail_pesanan.id_menu'
            )
            ->where(
                'detail_pesanan.id_pesanan',
                $pesanan['id_pesanan']
            )
            ->findAll();

        $pembayaran = $this->pembayaranModel
            ->where('id_pesanan', $pesanan['id_pesanan'])
            ->first();

        $notifikasi = $this->notifikasiModel
            ->where('id_pesanan', $pesanan['id_pesanan'])
            ->orderBy('waktu_kirim', 'DESC')
            ->first();

        return $this->response->setJSON([
            'status' => true,
            'data'   => [
                'pesanan'    => $pesanan,
                'detail'     => $detail,
                'pembayaran' => $pembayaran,
                'notifikasi' => $notifikasi
            ]
        ]);
    }

    /**
     * Konfirmasi pembayaran QRIS dari sisi customer.
     *
     * Pembayaran QRIS langsung dianggap valid setelah
     * customer menekan tombol konfirmasi. Pesanan langsung
     * berstatus "Dibayar" dan diteruskan ke dapur, tanpa
     * menunggu verifikasi kasir.
     */
    public function bayarQris($kodePesanan)
    {
        $pesanan = $this->findPesananByKode($kodePesanan);

        if (!$pesanan) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => false,
                'message' => 'Pesanan tidak ditemukan.'
            ]);
        }

        if (($pesanan['metode_bayar'] ?? 'Tunai') !== 'QRIS') {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => false,
                'message' => 'Pesanan ini tidak menggunakan pembayaran QRIS.'
            ]);
        }

        $pembayaran = $this->pembayaranModel
            ->where('id_pesanan', $pesanan['id_pesanan'])
            ->first();

        if ($pesanan['status'] === 'Kadaluarsa') {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => false,
                'message' => 'Waktu pembayaran 5 menit sudah habis. Pesanan dibatalkan otomatis.'
            ]);
        }

        // Jika sudah pernah diproses, jangan proses ulang.
        if ($pesanan['status'] !== 'Menunggu Pembayaran') {
            $statusSudahDiproses = [
                'Dibayar',
                'Diproses',
                'Siap Diambil',
                'Selesai',
            ];

            if (
                $pembayaran &&
                $pembayaran['status_bayar'] === 'Berhasil' &&
                in_array($pesanan['status'], $statusSudahDiproses, true)
            ) {
                return $this->response->setJSON([
                    'status'  => true,
                    'message' => 'Pembayaran QRIS sudah berhasil.',
                    'data'    => [
                        'status_bayar'   => 'Berhasil',
                        'waktu_bayar'    => $pembayaran['waktu_bayar'] ?? null,
                        'total_bayar'    => (float) $pesanan['total_bayar'],
                        'status_pesanan' => $pesanan['status'],
                    ]
                ]);
            }

            return $this->response->setStatusCode(400)->setJSON([
                'status'  => false,
                'message' => 'Pesanan ini sudah diproses lebih lanjut.'
            ]);
        }

        $details = $this->detailModel
            ->where('id_pesanan', $pesanan['id_pesanan'])
            ->findAll();

        if (empty($details)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => false,
                'message' => 'Detail pesanan tidak ditemukan.'
            ]);
        }

        // Validasi seluruh stok sebelum transaksi dijalankan.
        foreach ($details as $detail) {
            $menu = $this->menuModel->find($detail['id_menu']);

            if (!$menu) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => false,
                    'message' => 'Menu pada pesanan tidak ditemukan.'
                ]);
            }

            if (
                $menu['status_stok'] !== 'Tersedia' ||
                (int) $menu['jumlah_stok'] < (int) $detail['jumlah']
            ) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => false,
                    'message' => 'Stok menu "' . $menu['nama_menu'] . '" tidak mencukupi.'
                ]);
            }
        }

        $waktuBayar = date('Y-m-d H:i:s');
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            /*
             * Kunci transisi status. Hanya satu request yang boleh
             * mengubah Menunggu Pembayaran -> Dibayar, sehingga
             * stok dan total QRIS tidak terpotong dua kali saat
             * customer menekan tombol berulang kali.
             */
            $db->table('pesanan')
                ->where('id_pesanan', $pesanan['id_pesanan'])
                ->where('status', 'Menunggu Pembayaran')
                ->update([
                    'status'         => 'Dibayar',
                    'id_user_update' => null,
                    'updated_at'     => $waktuBayar,
                ]);

            if ($db->affectedRows() === 0) {
                $db->transRollback();

                $pesananTerbaru = $this->pesananModel
                    ->find($pesanan['id_pesanan']);

                $pembayaranTerbaru = $this->pembayaranModel
                    ->where('id_pesanan', $pesanan['id_pesanan'])
                    ->first();

                if (
                    $pesananTerbaru &&
                    $pembayaranTerbaru &&
                    $pembayaranTerbaru['status_bayar'] === 'Berhasil' &&
                    in_array(
                        $pesananTerbaru['status'],
                        ['Dibayar', 'Diproses', 'Siap Diambil', 'Selesai'],
                        true
                    )
                ) {
                    return $this->response->setJSON([
                        'status'  => true,
                        'message' => 'Pembayaran QRIS sudah berhasil.',
                        'data'    => [
                            'status_bayar'   => 'Berhasil',
                            'waktu_bayar'    => $pembayaranTerbaru['waktu_bayar'] ?? null,
                            'total_bayar'    => (float) $pesananTerbaru['total_bayar'],
                            'status_pesanan' => $pesananTerbaru['status'],
                        ]
                    ]);
                }

                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => false,
                    'message' => 'Pesanan ini sudah diproses lebih lanjut.'
                ]);
            }

            $dataPembayaran = [
                'id_pesanan'      => $pesanan['id_pesanan'],
                'id_kasir'        => null,
                'metode'          => 'QRIS',
                'status_bayar'    => 'Berhasil',
                'batas_waktu'     => $pembayaran['batas_waktu']
                    ?? date(
                        'Y-m-d H:i:s',
                        strtotime(
                            '+' . PesananModel::PAYMENT_TIMEOUT_MINUTES . ' minutes'
                        )
                    ),
                'jumlah_diterima' => (float) $pesanan['total_bayar'],
                'kembalian'       => 0,
                'waktu_bayar'     => $waktuBayar,
            ];

            if ($pembayaran) {
                $this->pembayaranModel->update(
                    $pembayaran['id_pembayaran'],
                    $dataPembayaran
                );
            } else {
                $this->pembayaranModel->insert($dataPembayaran);
            }

            // Kurangi stok menu.
            foreach ($details as $detail) {
                $menu = $this->menuModel->find($detail['id_menu']);
                $stokBaru = (int) $menu['jumlah_stok'] - (int) $detail['jumlah'];

                $this->menuModel->update($menu['id_menu'], [
                    'jumlah_stok' => $stokBaru,
                    'status_stok' => $stokBaru > 0 ? 'Tersedia' : 'Habis',
                ]);
            }

            // Catat QRIS ke shift aktif (jika ada).
            $shiftAktif = $this->shiftModel
                ->where('waktu_selesai', null)
                ->first();

            if ($shiftAktif) {
                $this->shiftModel->update($shiftAktif['id_shift'], [
                    'total_kas_qris' =>
                        (float) ($shiftAktif['total_kas_qris'] ?? 0)
                        + (float) $pesanan['total_bayar'],
                ]);
            }

            $this->notifikasiModel->insert([
                'id_pesanan'  => $pesanan['id_pesanan'],
                'pesan'       => 'Pesanan baru sudah dibayar via QRIS dan siap diproses di dapur.',
                'status_baca' => false,
                'waktu_kirim' => $waktuBayar,
            ]);

            if ($db->transStatus() === false) {
                $db->transRollback();

                return $this->response->setStatusCode(500)->setJSON([
                    'status'  => false,
                    'message' => 'Pembayaran QRIS gagal diproses.'
                ]);
            }

            $db->transCommit();

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Pembayaran QRIS berhasil. Pesanan langsung diteruskan ke dapur.',
                'data'    => [
                    'status_bayar'   => 'Berhasil',
                    'waktu_bayar'    => $waktuBayar,
                    'total_bayar'    => (float) $pesanan['total_bayar'],
                    'status_pesanan' => 'Dibayar',
                ]
            ]);

        } catch (\Throwable $e) {
            $db->transRollback();

            return $this->response->setStatusCode(400)->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Customer menandai bahwa pesanan sudah diambil
     * (self service). Status berubah Siap Diambil -> Selesai.
     */
    public function ambil($kodePesanan)
    {
        $pesanan = $this->findPesananByKode($kodePesanan);

        if (!$pesanan) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => false,
                'message' => 'Pesanan tidak ditemukan.'
            ]);
        }

        if ($pesanan['status'] === 'Selesai') {
            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Pesanan ini sudah ditandai selesai.',
                'data'    => [
                    'kode_pesanan'   => $pesanan['kode_pesanan'],
                    'status_pesanan' => 'Selesai',
                ]
            ]);
        }

        if ($pesanan['status'] !== 'Siap Diambil') {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => false,
                'message' => 'Pesanan belum berstatus Siap Diambil.'
            ]);
        }

        $berhasil = $this->pesananModel->update($pesanan['id_pesanan'], [
            'status' => 'Selesai',
        ]);

        if (!$berhasil) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => false,
                'message' => 'Gagal menandai pesanan selesai. Silakan coba lagi.'
            ]);
        }

        $this->notifikasiModel->insert([
            'id_pesanan'  => $pesanan['id_pesanan'],
            'pesan'       => 'Pesanan ' . $pesanan['kode_pesanan'] . ' telah diambil customer (self service).',
            'status_baca' => false,
            'waktu_kirim' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Pesanan sudah ditandai selesai. Terima kasih!',
            'data'    => [
                'kode_pesanan'   => $pesanan['kode_pesanan'],
                'status_pesanan' => 'Selesai',
            ]
        ]);
    }
}
