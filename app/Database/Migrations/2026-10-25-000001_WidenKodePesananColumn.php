<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class WidenKodePesananColumn extends Migration
{
    public function up()
    {
        /*
         * Kode pesanan yang dibuat aplikasi berformat
         * ORD-YYYYMMDDHHMMSS-XXX (22 karakter), sedangkan
         * kolom lama hanya VARCHAR(20) sehingga kode terpotong.
         */
        $this->forge->modifyColumn('pesanan', [
            'kode_pesanan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
        ]);
    }

    public function down()
    {
        // Tidak dikembalikan ke 20 karena kode yang sudah ada
        // bisa lebih panjang dan akan terpotong.
    }
}
