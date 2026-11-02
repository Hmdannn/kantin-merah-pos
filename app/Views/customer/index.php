<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Kantin Merah</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            color: #222;
        }

        button {
            font-family: inherit;
            cursor: pointer;
        }

        .customer-header {
            position: sticky;
            top: 0;
            z-index: 100;
            background: #ffffff;
            border-bottom: 1px solid #eeeeee;
            padding: 16px 24px;
        }

        .header-inner {
            max-width: 1200px;
            margin: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .brand {
            font-size: 22px;
            font-weight: 700;
            color: #b91c1c;
        }

        .cart-button {
            position: relative;
            border: none;
            background: #b91c1c;
            color: white;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 600;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            min-width: 22px;
            height: 22px;
            padding: 0 5px;
            border-radius: 50%;
            background: #111827;
            color: white;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        main {
            max-width: 1200px;
            margin: auto;
            padding: 24px;
        }

        .hero {
            background: #b91c1c;
            color: white;
            border-radius: 14px;
            padding: 28px;
            margin-bottom: 24px;
        }

        .hero h1 {
            margin-bottom: 8px;
            font-size: 30px;
        }

        .hero p {
            opacity: 0.9;
        }

        .section-title {
            font-size: 22px;
            margin-bottom: 16px;
        }

        .category-list {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 8px;
            margin-bottom: 24px;
        }

        .category-button {
            flex-shrink: 0;
            border: 1px solid #dddddd;
            background: white;
            padding: 10px 16px;
            border-radius: 999px;
            color: #333;
        }

        .category-button.active {
            background: #b91c1c;
            color: white;
            border-color: #b91c1c;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(
                auto-fill,
                minmax(220px, 1fr)
            );
            gap: 18px;
        }

        .menu-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #eeeeee;
            transition: 0.2s;
        }

        .menu-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(
                0,
                0,
                0,
                0.08
            );
        }

        .menu-image {
            width: 100%;
            aspect-ratio: 4 / 3;
            height: auto;
            object-fit: cover;
            background: #eeeeee;
        }

        .menu-content {
            padding: 16px;
        }

        .menu-category {
            font-size: 12px;
            color: #777;
            margin-bottom: 6px;
        }

        .menu-name {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .menu-price {
            font-weight: 700;
            color: #b91c1c;
            margin-bottom: 6px;
        }

        .menu-stock {
            font-size: 13px;
            color: #666;
            margin-bottom: 14px;
        }

        .menu-detail-button {
            width: 100%;
            border: none;
            background: #b91c1c;
            color: white;
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
        }

        .menu-detail-button:disabled {
            background: #e5e7eb;
            color: #9ca3af;
            cursor: not-allowed;
        }

        .menu-card-habis .menu-image {
            filter: grayscale(0.7);
            opacity: 0.6;
        }

        .menu-habis-badge {
            display: inline-block;
            background: #fee2e2;
            color: #b91c1c;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 999px;
            margin-bottom: 8px;
        }

        .menu-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
        }

        .menu-header-row .section-title {
            margin-bottom: 0;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
            background: white;
            border-radius: 12px;
            color: #777;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #777;
        }

        .error-message {
            background: #fee2e2;
            color: #991b1b;
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .checkout-method-options {
            display: grid;
            grid-template-columns: repeat(
                2,
                minmax(0, 1fr)
            );
            gap: 10px;
        }

        .checkout-method-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            border: 1px solid #dddddd;
            border-radius: 10px;
            background: white;
            cursor: pointer;
        }

        .checkout-method-option input {
            width: auto;
            accent-color: #b91c1c;
        }

        .checkout-method-option.active {
            border-color: #b91c1c;
            background: #fef2f2;
        }

        .checkout-method-info {
            margin-top: 10px;
            padding: 10px 12px;
            background: #f7f7f7;
            border-radius: 8px;
            font-size: 13px;
            color: #666;
        }

        .ready-section {
            margin-bottom: 30px;
        }

        .ready-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 6px;
        }

        .ready-header .section-title {
            margin-bottom: 0;
        }

        .ready-refresh {
            flex-shrink: 0;
            border: 1px solid #b91c1c;
            background: white;
            color: #b91c1c;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
        }

        .ready-hint {
            color: #666;
            font-size: 14px;
            margin-bottom: 14px;
        }

        .ready-grid {
            display: grid;
            grid-template-columns: repeat(
                auto-fill,
                minmax(220px, 1fr)
            );
            gap: 14px;
        }

        .ready-card {
            background: white;
            border: 1px solid #eeeeee;
            border-left: 4px solid #b91c1c;
            border-radius: 10px;
            padding: 16px;
        }

        .ready-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 10px;
        }

        .ready-code {
            font-weight: 700;
            color: #b91c1c;
            overflow-wrap: anywhere;
        }

        .ready-badge {
            flex-shrink: 0;
            background: #dcfce7;
            color: #166534;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 999px;
        }

        .ready-total {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .ready-time {
            font-size: 12px;
            color: #777;
            margin-bottom: 12px;
        }

        .ready-detail-button {
            width: 100%;
            border: none;
            background: #b91c1c;
            color: white;
            padding: 9px;
            border-radius: 8px;
            font-weight: 600;
        }

        .ready-card-mine {
            border-left-color: #16a34a;
            box-shadow: 0 0 0 2px #bbf7d0;
        }

        .ready-toast {
            display: none;
            position: fixed;
            top: 18px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2500;
            width: calc(100% - 32px);
            max-width: 440px;
            background: #166534;
            color: #ffffff;
            border-radius: 12px;
            padding: 14px 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        }

        .ready-toast strong {
            display: block;
            font-size: 15px;
            margin-bottom: 3px;
        }

        .ready-toast span {
            font-size: 13px;
            opacity: 0.92;
        }

        @media (max-width: 600px) {
            .customer-header {
                padding: 14px 16px;
            }

            main {
                padding: 16px;
            }

            .hero {
                padding: 22px;
            }

            .hero h1 {
                font-size: 24px;
            }

            .menu-grid {
                grid-template-columns: repeat(
                    2,
                    minmax(0, 1fr)
                );
                gap: 12px;
            }

            .menu-content {
                padding: 12px;
            }

            .menu-name {
                font-size: 16px;
            }

            .ready-grid {
                grid-template-columns: 1fr;
            }

            .checkout-method-options {
                grid-template-columns: 1fr;
            }
        }

        .qris-demo-card {
            background: #ffffff;
            border: 1px solid #ddd6fe;
            border-radius: 14px;
            padding: 14px;
            text-align: center;
            box-shadow: 0 4px 14px rgba(109, 40, 217, 0.08);
        }
        .qris-demo-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 6px;
        }
        .qris-demo-logo {
            color: #5b21b6;
            font-size: 20px;
            font-weight: 900;
            letter-spacing: 1px;
            font-style: italic;
        }
        .qris-demo-badge {
            background: #ede9fe;
            border: 1px solid #c4b5fd;
            border-radius: 9999px;
            color: #6d28d9;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.5px;
            padding: 3px 8px;
        }
        .qris-demo-merchant {
            color: #111827;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 0.3px;
        }
        .qris-demo-sub {
            color: #6b7280;
            font-size: 11px;
            margin: 2px 0 8px;
        }
        .qris-demo-image-wrap {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            display: inline-block;
            margin-bottom: 8px;
            padding: 8px;
        }
        .qris-demo-image-wrap img {
            border-radius: 6px;
            display: block;
            height: auto;
            max-width: 100%;
            width: 160px;
        }
        .qris-demo-amount-label {
            color: #6b7280;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.6px;
            text-transform: uppercase;
        }
        .qris-demo-amount {
            color: #111827;
            font-size: 20px;
            font-weight: 900;
            margin-bottom: 4px;
        }
        .qris-demo-meta {
            color: #4b5563;
            font-size: 12px;
            margin-bottom: 8px;
        }
        .qris-demo-meta strong {
            color: #111827;
        }
        .qris-demo-divider {
            border-top: 1px dashed #d1d5db;
            margin: 8px 0;
        }
        .qris-demo-status {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 8px;
            color: #b45309;
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 6px;
            padding: 6px 8px;
        }
        .qris-demo-status.paid {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: #15803d;
        }
        .qris-demo-paid-note {
            color: #374151;
            font-size: 12px;
            margin-bottom: 6px;
        }
        .qris-demo-demo-note {
            color: #9ca3af;
            font-size: 9px;
            line-height: 1.4;
            margin-top: 6px;
        }
    </style>
