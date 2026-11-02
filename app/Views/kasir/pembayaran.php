<?php
$idPesanan = $idPesanan ?? ($_GET['id_pesanan'] ?? '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Kantin Merah POS</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 24px;
            background: #f5f6f8;
            color: #1f2937;
            font-family: Inter, -apple-system, BlinkMacSystemFont,
                "Segoe UI", Arial, sans-serif;
        }

        .container { max-width: 900px; margin: auto; }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }

        h1 { margin: 0 0 6px; font-size: 26px; }
        .muted { color: #6b7280; font-size: 14px; }

        .back-link {
            color: #dc2626;
            text-decoration: none;
            font-weight: 600;
        }

        .card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,.03);
        }

        .card h2 { margin: 0 0 20px; font-size: 18px; }

        .form-group { margin-bottom: 18px; }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        input, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 15px;
            background: white;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220,38,38,.1);
        }

        .summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 14px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .summary:last-child { border-bottom: 0; }

        .total {
            color: #dc2626;
            font-size: 28px;
            font-weight: 800;
        }

        .change {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: 16px;
            margin-top: 16px;
        }

        .change strong {
            display: block;
            margin-top: 6px;
            font-size: 24px;
            color: #15803d;
        }

        .method-options {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .method-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            cursor: pointer;
        }

        .method-option input {
            width: auto;
            accent-color: #dc2626;
        }

        .method-option:has(input:checked) {
            border-color: #dc2626;
            background: #fef2f2;
        }

        .button {
            display: inline-block;
            border: 0;
            border-radius: 8px;
            padding: 12px 18px;
            background: #dc2626;
            color: white;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }

        .button:hover { background: #b91c1c; }
        .button:disabled { background: #9ca3af; cursor: not-allowed; }

        .button-secondary {
            background: #e5e7eb;
            color: #374151;
        }

        .button-secondary:hover { background: #d1d5db; }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 22px;
        }

        .alert {
            padding: 13px 15px;
            margin-bottom: 18px;
            border-radius: 8px;
            font-size: 14px;
            line-height: 1.5;
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .hidden { display: none !important; }

        .status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            background: #fef3c7;
            color: #92400e;
            font-size: 12px;
            font-weight: 700;
        }

        @media (max-width: 600px) {
            body { padding: 14px; }
            .card { padding: 18px; }
            .topbar { align-items: flex-start; }
            h1 { font-size: 22px; }
            .method-options { grid-template-columns: 1fr; }
            .total { font-size: 23px; }
        }
    </style>
</head>

<body>
<div class="container">
    <div class="topbar">
        <div>
            <h1>Pembayaran</h1>
            <div class="muted">Kantin Merah POS</div>
        </div>
        <a class="back-link" href="<?= site_url('kasir') ?>">Kembali ke Kasir</a>
    </div>

    <div id="alertBox" class="alert hidden" role="alert"></div>

    <section class="card">
        <h2>Informasi Pesanan</h2>

        <div class="form-group">
            <label for="orderId">ID Pesanan</label>
            <input
                type="number"
                id="orderId"
                min="1"
                value="<?= esc((string) $idPesanan) ?>"
                placeholder="Masukkan ID pesanan"
            >
        </div>

        <div class="summary">
            <span class="muted">Status pesanan</span>
            <strong id="orderStatus">-</strong>
        </div>

        <div class="summary">
            <span class="muted">Total pembayaran</span>
            <span class="total" id="totalBayar">Rp0</span>
        </div>

        <div class="actions">
            <button type="button" class="button" id="loadOrderBtn">
                Muat Pesanan
            </button>
        </div>
    </section>

    <section class="card">
        <h2>Metode Pembayaran</h2>

        <form id="paymentForm">
            <?= csrf_field() ?>

            <div class="method-options">
                <label class="method-option">
                    <input type="radio" name="metode" value="Tunai" checked>
                    <span>
                        <strong>Tunai</strong><br>
                        <small class="muted">Bayar langsung ke kasir</small>
                    </span>
                </label>

                <label class="method-option">
                    <input type="radio" name="metode" value="QRIS">
                    <span>
                        <strong>QRIS</strong><br>
                        <small class="muted">Buat pembayaran berstatus menunggu</small>
                    </span>
                </label>
            </div>

            <div class="form-group" id="cashGroup" style="margin-top:22px">
                <label for="jumlahDiterima">Uang Diterima (Rp)</label>
                <input
                    type="number"
                    id="jumlahDiterima"
                    min="1"
                    step="1"
                    placeholder="Contoh: 50000"
                >

                <div class="change">
                    <span class="muted">Estimasi kembalian</span>
                    <strong id="kembalian">Rp0</strong>
                </div>
            </div>

            <div id="qrisInfo" class="alert hidden" style="margin-top:20px">
                Pembayaran QRIS akan dibuat dengan status menunggu. Setelah customer
                mengonfirmasi pembayaran di halaman status, pesanan otomatis
                diteruskan ke dapur.
            </div>

            <div class="actions">
                <button type="submit" class="button" id="payButton">
                    Proses Pembayaran
                </button>
                <button type="button" class="button button-secondary" id="resetButton">
                    Reset
                </button>
            </div>
        </form>
    </section>

    <section class="card hidden" id="resultCard">
        <h2>Hasil Pembayaran</h2>
        <div id="resultContent"></div>
    </section>
</div>

<script>
(() => {
    const orderIdInput = document.getElementById('orderId');
    const totalElement = document.getElementById('totalBayar');
    const statusElement = document.getElementById('orderStatus');
    const cashGroup = document.getElementById('cashGroup');
    const qrisInfo = document.getElementById('qrisInfo');
    const cashInput = document.getElementById('jumlahDiterima');
    const changeElement = document.getElementById('kembalian');
    const alertBox = document.getElementById('alertBox');
    const payButton = document.getElementById('payButton');
    const resultCard = document.getElementById('resultCard');
    const resultContent = document.getElementById('resultContent');

    let currentOrder = null;
    let currentPayment = null;

    const rupiah = value => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(Number(value) || 0);

    function showAlert(message, type = 'error') {
        alertBox.textContent = message;
        alertBox.className = `alert alert-${type}`;
    }

    function clearAlert() {
        alertBox.textContent = '';
        alertBox.className = 'alert hidden';
    }

    function getOrderId() {
        return String(orderIdInput.value).trim();
    }

    function getMethod() {
        return document.querySelector('input[name="metode"]:checked').value;
    }

    function syncMethod() {
        const isCash = getMethod() === 'Tunai';
        cashGroup.classList.toggle('hidden', !isCash);
        qrisInfo.classList.toggle('hidden', isCash);
        cashInput.required = isCash;
        updateChange();
    }

    function updateChange() {
        const total = Number(currentOrder?.total_bayar || 0);
        const received = Number(cashInput.value || 0);
        changeElement.textContent = rupiah(Math.max(0, received - total));
    }

    async function requestJson(url, options = {}) {
        const response = await fetch(url, {
            ...options,
            headers: {
                Accept: 'application/json',
                ...(options.headers || {})
            }
        });

        let data;
        try {
            data = await response.json();
        } catch {
            throw new Error('Respons server bukan JSON yang valid.');
        }

        if (!response.ok || data.status === false) {
            throw new Error(data.message || 'Permintaan gagal diproses.');
        }

        return data;
    }

    async function loadOrder() {
        clearAlert();
        resultCard.classList.add('hidden');
        currentOrder = null;
        currentPayment = null;
        totalElement.textContent = rupiah(0);
        statusElement.textContent = '-';
        updateChange();

        const id = getOrderId();

        if (!id || !/^\d+$/.test(id) || Number(id) < 1) {
            showAlert('Masukkan ID pesanan yang valid.');
            return;
        }

        try {
            const response = await requestJson(
                `<?= site_url('pesanan') ?>/${encodeURIComponent(id)}`
            );

            currentOrder = response.data;

            if (!currentOrder) {
                throw new Error('Data pesanan tidak ditemukan.');
            }

            totalElement.textContent = rupiah(currentOrder.total_bayar);
            statusElement.textContent = currentOrder.status || '-';
            updateChange();

            // Cek apakah pembayaran untuk pesanan ini sudah pernah dibuat.
            try {
                const paymentResponse = await requestJson(
                    `<?= site_url('pesanan') ?>/${encodeURIComponent(id)}/pembayaran`
                );
                currentPayment = paymentResponse.data || null;
            } catch (error) {
                // Jangan menganggap pembayaran sudah ada jika endpoint gagal.
                console.warn('Pemeriksaan pembayaran gagal:', error.message);
            }

            if (currentPayment && currentPayment.status_bayar === 'Berhasil') {
                showAlert(
                    `Pembayaran untuk pesanan ini sudah berhasil (${currentPayment.metode || '-'}).`,
                    'error'
                );
                payButton.disabled = true;
            } else if (currentPayment) {
                showAlert(
                    `Data pembayaran awal ditemukan dengan status: ${currentPayment.status_bayar || 'Menunggu'}. Silakan pilih metode dan proses pembayaran.`,
                    'success'
                );
                payButton.disabled = false;
            } else {
                payButton.disabled = false;
                clearAlert();
            }
        } catch (error) {
            showAlert(error.message);
        }
    }

    document.querySelectorAll('input[name="metode"]').forEach(input => {
        input.addEventListener('change', syncMethod);
    });

    cashInput.addEventListener('input', updateChange);
    document.getElementById('loadOrderBtn').addEventListener('click', loadOrder);

    orderIdInput.addEventListener('keydown', event => {
        if (event.key === 'Enter') {
            event.preventDefault();
            loadOrder();
        }
    });

    document.getElementById('paymentForm').addEventListener('submit', async event => {
        event.preventDefault();
        clearAlert();

        if (!currentOrder) {
            showAlert('Muat pesanan terlebih dahulu.');
            return;
        }

        if (currentPayment && currentPayment.status_bayar === 'Berhasil') {
            showAlert('Pembayaran untuk pesanan ini sudah tercatat berhasil.');
            return;
        }

        const total = Number(currentOrder.total_bayar || 0);

        if (total <= 0) {
            showAlert('Total pembayaran belum tersedia.');
            return;
        }

        const method = getMethod();
        const received = Number(cashInput.value || 0);

        if (method === 'Tunai') {
            if (!cashInput.value || !Number.isFinite(received) || received <= 0) {
                showAlert('Masukkan jumlah uang yang diterima dengan benar.');
                return;
            }

            if (received < total) {
                showAlert('Uang yang diterima kurang dari total pembayaran.');
                return;
            }
        }

        const id = getOrderId();
        const formData = new FormData();
        formData.append('metode', method);

        if (method === 'Tunai') {
            formData.append('jumlah_diterima', String(received));
        }

        const csrfInput = document.querySelector(
            '#paymentForm input[type="hidden"]'
        );

        if (csrfInput && csrfInput.name) {
            formData.append(csrfInput.name, csrfInput.value);
        }

        payButton.disabled = true;
        payButton.textContent = 'Memproses...';

        try {
            const result = await requestJson(
                `<?= site_url('pesanan') ?>/${encodeURIComponent(id)}/pembayaran`,
                {
                    method: 'POST',
                    body: formData
                }
            );

            const data = result.data || {};

            resultCard.classList.remove('hidden');
            resultContent.replaceChildren();

            const details = [
                ['Metode', data.metode || method],
                ['Status pembayaran', data.status_bayar || '-'],
                ['Total pembayaran', rupiah(data.total_bayar ?? total)]
            ];

            if (method === 'Tunai') {
                details.push(
                    ['Uang diterima', rupiah(data.jumlah_diterima ?? received)],
                    ['Kembalian', rupiah(data.kembalian ?? (received - total))]
                );
            } else {
                details.push(['Batas waktu', data.batas_waktu || '-']);
            }

            details.forEach(([label, value]) => {
                const row = document.createElement('div');
                row.className = 'summary';

                const labelElement = document.createElement('span');
                labelElement.className = 'muted';
                labelElement.textContent = label;

                const valueElement = document.createElement('strong');
                valueElement.textContent = value;

                row.append(labelElement, valueElement);
                resultContent.appendChild(row);
            });

            showAlert(result.message || 'Permintaan pembayaran berhasil diproses.', 'success');

            currentPayment = data;
            payButton.disabled = true;
        } catch (error) {
            showAlert(error.message);
            payButton.disabled = false;
        } finally {
            payButton.textContent = 'Proses Pembayaran';
        }
    });

    document.getElementById('resetButton').addEventListener('click', () => {
        document.getElementById('paymentForm').reset();
        resultCard.classList.add('hidden');
        clearAlert();
        syncMethod();
    });

    syncMethod();

    if (getOrderId()) {
        loadOrder();
    }
})();
</script>
</body>
</html>