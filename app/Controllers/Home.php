<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $session = session();

        // Jika staf sudah login, arahkan langsung ke dashboard masing-masing
        if ($session->get('logged_in')) {
            switch ($session->get('role')) {
                case 'kasir':
                    return redirect()->to(site_url('kasir'));
                case 'dapur':
                    return redirect()->to(site_url('dapur'));
                case 'manajerial':
                    return redirect()->to(site_url('manajerial'));
            }
        }

        // Scan QR meja kantin mengarah langsung ke pemesanan customer
        return redirect()->to(site_url('customer'));
    }
}
