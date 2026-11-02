<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Kasir - Kantin Merah POS</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 24px;
            background: #f5f6f8;
            color: #1f2937;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .page-header { margin-bottom: 24px; }

        h1 {
            margin: 0 0 8px;
            font-size: 28px;
        }

        .subtitle {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
        }

        .card {
            margin-bottom: 20px;
            padding: 24px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
        }

        .card h2 {
            margin: 0 0 18px;
            font-size: 19px;
        }

        .status-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            background: #e5e7eb;
            color: #374151;
            font-size: 12px;
            font-weight: 700;
        }

        .badge.active {
            background: #dcfce7;
            color: #166534;
        }

        .badge.inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .info-item {
            padding: 16px;
            background: #f9fafb;
            border-radius: 8px;
        }

        .info-label {
            margin-bottom: 8px;
            color: #6b7280;
            font-size: 13px;
        }

        .info-value {
            font-size: 17px;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            padding: 11px 16px;
            border: 0;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        .btn-primary {
            background: #dc2626;
            color: white;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
        }

        .message {
            display: none;
            margin-bottom: 16px;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 14px;
        }

        .message.show { display: block; }

        .message.success {
            background: #dcfce7;
            color: #166534;
        }

        .message.error {
            background: #fee2e2;
            color: #991b1b;
        }

        .empty-state {
            padding: 20px 0;
            color: #6b7280;
            line-height: 1.6;
        }

        @media (max-width: 600px) {
            body { padding: 14px; }
            .card { padding: 18px; }
            .info-grid { grid-template-columns: 1fr; }
            h1 { font-size: 23px; }
        }
    </style>
</head>

<body>
<div class="container">

    <header class="page-header">
        <h1>Shift Kasir</h1>
        <p class="subtitle">
            Periksa shift aktif sebelum memproses pembayaran tunai.
        </p>
    </header>

    <div id="messageBox" class="message" role="status"></div>

    <section class="card">
        <div class="status-row">
            <h2>Status Shift</h2>
            <span id="shiftBadge" class="badge">Memuat...</span>
        </div>

        <div id="shiftContent">
            <p class="empty-state">Memuat informasi shift...</p>
        </div>

        <div class="actions">
            <button type="button" class="btn btn-primary" id="openShiftButton">
                Buka Shift
            </button>

            <button type="button" class="btn btn-secondary" id="refreshButton">
                Muat Ulang
            </button>
        </div>
    </section>

    <section class="card">
        <h2>Ringkasan Kas Shift</h2>

        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Total Kas Tunai</div>
                <div class="info-value" id="cashTotal">Rp0</div>
            </div>

            <div class="info-item">
                <div class="info-label">Total Kas QRIS</div>
                <div class="info-value" id="qrisTotal">Rp0</div>
            </div>
        </div>

        <p class="subtitle" style="margin-top: 14px;">
            Nilai di atas mengikuti data shift yang dikembalikan backend.
        </p>
    </section>

</div>

<script>
    const SHIFT_API = {
        active: <?= json_encode(site_url('shift/aktif')) ?>,
        open: <?= json_encode(site_url('shift/buka')) ?>,
        totals: <?= json_encode(site_url('shift/total')) ?>
    };

    const shiftBadge = document.getElementById('shiftBadge');
    const shiftContent = document.getElementById('shiftContent');
    const messageBox = document.getElementById('messageBox');
    const openShiftButton = document.getElementById('openShiftButton');
    const refreshButton = document.getElementById('refreshButton');

    let activeShift = null;

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (char) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[char];
        });
    }

    function currency(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(Number(value) || 0);
    }

    function dateTime(value) {
        if (!value) return '-';

        const date = new Date(String(value).replace(' ', 'T'));

        return Number.isNaN(date.getTime())
            ? escapeHtml(value)
            : date.toLocaleString('id-ID');
    }

    function showMessage(message, type) {
        messageBox.textContent = message;
        messageBox.className = 'message show ' + type;
    }

    function clearMessage() {
        messageBox.textContent = '';
        messageBox.className = 'message';
    }

    async function requestJson(url, options = {}) {
        const response = await fetch(url, {
            ...options,
            headers: {
                'Accept': 'application/json',
                ...(options.headers || {})
            }
        });

        const result = await response.json();

        if (!response.ok || result.status !== true) {
            throw new Error(result.message || 'Permintaan gagal.');
        }

        return result;
    }

    function renderShift(shift) {
        activeShift = shift;

        if (!shift) {
            shiftBadge.textContent = 'Tidak Aktif';
            shiftBadge.className = 'badge inactive';

            shiftContent.innerHTML = `
                <div class="empty-state">
                    Belum ada shift aktif. Buka shift sebelum memproses
                    pembayaran tunai.
                </div>
            `;

            document.getElementById('cashTotal').textContent = currency(0);
            document.getElementById('qrisTotal').textContent = currency(0);
            openShiftButton.disabled = false;

            return;
        }

        shiftBadge.textContent = 'Aktif';
        shiftBadge.className = 'badge active';

        shiftContent.innerHTML = `
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">ID Shift</div>
                    <div class="info-value">${escapeHtml(shift.id_shift)}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Waktu Mulai</div>
                    <div class="info-value">${dateTime(shift.waktu_mulai)}</div>
                </div>
            </div>
        `;

        document.getElementById('cashTotal').textContent =
            currency(shift.total_kas_tunai);

        document.getElementById('qrisTotal').textContent =
            currency(shift.total_kas_qris);

        openShiftButton.disabled = true;
    }

    async function loadShift() {
        clearMessage();
        refreshButton.disabled = true;

        try {
            const result = await requestJson(SHIFT_API.active);
            renderShift(result.data || null);
        } catch (error) {
            shiftBadge.textContent = 'Gagal Memuat';
            shiftBadge.className = 'badge inactive';

            shiftContent.innerHTML = `
                <p class="empty-state">
                    ${escapeHtml(error.message)}
                </p>
            `;

            openShiftButton.disabled = true;
            showMessage(error.message, 'error');
        } finally {
            refreshButton.disabled = false;
        }
    }

    async function openShift() {
        openShiftButton.disabled = true;
        clearMessage();

        try {
            const result = await requestJson(SHIFT_API.open, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            showMessage(
                result.message || 'Shift berhasil dibuka.',
                'success'
            );

            await loadShift();
        } catch (error) {
            showMessage(error.message, 'error');
            openShiftButton.disabled = false;
        }
    }

    openShiftButton.addEventListener('click', openShift);
    refreshButton.addEventListener('click', loadShift);

    loadShift();
</script>
</body>
</html>
