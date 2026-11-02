<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/', 'Home::index');

// ======================================================
// TEST
// ======================================================

$routes->get('test/user', 'Test::user');
$routes->get('test/init-users', 'Test::initUsers');

// ======================================================
// AUTHENTICATION
// ======================================================

$routes->get('login', 'AuthController::login');
$routes->post('login/attempt', 'AuthController::attemptLogin');
$routes->get('logout', 'AuthController::logout');

// ======================================================
// KASIR
// ======================================================

$routes->get(
    'kasir',
    'KasirController::index',
    [
        'filter' => ['auth', 'role:kasir']
    ]
);

$routes->post(
    'kasir/pesanan/(:num)/selesai',
    'KasirController::selesai/$1',
    [
        'filter' => ['auth', 'role:kasir']
    ]
);

$routes->get(
    'kasir/riwayat',
    'KasirController::riwayat',
    [
        'filter' => ['auth', 'role:kasir']
    ]
);

$routes->get(
    'kasir/riwayat/data',
    'KasirController::riwayatData',
    [
        'filter' => ['auth', 'role:kasir']
    ]
);

// Kompatibilitas tautan lama: Pemrosesan Dapur sekarang menjadi Riwayat Transaksi.
$routes->get(
    'kasir/pemrosesan',
    static function () {
        return redirect()->to(site_url('kasir/riwayat'));
    },
    [
        'filter' => ['auth', 'role:kasir']
    ]
);

$routes->get(
    'kasir/pemrosesan/data',
    'KasirController::riwayatData',
    [
        'filter' => ['auth', 'role:kasir']
    ]
);

// ======================================================
// PESANAN
// ======================================================

$routes->get(
    'pesanan',
    'PesananController::index'
);

$routes->get(
    'pesanan/(:num)',
    'PesananController::show/$1'
);

$routes->post(
    'pesanan',
    'PesananController::store'
);

$routes->post(
    'pesanan/(:num)/status',
    'PesananController::updateStatus/$1'
);

$routes->get(
    'pesanan/(:num)/total',
    'PesananController::calculateTotal/$1'
);

// ======================================================
// DETAIL PESANAN
// ======================================================

$routes->post(
    'pesanan/(:num)/detail',
    'DetailPesananController::store/$1'
);

$routes->delete(
    'pesanan/detail/(:num)',
    'DetailPesananController::delete/$1'
);

// ======================================================
// PEMBAYARAN
// ======================================================

$routes->get(
    'pesanan/(:num)/pembayaran',
    'PembayaranController::showByPesanan/$1'
);

$routes->post(
    'pesanan/(:num)/pembayaran',
    'PembayaranController::store/$1'
);

// ======================================================
// SHIFT - KHUSUS KASIR
// ======================================================

$routes->post(
    'shift/buka',
    'ShiftController::buka',
    [
        'filter' => ['auth', 'role:kasir']
    ]
);

$routes->get(
    'kasir/shift',
    'ShiftController::index',
    [
        'filter' => ['auth', 'role:kasir']
    ]
);

$routes->get(
    'shift/aktif',
    'ShiftController::aktif',
    [
        'filter' => ['auth', 'role:kasir']
    ]
);

$routes->get(
    'shift/total',
    'ShiftController::total',
    [
        'filter' => ['auth', 'role:kasir']
    ]
);

$routes->post(
    'shift/tutup',
    'ShiftController::tutup',
    [
        'filter' => ['auth', 'role:kasir']
    ]
);

$routes->get(
    'shift/riwayat',
    'ShiftController::riwayat',
    [
        'filter' => ['auth', 'role:kasir']
    ]
);

// ======================================================
// NOTIFIKASI
// ======================================================

$routes->get(
    'notifikasi',
    'NotifikasiController::index',
    ['filter' => ['auth', 'role:dapur']]
);

$routes->get(
    'notifikasi/belum-dibaca',
    'NotifikasiController::belumDibaca',
    ['filter' => ['auth', 'role:dapur']]
);

$routes->post(
    'notifikasi/(:num)/baca',
    'NotifikasiController::baca/$1',
    ['filter' => ['auth', 'role:dapur']]
);

