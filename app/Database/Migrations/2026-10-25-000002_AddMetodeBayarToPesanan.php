<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMetodeBayarToPesanan extends Migration
{
    public function up()
    {
        // Kolom bisa saja sudah ditambahkan otomatis oleh
        // controller pada instalasi lama. Jangan gagal jika
        // kolomnya sudah ada.
        if ($this->db->fieldExists('metode_bayar', 'pesanan')) {
            return;
        }

        $this->forge->addColumn('pesanan', [
            'metode_bayar' => [
                'type'       => 'ENUM',
                'constraint' => ['Tunai', 'QRIS'],
                'default'    => 'Tunai',
                'null'       => false,
                'after'      => 'total_bayar',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pesanan', 'metode_bayar');
    }
}
