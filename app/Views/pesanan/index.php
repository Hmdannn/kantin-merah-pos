```php
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pesanan - Kantin Merah POS</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 24px;
            font-family: Arial, sans-serif;
            background: #f5f6f8;
            color: #1f2937;
        }

        .container {
            max-width: 1200px;
            margin: auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }

        h1 {
            margin: 0 0 6px;
            font-size: 26px;
        }

        .subtitle {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
        }

        .panel {
            padding: 20px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
        }

        .toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 18px;
        }

        .search-input,
        .filter-select {
            min-height: 42px;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #fff;
        }

        .search-input {
            flex: 1;
            min-width: 200px;
        }

        .filter-select {
            min-width: 180px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 14px;
            border: 0;
            border-radius: 8px;
            background: #dc2626;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
        }

        .btn:hover {
            background: #b91c1c;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            white-space: nowrap;
        }

        th,
        td {
            padding: 13px 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            background: #f9fafb;
            font-size: 13px;
            color: #4b5563;
        }

        td {
            font-size: 14px;
        }

        .status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            background: #e5e7eb;
            color: #374151;
            font-size: 12px;
            font-weight: 600;
        }

        .status-dibayar,
        .status-selesai {
            background: #dcfce7;
            color: #166534;
        }

        .status-diproses {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .status-siap-diambil {
            background: #fef3c7;
            color: #92400e;
        }

        .status-menunggu-pembayaran {
            background: #ffedd5;
            color: #9a3412;
        }

        .status-dibatalkan,
        .status-kadaluarsa {
            background: #fee2e2;
            color: #991b1b;
        }

        .message {
            padding: 18px;
            text-align: center;
            color: #6b7280;
        }

        .error {
            color: #b91c1c;
        }

        .footer-info {
            margin-top: 16px;
            color: #6b7280;
            font-size: 13px;
        }

        @media (max-width: 600px) {
            body {
                padding: 14px;
            }

            .page-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .panel {
                padding: 14px;
            }

            h1 {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>
<div class="container">

    <header class="page-header">
        <div>
            <h1>Daftar Pesanan</h1>
            <p class="subtitle">Pantau status dan total pembayaran pesanan.</p>
        </div>

        <button type="button" class="btn" id="refreshButton">
            Muat Ulang
        </button>
    </header>

    <main class="panel">
        <div class="toolbar">
            <input
                type="search"
                id="searchInput"
                class="search-input"
                placeholder="Cari kode pesanan..."
                aria-label="Cari kode pesanan"
            >

            <select id="statusFilter" class="filter-select">
                <option value="">Semua status</option>
                <option value="Menunggu Pembayaran">Menunggu Pembayaran</option>
                <option value="Dibayar">Dibayar</option>
                <option value="Diproses">Diproses</option>
                <option value="Siap Diambil">Siap Diambil</option>
                <option value="Selesai">Selesai</option>
                <option value="Dibatalkan">Dibatalkan</option>
                <option value="Kadaluarsa">Kadaluarsa</option>
            </select>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Kode Pesanan</th>
                        <th>Waktu Pesan</th>
                        <th>Total Bayar</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody id="ordersTable">
                    <tr>
                        <td colspan="5" class="message">
                            Memuat data pesanan...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="footer-info" id="orderCount">
            Menyiapkan data pesanan...
        </div>
    </main>

</div>

<script>
    const ORDERS_API = <?= json_encode(site_url('pesanan')) ?>;

    const tableBody = document.getElementById('ordersTable');
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const orderCount = document.getElementById('orderCount');
    const refreshButton = document.getElementById('refreshButton');

    let orders = [];

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

    function formatCurrency(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(Number(value) || 0);
    }

    function formatDate(value) {
        if (!value) return '-';

        const date = new Date(String(value).replace(' ', 'T'));

        if (Number.isNaN(date.getTime())) {
            return escapeHtml(value);
        }

        return date.toLocaleString('id-ID', {
            dateStyle: 'medium',
            timeStyle: 'short'
        });
    }

    function statusClass(status) {
        return 'status-' + String(status || '')
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-|-$/g, '');
    }

    function renderOrders() {
        const keyword = searchInput.value.trim().toLowerCase();
        const selectedStatus = statusFilter.value;

        const filteredOrders = orders.filter(function (order) {
            const matchesKeyword = String(order.kode_pesanan || '')
                .toLowerCase()
                .includes(keyword);

            const matchesStatus = !selectedStatus ||
                order.status === selectedStatus;

            return matchesKeyword && matchesStatus;
        });

        if (filteredOrders.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="message">
                        Tidak ada pesanan yang sesuai.
                    </td>
                </tr>
            `;
        } else {
            tableBody.innerHTML = filteredOrders.map(function (order, index) {
                return `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${escapeHtml(order.kode_pesanan || '-')}</td>
                        <td>${formatDate(order.waktu_pesan)}</td>
                        <td>${formatCurrency(order.total_bayar)}</td>
                        <td>
                            <span class="status ${statusClass(order.status)}">
                                ${escapeHtml(order.status || '-')}
                            </span>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        orderCount.textContent =
            `Menampilkan ${filteredOrders.length} dari ${orders.length} pesanan.`;
    }

    async function loadOrders() {
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="message">Memuat data pesanan...</td>
            </tr>
        `;

        refreshButton.disabled = true;

        try {
            const response = await fetch(ORDERS_API, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (!response.ok || result.status !== true) {
                throw new Error(result.message || 'Gagal memuat pesanan.');
            }

            orders = Array.isArray(result.data) ? result.data : [];
            renderOrders();
        } catch (error) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="message error">
                        ${escapeHtml(error.message || 'Terjadi kesalahan saat memuat data.')}
                    </td>
                </tr>
            `;

            orderCount.textContent = 'Data pesanan gagal dimuat.';
        } finally {
            refreshButton.disabled = false;
        }
    }

    searchInput.addEventListener('input', renderOrders);
    statusFilter.addEventListener('change', renderOrders);
    refreshButton.addEventListener('click', loadOrders);

    loadOrders();
</script>
</body>
</html>
```