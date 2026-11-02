<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $role = session()->get('role');

        if (!$role) {
            return redirect()->to(site_url('login'))
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $userRole = strtolower(trim((string) $role));

        if (!empty($arguments)) {
            $allowedRoles = array_map(fn($a) => strtolower(trim((string) $a)), (array) $arguments);

            if (!in_array($userRole, $allowedRoles, true)) {
                return redirect()->to(site_url('login'))
                    ->with('error', 'Akses ditolak: Halaman ini khusus role [' . implode(', ', $allowedRoles) . '], sedangkan akun Anda ber-role [' . $userRole . '].');
            }
        }
    }

    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {
        // Tidak ada proses setelah request.
    }
}
