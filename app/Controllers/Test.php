<?php

namespace App\Controllers;

use App\Models\UserModel;

class Test extends BaseController
{
    public function user()
    {
        $userModel = new UserModel();
        $users = $userModel->findAll();

        return $this->response->setJSON($users);
    }

    public function initUsers()
    {
        $db = \Config\Database::connect();

        // 1. Pastikan tabel user ada
        $db->query("CREATE TABLE IF NOT EXISTS `user` (
          `id_user` INT AUTO_INCREMENT PRIMARY KEY,
          `nama` VARCHAR(100) NOT NULL,
          `email` VARCHAR(100) NOT NULL UNIQUE,
          `password` VARCHAR(255) NOT NULL,
          `role` ENUM('kasir', 'dapur', 'manajerial') NOT NULL,
          `created_at` DATETIME NULL,
          `updated_at` DATETIME NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // 2. Pastikan tabel shift ada (untuk kasir)
        $db->query("CREATE TABLE IF NOT EXISTS `shift` (
          `id_shift` INT AUTO_INCREMENT PRIMARY KEY,
          `id_kasir` INT NOT NULL,
          `waktu_mulai` DATETIME NOT NULL,
          `waktu_selesai` DATETIME NULL,
          `total_kas_tunai` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
          `total_kas_qris` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
          `created_at` DATETIME NULL,
          `updated_at` DATETIME NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $userModel = new UserModel();

        $defaultUsers = [
            [
                'nama'     => 'Kasir Kantin Merah',
                'email'    => 'kasir@kantinmerah.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role'     => 'kasir',
            ],
            [
                'nama'     => 'Chef Dapur Merah',
                'email'    => 'dapur@kantinmerah.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role'     => 'dapur',
            ],
            [
                'nama'     => 'Manajer Kantin Merah',
                'email'    => 'manajerial@kantinmerah.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role'     => 'manajerial',
            ],
        ];

        $results = [];
        foreach ($defaultUsers as $u) {
            $existing = $userModel->where('email', $u['email'])->first();
            if ($existing) {
                $userModel->update($existing['id_user'], $u);
                $results[] = "Updated: {$u['email']} (role: {$u['role']})";
            } else {
                $userModel->insert($u);
                $results[] = "Created: {$u['email']} (role: {$u['role']})";
            }
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Tabel dan Akun demo berhasil diinisialisasi! Password default: password123',
            'detail'  => $results
        ]);
    }
}
