<?php

namespace App\Models;

use CodeIgniter\Model;

class PesananModel extends Model
{
    /**
     * Batas waktu pembayaran dalam menit. Pesanan yang masih
     * "Menunggu Pembayaran" setelah batas ini akan otomatis
     * menjadi "Kadaluarsa".
     */
    public const PAYMENT_TIMEOUT_MINUTES = 5;

    protected $table            = 'pesanan';
    protected $primaryKey       = 'id_pesanan';
    protected $useAutoIncrement = true;

    protected $returnType = 'array';

    protected $allowedFields = [
        'no_antrian',
        'kode_pesanan',
        'nama_customer',
        'nomor_meja',
        'id_customer',
        'waktu_pesan',
        'status',
        'total_bayar',
        'metode_bayar',
        'id_user_update',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected static $schemaChecked = false;

    protected function initialize()
    {
        parent::initialize();
        $this->ensureColumns();
    }

    /**
     * Memastikan kolom identitas pemesan & nomor meja otomatis tersedia di MySQL
     */
    public function ensureColumns()
    {
        if (self::$schemaChecked) {
            return;
        }

        try {
            $db = \Config\Database::connect();
            $fields = $db->getFieldNames($this->table);

            if (!in_array('nama_customer', $fields, true)) {
                $db->query("ALTER TABLE `{$this->table}` ADD `nama_customer` VARCHAR(100) NULL DEFAULT 'Pelanggan'");
            }
            if (!in_array('nomor_meja', $fields, true)) {
                $db->query("ALTER TABLE `{$this->table}` ADD `nomor_meja` VARCHAR(50) NULL DEFAULT 'Meja -'");
            }
            if (!in_array('id_customer', $fields, true)) {
                $db->query("ALTER TABLE `{$this->table}` ADD `id_customer` VARCHAR(50) NULL DEFAULT ''");
            }
            if (!in_array('metode_bayar', $fields, true)) {
                $db->query("ALTER TABLE `{$this->table}` ADD `metode_bayar` VARCHAR(50) NULL DEFAULT 'Tunai'");
            }
            if (!in_array('no_antrian', $fields, true)) {
                $db->query("ALTER TABLE `{$this->table}` ADD `no_antrian` INT UNSIGNED NULL");
            }

            self::$schemaChecked = true;
        } catch (\Throwable $e) {
            // Lanjutkan jika ada kondisi offline / instalasi awal
        }
    }

    /**
     * Nomor antrian harian berdasarkan urutan transaksi masuk.
     *
     * Nomor dihitung dari jumlah pesanan pada tanggal yang sama
     * dengan id_pesanan lebih kecil atau sama dengan pesanan ini.
     * Dengan begitu nomor tetap stabil, berurutan, dan tidak
     * bergeser ketika ada pesanan lain yang dibatalkan.
     */
    public function nomorAntrianUntuk($idPesanan, $waktuPesan = null)
    {
        $waktuPesan = $waktuPesan ?: date('Y-m-d H:i:s');
        $tanggal = substr((string) $waktuPesan, 0, 10);
        $mulai = $tanggal . ' 00:00:00';
        $selesai = date('Y-m-d H:i:s', strtotime($tanggal . ' +1 day'));

        return (int) $this->db->table($this->table)
            ->where('waktu_pesan >=', $mulai)
            ->where('waktu_pesan <', $selesai)
            ->where('id_pesanan <=', (int) $idPesanan)
            ->countAllResults();
    }

    /**
     * Batalkan otomatis pesanan yang melewati batas pembayaran.
     *
     * Hanya pesanan berstatus "Menunggu Pembayaran" dengan
     * pembayaran "Menunggu" yang diproses. Pesanan yang sudah
     * dibayar tidak akan pernah dibatalkan oleh method ini.
     */
    public function expireUnpaidOrders()
    {
        $now = date('Y-m-d H:i:s');
        $batasFallback = date(
            'Y-m-d H:i:s',
            strtotime('-' . self::PAYMENT_TIMEOUT_MINUTES . ' minutes')
        );

        $sql = "SELECT p.id_pesanan, b.id_pembayaran
                FROM pesanan p
                LEFT JOIN pembayaran b ON b.id_pesanan = p.id_pesanan
                WHERE p.status = 'Menunggu Pembayaran'
                  AND (
                      (
                          b.status_bayar = 'Menunggu'
                          AND b.batas_waktu IS NOT NULL
                          AND b.batas_waktu < ?
                      )
                      OR (
                          b.status_bayar = 'Menunggu'
                          AND b.batas_waktu IS NULL
                          AND p.waktu_pesan < ?
                      )
                      OR (
                          b.id_pembayaran IS NULL
                          AND p.waktu_pesan < ?
                      )
                  )";

        $rows = $this->db->query(
            $sql,
            [$now, $batasFallback, $batasFallback]
        )->getResultArray();

        if (empty($rows)) {
            return 0;
        }

        $this->db->transBegin();
        $jumlah = 0;

        try {
            foreach ($rows as $row) {
                $this->db->table($this->table)
                    ->where('id_pesanan', $row['id_pesanan'])
                    ->where('status', 'Menunggu Pembayaran')
                    ->update([
                        'status'     => 'Kadaluarsa',
                        'updated_at' => $now,
                    ]);

                if ($this->db->affectedRows() < 1) {
                    continue;
                }

                $jumlah++;

                if (!empty($row['id_pembayaran'])) {
                    $this->db->table('pembayaran')
                        ->where('id_pembayaran', $row['id_pembayaran'])
                        ->where('status_bayar', 'Menunggu')
                        ->update(['status_bayar' => 'Kadaluarsa']);
                }
            }

            $this->db->transCommit();
        } catch (\Throwable $e) {
            $this->db->transRollback();

            throw $e;
        }

        return $jumlah;
    }
}
