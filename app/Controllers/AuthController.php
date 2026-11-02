<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login()
    {
        // Jika sudah login, langsung arahkan ke dashboard role yang sesuai
        $session = session();
        if ($session->get('logged_in')) {
            $role = strtolower(trim((string) $session->get('role')));
            switch ($role) {
                case 'kasir':
                    return redirect()->to(site_url('kasir'));
                case 'dapur':
                    return redirect()->to(site_url('dapur'));
                case 'manajerial':
                    return redirect()->to(site_url('manajerial'));
            }
        }

        return view('auth/login');
    }

    public function attemptLogin()
    {
        $session = session();
        $userModel = new UserModel();

        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');

        $user = $userModel->where('email', $email)->first();

        // Auto-provisioning akun demo jika belum ada di database
        if (!$user) {
            $demoAccounts = [
                'kasir@kantinmerah.com'      => ['nama' => 'Kasir Kantin Merah', 'role' => 'kasir'],
                'dapur@kantinmerah.com'      => ['nama' => 'Chef Dapur Merah', 'role' => 'dapur'],
                'manajerial@kantinmerah.com' => ['nama' => 'Manajer Kantin Merah', 'role' => 'manajerial'],
            ];

            if (isset($demoAccounts[$email]) && $password === 'password123') {
                try {
                    $insertId = $userModel->insert([
                        'nama'     => $demoAccounts[$email]['nama'],
                        'email'    => $email,
                        'password' => password_hash('password123', PASSWORD_DEFAULT),
                        'role'     => $demoAccounts[$email]['role'],
                    ]);
                    $user = $userModel->find($insertId);
                } catch (\Throwable $e) {
                    // jika table belum siap, lanjutkan error
                }
            }
        }

        if (!$user) {
            return redirect()->to(site_url('login'))
                ->withInput()
                ->with('error', 'Akun dengan email "' . esc($email) . '" belum terdaftar di database.');
        }

        // Verifikasi password (dengan fallback dev password123)
        $isValidPassword = password_verify($password, $user['password'])
            || ($user['password'] === $password)
            || ($password === 'password123');

        if (!$isValidPassword) {
            return redirect()->to(site_url('login'))
                ->withInput()
                ->with('error', 'Password salah untuk email ' . esc($email));
        }

        // Perbarui hash jika belum standar
        if (!password_verify($password, $user['password'])) {
            try {
                $userModel->update($user['id_user'], [
                    'password' => password_hash($password, PASSWORD_DEFAULT)
                ]);
            } catch (\Throwable $e) {}
        }

        $userRole = strtolower(trim((string) $user['role']));

        $session->set([
            'id_user'   => $user['id_user'],
            'nama'      => $user['nama'],
            'email'     => $user['email'],
            'role'      => $userRole,
            'logged_in' => true,
        ]);

        switch ($userRole) {
            case 'kasir':
                return redirect()->to(site_url('kasir'));

            case 'dapur':
                return redirect()->to(site_url('dapur'));

            case 'manajerial':
                return redirect()->to(site_url('manajerial'));

            default:
                $session->destroy();

                return redirect()->to(site_url('login'))
                    ->with('error', 'Role pengguna tidak valid: ' . esc($user['role']));
        }
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to(site_url('login'))
            ->with('success', 'Berhasil logout.');
    }
}
