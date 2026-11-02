<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePesananTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pesanan' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_pesanan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'unique'     => true,
            ],
            'waktu_pesan' => [
                'type' => 'DATETIME',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Menunggu Pembayaran', 'Dibayar', 'Diproses', 'Siap Diambil', 'Selesai', 'Kadaluarsa', 'Dibatalkan'],
                'default'    => 'Menunggu Pembayaran',
            ],
            'total_bayar' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'metode_bayar' => [
                'type'       => 'ENUM',
                'constraint' => ['Tunai', 'QRIS'],
                'default'    => 'Tunai',
                'null'       => false,
            ],
            'id_user_update' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id_pesanan');
        $this->forge->addForeignKey('id_user_update', 'user', 'id_user', 'CASCADE', 'SET NULL');
        $this->forge->createTable('pesanan');
    }

    public function down()
    {
        $this->forge->dropTable('pesanan');
    }
}