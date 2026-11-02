<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Display System (Dapur) - Kantin Merah POS</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #111827; color: #f9fafb; min-height: 100vh; }
        
        /* Top Navigation Bar */
        .topbar { background: #1f2937; padding: 14px 24px; border-bottom: 2px solid #374151; display: flex; justify-content: space-between; align-items: center; }
        .logo-group { display: flex; align-items: center; gap: 12px; }
        .brand-badge { background: #dc2626; color: white; padding: 6px 12px; border-radius: 8px; font-weight: 800; font-size: 14px; }
        .page-title { font-size: 18px; font-weight: 700; }
        .actions-right { display: flex; align-items: center; gap: 12px; }
        .btn-header { background: #374151; color: #f3f4f6; border: 1px solid #4b5563; padding: 7px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; }
        .btn-header:hover { background: #4b5563; }
        .btn-logout { background: #991b1b; border-color: #dc2626; color: white; }
        .btn-logout:hover { background: #b91c1c; }

        /* Main Container */
        .container { padding: 20px 24px; display: grid; grid-template-columns: 1fr 300px; gap: 20px; }
        @media (max-width: 1024px) {
            .container { grid-template-columns: 1fr; }
        }

        /* Order Columns (FIFO display) */
        .columns-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
        @media (max-width: 768px) {
            .columns-grid { grid-template-columns: 1fr; }
        }

        .column { background: #1f2937; border-radius: 12px; border: 1px solid #374151; padding: 16px; display: flex; flex-direction: column; min-height: 600px; }
        .column-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid #374151; }
        .column-title { font-size: 15px; font-weight: 700; display: flex; align-items: center; gap: 8px; }
        .count-pill { font-size: 12px; padding: 2px 8px; border-radius: 9999px; font-weight: 700; }
        
        .pill-dibayar { background: #fef3c7; color: #92400e; }
        .pill-proses { background: #dbeafe; color: #1e40af; }

        /* Order Ticket Cards */
        .ticket-list { display: flex; flex-direction: column; gap: 14px; }
        .ticket { background: #283548; border-radius: 10px; border: 1px solid #475569; padding: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2); }
        .ticket.active-process { border-color: #3b82f6; border-left: 6px solid #3b82f6; }
        .ticket.waiting-cook { border-left: 6px solid #f59e0b; }

        .ticket-head { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px; border-bottom: 1px dashed #475569; padding-bottom: 8px; }
        .ticket-order-id { font-size: 16px; font-weight: 800; color: #f87171; }
        .ticket-timer { font-size: 12px; color: #94a3b8; }

        /* Identity Badge inside Kitchen Ticket */
        .ticket-identity-badge { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12); border-radius: 6px; padding: 6px 10px; margin: 8px 0; display: flex; justify-content: space-between; align-items: center; font-size: 13px; }
        
        .ticket-items { list-style: none; margin: 12px 0; }
        .ticket-item { margin-bottom: 8px; font-size: 14px; }
        .item-qty { font-weight: 800; color: #fbbf24; font-size: 15px; margin-right: 6px; }
        .item-special-note { display: block; background: #7c2d12; color: #fef08a; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 600; margin-top: 4px; }

        /* Action Buttons */
        .btn-action { width: 100%; padding: 10px; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; transition: 0.15s; }
        .btn-start { background: #d97706; color: white; }
        .btn-start:hover { background: #b45309; }
        .btn-ready { background: #2563eb; color: white; }
        .btn-ready:hover { background: #1d4ed8; }

        /* Sidebar: Menu Availability Management (Menandai Menu Habis) */
        .stock-sidebar { background: #1f2937; border-radius: 12px; border: 1px solid #374151; padding: 16px; height: fit-content; }
        .stock-header { font-size: 15px; font-weight: 700; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #374151; }
        .menu-list { list-style: none; max-height: 650px; overflow-y: auto; }
        .menu-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #374151; font-size: 13px; }
        .menu-item:last-child { border-bottom: none; }
        .menu-name { font-weight: 600; color: #e2e8f0; }
        .badge-stok { font-size: 11px; padding: 4px 8px; border-radius: 6px; font-weight: 700; cursor: pointer; border: none; }
        .stok-tersedia { background: #065f46; color: #a7f3d0; }
        .stok-habis { background: #991b1b; color: #fecaca; }

        .empty-tickets { text-align: center; color: #64748b; padding: 40px 10px; font-size: 13px; }
    </style>
</head>
<body>

    <header class="topbar">
        <div class="logo-group">
            <div class="brand-badge">KM DAPUR</div>
            <div class="page-title">Kitchen Display System (Antrean FIFO)</div>
        </div>

        <div class="actions-right">
            <span style="font-size: 13px; color: #94a3b8;">Koki: <strong><?= esc(session()->get('nama') ?? 'Dapur') ?></strong></span>
            <button class="btn-header" onclick="location.reload()">🔄 Refresh Tiket</button>
            <a class="btn-header btn-logout" href="<?= site_url('logout') ?>">🚪 Logout</a>
        </div>
    </header>

    <div class="container">
        <!-- Main: Kitchen Order Queues (FIFO) -->
        <main class="columns-grid">
            
            <!-- Column 1: Pesanan Masuk (Status: Dibayar) -->
            <section class="column">
                <div class="column-header">
                    <div class="column-title">
                        <span>🔥 Pesanan Masuk (Siap Dimasak)</span>
                    </div>
                    <?php 
                        $antreanMasuk = array_filter($pesanan, fn($p) => $p['status'] === 'Dibayar');
                    ?>
                    <span class="count-pill pill-dibayar"><?= count($antreanMasuk) ?> Antrean</span>
                </div>

                <div class="ticket-list">
                    <?php if (empty($antreanMasuk)): ?>
                        <div class="empty-tickets">Tidak ada pesanan baru yang menunggu dimasak.</div>
                    <?php else: ?>
                        <?php foreach ($antreanMasuk as $p): ?>
                            <div class="ticket waiting-cook">
                                <div class="ticket-head">
                                    <div>
                                        <div class="ticket-order-id">No. <?= format_no_antrian($p['no_antrian'] ?? null) ?></div>
                                        <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;"><?= esc($p['kode_pesanan']) ?></div>
                                        <div class="ticket-timer">Masuk: <?= date('H:i:s', strtotime($p['waktu_pesan'])) ?> WIB</div>
                                    </div>
                                    <span style="background: #f59e0b; color: #78350f; font-size: 11px; font-weight: 800; padding: 3px 6px; border-radius: 4px;">ANTREAN</span>
                                </div>

                                <!-- IDENTITAS MEJA & PEMESAN -->
                                <div class="ticket-identity-badge">
                                    <span>🪑 Meja/ID: <strong style="color: #fbbf24;"><?= esc(!empty($p['nomor_meja']) ? $p['nomor_meja'] : 'Meja -') ?></strong></span>
                                    <span style="color: #e2e8f0; font-weight: 600;">👤 <?= esc(!empty($p['nama_customer']) ? $p['nama_customer'] : 'Pelanggan') ?></span>
                                </div>

                                <ul class="ticket-items">
                                    <?php foreach ($p['items'] as $item): ?>
                                        <li class="ticket-item">
                                            <span class="item-qty"><?= esc($item['jumlah']) ?>x</span>
                                            <span style="font-weight: 600;"><?= esc($item['nama_menu']) ?></span>
                                            <?php if (!empty($item['catatan_khusus'])): ?>
                                                <span class="item-special-note">⚠️ Note: <?= esc($item['catatan_khusus']) ?></span>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>

                                <button class="btn-action btn-start" onclick="mulaiMasak(<?= $p['id_pesanan'] ?>)">
                                    🍳 Mulai Masak (Proses FIFO) &rarr;
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Column 2: Sedang Dimasak (Status: Diproses) -->
            <section class="column">
                <div class="column-header">
                    <div class="column-title">
                        <span>⏳ Sedang Dimasak</span>
                    </div>
                    <?php 
                        $sedangMasak = array_filter($pesanan, fn($p) => $p['status'] === 'Diproses');
                    ?>
                    <span class="count-pill pill-proses"><?= count($sedangMasak) ?> Sedang Dibuat</span>
                </div>

                <div class="ticket-list">
                    <?php if (empty($sedangMasak)): ?>
                        <div class="empty-tickets">Belum ada makanan yang sedang aktif dimasak.</div>
                    <?php else: ?>
                        <?php foreach ($sedangMasak as $p): ?>
                            <div class="ticket active-process">
                                <div class="ticket-head">
                                    <div>
                                        <div class="ticket-order-id" style="color: #60a5fa;">No. <?= format_no_antrian($p['no_antrian'] ?? null) ?></div>
                                        <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;"><?= esc($p['kode_pesanan']) ?></div>
                                        <div class="ticket-timer">Diproses sejak: <?= date('H:i:s', strtotime($p['updated_at'] ?? $p['waktu_pesan'])) ?> WIB</div>
                                    </div>
                                    <span style="background: #2563eb; color: #eff6ff; font-size: 11px; font-weight: 800; padding: 3px 6px; border-radius: 4px;">MEMASAK</span>
                                </div>

                                <!-- IDENTITAS MEJA & PEMESAN -->
                                <div class="ticket-identity-badge">
                                    <span>🪑 Meja/ID: <strong style="color: #60a5fa;"><?= esc(!empty($p['nomor_meja']) ? $p['nomor_meja'] : 'Meja -') ?></strong></span>
                                    <span style="color: #e2e8f0; font-weight: 600;">👤 <?= esc(!empty($p['nama_customer']) ? $p['nama_customer'] : 'Pelanggan') ?></span>
                                </div>

                                <ul class="ticket-items">
                                    <?php foreach ($p['items'] as $item): ?>
                                        <li class="ticket-item">
                                            <span class="item-qty"><?= esc($item['jumlah']) ?>x</span>
                                            <span style="font-weight: 600;"><?= esc($item['nama_menu']) ?></span>
                                            <?php if (!empty($item['catatan_khusus'])): ?>
                                                <span class="item-special-note">⚠️ Note: <?= esc($item['catatan_khusus']) ?></span>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>

                                <button class="btn-action btn-ready" onclick="siapDiambil(<?= $p['id_pesanan'] ?>)">
                                    🔔 Selesai Masak &rarr; Siap Diambil
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

        </main>

        <!-- Right Sidebar: Menandai Menu Habis (Use Case PDF Dapur) -->
        <aside class="stock-sidebar">
            <div class="stock-header">
                <div>🍽️ Ketersediaan Menu</div>
                <div style="font-size: 11px; color: #94a3b8; font-weight: normal; margin-top: 4px;">
                    Klik tombol status untuk menandai menu Habis/Tersedia seketika.
                </div>
            </div>

            <ul class="menu-list">
                <?php foreach ($menus as $m): ?>
                    <li class="menu-item">
                        <div>
                            <div class="menu-name"><?= esc($m['nama_menu']) ?></div>
                            <small style="color: #64748b;">Stok: <?= esc($m['jumlah_stok']) ?></small>
                        </div>
                        <button
                            class="badge-stok <?= $m['status_stok'] === 'Tersedia' ? 'stok-tersedia' : 'stok-habis' ?>"
                            onclick="toggleStokMenu(<?= $m['id_menu'] ?>)"
                            id="btn-menu-<?= $m['id_menu'] ?>"
                        >
                            <?= esc($m['status_stok']) ?>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>
    </div>

    <script>
        // Auto-refresh KDS setiap 15 detik agar sinkron dengan kasir / customer
        setTimeout(() => {
            location.reload();
        }, 15000);

        async function mulaiMasak(idPesanan) {
            try {
                const res = await fetch('<?= site_url('dapur/pesanan') ?>/' + idPesanan + '/proses', {
                    method: 'POST'
                });
                const data = await res.json();
                if (data.status) {
                    location.reload();
                } else {
                    alert('Gagal: ' + data.message);
                }
            } catch (e) {
                alert('Terjadi kesalahan jaringan.');
            }
        }

        async function siapDiambil(idPesanan) {
            try {
                const res = await fetch('<?= site_url('dapur/pesanan') ?>/' + idPesanan + '/siap-diambil', {
                    method: 'POST'
                });
                const data = await res.json();
                if (data.status) {
                    location.reload();
                } else {
                    alert('Gagal: ' + data.message);
                }
            } catch (e) {
                alert('Terjadi kesalahan jaringan.');
            }
        }

        async function toggleStokMenu(idMenu) {
            try {
                const res = await fetch('<?= site_url('dapur/menu') ?>/' + idMenu + '/toggle', {
                    method: 'POST'
                });
                const data = await res.json();
                if (data.status) {
                    const btn = document.getElementById('btn-menu-' + idMenu);
                    btn.textContent = data.status_stok;
                    if (data.status_stok === 'Tersedia') {
                        btn.className = 'badge-stok stok-tersedia';
                    } else {
                        btn.className = 'badge-stok stok-habis';
                    }
                } else {
                    alert('Gagal: ' + data.message);
                }
            } catch (e) {
                alert('Terjadi kesalahan jaringan.');
            }
        }
    </script>
</body>
</html>
