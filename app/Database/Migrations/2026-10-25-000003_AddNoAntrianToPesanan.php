<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNoAntrianToPesanan extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('no_antrian', 'pesanan')) {
            $this->forge->addColumn('pesanan', [
                'no_antrian' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
            ]);
        }

        // Isi nomor antrian pesanan lama berdasarkan urutan masuk per hari.
        $rows = $this->db->table('pesanan')
            ->select('id_pesanan, waktu_pesan')
            ->orderBy('waktu_pesan', 'ASC')
            ->orderBy('id_pesanan', 'ASC')
            ->get()
            ->getResultArray();

        $tanggalTerakhir = null;
        $nomor = 0;

        foreach ($rows as $row) {
            $tanggal = substr((string) $row['waktu_pesan'], 0, 10);

            if ($tanggal !== $tanggalTerakhir) {
                $tanggalTerakhir = $tanggal;
                $nomor = 0;
            }

            $nomor++;

            $this->db->table('pesanan')
                ->where('id_pesanan', $row['id_pesanan'])
                ->update(['no_antrian' => $nomor]);
        }

        try {
            $this->db->query(
                'ALTER TABLE `pesanan` ADD INDEX `idx_pesanan_no_antrian` (`no_antrian`)'
            );
        } catch (\Throwable $e) {
            // Index mungkin sudah ada.
        }
    }

    public function down()
    {
        try {
            $this->db->query(
                'ALTER TABLE `pesanan` DROP INDEX `idx_pesanan_no_antrian`'
            );
        } catch (\Throwable $e) {
            // Index mungkin belum ada.
        }

        if ($this->db->fieldExists('no_antrian', 'pesanan')) {
            $this->forge->dropColumn('pesanan', 'no_antrian');
        }
    }
}