</head>

<body>

<div
    id="readyToast"
    class="ready-toast"
    role="status"
    aria-live="polite"
>
    <strong>🔔 Makanan Siap Diambil!</strong>
    <span id="readyToastMessage">
        Silakan ambil makanan Anda di kasir.
    </span>
</div>

<header class="customer-header">

    <div class="header-inner">

        <div class="brand">
            Kantin Merah
        </div>

        <button
            type="button"
            id="cartButton"
            class="cart-button">

            Keranjang

            <span
                id="cartCount"
                class="cart-count">
                0
            </span>

        </button>

    </div>

</header>


<main>

    <section class="hero">

        <h1>
            Selamat Datang
        </h1>

        <p>
            Pilih makanan dan minuman favoritmu.
        </p>

    </section>


    <section class="ready-section">

        <div class="ready-header">

            <h2 class="section-title">
                Pesanan Siap Diambil
            </h2>

            <button
                type="button"
                id="refreshReadyOrders"
                class="ready-refresh">

                Muat Ulang

            </button>

        </div>

        <p class="ready-hint">
        Pesanan yang sudah selesai dimasak dan siap
        diambil sendiri di kasir (self service).
        </p>

        <div
            id="readyLoading"
            class="loading">

            Memuat pesanan siap diambil...

        </div>

        <div
            id="readyError"
            class="error-message"
            style="display: none;">

        </div>

        <div
            id="readyEmpty"
            class="empty-state"
            style="display: none;">

            Belum ada pesanan yang siap diambil.

        </div>

        <div
            id="readyGrid"
            class="ready-grid"
            style="display: none;">

        </div>

    </section>


    <section>

        <h2 class="section-title">
            Kategori
        </h2>

        <div
            id="categoryList"
            class="category-list">

            <button
                type="button"
                class="category-button active"
                data-category-id="all">

                Semua

            </button>

        </div>

    </section>


    <section>

        <div class="menu-header-row">

            <h2 class="section-title">
                Menu
            </h2>

            <button
                type="button"
                id="refreshMenu"
                class="ready-refresh">

                Muat Ulang

            </button>

        </div>

        <div
            id="menuLoading"
            class="loading">

            Memuat menu...

        </div>

        <div
            id="menuError"
            class="error-message"
            style="display: none;">

        </div>

        <div
            id="menuEmpty"
            class="empty-state"
            style="display: none;">

            Tidak ada menu tersedia.

        </div>

        <div
            id="menuGrid"
            class="menu-grid"
            style="display: none;">

        </div>

    </section>

