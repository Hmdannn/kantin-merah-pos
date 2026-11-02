<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - Kantin Merah POS</title>
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
        .intro { background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 18px 20px; margin-bottom: 18px; display: flex; justify-content: space-between; gap: 18px; align-items: center; flex-wrap: wrap; }
        .intro h2 { font-size: 17px; margin-bottom: 4px; }
        .intro p { font-size: 13px; color: #6b7280; }
        .live-box { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; border-radius: 10px; padding: 9px 13px; font-size: 12px; font-weight: 700; }
        .panel { background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.03); }
        .panel-head { padding: 14px 18px; border-bottom: 1px solid #f3f4f6; display: flex; justify-content: space-between; gap: 12px; align-items: center; flex-wrap: wrap; font-size: 12px; color: #6b7280; }
        .panel-head strong { color: #374151; font-size: 13px; }
        .table-wrap { overflow-x: auto; }
        .kitchen-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .kitchen-table th { background: #f9fafb; color: #6b7280; text-align: left; padding: 12px 16px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.4px; border-bottom: 1px solid #e5e7eb; white-space: nowrap; }
        .kitchen-table td { padding: 13px 16px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
        .kitchen-table tbody tr:last-child td { border-bottom: none; }
        .queue-number { display: inline-block; min-width: 52px; text-align: center; background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; border-radius: 8px; padding: 6px 9px; font-size: 15px; font-weight: 900; }
        .order-code { color: #991b1b; font-weight: 800; }
        .payment-method { display: inline-block; padding: 3px 8px; border-radius: 6px; background: #eff6ff; color: #1d4ed8; font-size: 10px; font-weight: 800; white-space: nowrap; }
        .sub-text { color: #6b7280; font-size: 11px; margin-top: 3px; }
        .status-pill { display: inline-block; padding: 4px 9px; border-radius: 9999px; font-size: 10px; font-weight: 800; white-space: nowrap; }
        .status-menunggu-pembayaran { background: #ffedd5; color: #9a3412; }
        .status-dibayar { background: #fef3c7; color: #92400e; }
        .status-diproses { background: #dbeafe; color: #1e40af; }
        .status-siap-diambil { background: #dcfce7; color: #166534; }
        .status-selesai { background: #f3f4f6; color: #374151; }
        .status-dibatalkan { background: #fee2e2; color: #991b1b; }
        .status-kadaluarsa { background: #e5e7eb; color: #6b7280; }
        .empty-row { text-align: center; color: #9ca3af; padding: 28px !important; }
        .pager { padding: 14px 18px; border-top: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; }
        .pager-meta { font-size: 12px; color: #6b7280; }
        .pager-controls { display: flex; align-items: center; gap: 7px; flex-wrap: wrap; }
        .pager-btn { border: 1px solid #d1d5db; background: white; color: #374151; border-radius: 7px; padding: 8px 12px; font-size: 12px; font-weight: 700; cursor: pointer; }
        .pager-btn:hover:not(:disabled) { background: #f9fafb; }
        .pager-btn:disabled { opacity: 0.45; cursor: not-allowed; }
        .pager-page { font-size: 12px; color: #4b5563; margin: 0 4px; white-space: nowrap; }
        .go-input { width: 58px; border: 1px solid #d1d5db; border-radius: 7px; padding: 8px; font-size: 12px; text-align: center; }
        .go-btn { background: #8f1726; border-color: #8f1726; color: white; }
        .go-btn:hover:not(:disabled) { background: #6f111d; }
        .refresh-note { font-size: 11px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand">KANTIN MERAH</div>
        <div class="brand-subtitle">Point of Sale &bull; UNP Kampus 1</div>

        <div class="nav-label">Menu Kasir</div>
        <a class="nav-link" href="<?= site_url('kasir') ?>">📊 Antrean Pembayaran</a>
        <a class="nav-link active" href="<?= site_url('kasir/riwayat') ?>">📜 Riwayat Transaksi</a>
        <a class="nav-link" href="<?= site_url('kasir/shift') ?>">⏰ Kelola Shift Kasir</a>
        <a class="nav-link" href="<?= site_url('customer') ?>" target="_blank">🛒 Halaman Customer</a>

        <div class="sidebar-bottom">
            <div style="font-size: 12px; color: #fecaca; margin-bottom: 8px;">
                Petugas: <strong><?= esc(session()->get('nama') ?? 'Kasir') ?></strong>
            </div>
            <a class="nav-link" href="<?= site_url('logout') ?>" style="background: rgba(0,0,0,0.2);">🚪 Keluar / Logout</a>
        </div>
    </aside>

    <main class="main">
        <header class="topbar">
            <h1>📜 Riwayat Transaksi</h1>
            <div class="user-pill">Role: Kasir</div>
        </header>

        <div class="content">
            <div class="intro">
                <div>
                    <h2>Riwayat Semua Transaksi</h2>
                    <p>Seluruh transaksi ditampilkan dari yang paling baru. 5 baris per halaman.</p>
                </div>
                <div class="live-box" id="liveStatus">● Riwayat — diperbarui otomatis</div>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <strong>Daftar Riwayat Transaksi</strong>
                    <span class="refresh-note" id="riwayatUpdatedAt">Diperbarui: <?= date('H:i:s') ?> WIB</span>
                </div>

                <div class="table-wrap">
                    <table class="kitchen-table">
                        <thead>
                            <tr>
                                <th>No. Antrian</th>
                                <th>Kode Pesanan</th>
                                <th>ID Customer / Pemesan</th>
                                <th>Metode</th>
                                <th>Status</th>
                                <th>Waktu Pesan</th>
                            </tr>
                        </thead>
                        <tbody id="riwayatBody">
                            <?php if (empty($riwayatTransaksi)): ?>
                                <tr>
                                    <td colspan="6" class="empty-row">Belum ada transaksi.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($riwayatTransaksi as $p): ?>
                                    <?php
                                        $statusSlug = strtolower(str_replace(' ', '-', $p['status']));
                                        $idCustomer = trim((string) ($p['id_customer'] ?? ''));
                                        $metodeBayar = trim((string) ($p['metode_bayar'] ?? ''));

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
                                            <span class="queue-number"><?= format_no_antrian($p['no_antrian'] ?? null) ?></span>
                                        </td>
                                        <td>
                                            <div class="order-code"><?= esc($p['kode_pesanan']) ?></div>
                                            <div class="sub-text">Rp <?= number_format($p['total_bayar'], 0, ',', '.') ?></div>
                                        </td>
                                        <td>
                                            <strong><?= esc($idCustomer) ?></strong>
                                            <div class="sub-text">
                                                <?= esc(!empty($p['nama_customer']) ? $p['nama_customer'] : 'Pelanggan') ?>
                                                &middot; <?= esc(!empty($p['nomor_meja']) ? $p['nomor_meja'] : 'Meja -') ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="payment-method"><?= esc($metodeBayar !== '' ? $metodeBayar : '-') ?></span>
                                        </td>
                                        <td>
                                            <span class="status-pill status-<?= esc($statusSlug) ?>"><?= esc($p['status']) ?></span>
                                        </td>
                                        <td style="white-space: nowrap; color: #4b5563;">
                                            <?= esc(date('d/m/Y H:i', strtotime($p['waktu_pesan']))) ?> WIB
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="pager">
                    <div class="pager-meta" id="riwayatMeta">
                        <?php if ($total > 0): ?>
                            Menampilkan <?= esc($from) ?>&ndash;<?= esc($to) ?> dari <?= esc($total) ?> transaksi
                        <?php else: ?>
                            Tidak ada transaksi
                        <?php endif; ?>
                    </div>

                    <div class="pager-controls">
                        <button class="pager-btn" id="prevPage" type="button" <?= $page <= 1 ? 'disabled' : '' ?>>← Previous</button>
                        <span class="pager-page">Halaman <strong id="riwayatPageInfo"><?= esc($page) ?> / <?= esc($totalPages) ?></strong></span>
                        <input class="go-input" id="goPageInput" type="number" min="1" max="<?= esc($totalPages) ?>" value="<?= esc($page) ?>" aria-label="Nomor halaman">
                        <button class="pager-btn go-btn" id="goPageBtn" type="button">Go</button>
                        <button class="pager-btn" id="nextPage" type="button" <?= $page >= $totalPages ? 'disabled' : '' ?>>Next →</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
const RIWAYAT_API = '<?= site_url('kasir/riwayat/data') ?>';

let riwayatPage = <?= (int) $page ?>;
let riwayatTotalPages = <?= (int) $totalPages ?>;
let riwayatLoading = false;

function escapeHtml(value) {
    return String(value === null || value === undefined ? '' : value).replace(/[&<>"']/g, function (char) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        }[char];
    });
}

function formatQueue(value) {
    const nomor = parseInt(value, 10);

    if (!nomor || nomor < 1) {
        return '#-';
    }

    return '#' + String(nomor).padStart(2, '0');
}

function formatIdCustomer(pesanan) {
    let idCustomer = String(pesanan.id_customer || '').trim();

    if (idCustomer !== '') {
        return idCustomer;
    }

    const meja = String(pesanan.nomor_meja || '').trim();
    const nama = String(pesanan.nama_customer || '').trim();

    if (meja !== '' && nama !== '') {
        return meja + ' - ' + nama;
    }

    if (meja !== '') {
        return meja;
    }

    if (nama !== '') {
        return nama;
    }

    return 'Pelanggan';
}

function formatTanggalPesan(value) {
    const raw = String(value || '');

    if (raw.length < 16) {
        return '-';
    }

    return raw.substring(8, 10) + '/' + raw.substring(5, 7) + '/' + raw.substring(0, 4) + ' ' + raw.substring(11, 16);
}

function renderRiwayatRows(rows) {
    const tbody = document.getElementById('riwayatBody');

    if (!tbody) {
        return;
    }

    if (!Array.isArray(rows) || rows.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="empty-row">Belum ada transaksi.</td></tr>';
        return;
    }

    tbody.innerHTML = rows.map(function (pesanan) {
        const status = pesanan.status || '-';
        const statusSlug = status.toLowerCase().replace(/\s+/g, '-');
        const total = Number(pesanan.total_bayar || 0).toLocaleString('id-ID');
        const metode = pesanan.metode_bayar || '-';
        const waktu = formatTanggalPesan(pesanan.waktu_pesan);

        return '<tr>' +
            '<td><span class="queue-number">' + escapeHtml(formatQueue(pesanan.no_antrian)) + '</span></td>' +
            '<td><div class="order-code">' + escapeHtml(pesanan.kode_pesanan || '-') + '</div>' +
            '<div class="sub-text">Rp ' + total + '</div></td>' +
            '<td><strong>' + escapeHtml(formatIdCustomer(pesanan)) + '</strong>' +
            '<div class="sub-text">' + escapeHtml(pesanan.nama_customer || 'Pelanggan') + ' &middot; ' +
            escapeHtml(pesanan.nomor_meja || 'Meja -') + '</div></td>' +
            '<td><span class="payment-method">' + escapeHtml(metode) + '</span></td>' +
            '<td><span class="status-pill status-' + escapeHtml(statusSlug) + '">' + escapeHtml(status) + '</span></td>' +
            '<td style="white-space:nowrap;color:#4b5563;">' + escapeHtml(waktu) + ' WIB</td>' +
            '</tr>';
    }).join('');
}

function updateRiwayatPager(data) {
    riwayatPage = parseInt(data.page, 10) || 1;
    riwayatTotalPages = Math.max(1, parseInt(data.total_pages, 10) || 1);

    const meta = document.getElementById('riwayatMeta');
    const pageInfo = document.getElementById('riwayatPageInfo');
    const prev = document.getElementById('prevPage');
    const next = document.getElementById('nextPage');
    const goInput = document.getElementById('goPageInput');
    const updated = document.getElementById('riwayatUpdatedAt');

    if (meta) {
        meta.innerHTML = Number(data.total) > 0
            ? 'Menampilkan ' + escapeHtml(data.from) + '&ndash;' + escapeHtml(data.to) + ' dari ' + escapeHtml(data.total) + ' transaksi'
            : 'Tidak ada transaksi';
    }

    if (pageInfo) {
        pageInfo.textContent = riwayatPage + ' / ' + riwayatTotalPages;
    }

    if (prev) {
        prev.disabled = riwayatPage <= 1;
    }

    if (next) {
        next.disabled = riwayatPage >= riwayatTotalPages;
    }

    if (goInput) {
        goInput.max = riwayatTotalPages;

        if (document.activeElement !== goInput) {
            goInput.value = riwayatPage;
        }
    }

    if (updated) {
        updated.textContent = 'Diperbarui: ' +
            new Date().toLocaleTimeString('id-ID', { hour12: false }) +
            ' WIB';
    }
}

async function loadRiwayatPage(page, updateUrl) {
    if (riwayatLoading) {
        return;
    }

    riwayatLoading = true;

    try {
        const target = Math.max(1, Math.min(
            riwayatTotalPages,
            parseInt(page, 10) || 1
        ));

        const response = await fetch(
            RIWAYAT_API + '?page=' + encodeURIComponent(target),
            {
                headers: {
                    'Accept': 'application/json'
                }
            }
        );

        const data = await response.json();

        if (!response.ok || !data.status) {
            throw new Error(data.message || 'Gagal memuat riwayat transaksi.');
        }

        renderRiwayatRows(data.data || []);
        updateRiwayatPager(data);

        const liveStatus = document.getElementById('liveStatus');

        if (liveStatus) {
            liveStatus.textContent = '● Riwayat — diperbarui otomatis';
        }

        if (updateUrl !== false) {
            window.history.replaceState(
                null,
                '',
                '?page=' + encodeURIComponent(riwayatPage)
            );
        }
    } catch (error) {
        const liveStatus = document.getElementById('liveStatus');

        if (liveStatus) {
            liveStatus.textContent = '● Gagal memperbarui — mencoba lagi';
        }
    } finally {
        riwayatLoading = false;
    }
}

function goToRiwayatPage(page) {
    if (riwayatLoading) {
        return;
    }

    loadRiwayatPage(page, true);
}

document.getElementById('prevPage').addEventListener('click', function () {
    if (riwayatPage > 1) {
        goToRiwayatPage(riwayatPage - 1);
    }
});

document.getElementById('nextPage').addEventListener('click', function () {
    if (riwayatPage < riwayatTotalPages) {
        goToRiwayatPage(riwayatPage + 1);
    }
});

document.getElementById('goPageBtn').addEventListener('click', function () {
    const input = document.getElementById('goPageInput');
    goToRiwayatPage(input ? input.value : 1);
});

document.getElementById('goPageInput').addEventListener('keydown', function (event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        goToRiwayatPage(event.target.value);
    }
});

setInterval(function () {
    loadRiwayatPage(riwayatPage, false);
}, 30000);
</script>
</body>
</html>
