<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\UserModel;

class UserSeeder extends Seeder
{
    public function run()
    {
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

        foreach ($defaultUsers as $u) {
            $existing = $userModel->where('email', $u['email'])->first();
            if ($existing) {
                $userModel->update($existing['id_user'], $u);
            } else {
                $userModel->insert($u);
            }
        }
    }
}