</main>


<script>

    const CUSTOMER_API =
        <?= json_encode(site_url('customer')) ?>;

    const MENU_API =
        <?= json_encode(site_url('customer/menu')) ?>;

    const CATEGORY_API =
        <?= json_encode(site_url('customer/kategori')) ?>;

    const READY_ORDERS_API =
        <?= json_encode(site_url('customer/pesanan-siap')) ?>;

    const CUSTOMER_ORDER_API =
        <?= json_encode(site_url('customer/pesanan')) ?>;

    const CART_STORAGE_KEY =
        'kantin_merah_cart';

    const LAST_ORDER_STORAGE_KEY =
        'kantin_merah_last_order';

    const READY_NOTIFIED_STORAGE_KEY =
        'kantin_merah_ready_notified';


    let customerData = {
        menu: [],
        kategori: []
    };


    const menuGrid =
        document.getElementById('menuGrid');

    const menuLoading =
        document.getElementById('menuLoading');

    const menuEmpty =
        document.getElementById('menuEmpty');

    const menuError =
        document.getElementById('menuError');

    const categoryList =
        document.getElementById('categoryList');


    function formatRupiah(value)
    {
        return new Intl.NumberFormat(
            'id-ID',
            {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }
        ).format(value);
    }


    function getCart()
    {
        try {

            const cart =
                localStorage.getItem(
                    CART_STORAGE_KEY
                );

            if (!cart) {
                return [];
            }

            const parsed =
                JSON.parse(cart);

            return Array.isArray(parsed)
                ? parsed
                : [];

        } catch (error) {

            return [];

        }
    }


    function updateCartCount()
    {
        const cart =
            getCart();

        const total =
            cart.reduce(
                function (sum, item) {

                    return sum +
                        Number(item.jumlah || 0);

                },
                0
            );

        const cartCount =
            document.getElementById(
                'cartCount'
            );

        if (cartCount) {

            cartCount.textContent =
                total;

        }
    }


    function showMenuLoading()
    {
        menuLoading.style.display =
            'block';

        menuGrid.style.display =
            'none';

        menuEmpty.style.display =
            'none';

        menuError.style.display =
            'none';
    }


    function showMenuError(message)
    {
        menuLoading.style.display =
            'none';

        menuGrid.style.display =
            'none';

        menuEmpty.style.display =
            'none';

        menuError.textContent =
            message;

        menuError.style.display =
            'block';
    }


    function renderCategories()
    {
        categoryList.innerHTML = '';

        const allButton =
            document.createElement('button');

        allButton.type = 'button';

        allButton.className =
            'category-button active';

        allButton.dataset.categoryId =
            'all';

        allButton.textContent =
            'Semua';

        categoryList.appendChild(
            allButton
        );


        customerData.kategori.forEach(
            function (kategori) {

                const button =
                    document.createElement(
                        'button'
                    );

                button.type = 'button';

                button.className =
                    'category-button';

                button.dataset.categoryId =
                    kategori.id_kategori;

                button.textContent =
                    kategori.nama_kategori;

                categoryList.appendChild(
                    button
                );

            }
        );
    }


    function renderMenu(menu)
    {
        menuLoading.style.display =
            'none';

        menuError.style.display =
            'none';


        if (!menu || menu.length === 0) {

            menuGrid.innerHTML = '';

            menuGrid.style.display =
                'none';

            menuEmpty.style.display =
                'block';

            return;
        }


        menuEmpty.style.display =
            'none';

        menuGrid.style.display =
            'grid';

        menuGrid.innerHTML = '';


        menu.forEach(
            function (item) {

                const habis =
                    item.status_stok !== 'Tersedia' ||
                    Number(item.jumlah_stok) <= 0;

                const card =
                    document.createElement(
                        'div'
                    );

                card.className = habis
                    ? 'menu-card menu-card-habis'
                    : 'menu-card';


                let imageHtml = '';

                if (item.foto) {

                    imageHtml = `
                        <img
                            src="${escapeHtml(
                                item.foto
                            )}"
                            alt="${escapeHtml(
                                item.nama_menu
                            )}"
                            class="menu-image"
                        >
                    `;

                } else {

                    imageHtml = `
                        <div
                            class="menu-image"
                            style="
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                color:#999;
                            "
                        >
                            Tidak ada foto
                        </div>
                    `;

                }


                card.innerHTML = `

                    ${imageHtml}

                    <div class="menu-content">

                        <div class="menu-category">
                            ${escapeHtml(
                                item.nama_kategori ||
                                ''
                            )}
                        </div>

                        <div class="menu-name">
                            ${escapeHtml(
                                item.nama_menu
                            )}
                        </div>

                        <div class="menu-price">
                            ${formatRupiah(
                                item.harga
                            )}
                        </div>

                        ${habis ? `
                            <div class="menu-habis-badge">
                                Stok Habis
                            </div>
                        ` : ''}

                        <div class="menu-stock">
                            ${habis
                                ? 'Menu sedang tidak tersedia.'
                                : `Stok: ${Number(item.jumlah_stok)}`}
                        </div>

                        <button
                            type="button"
                            class="menu-detail-button"
                            data-menu-id="${Number(
                                item.id_menu
                            )}"
                            ${habis ? 'disabled' : ''}
                        >
                            ${habis ? 'Stok Habis' : 'Lihat Detail'}
                        </button>

                    </div>

                `;


                menuGrid.appendChild(
                    card
                );

            }
        );
    }


    function escapeHtml(value)
    {
        const div =
            document.createElement('div');

        div.textContent =
            value ?? '';

        return div.innerHTML;
    }


    async function loadCustomerData()
    {
        showMenuLoading();

        try {

            const response =
                await fetch(
                    CUSTOMER_API,
                    {
                        method: 'GET',
                        headers: {
                            'Accept':
                                'application/json'
                        }
                    }
                );


            if (!response.ok) {
                throw new Error(
                    'Gagal mengambil data customer.'
                );
            }


            const result =
                await response.json();


            if (
                !result.status ||
                !result.data
            ) {

                throw new Error(
                    result.message ||
                    'Data customer tidak tersedia.'
                );

            }


            customerData =
                result.data;


            renderCategories();

            renderMenu(
                customerData.menu || []
            );


        } catch (error) {

            showMenuError(
                error.message ||
                'Terjadi kesalahan saat memuat menu.'
            );

        }
    }


    async function loadMenuByKategori(
        idKategori
    )
    {
        showMenuLoading();

        try {

            const response =
                await fetch(
                    `${CATEGORY_API}/${idKategori}`,
                    {
                        method: 'GET',
                        headers: {
                            'Accept':
                                'application/json'
                        }
                    }
                );


            if (!response.ok) {

                throw new Error(
                    'Gagal mengambil menu kategori.'
                );

            }


            const result =
                await response.json();


            if (
                !result.status ||
                !result.data
            ) {

                throw new Error(
                    result.message ||
                    'Menu kategori tidak tersedia.'
                );

            }


            renderMenu(
                result.data.menu || []
            );


        } catch (error) {

            showMenuError(
                error.message ||
                'Terjadi kesalahan saat memuat kategori.'
            );

        }
    }


    categoryList.addEventListener(
        'click',
        function (event) {

            const button =
                event.target.closest(
                    '[data-category-id]'
                );


            if (!button) {
                return;
            }


            document
                .querySelectorAll(
                    '.category-button'
                )
                .forEach(
                    function (item) {

                        item.classList.remove(
                            'active'
                        );

                    }
                );


            button.classList.add(
                'active'
            );


            const categoryId =
                button.dataset.categoryId;


            if (
                categoryId === 'all'
            ) {

                renderMenu(
                    customerData.menu || []
                );

                return;
            }


            loadMenuByKategori(
                categoryId
            );

        }
    );


    menuGrid.addEventListener(
        'click',
        function (event) {

            const button =
                event.target.closest(
                    '[data-menu-id]'
                );


            if (!button) {
                return;
            }


            const menuId =
                button.dataset.menuId;


            if (
                typeof window.openCustomerMenuDetail ===
                'function'
            ) {

                window.openCustomerMenuDetail(
                    menuId
                );

            }

        }
    );


    document
        .getElementById('cartButton')
        .addEventListener(
            'click',
            function () {

                if (
                    typeof window.openCustomerCart ===
                    'function'
                ) {

                    window.openCustomerCart();

                }

            }
        );


    const readyLoading =
        document.getElementById('readyLoading');

    const readyError =
        document.getElementById('readyError');

    const readyEmpty =
        document.getElementById('readyEmpty');

    const readyGrid =
        document.getElementById('readyGrid');


    function readyTime(value)
    {
        if (!value) {
            return '-';
        }

        const date =
            new Date(String(value).replace(' ', 'T'));

        if (Number.isNaN(date.getTime())) {
            return String(value);
        }

        return date.toLocaleTimeString(
            'id-ID',
            {
                hour: '2-digit',
                minute: '2-digit'
            }
        ) + ' WIB';
    }


    function showReadyLoading()
    {
        readyLoading.style.display =
            'block';

        readyGrid.style.display =
            'none';

        readyEmpty.style.display =
            'none';

        readyError.style.display =
            'none';
    }


    function showReadyError(message)
    {
        readyLoading.style.display =
            'none';

        readyGrid.style.display =
            'none';

        readyEmpty.style.display =
            'none';

        readyError.textContent =
            message;

        readyError.style.display =
            'block';
    }


    function getSavedOrderCodeForReady()
    {
        try {

            const saved =
                localStorage.getItem(
                    LAST_ORDER_STORAGE_KEY
                );

            if (!saved) {
                return null;
            }

            const order =
                JSON.parse(saved);

            return order && order.kode_pesanan
                ? order.kode_pesanan
                : null;

        } catch (error) {

            return null;

        }
    }


    function showReadyToast(message)
    {
        const toast =
            document.getElementById('readyToast');

        const messageEl =
            document.getElementById(
                'readyToastMessage'
            );

        if (!toast || !messageEl) {
            return;
        }

        messageEl.textContent =
            message;

        toast.style.display =
            'block';

        clearTimeout(showReadyToast.timer);

        showReadyToast.timer = setTimeout(
            function () {

                toast.style.display =
                    'none';

            },
            15000
        );
    }


    function notifyReadyOrders(orders)
    {
        const kode =
            getSavedOrderCodeForReady();

        if (!kode) {
            return;
        }

        const ditemukan =
            (orders || []).some(
                function (order) {

                    return order.kode_pesanan === kode;

                }
            );

        if (!ditemukan) {
            return;
        }

        if (
            sessionStorage.getItem(
                READY_NOTIFIED_STORAGE_KEY
            ) === kode
        ) {
            return;
        }

        sessionStorage.setItem(
            READY_NOTIFIED_STORAGE_KEY,
            kode
        );

        showReadyToast(
            'Pesanan ' +
            kode +
            ' sudah siap. Silakan ambil makanan Anda di kasir.'
        );

        if (
            'Notification' in window &&
            Notification.permission === 'granted'
        ) {
            try {

                new Notification(
                    '🔔 Makanan Siap Diambil!',
                    {
                        body:
                            'Pesanan ' +
                            kode +
                            ' silakan diambil di kasir.',
                        tag:
                            'kantin-ready-' + kode
                    }
                );

            } catch (error) {
                // Notifikasi browser tidak tersedia.
            }
        }
    }


    async function ambilPesananCustomer(kodePesanan)
    {
        if (!kodePesanan) {
            return;
        }

        if (
            !confirm(
                'Konfirmasi bahwa Anda sudah mengambil makanan ini?'
            )
        ) {
            return;
        }

        try {

            const response =
                await fetch(
                    `${CUSTOMER_ORDER_API}/${encodeURIComponent(
                        kodePesanan
                    )}/ambil`,
                    {
                        method: 'POST',
                        headers: {
                            'Accept':
                                'application/json'
                        }
                    }
                );

            const result =
                await response.json();

            if (!response.ok || !result.status) {
                throw new Error(
                    result.message ||
                    'Gagal menandai pesanan selesai.'
                );
            }

            alert(
                'Terima kasih! Pesanan sudah ditandai selesai.'
            );

            await loadReadyOrders(true);

        } catch (error) {

            alert(
                error.message ||
                'Gagal menandai pesanan selesai.'
            );

        }
    }


    function renderReadyOrders(orders)
    {
        readyLoading.style.display =
            'none';

        readyError.style.display =
            'none';


        if (!orders || orders.length === 0) {

            readyGrid.innerHTML = '';

            readyGrid.style.display =
                'none';

            readyEmpty.style.display =
                'block';

            return;

        }


        readyEmpty.style.display =
            'none';

        readyGrid.style.display =
            'grid';

        readyGrid.innerHTML = '';


        orders.forEach(
            function (order) {

                const card =
                    document.createElement('article');

                card.className =
                    'ready-card';


                const header =
                    document.createElement('div');

                header.className =
                    'ready-card-top';


                const code =
                    document.createElement('span');

                code.className =
                    'ready-code';

                code.textContent =
                    order.kode_pesanan || '-';


                const badge =
                    document.createElement('span');

                badge.className =
                    'ready-badge';

                badge.textContent =
                    'Siap Diambil';


                header.append(code, badge);


                const total =
                    document.createElement('div');

                total.className =
                    'ready-total';

                total.textContent =
                    formatRupiah(
                        Number(order.total_bayar || 0)
                    );


                const time =
                    document.createElement('div');

                time.className =
                    'ready-time';

                time.textContent =
                    'Siap sejak ' +
                    readyTime(
                        order.updated_at ||
                        order.waktu_pesan
                    );


                const payment =
                    document.createElement('div');

                payment.className =
                    'ready-time';

                payment.style.color =
                    '#15803d';

                payment.style.fontWeight =
                    '700';

                payment.textContent =
                    (order.metode_bayar === 'QRIS')
                        ? 'Pembayaran: QRIS (lunas)'
                        : 'Pembayaran: Tunai (lunas)';


                const actionWrapper =
                    document.createElement('div');

                actionWrapper.style.cssText =
                    'display:flex; flex-direction:column; gap:8px;';


                const button =
                    document.createElement('button');

                button.type = 'button';

                button.className =
                    'ready-detail-button';

                button.textContent =
                    'Lihat Status Pesanan';

                button.addEventListener(
                    'click',
                    function () {

                        if (
                            typeof window.openCustomerStatus ===
                            'function'
                        ) {

                            window.openCustomerStatus(
                                order.kode_pesanan
                            );

                        }

                    }
                );

                actionWrapper.appendChild(button);


                if (
                    getSavedOrderCodeForReady() ===
                    order.kode_pesanan
                ) {

                    card.classList.add(
                        'ready-card-mine'
                    );

                    const ambilButton =
                        document.createElement('button');

                    ambilButton.type = 'button';

                    ambilButton.className =
                        'ready-detail-button';

                    ambilButton.style.background =
                        '#16a34a';

                    ambilButton.textContent =
                        '✅ Ambil Makanan';

                    ambilButton.addEventListener(
                        'click',
                        function () {

                            ambilPesananCustomer(
                                order.kode_pesanan
                            );

                        }
                    );

                    actionWrapper.appendChild(
                        ambilButton
                    );
                }


                card.append(
                    header,
                    total,
                    time,
                    payment,
                    actionWrapper
                );

                const custInfo = document.createElement('div');
                custInfo.style.cssText = 'font-size:12px; color:#c2410c; background:#fff7ed; border:1px solid #fed7aa; padding:4px 8px; border-radius:6px; margin-bottom:8px; font-weight:700; display:flex; justify-content:space-between; align-items:center;';
                custInfo.innerHTML = `<span>🪑 ${order.nomor_meja || 'Meja -'}</span><span>👤 ${order.nama_customer || 'Pelanggan'}</span>`;
                card.insertBefore(custInfo, total);
                readyGrid.appendChild(card);

            }
        );
    }


    async function loadReadyOrders(silent = false)
    {
        if (!silent) {
            showReadyLoading();
        }

        try {

            const response =
                await fetch(
                    READY_ORDERS_API,
                    {
                        method: 'GET',
                        cache: 'no-store',
                        headers: {
                            'Accept':
                                'application/json'
                        }
                    }
                );


            const result =
                await response.json();


            if (
                !response.ok ||
                !result.status
            ) {

                throw new Error(
                    result.message ||
                    'Gagal mengambil pesanan siap diambil.'
                );

            }


            renderReadyOrders(
                result.data || []
            );

            notifyReadyOrders(
                result.data || []
            );


        } catch (error) {

            if (!silent) {

                showReadyError(
                    error.message ||
                    'Gagal memuat pesanan siap diambil.'
                );

            }

        }
    }


    document
        .getElementById('refreshReadyOrders')
        .addEventListener(
            'click',
            function () {

                loadReadyOrders();

            }
        );


    document.addEventListener(
        'visibilitychange',
        function () {

            if (document.visibilityState === 'visible') {

                loadReadyOrders(true);

            }

        }
    );


    loadReadyOrders();

    setInterval(
        function () {

            loadReadyOrders(true);

        },
        15000
    );


    updateCartCount();

    const refreshMenuButton =
        document.getElementById('refreshMenu');

    if (refreshMenuButton) {

        refreshMenuButton.addEventListener(
            'click',
            function () {

                loadCustomerData();

            }
        );

    }

    loadCustomerData();

</script>


<?= view('customer/menu_detail') ?>
<?= view('customer/cart') ?>
<?= view('customer/checkout') ?>
<?= view('customer/status') ?>


</body>

</html>