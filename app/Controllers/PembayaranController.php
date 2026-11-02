<?php

namespace App\Controllers;

use App\Models\PesananModel;
use App\Models\DetailPesananModel;
use App\Models\PembayaranModel;
use App\Models\MenuModel;
use App\Models\ShiftModel;
use App\Models\NotifikasiModel;

class PembayaranController extends BaseController
{
    protected $pembayaranModel;
    protected $pesananModel;
    protected $detailPesananModel;
    protected $menuModel;
    protected $shiftModel;
    protected $notifikasiModel;

    public function __construct()
    {
        $this->pembayaranModel = new PembayaranModel();
        $this->pesananModel = new PesananModel();
        $this->detailPesananModel = new DetailPesananModel();
        $this->menuModel = new MenuModel();
        $this->shiftModel = new ShiftModel();
        $this->notifikasiModel = new NotifikasiModel();
    }

    /*
     * ==========================================
     * MENAMPILKAN PEMBAYARAN BERDASARKAN PESANAN
     * ==========================================
     */
    public function showByPesanan($idPesanan)
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

        $pembayaran = $this->pembayaranModel
            ->where('id_pesanan', $idPesanan)
            ->first();

        return $this->response->setJSON([
            'status' => true,
            'data'   => $pembayaran
        ]);
    }

    /*
     * ==========================================
     * MEMPROSES PEMBAYARAN
     * ==========================================
     */
    public function store($idPesanan)
    {
        /*
         * 1. Validasi pesanan.
         */
        $pesanan = $this->pesananModel->find($idPesanan);

        if (!$pesanan) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Pesanan tidak ditemukan.'
                ]);
        }

        $totalBayar = (float) $pesanan['total_bayar'];

        if ($totalBayar <= 0) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Total pembayaran belum tersedia.'
                ]);
        }

        /*
         * 2. Pastikan pesanan belum dibayar.
         */
        if ($pesanan['status'] === 'Kadaluarsa') {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Waktu pembayaran 5 menit sudah habis. Pesanan dibatalkan otomatis.'
                ]);
        }

        if ($pesanan['status'] !== 'Menunggu Pembayaran') {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Pesanan tidak berada dalam status Menunggu Pembayaran.'
                ]);
        }

        /*
         * 3. Validasi metode pembayaran.
         */
        $metode = $this->request->getPost('metode');

        if (!in_array($metode, ['Tunai', 'QRIS'], true)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Metode pembayaran tidak valid.'
                ]);
        }

        /*
         * 4. Periksa data pembayaran yang mungkin sudah
         *    dibuat saat customer melakukan checkout.
         *
         *    Record berstatus Menunggu / Kadaluarsa / Gagal
         *    masih boleh diproses ulang dengan cara
         *    memperbarui record yang sama.
         */
        $existingPayment = $this->pembayaranModel
            ->where('id_pesanan', $idPesanan)
            ->first();

        if (
            $existingPayment &&
            $existingPayment['status_bayar'] === 'Berhasil' &&
            $metode === 'QRIS'
        ) {
            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Pembayaran QRIS sudah dikonfirmasi customer.',
                'id'      => $existingPayment['id_pembayaran'],
                'data'    => [
                    'id_pesanan'   => $idPesanan,
                    'metode'       => 'QRIS',
                    'total_bayar'  => $totalBayar,
                    'status_bayar' => 'Berhasil',
                    'batas_waktu'  => $existingPayment['batas_waktu'] ?? null,
                ]
            ]);
        }

        /*
         * ==========================================
         * PEMBAYARAN TUNAI
         * ==========================================
         */
        if ($metode === 'Tunai') {

            /*
             * Pembayaran tunai hanya untuk kasir
             * yang sudah login.
             */
            if (
                !session()->get('logged_in') ||
                session()->get('role') !== 'kasir'
            ) {
                return $this->response
                    ->setStatusCode(403)
                    ->setJSON([
                        'status'  => false,
                        'message' => 'Hanya kasir yang sudah login yang dapat memproses pembayaran tunai.'
                    ]);
            }

            $idKasir = session()->get('id_user');

            /*
             * Cari shift aktif milik kasir.
             */
            $shiftAktif = $this->shiftModel
                ->where('id_kasir', $idKasir)
                ->where('waktu_selesai', null)
                ->first();

            if (!$shiftAktif) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'status'  => false,
                        'message' => 'Kasir belum memiliki shift aktif.'
                    ]);
            }

            /*
             * Validasi jumlah uang yang diterima.
             */
            $jumlahDiterima = $this->request
                ->getPost('jumlah_diterima');

            if (
                $jumlahDiterima === null ||
                $jumlahDiterima === '' ||
                !is_numeric($jumlahDiterima)
            ) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'status'  => false,
                        'message' => 'Jumlah uang yang diterima wajib diisi dengan angka yang valid.'
                    ]);
            }

            $jumlahDiterima = (float) $jumlahDiterima;

            if ($jumlahDiterima <= 0) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'status'  => false,
                        'message' => 'Jumlah uang yang diterima harus lebih dari 0.'
                    ]);
            }

            if ($jumlahDiterima < $totalBayar) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'status'  => false,
                        'message' => 'Uang yang diterima kurang dari total pembayaran.'
                    ]);
            }

            $kembalian = $jumlahDiterima - $totalBayar;

            /*
             * ==========================================
             * TRANSAKSI DATABASE PEMBAYARAN TUNAI
             * ==========================================
             */
            $db = \Config\Database::connect();
            $db->transBegin();

            try {
                /*
                 * 1. Simpan / perbarui pembayaran.
                 *
                 *    Record awal biasanya sudah dibuat
                 *    ketika customer checkout, sehingga
                 *    record tersebut diperbarui agar tidak
                 *    melanggar UNIQUE(id_pesanan).
                 */
                $dataPembayaran = [
                    'id_pesanan'      => $idPesanan,
                    'id_kasir'        => $idKasir,
                    'metode'          => 'Tunai',
                    'status_bayar'    => 'Berhasil',
                    'batas_waktu'     => null,
                    'jumlah_diterima' => $jumlahDiterima,
                    'kembalian'       => $kembalian,
                    'waktu_bayar'     => date('Y-m-d H:i:s'),
                ];

                if ($existingPayment) {
                    $simpanPembayaran = $this->pembayaranModel
                        ->update(
                            $existingPayment['id_pembayaran'],
                            $dataPembayaran
                        );
                } else {
                    $simpanPembayaran = $this->pembayaranModel
                        ->insert($dataPembayaran);
                }

                if (!$simpanPembayaran) {
                    $db->transRollback();

                    return $this->response
                        ->setStatusCode(500)
                        ->setJSON([
                            'status'  => false,
                            'message' => 'Data pembayaran gagal disimpan.'
                        ]);
                }

                /*
                 * 2. Kurangi stok menu yang dipesan.
                 */
                $stokBerhasil = $this->kurangiStokPesanan(
                    $idPesanan
                );

                if (!$stokBerhasil) {
                    $db->transRollback();

                    return $this->response
                        ->setStatusCode(400)
                        ->setJSON([
                            'status'  => false,
                            'message' => 'Stok menu tidak mencukupi atau detail pesanan tidak ditemukan.'
                        ]);
                }

                /*
                 * 3. Ubah status pesanan menjadi Dibayar.
                 */
                $this->pesananModel->update($idPesanan, [
                    'status'         => 'Dibayar',
                    'metode_bayar'   => 'Tunai',
                    'id_user_update' => $idKasir,
                ]);

                /*
                 * 4. Tambahkan total transaksi
                 *    ke kas tunai shift.
                 */
                $totalKasTunaiBaru =
                    (float) ($shiftAktif['total_kas_tunai'] ?? 0)
                    + $totalBayar;

                $this->shiftModel->update(
                    $shiftAktif['id_shift'],
                    [
                        'total_kas_tunai' => $totalKasTunaiBaru,
                    ]
                );

                /*
                 * 5. Buat notifikasi untuk dapur.
                 */
                $this->notifikasiModel->insert([
                    'id_pesanan'  => $idPesanan,
                    'pesan'       => 'Pesanan baru siap diproses di dapur.',
                    'status_baca' => false,
                    'waktu_kirim' => date('Y-m-d H:i:s'),
                ]);

                /*
                 * 6. Periksa hasil transaksi database.
                 */
                if ($db->transStatus() === false) {
                    $db->transRollback();

                    return $this->response
                        ->setStatusCode(500)
                        ->setJSON([
                            'status'  => false,
                            'message' => 'Transaksi pembayaran gagal diproses.'
                        ]);
                }

                /*
                 * 7. Simpan seluruh perubahan.
                 */
                $db->transCommit();

            } catch (\Throwable $e) {
                $db->transRollback();

                log_message(
                    'error',
                    'Pembayaran tunai gagal untuk pesanan '
                    . $idPesanan . ': ' . $e->getMessage()
                );

                return $this->response
                    ->setStatusCode(500)
                    ->setJSON([
                        'status'  => false,
                        'message' => 'Terjadi kesalahan saat memproses pembayaran.'
                    ]);
            }

            /*
             * Respons jika pembayaran tunai berhasil.
             */
            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Pembayaran tunai berhasil.',
                'data'    => [
                    'id_pesanan'      => $idPesanan,
                    'id_shift'        => $shiftAktif['id_shift'],
                    'metode'          => 'Tunai',
                    'total_bayar'     => $totalBayar,
                    'jumlah_diterima' => $jumlahDiterima,
                    'kembalian'       => $kembalian,
                    'status_bayar'    => 'Berhasil',
                    'status_pesanan'  => 'Dibayar',
                    'total_kas_tunai' => $totalKasTunaiBaru,
                ]
            ]);
        }

        /*
         * ==========================================
         * PEMBAYARAN QRIS
         * ==========================================
         *
         * Bagian ini menyiapkan data pembayaran
         * berstatus Menunggu.
         *
         * Customer kemudian mengonfirmasi pembayaran
         * QRIS melalui halaman status pesanan. Setelah
         * konfirmasi, pembayaran langsung valid dan
         * pesanan diteruskan ke dapur.
         */

        if ($metode === 'QRIS') {
            $batasWaktu = !empty($existingPayment['batas_waktu'])
                ? $existingPayment['batas_waktu']
                : date(
                    'Y-m-d H:i:s',
                    strtotime(
                        '+' . \App\Models\PesananModel::PAYMENT_TIMEOUT_MINUTES . ' minutes'
                    )
                );

            $dataPembayaran = [
                'id_pesanan'      => $idPesanan,
                'id_kasir'        => null,
                'metode'          => 'QRIS',
                'status_bayar'    => 'Menunggu',
                'batas_waktu'     => $batasWaktu,
                'jumlah_diterima' => 0,
                'kembalian'       => 0,
                'waktu_bayar'     => null,
            ];

            if ($existingPayment) {
                $berhasil = $this->pembayaranModel
                    ->update(
                        $existingPayment['id_pembayaran'],
                        $dataPembayaran
                    );
            } else {
                $berhasil = $this->pembayaranModel
                    ->insert($dataPembayaran);
            }

            if (!$berhasil) {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON([
                        'status'  => false,
                        'message' => 'Data pembayaran QRIS gagal dibuat.'
                    ]);
            }

            // Sinkronkan metode bayar pesanan.
            $this->pesananModel->update($idPesanan, [
                'metode_bayar' => 'QRIS',
            ]);

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Data pembayaran QRIS berhasil dibuat dan menunggu pembayaran.',
                'id'      => $existingPayment['id_pembayaran']
                    ?? $this->pembayaranModel->getInsertID(),
                'data'    => [
                    'id_pesanan'   => $idPesanan,
                    'metode'       => 'QRIS',
                    'total_bayar'  => $totalBayar,
                    'status_bayar' => 'Menunggu',
                    'batas_waktu'  => $batasWaktu,
                ]
            ]);
        }

        /*
         * Pengaman jika metode tidak dikenali.
         */
        return $this->response
            ->setStatusCode(400)
            ->setJSON([
                'status'  => false,
                'message' => 'Metode pembayaran tidak dikenali.'
            ]);
    }

    /*
     * ==========================================
     * MENGURANGI STOK PESANAN
     * ==========================================
     */
    private function kurangiStokPesanan($idPesanan)
    {
        $details = $this->detailPesananModel
            ->where('id_pesanan', $idPesanan)
            ->findAll();
         if (empty($details)) {
            return false;
        }

        /*
         * Validasi semua stok terlebih dahulu,
         * sebelum mengubah stok menu mana pun.
         */
        foreach ($details as $detail) {
            $menu = $this->menuModel->find(
                $detail['id_menu']
            );

            if (!$menu) {
                return false;
            }

            $stokSekarang = (int) $menu['jumlah_stok'];
            $jumlahDipesan = (int) $detail['jumlah'];

            if ($jumlahDipesan <= 0) {
                return false;
            }

            if ($stokSekarang < $jumlahDipesan) {
                return false;
            }
        }

        /*
         * Jika seluruh stok mencukupi,
         * baru lakukan pengurangan stok.
         */
        foreach ($details as $detail) {
            $menu = $this->menuModel->find(
                $detail['id_menu']
            );

            $stokBaru =
                (int) $menu['jumlah_stok']
                - (int) $detail['jumlah'];

            $statusStok = ($stokBaru > 0)
                ? 'Tersedia'
                : 'Habis';

            $berhasil = $this->menuModel->update(
                $menu['id_menu'],
                [
                    'jumlah_stok' => $stokBaru,
                    'status_stok' => $statusStok,
                ]
            );

            if (!$berhasil) {
                return false;
            }
        }

        return true;
    }
}