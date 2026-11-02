<style>
    .btn-meja-chip {
        background: #ffffff;
        border: 1px solid #fdba74;
        color: #9a3412;
        padding: 5px 12px;
        border-radius: 9999px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .btn-meja-chip:hover, .btn-meja-chip.active {
        background: #ea580c;
        color: #ffffff;
        border-color: #ea580c;
        box-shadow: 0 2px 4px rgba(234, 88, 12, 0.2);
    }
</style>

<div
    id="customerCheckoutModal"
    style="
        display:none;
        position:fixed;
        inset:0;
        z-index:1200;
        background:rgba(0,0,0,.55);
        padding:20px;
        overflow-y:auto;
    "
>

    <div
        style="
            max-width:600px;
            margin:30px auto;
            background:#fff;
            border-radius:14px;
            overflow:hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        "
    >

        <div
            style="
                display:flex;
                justify-content:space-between;
                align-items:center;
                padding:18px 20px;
                border-bottom:1px solid #eee;
            "
        >

            <h2 style="margin:0; font-size:20px; color:#111827;">
                Checkout Pesanan
            </h2>

            <button
                type="button"
                id="closeCustomerCheckout"
                style="
                    border:none;
                    background:none;
                    font-size:26px;
                    color:#666;
                    cursor:pointer;
                "
            >
                &times;
            </button>

        </div>


        <div
            id="customerCheckoutLoading"
            style="
                display:none;
                padding:40px 20px;
                text-align:center;
                color:#777;
            "
        >
            Memproses pesanan...
        </div>


        <div
            id="customerCheckoutError"
            style="
                display:none;
                margin:20px;
                padding:14px;
                border-radius:8px;
                background:#fee2e2;
                color:#991b1b;
                font-weight: 500;
            "
        >
        </div>


        <div
            id="customerCheckoutContent"
            style="padding:20px;"
        >

            <!-- 1. IDENTITAS PEMESAN & TUJUAN PENGANTARAN (SESUAI REQUEST USER) -->
            <div
                style="
                    margin-bottom:20px;
                    background:#fff7ed;
                    border:1px solid #fed7aa;
                    border-radius:10px;
                    padding:16px;
                "
            >
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
                    <span style="font-size:18px;">📍</span>
                    <h3 style="margin:0; font-size:16px; color:#9a3412; font-weight:700;">
                        Identitas Pemesan & Nomor Meja
                    </h3>
                </div>
                <p style="font-size:12px; color:#7c2d12; margin-bottom:14px;">
                    Pesanan Anda akan disiapkan di dapur. Setelah siap, silakan ambil sendiri di kasir dengan menunjukkan kode pesanan.
                </p>

                <div style="margin-bottom:12px;">
                    <label for="checkoutCustomerName" style="display:block; font-size:13px; font-weight:700; margin-bottom:5px; color:#374151;">
                        Nama Pemesan <span style="color:#b91c1c;">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="checkoutCustomerName" 
                        placeholder="Contoh: Budi / Siti / Rivaldo"
                        style="
                            width:100%;
                            padding:10px 12px;
                            border:1px solid #cbd5e1;
                            border-radius:8px;
                            font-size:14px;
                            outline:none;
                            box-sizing:border-box;
                        "
                    >
                </div>

                <div>
                    <label for="checkoutTableNumber" style="display:block; font-size:13px; font-weight:700; margin-bottom:5px; color:#374151;">
                        Nomor Meja / ID Tempat Duduk <span style="color:#b91c1c;">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="checkoutTableNumber" 
                        placeholder="Pilih cepat di bawah atau ketik Meja Anda"
                        style="
                            width:100%;
                            padding:10px 12px;
                            border:1px solid #cbd5e1;
                            border-radius:8px;
                            font-size:14px;
                            outline:none;
                            box-sizing:border-box;
                            margin-bottom:10px;
                        "
                    >
                    <div style="display:flex; flex-wrap:wrap; gap:6px;">
                        <button type="button" class="btn-meja-chip" onclick="pilihMeja('Meja 01')">Meja 01</button>
                        <button type="button" class="btn-meja-chip" onclick="pilihMeja('Meja 02')">Meja 02</button>
                        <button type="button" class="btn-meja-chip" onclick="pilihMeja('Meja 03')">Meja 03</button>
                        <button type="button" class="btn-meja-chip" onclick="pilihMeja('Meja 04')">Meja 04</button>
                        <button type="button" class="btn-meja-chip" onclick="pilihMeja('Meja 05')">Meja 05</button>
                        <button type="button" class="btn-meja-chip" onclick="pilihMeja('Meja 06')">Meja 06</button>
                        <button type="button" class="btn-meja-chip" onclick="pilihMeja('Meja 07')">Meja 07</button>
                        <button type="button" class="btn-meja-chip" onclick="pilihMeja('Meja 08')">Meja 08</button>
                        <button type="button" class="btn-meja-chip" onclick="pilihMeja('Bungkus')">🥡 Bungkus / Take Away</button>
                    </div>
                </div>
            </div>

            <!-- 2. RINGKASAN PESANAN -->
            <div
                style="
                    margin-bottom:20px;
                "
            >

                <h3
                    style="
                        margin-bottom:12px;
                        font-size:17px;
                    "
                >
                    Ringkasan Pesanan
                </h3>


                <div
                    id="checkoutItems"
                    style="
                        border:1px solid #eee;
                        border-radius:8px;
                        overflow:hidden;
                    "
                >
                </div>

            </div>


            <div
                style="
                    display:flex;
                    justify-content:space-between;
                    align-items:center;
                    padding:15px 0;
                    border-top:1px solid #eee;
                    border-bottom:1px solid #eee;
                    margin-bottom:20px;
                "
            >

                <span
                    style="
                        color:#666;
                    "
                >
                    Total Pembayaran
                </span>

                <strong
                    id="checkoutTotal"
                    style="
                        font-size:22px;
                        color:#b91c1c;
                    "
                >
                    Rp0
                </strong>

            </div>


            <!-- 3. METODE PEMBAYARAN -->
            <div
                style="
                    margin-bottom:20px;
                "
            >

                <h3
                    style="
                        margin-bottom:12px;
                        font-size:17px;
                    "
                >
                    Metode Pembayaran
                </h3>


                <div class="checkout-method-options">

                    <label
                        class="checkout-method-option active"
                        data-checkout-method="Tunai"
                    >

                        <input
                            type="radio"
                            name="checkoutMetode"
                            value="Tunai"
                            checked
                        >

                        <span>

                            <strong>
                                Tunai
                            </strong>

                            <br>

                            <small style="color:#777;">
                                Bayar langsung di kasir
                            </small>

                        </span>

                    </label>


                    <label
                        class="checkout-method-option"
                        data-checkout-method="QRIS"
                    >

                        <input
                            type="radio"
                            name="checkoutMetode"
                            value="QRIS"
                        >

                        <span>

                            <strong>
                                QRIS
                            </strong>

                            <br>

                            <small style="color:#777;">
                                Scan QRIS di halaman ini
                            </small>

                        </span>

                    </label>

                </div>


                <div
                    id="checkoutMethodInfo"
                    class="checkout-method-info"
                >
                    Silakan bayar tunai di kasir dengan
                    menunjukkan kode pesanan.
                </div>

            </div>


            <button
                type="button"
                id="submitCustomerCheckout"
                style="
                    width:100%;
                    border:none;
                    background:#b91c1c;
                    color:#fff;
                    padding:13px;
                    border-radius:8px;
                    font-weight:700;
                    font-size:16px;
                    cursor:pointer;
                "
            >
                Buat Pesanan &rarr;
            </button>

        </div>


        <!-- SUCCESS POPUP -->
        <div
            id="customerCheckoutSuccess"
            style="
                display:none;
                padding:35px 20px;
                text-align:center;
            "
        >

            <div
                style="
                    width:60px;
                    height:60px;
                    border-radius:50%;
                    background:#dcfce7;
                    color:#16a34a;
                    font-size:32px;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    margin:0 auto 16px;
                    font-weight:bold;
                "
            >
                ✓
            </div>


            <h3
                style="
                    margin-bottom:8px;
                    font-size:20px;
                    color:#111827;
                "
            >
                Pesanan Berhasil Dibuat!
            </h3>

            <!-- HIGHLIGHT TUJUAN PENGANTARAN SESUAI REQUEST USER -->
            <div
                id="checkoutSuccessDeliveryBox"
                style="
                    background:#fff7ed;
                    border:1px solid #fed7aa;
                    border-radius:10px;
                    padding:14px;
                    margin:16px 0;
                    text-align:left;
                "
            >
                <div style="font-size:11px; font-weight:800; color:#c2410c; text-transform:uppercase; letter-spacing:0.5px;">
                    🍽️ Identitas Pengambilan (Self Service)
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:6px;">
                    <div>
                        <div style="font-size:16px; font-weight:800; color:#111827;" id="checkoutSuccessTable">
                            Meja 01
                        </div>
                        <div style="font-size:13px; color:#4b5563;" id="checkoutSuccessName">
                            Pemesan: Budi
                        </div>
                    </div>
                    <span style="background:#ea580c; color:#fff; font-size:11px; font-weight:700; padding:4px 8px; border-radius:6px;">
                        AMBIL DI KASIR
                    </span>
                </div>
            </div>

            <div
                id="checkoutQueueBox"
                style="
                    background:#fef2f2;
                    border:1px solid #fecaca;
                    border-radius:12px;
                    padding:14px;
                    margin-bottom:16px;
                    text-align:center;
                "
            >
                <div style="font-size:11px; font-weight:800; color:#991b1b; text-transform:uppercase; letter-spacing:0.7px;">
                    Nomor Antrian
                </div>
                <div
                    id="checkoutQueueNumber"
                    style="
                        font-size:38px;
                        line-height:1.1;
                        font-weight:900;
                        color:#b91c1c;
                        letter-spacing:1px;
                    "
                >
                    #-
                </div>
                <div style="font-size:11px; color:#6b7280; margin-top:3px;">
                    Sesuai urutan transaksi masuk
                </div>
            </div>


            <p
                style="
                    color:#666;
                    margin-bottom:6px;
                    font-size:13px;
                "
            >
                Kode Pesanan Anda:
            </p>


            <strong
                id="checkoutOrderCode"
                style="
                    display:block;
                    font-size:22px;
                    color:#b91c1c;
                    margin-bottom:12px;
                    letter-spacing:0.5px;
                "
            >
            </strong>


            <div
                id="checkoutPaymentMethod"
                style="
                    display:inline-block;
                    padding:6px 14px;
                    border-radius:999px;
                    background:#fef2f2;
                    color:#b91c1c;
                    font-weight:700;
                    font-size:13px;
                    margin-bottom:12px;
                "
            >
            </div>


            <p
                id="checkoutPaymentInstruction"
                style="
                    color:#4b5563;
                    font-size:13px;
                    margin-bottom:12px;
                "
            >
            </p>


            <div
                id="checkoutPaymentCountdownBox"
                style="
                    display:none;
                    background:#fffbeb;
                    border:1px solid #fde68a;
                    color:#92400e;
                    border-radius:8px;
                    padding:10px 12px;
                    margin-bottom:14px;
                    font-size:13px;
                    font-weight:700;
                    text-align:center;
                "
            >
                Waktu pembayaran tersisa:
                <span id="checkoutPaymentCountdown">05:00</span>
                <div style="font-size:11px; font-weight:500; margin-top:3px;">
                    Pesanan otomatis dibatalkan jika melewati batas 5 menit.
                </div>
            </div>


            <p
                style="
                    color:#6b7280;
                    font-size:12px;
                    margin-bottom:24px;
                "
            >
                Pesanan Anda akan disiapkan di dapur. Setelah siap, Anda akan mendapat notifikasi untuk mengambil sendiri di kasir.
            </p>


            <div
                id="checkoutQrisBox"
                class="qris-demo-card"
                style="
                    display:none;
                    margin-bottom:18px;
                "
            >
                <div class="qris-demo-head">
                    <div class="qris-demo-logo">QRIS</div>
                    <div class="qris-demo-badge">CONTOH / DEMO</div>
                </div>

                <div class="qris-demo-merchant">KANTIN MERAH</div>
                <div class="qris-demo-sub">Scan QRIS untuk menyelesaikan pembayaran</div>

                <div class="qris-demo-image-wrap">
                    <img
                        src="<?= base_url('assets/qris/contoh-qris.png') ?>"
                        alt="Contoh QRIS Kantin Merah"
                    >
                </div>

                <div class="qris-demo-amount-label">Total Pembayaran</div>
                <div class="qris-demo-amount" id="checkoutQrisTotal">Rp 0</div>

                <div class="qris-demo-meta">
                    Kode Pesanan: <strong id="checkoutQrisOrderCode">-</strong>
                </div>

                <div class="qris-demo-divider"></div>

                <div class="qris-demo-status" id="checkoutQrisStatus">
                    Menunggu pembayaran
                </div>

                <div class="qris-demo-paid-note">
                    Sudah membayar? Tekan tombol di bawah ini.
                </div>

                <button
                    type="button"
                    id="checkoutQrisConfirm"
                    style="
                        width:100%;
                        border:none;
                        background:#7c3aed;
                        color:#fff;
                        padding:12px;
                        border-radius:8px;
                        font-weight:800;
                        font-size:14px;
                        cursor:pointer;
                    "
                >
                    Saya Sudah Membayar QRIS
                </button>

                <div class="qris-demo-demo-note">
                    *QRIS contoh untuk demonstrasi. Tidak ada transaksi uang nyata.
                    Setelah menekan tombol, pembayaran langsung valid dan pesanan diteruskan ke dapur.
                </div>
            </div>


            <button
                type="button"
                id="viewCustomerOrderStatus"
                style="
                    width:100%;
                    border:none;
                    background:#b91c1c;
                    color:#fff;
                    padding:13px;
                    border-radius:8px;
                    font-weight:700;
                    font-size:15px;
                    cursor:pointer;
                "
            >
                Lihat Status Pesanan Saya
            </button>

        </div>

    </div>

</div>


<script>

    const CUSTOMER_CHECKOUT_API =
        <?= json_encode(
            site_url('customer/checkout')
        ) ?>;

    const CUSTOMER_ORDER_STATUS_API =
        <?= json_encode(
            site_url('customer/pesanan')
        ) ?>;

    const CUSTOMER_CHECKOUT_STORAGE =
        'kantin_merah_cart';

    const CUSTOMER_ORDER_STORAGE =
        'kantin_merah_last_order';

    const CUSTOMER_NAME_STORAGE =
        'kantin_merah_customer_name';

    const CUSTOMER_TABLE_STORAGE =
        'kantin_merah_table_number';


    let checkoutSubmitting =
        false;


    const customerCheckoutModal =
        document.getElementById(
            'customerCheckoutModal'
        );

    const customerCheckoutLoading =
        document.getElementById(
            'customerCheckoutLoading'
        );

    const customerCheckoutError =
        document.getElementById(
            'customerCheckoutError'
        );

    const customerCheckoutContent =
        document.getElementById(
            'customerCheckoutContent'
        );

    const customerCheckoutSuccess =
        document.getElementById(
            'customerCheckoutSuccess'
        );

    const checkoutItems =
        document.getElementById(
            'checkoutItems'
        );

    const checkoutTotal =
        document.getElementById(
            'checkoutTotal'
        );

    const checkoutOrderCode =
        document.getElementById(
            'checkoutOrderCode'
        );

    const checkoutMethodInfo =
        document.getElementById(
            'checkoutMethodInfo'
        );

    const checkoutPaymentMethod =
        document.getElementById(
            'checkoutPaymentMethod'
        );

    const checkoutPaymentInstruction =
        document.getElementById(
            'checkoutPaymentInstruction'
        );

    const checkoutCustomerNameInput =
        document.getElementById(
            'checkoutCustomerName'
        );

    const checkoutTableNumberInput =
        document.getElementById(
            'checkoutTableNumber'
        );

    const checkoutSuccessTable =
        document.getElementById(
            'checkoutSuccessTable'
        );

    const checkoutSuccessName =
        document.getElementById(
            'checkoutSuccessName'
        );

    const checkoutQrisBox =
        document.getElementById(
            'checkoutQrisBox'
        );

    const checkoutQrisStatus =
        document.getElementById(
            'checkoutQrisStatus'
        );

    const checkoutQrisTotal =
        document.getElementById(
            'checkoutQrisTotal'
        );

    const checkoutQrisOrderCode =
        document.getElementById(
            'checkoutQrisOrderCode'
        );

    const checkoutQrisConfirm =
        document.getElementById(
            'checkoutQrisConfirm'
        );

    const checkoutQueueNumber =
        document.getElementById(
            'checkoutQueueNumber'
        );

    const checkoutPaymentCountdownBox =
        document.getElementById(
            'checkoutPaymentCountdownBox'
        );

    const checkoutPaymentCountdown =
        document.getElementById(
            'checkoutPaymentCountdown'
        );


    let currentQrisOrderCode =
        null;

    let qrisConfirming =
        false;

    let checkoutCountdownTimer =
        null;

    let checkoutCountdownDeadline =
        0;

    let checkoutCountdownExpired =
        false;


    function pilihMeja(meja) {
        if (checkoutTableNumberInput) {
            checkoutTableNumberInput.value = meja;
        }

        document.querySelectorAll('.btn-meja-chip').forEach(function (btn) {
            if (btn.textContent.trim() === meja || (meja === 'Bungkus' && btn.textContent.includes('Bungkus'))) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }


    function checkoutFormatRupiah(
        value
    )
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


    function checkoutFormatNoAntrian(
        value
    )
    {
        const nomor =
            parseInt(value, 10);

        if (!nomor || nomor < 1) {
            return '#-';
        }

        return '#' + String(nomor).padStart(2, '0');
    }


    function checkoutFormatCountdown(
        totalSeconds
    )
    {
        const seconds =
            Math.max(0, Math.floor(totalSeconds));

        const minutes =
            Math.floor(seconds / 60);

        const rest =
            seconds % 60;

        return String(minutes).padStart(2, '0') +
            ':' +
            String(rest).padStart(2, '0');
    }


    function stopCheckoutPaymentCountdown()
    {
        if (checkoutCountdownTimer) {
            clearInterval(checkoutCountdownTimer);
            checkoutCountdownTimer = null;
        }
    }


    function startCheckoutPaymentCountdown(
        batasWaktu
    )
    {
        stopCheckoutPaymentCountdown();

        checkoutCountdownExpired =
            false;

        let deadline =
            Date.parse(
                String(batasWaktu || '').replace(' ', 'T')
            );

        if (Number.isNaN(deadline)) {
            deadline =
                Date.now() + (5 * 60 * 1000);
        }

        checkoutCountdownDeadline =
            deadline;

        const oldMessage =
            document.getElementById(
                'checkoutPaymentCountdownMessage'
            );

        if (oldMessage) {
            oldMessage.remove();
        }

        if (checkoutPaymentCountdownBox) {
            checkoutPaymentCountdownBox.style.display =
                'block';

            checkoutPaymentCountdownBox.style.background =
                '#fffbeb';

            checkoutPaymentCountdownBox.style.borderColor =
                '#fde68a';

            checkoutPaymentCountdownBox.style.color =
                '#92400e';
        }

        if (checkoutPaymentCountdown) {
            checkoutPaymentCountdown.style.color =
                '#92400e';
        }

        const tick =
            function () {
                const remaining =
                    Math.max(
                        0,
                        Math.ceil(
                            (checkoutCountdownDeadline - Date.now()) / 1000
                        )
                    );

                if (checkoutPaymentCountdown) {
                    checkoutPaymentCountdown.textContent =
                        checkoutFormatCountdown(remaining);

                    if (remaining <= 60) {
                        checkoutPaymentCountdown.style.color =
                            '#dc2626';
                    }
                }

                if (remaining > 0) {
                    return;
                }

                stopCheckoutPaymentCountdown();

                if (checkoutCountdownExpired) {
                    return;
                }

                checkoutCountdownExpired =
                    true;

                if (checkoutPaymentCountdownBox) {
                    checkoutPaymentCountdownBox.style.background =
                        '#fef2f2';

                    checkoutPaymentCountdownBox.style.borderColor =
                        '#fecaca';

                    checkoutPaymentCountdownBox.style.color =
                        '#991b1b';

                    const message =
                        document.createElement('div');

                    message.id =
                        'checkoutPaymentCountdownMessage';

                    message.style.marginTop =
                        '4px';

                    message.textContent =
                        'Waktu pembayaran 5 menit habis — pesanan dibatalkan otomatis.';

                    checkoutPaymentCountdownBox.appendChild(
                        message
                    );
                }

                if (checkoutQrisStatus) {
                    checkoutQrisStatus.textContent =
                        '❌ Waktu pembayaran habis — pesanan dibatalkan otomatis';
                    checkoutQrisStatus.style.color =
                        '#dc2626';
                    checkoutQrisStatus.classList.remove(
                        'paid'
                    );
                }

                if (checkoutPaymentInstruction) {
                    checkoutPaymentInstruction.textContent =
                        'Waktu pembayaran 5 menit habis. Pesanan dibatalkan otomatis.';
                }

                if (checkoutQrisConfirm) {
                    checkoutQrisConfirm.disabled =
                        true;

                    checkoutQrisConfirm.style.opacity =
                        '0.6';

                    checkoutQrisConfirm.textContent =
                        'Waktu Pembayaran Habis';
                }

                if (currentQrisOrderCode) {
                    fetch(
                        `${CUSTOMER_ORDER_STATUS_API}/${encodeURIComponent(
                            currentQrisOrderCode
                        )}`,
                        {
                            method: 'GET',
                            headers: {
                                'Accept':
                                    'application/json'
                            }
                        }
                    ).catch(function () {});
                }
            };

        tick();

        if (!checkoutCountdownExpired) {
            checkoutCountdownTimer =
                setInterval(tick, 1000);
        }
    }


    function checkoutEscapeHtml(
        value
    )
    {
        const div =
            document.createElement('div');

        div.textContent =
            value ?? '';

        return div.innerHTML;
    }


    function getCheckoutCart()
    {
        try {

            const saved =
                localStorage.getItem(
                    CUSTOMER_CHECKOUT_STORAGE
                );


            if (!saved) {
                return [];
            }


            const cart =
                JSON.parse(saved);


            return Array.isArray(cart)
                ? cart
                : [];

        } catch (error) {

            return [];

        }
    }


    function showCheckoutLoading()
    {
        customerCheckoutLoading.style.display =
            'block';

        customerCheckoutContent.style.display =
            'none';

        customerCheckoutError.style.display =
            'none';

        customerCheckoutSuccess.style.display =
            'none';
    }


    function showCheckoutForm()
    {
        customerCheckoutLoading.style.display =
            'none';

        customerCheckoutContent.style.display =
            'block';

        customerCheckoutError.style.display =
            'none';

        customerCheckoutSuccess.style.display =
            'none';
    }


    function showCheckoutError(
        message
    )
    {
        customerCheckoutLoading.style.display =
            'none';

        customerCheckoutContent.style.display =
            'block';

        customerCheckoutSuccess.style.display =
            'none';

        customerCheckoutError.textContent =
            message;

        customerCheckoutError.style.display =
            'block';
    }


    function renderCheckoutItems(
        items
    )
    {
        checkoutItems.innerHTML = '';

        let total =
            0;


        items.forEach(
            function (item) {

                const harga =
                    Number(
                        item.harga || 0
                    );

                const jumlah =
                    Number(
                        item.jumlah || 0
                    );

                const subtotal =
                    harga * jumlah;


                total += subtotal;

                const element =
                    document.createElement(
                        'div'
                    );


                element.style.cssText = `
                    padding:14px;
                    border-bottom:1px solid #eee;
                `;


                element.innerHTML = `

                    <div
                        style="
                            display:flex;
                            justify-content:space-between;
                            gap:15px;
                        "
                    >

                        <div>

                            <div
                                style="
                                    font-weight:600;
                                    margin-bottom:4px;
                                "
                            >
                                ${checkoutEscapeHtml(
                                    item.nama_menu
                                )}
                            </div>

                            <div
                                style="
                                    font-size:13px;
                                    color:#777;
                                "
                            >
                                ${jumlah} x
                                ${checkoutFormatRupiah(
                                    harga
                                )}
                            </div>

                            ${
                                item.catatan_khusus
                                    ? `
                                        <div
                                            style="
                                                margin-top:6px;
                                                font-size:12px;
                                                color:#666;
                                            "
                                        >
                                            Catatan:
                                            ${checkoutEscapeHtml(
                                                item.catatan_khusus
                                            )}
                                        </div>
                                    `
                                    : ''
                            }

                        </div>


                        <strong>
                            ${checkoutFormatRupiah(
                                subtotal
                            )}
                        </strong>

                    </div>

                `;


                checkoutItems.appendChild(
                    element
                );

            }
        );


        checkoutTotal.textContent =
            checkoutFormatRupiah(total);


        return total;
    }


    async function loadCheckoutData()
    {
        const cart =
            getCheckoutCart();


        if (cart.length === 0) {

            showCheckoutError(
                'Keranjang masih kosong.'
            );

            return;

        }


        // Load saved identity if available
        const savedName = localStorage.getItem(CUSTOMER_NAME_STORAGE);
        const savedTable = localStorage.getItem(CUSTOMER_TABLE_STORAGE);

        if (checkoutCustomerNameInput && savedName && !checkoutCustomerNameInput.value) {
            checkoutCustomerNameInput.value = savedName;
        }

        if (checkoutTableNumberInput && savedTable && !checkoutTableNumberInput.value) {
            checkoutTableNumberInput.value = savedTable;
            pilihMeja(savedTable);
        }


        showCheckoutLoading();


        try {

            const menuResults =
                await Promise.all(
                    cart.map(
                        async function (item) {

                            const response =
                                await fetch(
                                    `${<?= json_encode(
                                        site_url('customer/menu')
                                    ) ?>}/${Number(
                                        item.id_menu
                                    )}`,
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
                                    'Gagal mengambil data menu.'
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
                                    'Data menu tidak tersedia.'
                                );

                            }


                            return {
                                ...item,
                                nama_menu:
                                    result.data.nama_menu,
                                harga:
                                    result.data.harga,
                                jumlah_stok:
                                    result.data.jumlah_stok
                            };

                        }
                    )
                );


            renderCheckoutItems(
                menuResults
            );


            customerCheckoutContent.dataset.items =
                JSON.stringify(
                    cart
                );


            showCheckoutForm();


        } catch (error) {

            showCheckoutError(
                error.message ||
                'Gagal memuat data checkout.'
            );

        }
    }


    function openCustomerCheckout()
    {
        customerCheckoutModal.style.display =
            'block';

        document.body.style.overflow =
            'hidden';

        checkoutSubmitting =
            false;

        loadCheckoutData();
    }


    function closeCustomerCheckout()
    {
        customerCheckoutModal.style.display =
            'none';

        document.body.style.overflow =
            '';

        checkoutSubmitting =
            false;

        stopCheckoutPaymentCountdown();

        if (checkoutPaymentCountdownBox) {
            checkoutPaymentCountdownBox.style.display =
                'none';
        }
    }


    async function submitCustomerCheckout()
    {
        if (checkoutSubmitting) {
            return;
        }


        const cart =
            getCheckoutCart();


        if (cart.length === 0) {

            showCheckoutError(
                'Keranjang masih kosong.'
            );

            return;

        }

        const namaCustomer = checkoutCustomerNameInput ? checkoutCustomerNameInput.value.trim() : '';
        const nomorMeja = checkoutTableNumberInput ? checkoutTableNumberInput.value.trim() : '';

        if (!namaCustomer) {
            showCheckoutError('Harap isi Nama Pemesan agar kasir mengetahui pesanan Anda.');
            if (checkoutCustomerNameInput) {
                checkoutCustomerNameInput.focus();
            }
            return;
        }

        if (!nomorMeja) {
            showCheckoutError('Harap pilih atau isi Nomor Meja / ID Lokasi pengantaran.');
            if (checkoutTableNumberInput) {
                checkoutTableNumberInput.focus();
            }
            return;
        }


        checkoutSubmitting =
            true;


        const submitButton =
            document.getElementById(
                'submitCustomerCheckout'
            );


        submitButton.disabled =
            true;

        submitButton.style.opacity =
            '0.6';


        showCheckoutLoading();


        try {

            const selectedMetode =
                document.querySelector(
                    'input[name="checkoutMetode"]:checked'
                )?.value || 'Tunai';

            const response =
                await fetch(
                    CUSTOMER_CHECKOUT_API,
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type':
                                'application/json',
                            'Accept':
                                'application/json'
                        },
                        body: JSON.stringify({
                            items: cart,
                            nama_customer: namaCustomer,
                            nomor_meja: nomorMeja,
                            id_customer: nomorMeja + ' - ' + namaCustomer,
                            metode: selectedMetode
                        })
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
                    'Checkout gagal.'
                );

            }


            // Simpan nama dan meja ke localStorage untuk kunjungan berikutnya
            localStorage.setItem(CUSTOMER_NAME_STORAGE, namaCustomer);
            localStorage.setItem(CUSTOMER_TABLE_STORAGE, nomorMeja);

            const orderData = {
                id_pesanan:
                    result.id_pesanan,

                kode_pesanan:
                    result.kode_pesanan,

                nama_customer:
                    result.nama_customer || namaCustomer,

                nomor_meja:
                    result.nomor_meja || nomorMeja,

                id_customer:
                    result.id_customer || (nomorMeja + ' - ' + namaCustomer),

                total_bayar:
                    result.total_bayar,

                status:
                    result.status || 'Menunggu Pembayaran',

                metode_bayar:
                    result.metode_bayar || selectedMetode,

                no_antrian:
                    result.no_antrian || null,

                batas_waktu:
                    result.batas_waktu || null
            };


            localStorage.setItem(
                CUSTOMER_ORDER_STORAGE,
                JSON.stringify(
                    orderData
                )
            );


            localStorage.removeItem(
                CUSTOMER_CHECKOUT_STORAGE
            );


            checkoutOrderCode.textContent =
                result.kode_pesanan;

            if (checkoutSuccessTable) {
                checkoutSuccessTable.textContent = result.nomor_meja || nomorMeja;
            }

            if (checkoutSuccessName) {
                checkoutSuccessName.textContent = 'Pemesan: ' + (result.nama_customer || namaCustomer);
            }


            const metodeBayar =
                result.metode_bayar || selectedMetode;

            checkoutPaymentMethod.textContent =
                metodeBayar === 'QRIS'
                    ? 'Pembayaran: QRIS'
                    : 'Pembayaran: Tunai';

            if (metodeBayar === 'QRIS') {
                checkoutPaymentInstruction.textContent =
                    'Silakan scan QRIS yang tampil di halaman ini, lalu tekan tombol Saya Sudah Membayar.';
            } else {
                checkoutPaymentInstruction.textContent =
                    'Silakan bayar tunai di kasir dengan menunjukkan kode pesanan.';
            }

            currentQrisOrderCode =
                result.kode_pesanan || null;

            qrisConfirming = false;

            if (checkoutQrisBox) {
                checkoutQrisBox.style.display =
                    metodeBayar === 'QRIS' ? 'block' : 'none';
            }

            if (checkoutQrisTotal) {
                checkoutQrisTotal.textContent =
                    checkoutFormatRupiah(
                        result.total_bayar || 0
                    );
            }

            if (checkoutQrisOrderCode) {
                checkoutQrisOrderCode.textContent =
                    result.kode_pesanan || '-';
            }

            if (checkoutQrisStatus) {
                checkoutQrisStatus.textContent =
                    'Menunggu pembayaran';
                checkoutQrisStatus.style.color = '#b45309';
                checkoutQrisStatus.classList.remove('paid');
            }

            if (checkoutQrisConfirm) {
                checkoutQrisConfirm.disabled = false;
                checkoutQrisConfirm.style.opacity = '1';
                checkoutQrisConfirm.textContent =
                    'Saya Sudah Membayar QRIS';
            }

            if (checkoutQueueNumber) {
                checkoutQueueNumber.textContent =
                    checkoutFormatNoAntrian(
                        result.no_antrian
                    );
            }

            startCheckoutPaymentCountdown(
                result.batas_waktu || null
            );


            customerCheckoutLoading.style.display =
                'none';

            customerCheckoutContent.style.display =
                'none';

            customerCheckoutError.style.display =
                'none';

            customerCheckoutSuccess.style.display =
                'block';


            if (
                typeof window.updateCartCount ===
                'function'
            ) {

                window.updateCartCount();

            } else {

                const cartCount =
                    document.getElementById(
                        'cartCount'
                    );

                if (cartCount) {
                    cartCount.textContent =
                        '0';
                }

            }


        } catch (error) {

            showCheckoutError(
                error.message ||
                'Terjadi kesalahan saat checkout.'
            );


        } finally {

            checkoutSubmitting =
                false;

            submitButton.disabled =
                false;

            submitButton.style.opacity =
                '1';

        }
    }


    document
        .getElementById(
            'closeCustomerCheckout'
        )
        .addEventListener(
            'click',
            closeCustomerCheckout
        );


    customerCheckoutModal.addEventListener(
        'click',
        function (event) {

            if (
                event.target ===
                customerCheckoutModal
            ) {

                closeCustomerCheckout();

            }

        }
    );


    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Escape' &&
                customerCheckoutModal.style.display ===
                    'block'
            ) {

                closeCustomerCheckout();

            }

        }
    );


    document
        .getElementById(
            'submitCustomerCheckout'
        )
        .addEventListener(
            'click',
            submitCustomerCheckout
        );


    document
        .getElementById(
            'viewCustomerOrderStatus'
        )
        .addEventListener(
            'click',
            function () {

                const savedOrder =
                    localStorage.getItem(
                        CUSTOMER_ORDER_STORAGE
                    );


                if (
                    typeof window.openCustomerStatus ===
                    'function'
                ) {

                    closeCustomerCheckout();

                    window.openCustomerStatus(
                        savedOrder
                            ? JSON.parse(
                                savedOrder
                            ).kode_pesanan
                            : null
                    );

                } else {

                    alert(
                        'Halaman status pesanan belum tersedia.'
                    );

                }

            }
        );


    async function konfirmasiQrisCustomer()
    {
        if (qrisConfirming || !currentQrisOrderCode) {
            return;
        }

        qrisConfirming = true;

        if (checkoutQrisConfirm) {
            checkoutQrisConfirm.disabled = true;
            checkoutQrisConfirm.style.opacity = '0.6';
            checkoutQrisConfirm.textContent = 'Memproses...';
        }

        try {

            const response = await fetch(
                `${CUSTOMER_ORDER_STATUS_API}/${encodeURIComponent(currentQrisOrderCode)}/bayar-qris`,
                {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json'
                    }
                }
            );

            const result = await response.json();

            if (!response.ok || !result.status) {
                throw new Error(
                    result.message ||
                    'Konfirmasi pembayaran QRIS gagal.'
                );
            }

            if (checkoutQrisStatus) {
                checkoutQrisStatus.textContent =
                    '✅ Pembayaran berhasil — pesanan diteruskan ke dapur';
                checkoutQrisStatus.style.color = '#15803d';
                checkoutQrisStatus.classList.add('paid');
            }

            if (checkoutPaymentInstruction) {
                checkoutPaymentInstruction.textContent =
                    'Pembayaran QRIS berhasil. Pesanan sedang diproses di dapur.';
            }

            stopCheckoutPaymentCountdown();

            if (checkoutPaymentCountdownBox) {
                checkoutPaymentCountdownBox.style.display =
                    'none';
            }

            if (checkoutQrisConfirm) {
                checkoutQrisConfirm.textContent =
                    'Pembayaran Berhasil ✓';
                checkoutQrisConfirm.disabled = true;
                checkoutQrisConfirm.style.opacity = '0.75';
            }

            // Simpan status terbaru agar halaman status langsung
            // menampilkan pesanan sebagai Dibayar.
            try {
                const savedOrder =
                    localStorage.getItem(CUSTOMER_ORDER_STORAGE);

                if (savedOrder) {
                    const saved = JSON.parse(savedOrder);
                    saved.status =
                        result.data && result.data.status_pesanan
                            ? result.data.status_pesanan
                            : 'Dibayar';

                    localStorage.setItem(
                        CUSTOMER_ORDER_STORAGE,
                        JSON.stringify(saved)
                    );
                }
            } catch (e) {
                // Abaikan jika data localStorage tidak valid.
            }

        } catch (error) {

            alert(
                error.message ||
                'Konfirmasi pembayaran QRIS gagal.'
            );

            if (checkoutQrisConfirm) {
                checkoutQrisConfirm.disabled = false;
                checkoutQrisConfirm.style.opacity = '1';
                checkoutQrisConfirm.textContent =
                    'Saya Sudah Membayar QRIS';
            }

        } finally {

            qrisConfirming = false;

        }
    }


    window.openCustomerCheckout =
        openCustomerCheckout;


    window.closeCustomerCheckout =
        closeCustomerCheckout;


    if (checkoutQrisConfirm) {
        checkoutQrisConfirm.addEventListener(
            'click',
            konfirmasiQrisCustomer
        );
    }


    document.querySelectorAll(
        '.checkout-method-option'
    ).forEach(function (label) {

        label.addEventListener(
            'click',
            function () {

                document.querySelectorAll(
                    '.checkout-method-option'
                ).forEach(function (opt) {
                    opt.classList.remove('active');
                });

                label.classList.add('active');

                const radio =
                    label.querySelector(
                        'input[type="radio"]'
                    );

                if (radio) {
                    radio.checked = true;
                }

                const metode =
                    label.dataset.checkoutMethod;

                if (metode === 'QRIS') {
                    checkoutMethodInfo.textContent =
                        'Silakan scan QRIS yang tampil di halaman ini, lalu tekan tombol Saya Sudah Membayar.';
                } else {
                    checkoutMethodInfo.textContent =
                        'Silakan bayar tunai di kasir dengan menunjukkan kode pesanan.';
                }

            }
        );

    });

</script>