// ======================================================
// DAPUR - KHUSUS DAPUR
// ======================================================

$routes->get(
    'dapur',
    'DapurController::index',
    [
        'filter' => ['auth', 'role:dapur']
    ]
);

$routes->get(
    'dapur/pesanan/(:num)',
    'DapurController::show/$1',
    [
        'filter' => ['auth', 'role:dapur']
    ]
);

$routes->post(
    'dapur/pesanan/(:num)/proses',
    'DapurController::mulaiProses/$1',
    [
        'filter' => ['auth', 'role:dapur']
    ]
);

$routes->post(
    'dapur/pesanan/(:num)/siap-diambil',
    'DapurController::siapDiambil/$1',
    [
        'filter' => ['auth', 'role:dapur']
    ]
);

$routes->post(
    'dapur/pesanan/(:num)/selesai',
    'DapurController::selesai/$1',
    [
        'filter' => ['auth', 'role:dapur']
    ]
);

$routes->post(
    'dapur/menu/(:num)/toggle',
    'DapurController::toggleMenuHabis/$1',
    [
        'filter' => ['auth', 'role:dapur']
    ]
);

// ======================================================
// CUSTOMER - PUBLIC / TANPA LOGIN
// ======================================================

$routes->get(
    'customer',
    'CustomerController::index'
);

$routes->get(
    'customer/pesanan-siap',
    'CustomerController::pesananSiap'
);

$routes->get(
    'customer/kategori/(:num)',
    'CustomerController::menuByKategori/$1'
);

$routes->get(
    'customer/menu/(:num)',
    'CustomerController::detailMenu/$1'
);

$routes->post(
    'customer/checkout',
    'CustomerOrderController::checkout'
);

$routes->get(
    'customer/pesanan/(:segment)',
    'CustomerOrderController::status/$1'
);

$routes->post(
    'customer/pesanan/(:segment)/bayar-qris',
    'CustomerOrderController::bayarQris/$1'
);

$routes->post(
    'customer/pesanan/(:segment)/ambil',
    'CustomerOrderController::ambil/$1'
);

// ======================================================
// MANAJERIAL - KELOLA MENU & KATEGORI
// ======================================================

$routes->get(
    'menu',
    'MenuController::index',
    [
        'filter' => ['auth', 'role:manajerial']
    ]
);

$routes->get(
    'menu/(:num)',
    'MenuController::show/$1',
    [
        'filter' => ['auth', 'role:manajerial']
    ]
);

$routes->post(
    'menu',
    'MenuController::store',
    [
        'filter' => ['auth', 'role:manajerial']
    ]
);

$routes->post(
    'menu/(:num)/toggle',
    'MenuController::toggle/$1',
    [
        'filter' => ['auth', 'role:manajerial']
    ]
);

$routes->post(
    'menu/(:num)',
    'MenuController::update/$1',
    [
        'filter' => ['auth', 'role:manajerial']
    ]
);

$routes->delete(
    'menu/(:num)',
    'MenuController::delete/$1',
    [
        'filter' => ['auth', 'role:manajerial']
    ]
);

$routes->get(
    'kategori',
    'KategoriController::index',
    [
        'filter' => ['auth', 'role:manajerial']
    ]
);

$routes->post(
    'kategori',
    'KategoriController::store',
    [
        'filter' => ['auth', 'role:manajerial']
    ]
);

$routes->post(
    'kategori/(:num)',
    'KategoriController::update/$1',
    [
        'filter' => ['auth', 'role:manajerial']
    ]
);

$routes->delete(
    'kategori/(:num)',
    'KategoriController::delete/$1',
    [
        'filter' => ['auth', 'role:manajerial']
    ]
);

// ======================================================
// MANAJERIAL - LAPORAN
// ======================================================

$routes->get(
    'manajerial',
    'ManajerialController::index',
    [
        'filter' => ['auth', 'role:manajerial']
    ]
);

$routes->get(
    'manajerial/laporan',
    'ManajerialController::laporan',
    [
        'filter' => ['auth', 'role:manajerial']
    ]
);