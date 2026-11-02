<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kasir - Kantin Merah POS</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #f3f4f6; color: #1f2937; }
        .layout { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: #8f1726; color: white; padding: 24px 18px; flex-shrink: 0; display: flex; flex-direction: column; }
        .brand { font-size: 20px; font-weight: 800; letter-spacing: 0.5px; }
        .brand-subtitle { font-size: 11px; color: #fecaca; margin-bottom: 28px; }
        .nav-label { font-size: 11px; color: #fca5a5; text-transform: uppercase; letter-spacing: 1px; margin: 18px 8px 8px; }
        .nav-link { display: block; color: #fef2f2; text-decoration: none; padding: 11px 12px; border-radius: 8px; font-size: 14px; font-weight: 500; margin-bottom: 4px; transition: 0.15s; }
        .nav-link:hover, .nav-link.active { background: #b91c1c; color: white; }
        .sidebar-bottom { margin-top: auto; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.15); }
        .main { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .topbar { background: white; padding: 18px 28px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e5e7eb; }
        .topbar h1 { font-size: 20px; font-weight: 700; }
        .user-pill { background: #fee2e2; color: #991b1b; padding: 6px 14px; border-radius: 9999px; font-size: 13px; font-weight: 600; }
        .content { padding: 24px 28px; }

        /* Shift Alert Banner */
        .shift-banner { background: white; border-radius: 12px; padding: 16px 20px; border: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .shift-badge { display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; }
        .shift-active { background: #dcfce7; color: #166534; }
        .shift-inactive { background: #fee2e2; color: #991b1b; }
        .btn-shift { background: #dc2626; color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600; border: none; cursor: pointer; }
        .btn-shift-close { background: #4b5563; }

        /* Stats Cards */
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: white; border-radius: 12px; padding: 18px; border: 1px solid #e5e7eb; }
        .stat-label { font-size: 12px; color: #6b7280; font-weight: 600; text-transform: uppercase; margin-bottom: 6px; }
        .stat-val { font-size: 22px; font-weight: 800; color: #111827; }

        /* Tables & Order Cards */
        .section-title { font-size: 16px; font-weight: 700; margin-bottom: 14px; display: flex; align-items: center; justify-content: space-between; }
        .grid-orders { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 16px; margin-bottom: 30px; }
        .order-card { background: white; border-radius: 12px; border: 1px solid #e5e7eb; padding: 18px; box-shadow: 0 2px 4px rgba(0,0,0,0.03); display: flex; flex-direction: column; }
        .order-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; border-bottom: 1px dashed #f3f4f6; padding-bottom: 8px; }
        .order-code { font-weight: 800; font-size: 15px; color: #991b1b; }
        .order-time { font-size: 11px; color: #9ca3af; }
        .order-items { list-style: none; margin-bottom: 14px; flex-grow: 1; }
        .order-items li { font-size: 13px; margin-bottom: 6px; display: flex; justify-content: space-between; }
        .item-note { font-size: 11px; color: #d97706; display: block; font-style: italic; }
        .order-total-bar { border-top: 1px solid #e5e7eb; padding-top: 10px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
        .total-price { font-size: 17px; font-weight: 800; color: #111827; }

        /* Customer ID & Table Badge */
        .customer-pill-box { background: #fff7ed; border: 1px solid #fed7aa; border-radius: 8px; padding: 10px 12px; margin-bottom: 12px; }
        .customer-pill-label { font-size: 10px; font-weight: 800; color: #c2410c; text-transform: uppercase; letter-spacing: 0.5px; }
        .customer-pill-target { font-size: 15px; font-weight: 800; color: #111827; }
        .customer-pill-user { font-size: 13px; font-weight: 700; color: #991b1b; }

        /* Delivery Target Banner for Ready Orders */
        .delivery-banner { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 12px; margin-bottom: 12px; }
        .delivery-title { font-size: 11px; font-weight: 800; color: #1d4ed8; text-transform: uppercase; letter-spacing: 0.5px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }

        /* Payment Form Box */
        .pay-box { background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px; margin-bottom: 12px; }
        .pay-box label { font-size: 11px; font-weight: 700; color: #991b1b; display: block; margin-bottom: 4px; }
        .pay-box input { width: 100%; padding: 8px; border: 1px solid #fca5a5; border-radius: 6px; font-size: 14px; font-weight: 700; margin-bottom: 6px; }
        .change-text { font-size: 12px; font-weight: 700; color: #15803d; }

        .btn-group { display: flex; gap: 8px; }
        .btn-action { flex: 1; padding: 10px; border-radius: 6px; border: none; font-size: 13px; font-weight: 700; cursor: pointer; text-align: center; transition: 0.15s; }
        .btn-pay { background: #16a34a; color: white; }
        .btn-pay:hover { background: #15803d; }
        .btn-done { background: #2563eb; color: white; }
        .btn-done:hover { background: #1d4ed8; }
        .btn-cancel { background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; font-size: 12px; padding: 8px 12px; }

        /* Transaksi Baru Masuk */
        .incoming-panel { background: white; border-radius: 12px; border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 26px; box-shadow: 0 2px 4px rgba(0,0,0,0.03); }
        .incoming-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 12px 16px; border-bottom: 1px solid #f3f4f6; font-size: 12px; color: #6b7280; flex-wrap: wrap; }
        .incoming-head strong { color: #374151; font-size: 13px; }
        .incoming-table { width: 100%; border-collapse: collapse; font-size: 12px; }
        .incoming-table th { background: #f9fafb; color: #6b7280; text-align: left; padding: 10px 14px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.4px; border-bottom: 1px solid #e5e7eb; white-space: nowrap; }
        .incoming-table td { padding: 10px 14px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
        .incoming-table tbody tr:last-child td { border-bottom: none; }
        .incoming-queue { display: inline-block; min-width: 46px; text-align: center; padding: 5px 8px; border-radius: 7px; background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; font-size: 14px; font-weight: 900; }
        .incoming-code { color: #991b1b; font-weight: 800; }
        .incoming-sub { color: #6b7280; font-size: 11px; margin-top: 2px; }
        .incoming-status { display: inline-block; padding: 4px 9px; border-radius: 9999px; background: #fef3c7; color: #92400e; font-size: 10px; font-weight: 800; white-space: nowrap; }
        .incoming-empty { text-align: center; color: #9ca3af; padding: 22px !important; }
        .link-processing { color: #8f1726; text-decoration: none; font-weight: 800; font-size: 12px; }

        .empty-state { background: white; border-radius: 12px; padding: 32px; text-align: center; color: #9ca3af; border: 1px dashed #d1d5db; }
    </style>
</head>
<body>
<div class="layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="brand">KANTIN MERAH</div>
        <div class="brand-subtitle">Point of Sale &bull; UNP Kampus 1</div>

        <div class="nav-label">Menu Kasir</div>
        <a class="nav-link active" href="<?= site_url('kasir') ?>">📊 Antrean Pembayaran</a>
        <a class="nav-link" href="<?= site_url('kasir/riwayat') ?>">📜 Riwayat Transaksi</a>
        <a class="nav-link" href="<?= site_url('kasir/shift') ?>">⏰ Kelola Shift Kasir</a>
        <a class="nav-link" href="<?= site_url('customer') ?>" target="_blank">🛒 Halaman Customer</a>

        <div class="sidebar-bottom">
            <div style="font-size: 12px; color: #fecaca; margin-bottom: 8px;">
                Petugas: <strong><?= esc(session()->get('nama') ?? 'Kasir') ?></strong>
            </div>
            <a class="nav-link" href="<?= site_url('logout') ?>" style="background: rgba(0,0,0,0.2);">🚪 Keluar / Logout</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main">
        <header class="topbar">
            <h1>Dashboard & Antrean Kasir</h1>
            <div class="user-pill">Role: Kasir</div>
        </header>

        <div class="content">
            <!-- Shift Status Banner (Class Diagram: Shift kasir) -->
            <div class="shift-banner">
                <div>
                    <span style="font-size: 13px; color: #6b7280;">Status Shift Anda: </span>
                    <?php if ($shiftAktif): ?>
                        <span class="shift-badge shift-active">🟢 Sedang Buka (Shift #<?= esc($shiftAktif['id_shift']) ?>)</span>
                        <div style="font-size: 12px; color: #4b5563; margin-top: 4px;">
                            Mulai: <?= esc($shiftAktif['waktu_mulai']) ?> |
                            Kas Tunai Masuk: <strong>Rp <?= number_format($shiftAktif['total_kas_tunai'], 0, ',', '.') ?></strong> |
                            QRIS: <strong>Rp <?= number_format($shiftAktif['total_kas_qris'], 0, ',', '.') ?></strong>
                        </div>
                    <?php else: ?>
                        <span class="shift-badge shift-inactive">🔴 Belum Ada Shift Aktif</span>
                        <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">Harap buka shift terlebih dahulu untuk memproses transaksi tunai.</div>
                    <?php endif; ?>
                </div>
                <div>
                    <?php if ($shiftAktif): ?>
                        <button class="btn-shift btn-shift-close" onclick="tutupShift(<?= esc($shiftAktif['id_shift']) ?>)">Tutup Shift Ini</button>
                    <?php else: ?>
                        <button class="btn-shift" onclick="bukaShift()">Buka Shift Sekarang</button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Stats -->
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-label">Menunggu Pembayaran (Tunai)</div>
                    <div class="stat-val" style="color: #d97706;"><?= count($pesananTunai) ?> Pesanan</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Siap Diserahkan ke Pembeli</div>
                    <div class="stat-val" style="color: #2563eb;"><?= count($pesananSiap) ?> Pesanan</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Kas Tunai Shift Ini</div>
                    <div class="stat-val" style="color: #16a34a;">Rp <?= number_format($shiftAktif['total_kas_tunai'] ?? 0, 0, ',', '.') ?></div>
                </div>
            </div>

            <!-- Section 0: Transaksi Baru Masuk ke Dapur -->
            <div class="section-title">
                <span>🔔 Transaksi Baru Masuk (Siap Dapur)</span>
                <a class="link-processing" href="<?= site_url('kasir/riwayat') ?>">Lihat Riwayat Transaksi &rarr;</a>
            </div>

            <div class="incoming-panel">
                <div class="incoming-head">
                    <strong>5 transaksi terbaru yang sudah dibayar</strong>
                    <span>
                        Urut nomor antrian &middot;
                        <a class="link-processing" href="<?= site_url('kasir') ?>">🔄 Refresh</a>
                    </span>
                </div>

                <div style="overflow-x: auto;">
                    <table class="incoming-table">
                        <thead>
                            <tr>
                                <th>No. Antrian</th>
                                <th>Kode Pesanan</th>
                                <th>ID Customer / Pemesan</th>
                                <th>Status</th>
                                <th>Masuk</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pesananBaruMasuk)): ?>
                                <tr>
                                    <td colspan="5" class="incoming-empty">Belum ada transaksi baru yang masuk ke dapur.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pesananBaruMasuk as $p): ?>
                                    <?php
                                        $idCustomer = trim((string) ($p['id_customer'] ?? ''));

                                        if ($idCustomer === '') {
                                            $mejaPemesan = trim((string) ($p['nomor_meja'] ?? ''));
                                            $namaPemesan = trim((string) ($p['nama_customer'] ?? ''));

                                            if ($mejaPemesan !== '' && $namaPemesan !== '') {
                                                $idCustomer = $mejaPemesan . ' - ' . $namaPemesan;
                                            } elseif ($mejaPemesan !== '') {
                                                $idCustomer = $mejaPemesan;
                                            } elseif ($namaPemesan !== '') {
                                                $idCustomer = $namaPemesan;
                                            } else {
                                                $idCustomer = 'Pelanggan';
                                            }
                                        }
                                    ?>
                                    <tr>
                                        <td>
                                            <span class="incoming-queue"><?= format_no_antrian($p['no_antrian'] ?? null) ?></span>
                                        </td>
                                        <td>
                                            <div class="incoming-code"><?= esc($p['kode_pesanan']) ?></div>
                                            <div class="incoming-sub">Rp <?= number_format($p['total_bayar'], 0, ',', '.') ?></div>
                                        </td>
                                        <td>
                                            <strong><?= esc($idCustomer) ?></strong>
                                            <div class="incoming-sub">
                                                <?= esc(!empty($p['nama_customer']) ? $p['nama_customer'] : 'Pelanggan') ?>
                                                &middot; <?= esc(!empty($p['nomor_meja']) ? $p['nomor_meja'] : 'Meja -') ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="incoming-status"><?= esc($p['status']) ?></span>
                                        </td>
                                        <td style="white-space: nowrap; color: #4b5563;">
                                            <?= date('H:i', strtotime($p['waktu_pesan'])) ?> WIB
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Section 1: Antrean Menunggu Pembayaran Tunai -->
            <div class="section-title">
                <span>💰 Antrean Pembayaran Tunai (Verifikasi Kasir)</span>
                <small style="color: #6b7280; font-weight: normal;">Sesuai Sequence Diagram Kasir</small>
            </div>

            <?php if (empty($pesananTunai)): ?>
                <div class="empty-state" style="margin-bottom: 24px;">Tidak ada pesanan yang sedang menunggu pembayaran tunai.</div>
            <?php else: ?>
                <div class="grid-orders">
                    <?php foreach ($pesananTunai as $p): ?>
                        <div class="order-card" id="card-pesanan-<?= $p['id_pesanan'] ?>">
                            <div class="order-header">
                                <span class="order-code"><?= esc($p['kode_pesanan']) ?></span>
                                <div style="text-align:right;">
                                    <span class="order-time"><?= date('H:i', strtotime($p['waktu_pesan'])) ?> WIB</span>
                                    <?php if (!empty($p['pembayaran']['batas_waktu'])): ?>
                                        <div style="font-size:10px; color:#b45309; font-weight:700; margin-top:2px;">
                                            Bayar sebelum <?= date('H:i', strtotime($p['pembayaran']['batas_waktu'])) ?> WIB
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- IDENTITAS PEMESAN & MEJA (SESUAI REQUEST USER) -->
                            <div class="customer-pill-box">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <div class="customer-pill-label">🪑 Lokasi / ID Meja:</div>
                                        <div class="customer-pill-target">
                                            <?= esc(!empty($p['nomor_meja']) ? $p['nomor_meja'] : 'Meja -') ?>
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div class="customer-pill-label" style="color: #6b7280;">👤 Pemesan:</div>
                                        <div class="customer-pill-user">
                                            <?= esc(!empty($p['nama_customer']) ? $p['nama_customer'] : 'Pelanggan') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <ul class="order-items">
                                <?php foreach ($p['items'] as $item): ?>
                                    <li>
                                        <div>
                                            <strong><?= esc($item['jumlah']) ?>x</strong> <?= esc($item['nama_menu']) ?>
                                            <?php if (!empty($item['catatan_khusus'])): ?>
                                                <span class="item-note">Catatan: <?= esc($item['catatan_khusus']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <span>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <div class="order-total-bar">
                                <span style="font-size: 13px; color: #4b5563;">Total Tagihan:</span>
                                <span class="total-price" id="total-val-<?= $p['id_pesanan'] ?>" data-total="<?= $p['total_bayar'] ?>">
                                    Rp <?= number_format($p['total_bayar'], 0, ',', '.') ?>
                                </span>
                            </div>

                            <!-- Input Kalkulasi Kembalian Otomatis (Include Use Case di PDF) -->
                            <div class="pay-box">
                                <label for="uang-<?= $p['id_pesanan'] ?>">Uang Diterima (Rp):</label>
                                <input
                                    type="number"
                                    id="uang-<?= $p['id_pesanan'] ?>"
                                    placeholder="Contoh: 20000"
                                    oninput="hitungKembalian(<?= $p['id_pesanan'] ?>)"
                                >
                                <div class="change-text" id="kembalian-<?= $p['id_pesanan'] ?>">Kembalian: Rp 0</div>
                            </div>

                            <div class="btn-group">
                                <button class="btn-action btn-pay" onclick="prosesBayar(<?= $p['id_pesanan'] ?>)">
                                    Konfirmasi Bayar &rarr;
                                </button>
                                <button class="btn-action btn-cancel" onclick="batalkanPesanan(<?= $p['id_pesanan'] ?>)">
                                    Batalkan
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Section 2: Pesanan Siap Diambil (Menyerahkan ke Customer & Menandai Selesai) -->
            <div class="section-title">
                <span>📦 Pesanan Siap Diantar / Diserahkan (Kirim Sesuai ID User)</span>
                <small style="color: #6b7280; font-weight: normal;">Dapur sudah selesai memasak</small>
            </div>

            <?php if (empty($pesananSiap)): ?>
                <div class="empty-state">Belum ada pesanan yang siap diambil.</div>
            <?php else: ?>
                <div class="grid-orders">
                    <?php foreach ($pesananSiap as $p): ?>
                        <div class="order-card" style="border-left: 5px solid #2563eb;">
                            <div class="order-header">
                                <span class="order-code" style="color: #1d4ed8;"><?= esc($p['kode_pesanan']) ?></span>
                                <span style="background: #dbeafe; color: #1e40af; font-size: 11px; padding: 3px 8px; border-radius: 9999px; font-weight: 800;">SIAP DIKIRIM</span>
                            </div>

                            <!-- BANNER TUJUAN PENGANTARAN / PENGIRIMAN SESUAI ID DARI USER YANG PESAN -->
                            <div class="delivery-banner">
                                <div class="delivery-title">
                                    <span>🚀 TUJUAN PENGIRIMAN</span>
                                    <span style="background: #2563eb; color: #fff; font-size: 10px; padding: 2px 6px; border-radius: 4px;">ID USER</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: baseline;">
                                    <strong style="font-size: 16px; color: #1e3a8a;">
                                        🪑 <?= esc(!empty($p['nomor_meja']) ? $p['nomor_meja'] : 'Meja -') ?>
                                    </strong>
                                    <span style="font-size: 13px; font-weight: 700; color: #1f2937;">
                                        👤 <?= esc(!empty($p['nama_customer']) ? $p['nama_customer'] : 'Pelanggan') ?>
                                    </span>
                                </div>
                            </div>

                            <ul class="order-items">
                                <?php foreach ($p['items'] as $item): ?>
                                    <li>
                                        <div>
                                            <strong><?= esc($item['jumlah']) ?>x</strong> <?= esc($item['nama_menu']) ?>
                                            <?php if (!empty($item['catatan_khusus'])): ?>
                                                <span class="item-note">Catatan: <?= esc($item['catatan_khusus']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <button class="btn-action btn-done" onclick="tandaiSelesai(<?= $p['id_pesanan'] ?>)">
                                🚀 Kirim / Antar ke <?= esc(!empty($p['nomor_meja']) ? $p['nomor_meja'] : 'Customer') ?> (Selesai)
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </main>
</div>

<script>
async function bacaJson(res) {
    let data = null;

    try {
        data = await res.json();
    } catch (e) {
        data = null;
    }

    if (!res.ok) {
        throw new Error((data && data.message) ? data.message : 'Server mengembalikan HTTP ' + res.status + '.');
    }

    if (!data) {
        throw new Error('Respons server tidak valid. Pastikan sesi login Anda masih aktif.');
    }

    return data;
}

function hitungKembalian(idPesanan) {
    const total = parseFloat(document.getElementById('total-val-' + idPesanan).dataset.total);
    const uangInput = parseFloat(document.getElementById('uang-' + idPesanan).value) || 0;
    const kembalianEl = document.getElementById('kembalian-' + idPesanan);

    const kembalian = uangInput - total;
    if (kembalian >= 0) {
        kembalianEl.innerText = 'Kembalian: Rp ' + Number(kembalian).toLocaleString('id-ID');
        kembalianEl.style.color = '#15803d';
    } else {
        kembalianEl.innerText = 'Kurang: Rp ' + Number(Math.abs(kembalian)).toLocaleString('id-ID');
        kembalianEl.style.color = '#dc2626';
    }
}

async function prosesBayar(idPesanan) {
    const total = parseFloat(document.getElementById('total-val-' + idPesanan).dataset.total);
    const uangInput = parseFloat(document.getElementById('uang-' + idPesanan).value);

    if (!uangInput || uangInput < total) {
        alert('Jumlah uang yang diterima kurang dari total tagihan!');
        return;
    }

    if (!confirm('Konfirmasi pembayaran tunai sebesar Rp ' + Number(uangInput).toLocaleString('id-ID') + '?')) {
        return;
    }

    try {
        const formData = new FormData();
        formData.append('metode', 'Tunai');
        formData.append('jumlah_diterima', uangInput);

        const res = await fetch('<?= site_url('pesanan') ?>/' + idPesanan + '/pembayaran', {
            method: 'POST',
            body: formData
        });
        const data = await bacaJson(res);

        if (data.status) {
            alert('Pembayaran berhasil dikonfirmasi! Pesanan otomatis diteruskan ke antrean dapur.');
            location.reload();
        } else {
            alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
        }
    } catch (e) {
        alert('Gagal memproses pembayaran: ' + e.message);
    }
}

async function batalkanPesanan(idPesanan) {
    if (!confirm('Apakah yakin ingin membatalkan pesanan ini?')) return;
    try {
        const formData = new FormData();
        formData.append('status', 'Dibatalkan');

        const res = await fetch('<?= site_url('pesanan') ?>/' + idPesanan + '/status', {
            method: 'POST',
            body: formData
        });
        const data = await bacaJson(res);
        if (data.status) {
            alert('Pesanan berhasil dibatalkan.');
            location.reload();
        } else {
            alert('Gagal: ' + data.message);
        }
    } catch (e) {
        alert('Gagal membatalkan pesanan: ' + e.message);
    }
}

async function tandaiSelesai(idPesanan) {
    if (!confirm('Konfirmasi pesanan telah dikirim/diserahkan kepada pemesan sesuai ID?')) return;
    try {
        const res = await fetch('<?= site_url('kasir/pesanan') ?>/' + idPesanan + '/selesai', {
            method: 'POST'
        });
        const data = await bacaJson(res);
        if (data.status) {
            alert('Pesanan selesai diserahkan ke pelanggan!');
            location.reload();
        } else {
            alert('Gagal: ' + data.message);
        }
    } catch (e) {
        alert('Gagal menandai pesanan selesai: ' + e.message);
    }
}

async function bukaShift() {
    try {
        const res = await fetch('<?= site_url('shift/buka') ?>', { method: 'POST' });
        const data = await bacaJson(res);
        if (data.status) {
            alert('Shift berhasil dibuka! Anda dapat mulai melayani transaksi.');
            location.reload();
        } else {
            alert(data.message);
        }
    } catch (e) {
        alert('Gagal membuka shift: ' + e.message);
    }
}

async function tutupShift(idShift) {
    if (!confirm('Apakah Anda yakin ingin menutup shift ini?')) return;
    try {
        const formData = new FormData();
        formData.append('id_shift', idShift);
        const res = await fetch('<?= site_url('shift/tutup') ?>', { method: 'POST', body: formData });
        const data = await bacaJson(res);
        if (data.status) {
            alert('Shift berhasil ditutup! Total tunai: Rp ' + Number(data.data.total_kas_tunai).toLocaleString('id-ID'));
            location.reload();
        } else {
            alert(data.message);
        }
    } catch (e) {
        alert('Gagal menutup shift: ' + e.message);
    }
}
</script>
</body>
</html>
