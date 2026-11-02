<div
    id="customerStatusModal"
    style="
        display:none;
        position:fixed;
        inset:0;
        z-index:1300;
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
                Status Pesanan
            </h2>

            <button
                type="button"
                id="closeCustomerStatus"
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
            id="customerStatusLoading"
            style="
                display:none;
                padding:40px 20px;
                text-align:center;
                color:#777;
            "
        >
            Memuat status pesanan...
        </div>


        <div
            id="customerStatusError"
            style="
                display:none;
                margin:20px;
                padding:14px;
                border-radius:8px;
                background:#fee2e2;
                color:#991b1b;
            "
        >
        </div>


        <div
            id="customerStatusEmpty"
            style="
                display:none;
                padding:50px 20px;
                text-align:center;
                color:#777;
            "
        >
            Tidak ada pesanan yang dapat ditampilkan.
        </div>


        <div
            id="customerStatusContent"
            style="
                display:none;
                padding:20px;
            "
        >

            <!-- 1. IDENTITAS PENGAMBILAN (SELF SERVICE) -->
            <div
                style="
                    background:#fff7ed;
                    border:1px solid #fed7aa;
                    border-radius:10px;
                    padding:14px 16px;
                    margin-bottom:16px;
                "
            >
                <div style="font-size:11px; font-weight:800; color:#c2410c; text-transform:uppercase; letter-spacing:0.5px;">
                    🍽️ Identitas Pengambilan (Self Service)
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:4px;">
                    <div>
                        <strong id="statusOrderTable" style="font-size:17px; color:#111827;">
                            Meja -
                        </strong>
                        <div id="statusOrderCustomer" style="font-size:13px; color:#4b5563; margin-top:2px;">
                            Pemesan: Pelanggan
                        </div>
                    </div>
                    <span style="background:#ea580c; color:#fff; font-size:11px; font-weight:700; padding:4px 8px; border-radius:6px;">
                        AMBIL DI KASIR
                    </span>
                </div>
            </div>

            <!-- BANNER NOTIFIKASI MAKANAN SIAP -->
            <div
                id="statusReadyNotice"
                style="
                    display:none;
                    background:#dcfce7;
                    border:1px solid #86efac;
                    border-radius:10px;
                    padding:14px 16px;
                    margin-bottom:16px;
                "
            >
                <div style="font-size:15px; font-weight:800; color:#166534; margin-bottom:4px;">
                    🔔 Makanan Siap Diambil!
                </div>
                <div id="statusReadyMessage" style="font-size:13px; color:#166534;">
                    Silakan ambil makanan Anda di kasir.
                </div>
            </div>

            <!-- 2. KODE PESANAN -->
            <div
                style="
                    background:#f7f7f7;
                    border-radius:10px;
                    padding:16px;
                    margin-bottom:16px;
                "
            >

                <div
                    style="
                        font-size:13px;
                        color:#777;
                        margin-bottom:5px;
                    "
                >
                    Kode Pesanan
                </div>

                <strong
                    id="statusOrderCode"
                    style="
                        display:block;
                        font-size:20px;
                        color:#b91c1c;
                        letter-spacing:0.5px;
                    "
                >
                </strong>

                <div
                    style="
                        margin-top:8px;
                        padding-top:8px;
                        border-top:1px dashed #e5e7eb;
                        display:flex;
                        justify-content:space-between;
                        align-items:center;
                        gap:12px;
                    "
                >
                    <span style="font-size:13px; color:#777;">
                        Nomor Antrian
                    </span>
                    <strong
                        id="statusOrderQueue"
                        style="
                            font-size:20px;
                            color:#991b1b;
                        "
                    >
                        #-
                    </strong>
                </div>

            </div>


            <!-- 3. STATUS PESANAN -->
            <div
                style="
                    border:1px solid #eee;
                    border-radius:10px;
                    padding:16px;
                    margin-bottom:16px;
                "
            >

                <div
                    style="
                        font-size:13px;
                        color:#777;
                        margin-bottom:6px;
                    "
                >
                    Status Pesanan
                </div>

                <strong
                    id="statusOrderStatus"
                    style="
                        font-size:20px;
                    "
                >
                </strong>

            </div>


            <!-- 3B. METODE & STATUS PEMBAYARAN -->
            <div
                style="
                    border:1px solid #eee;
                    border-radius:10px;
                    padding:16px;
                    margin-bottom:16px;
                "
            >

                <div
                    style="
                        display:flex;
                        justify-content:space-between;
                        gap:12px;
                        margin-bottom:8px;
                    "
                >
                    <span style="font-size:13px; color:#777;">
                        Metode Pembayaran
                    </span>
                    <strong
                        id="statusPaymentMethod"
                        style="font-size:14px;"
                    >
                        -
                    </strong>
                </div>

                <div
                    style="
                        display:flex;
                        justify-content:space-between;
                        gap:12px;
                    "
                >
                    <span style="font-size:13px; color:#777;">
                        Status Pembayaran
                    </span>
                    <strong
                        id="statusPaymentState"
                        style="font-size:14px;"
                    >
                        -
                    </strong>
                </div>

                <div
                    id="statusPaymentCountdownRow"
                    style="
                        display:none;
                        justify-content:space-between;
                        gap:12px;
                        margin-top:10px;
                        padding-top:10px;
                        border-top:1px dashed #e5e7eb;
                    "
                >
                    <span style="font-size:13px; color:#777;">
                        Sisa Waktu Pembayaran
                    </span>
                    <strong
                        id="statusPaymentCountdown"
                        style="font-size:14px; color:#b45309;"
                    >
                        05:00
                    </strong>
                </div>

            </div>


            <!-- 3C. QRIS CONTOH / DEMO -->
            <div
                id="statusQrisBox"
                class="qris-demo-card"
                style="
                    display:none;
                    margin-bottom:16px;
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
                <div class="qris-demo-amount" id="statusQrisTotal">Rp 0</div>

                <div class="qris-demo-meta">
                    Kode Pesanan: <strong id="statusQrisOrderCode">-</strong>
                </div>

                <div class="qris-demo-divider"></div>

                <div class="qris-demo-status" id="statusQrisStatus">
                    Menunggu pembayaran
                </div>

                <div class="qris-demo-paid-note">
                    Sudah membayar? Tekan tombol di bawah ini.
                </div>

                <div class="qris-demo-demo-note">
                    *QRIS contoh untuk demonstrasi. Tidak ada transaksi uang nyata.
                    Setelah menekan tombol, pembayaran langsung valid dan pesanan diteruskan ke dapur.
                </div>
            </div>


            <button
                type="button"
                id="statusQrisConfirm"
                style="
                    display:none;
                    width:100%;
                    border:none;
                    background:#7c3aed;
                    color:#fff;
                    padding:12px;
                    border-radius:8px;
                    font-weight:700;
                    font-size:14px;
                    cursor:pointer;
                    margin-bottom:16px;
                "
            >
                Saya Sudah Membayar QRIS
            </button>


            <button
                type="button"
                id="statusAmbilButton"
                style="
                    display:none;
                    width:100%;
                    border:none;
                    background:#16a34a;
                    color:#fff;
                    padding:13px;
                    border-radius:8px;
                    font-weight:800;
                    font-size:15px;
                    cursor:pointer;
                    margin-bottom:16px;
                "
            >
                ✅ Saya Sudah Ambil Makanan
            </button>


            <!-- 4. DETAIL PESANAN -->
            <div
                style="
                    margin-bottom:20px;
                "
            >

                <h3
                    style="
                        font-size:17px;
                        margin-bottom:12px;
                    "
                >
                    Detail Pesanan
                </h3>


                <div
                    id="statusOrderDetails"
                    style="
                        border:1px solid #eee;
                        border-radius:10px;
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
                    padding-top:16px;
                    border-top:1px solid #eee;
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
                    id="statusOrderTotal"
                    style="
                        font-size:21px;
                        color:#b91c1c;
                    "
                >
                    Rp0
                </strong>

            </div>


            <button
                type="button"
                id="refreshCustomerStatus"
                style="
                    width:100%;
                    border:1px solid #b91c1c;
                    background:#fff;
                    color:#b91c1c;
                    padding:12px;
                    border-radius:8px;
                    font-weight:700;
                    cursor:pointer;
                    transition:0.15s;
                "
            >
                🔄 Perbarui Status
            </button>

        </div>

    </div>

</div>


<script>

    const CUSTOMER_STATUS_API =
        <?= json_encode(
            site_url('customer/pesanan')
        ) ?>;

    const CUSTOMER_STATUS_STORAGE =
        'kantin_merah_last_order';


    let currentCustomerOrderCode =
        null;


    const customerStatusModal =
        document.getElementById(
            'customerStatusModal'
        );

    const customerStatusLoading =
        document.getElementById(
            'customerStatusLoading'
        );

    const customerStatusError =
        document.getElementById(
            'customerStatusError'
        );

    const customerStatusEmpty =
        document.getElementById(
            'customerStatusEmpty'
        );

    const customerStatusContent =
        document.getElementById(
            'customerStatusContent'
        );

    const statusOrderCode =
        document.getElementById(
            'statusOrderCode'
        );

    const statusOrderStatus =
        document.getElementById(
            'statusOrderStatus'
        );

    const statusOrderDetails =
        document.getElementById(
            'statusOrderDetails'
        );

    const statusOrderTotal =
        document.getElementById(
            'statusOrderTotal'
        );

    const statusOrderTable =
        document.getElementById(
            'statusOrderTable'
        );

    const statusOrderCustomer =
        document.getElementById(
            'statusOrderCustomer'
        );

    const statusPaymentMethod =
        document.getElementById(
            'statusPaymentMethod'
        );

    const statusPaymentState =
        document.getElementById(
            'statusPaymentState'
        );

    const statusQrisConfirm =
        document.getElementById(
            'statusQrisConfirm'
        );

    const statusQrisBox =
        document.getElementById(
            'statusQrisBox'
        );

    const statusQrisTotal =
        document.getElementById(
            'statusQrisTotal'
        );

    const statusQrisOrderCode =
        document.getElementById(
            'statusQrisOrderCode'
        );

    const statusQrisStatus =
        document.getElementById(
            'statusQrisStatus'
        );

    const statusReadyNotice =
        document.getElementById(
            'statusReadyNotice'
        );

    const statusReadyMessage =
        document.getElementById(
            'statusReadyMessage'
        );

    const statusAmbilButton =
        document.getElementById(
            'statusAmbilButton'
        );

    const statusOrderQueue =
        document.getElementById(
            'statusOrderQueue'
        );

    const statusPaymentCountdownRow =
        document.getElementById(
            'statusPaymentCountdownRow'
        );

    const statusPaymentCountdown =
        document.getElementById(
            'statusPaymentCountdown'
        );


    let statusQrisConfirming =
        false;

    let statusAmbilProcessing =
        false;

    let statusPollTimer =
        null;

    let statusCountdownTimer =
        null;

    let statusCountdownDeadline =
        0;

    let statusCountdownExpired =
        false;


    function statusFormatRupiah(
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


    function statusFormatNoAntrian(
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


    function statusFormatCountdown(
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


    function stopStatusPaymentCountdown()
    {
        if (statusCountdownTimer) {
            clearInterval(statusCountdownTimer);
            statusCountdownTimer = null;
        }
    }


    function startStatusPaymentCountdown(
        batasWaktu
    )
    {
        let deadline =
            Date.parse(
                String(batasWaktu || '').replace(' ', 'T')
            );

        if (Number.isNaN(deadline)) {
            return;
        }

        if (
            statusCountdownTimer &&
            statusCountdownDeadline === deadline
        ) {
            return;
        }

        stopStatusPaymentCountdown();

        statusCountdownDeadline =
            deadline;

        statusCountdownExpired =
            false;

        if (statusPaymentCountdownRow) {
            statusPaymentCountdownRow.style.display =
                'flex';
        }

        if (statusPaymentCountdown) {
            statusPaymentCountdown.style.color =
                '#b45309';
        }

        const tick =
            function () {
                const remaining =
                    Math.max(
                        0,
                        Math.ceil(
                            (statusCountdownDeadline - Date.now()) / 1000
                        )
                    );

                if (statusPaymentCountdown) {
                    statusPaymentCountdown.textContent =
                        statusFormatCountdown(remaining);

                    if (remaining <= 60) {
                        statusPaymentCountdown.style.color =
                            '#dc2626';
                    }
                }

                if (remaining > 0) {
                    return;
                }

                stopStatusPaymentCountdown();

                if (statusCountdownExpired) {
                    return;
                }

                statusCountdownExpired =
                    true;

                if (statusPaymentCountdown) {
                    statusPaymentCountdown.textContent =
                        '00:00';
                    statusPaymentCountdown.style.color =
                        '#dc2626';
                }

                if (statusQrisConfirm) {
                    statusQrisConfirm.disabled =
                        true;

                    statusQrisConfirm.style.opacity =
                        '0.6';

                    statusQrisConfirm.textContent =
                        'Waktu Pembayaran Habis';
                }

                if (statusQrisStatus) {
                    statusQrisStatus.textContent =
                        '❌ Waktu pembayaran habis — pesanan dibatalkan otomatis';
                    statusQrisStatus.style.color =
                        '#dc2626';
                    statusQrisStatus.classList.remove(
                        'paid'
                    );
                }

                loadCustomerStatus(
                    currentCustomerOrderCode,
                    true
                );
            };

        tick();

        if (!statusCountdownExpired) {
            statusCountdownTimer =
                setInterval(tick, 1000);
        }
    }


    function statusEscapeHtml(
        value
    )
    {
        const div =
            document.createElement('div');

        div.textContent =
            value ?? '';

        return div.innerHTML;
    }


    function showStatusLoading()
    {
        customerStatusLoading.style.display =
            'block';

        customerStatusError.style.display =
            'none';

        customerStatusEmpty.style.display =
            'none';

        customerStatusContent.style.display =
            'none';
    }


    function showStatusError(
        message
    )
    {
        customerStatusLoading.style.display =
            'none';

        customerStatusEmpty.style.display =
            'none';

        customerStatusContent.style.display =
            'none';

        customerStatusError.textContent =
            message;

        customerStatusError.style.display =
            'block';
    }


    function showStatusEmpty()
    {
        customerStatusLoading.style.display =
            'none';

        customerStatusError.style.display =
            'none';

        customerStatusContent.style.display =
            'none';

        customerStatusEmpty.style.display =
            'block';
    }


    function getSavedOrderCode()
    {
        try {

            const saved =
                localStorage.getItem(
                    CUSTOMER_STATUS_STORAGE
                );


            if (!saved) {
                return null;
            }


            const order =
                JSON.parse(saved);


            if (
                !order ||
                !order.kode_pesanan
            ) {

                return null;

            }


            return order.kode_pesanan;

        } catch (error) {

            return null;

        }
    }


    function renderCustomerStatus(
        result
    )
    {
        const pesanan =
            result.pesanan;

        const detail =
            result.detail || [];


        statusOrderCode.textContent =
            pesanan.kode_pesanan || '-';

        if (statusOrderQueue) {
            statusOrderQueue.textContent =
                statusFormatNoAntrian(
                    pesanan.no_antrian
                );
        }


        const pembayaran =
            result.pembayaran || null;

        const metodeBayar =
            pesanan.metode_bayar ||
            (pembayaran ? pembayaran.metode : null) ||
            'Tunai';

        const statusBayar =
            pembayaran
                ? pembayaran.status_bayar
                : '-';

        statusOrderStatus.textContent =
            pesanan.status === 'Kadaluarsa'
                ? 'Kadaluarsa — Dibatalkan Otomatis'
                : (pesanan.status || '-');

        if (statusPaymentMethod) {
            statusPaymentMethod.textContent = metodeBayar;
        }

        if (statusPaymentState) {

            let labelStatus = statusBayar || '-';
            let warnaStatus = '#111827';

            if (statusBayar === 'Berhasil') {
                labelStatus = 'Berhasil';
                warnaStatus = '#15803d';
            } else if (statusBayar === 'Menunggu') {
                labelStatus = 'Belum dibayar';
                warnaStatus = '#b45309';
            } else if (
                statusBayar === 'Kadaluarsa' ||
                statusBayar === 'Gagal'
            ) {
                warnaStatus = '#b91c1c';
            }

            statusPaymentState.textContent = labelStatus;
            statusPaymentState.style.color = warnaStatus;
        }

        const tampilkanCountdown =
            pesanan.status === 'Menunggu Pembayaran' &&
            statusBayar === 'Menunggu' &&
            pembayaran &&
            pembayaran.batas_waktu;

        if (tampilkanCountdown) {
            if (statusPaymentCountdownRow) {
                statusPaymentCountdownRow.style.display =
                    'flex';
            }

            startStatusPaymentCountdown(
                pembayaran.batas_waktu
            );
        } else {
            stopStatusPaymentCountdown();

            if (statusPaymentCountdownRow) {
                statusPaymentCountdownRow.style.display =
                    'none';
            }
        }

        const pembayaranQrisDiterima =
            statusBayar === 'Berhasil';

        const bisaKonfirmasiQris =
            metodeBayar === 'QRIS' &&
            pesanan.status === 'Menunggu Pembayaran' &&
            statusBayar !== 'Gagal' &&
            statusBayar !== 'Kadaluarsa';

        if (statusQrisConfirm) {
            statusQrisConfirm.style.display =
                bisaKonfirmasiQris ? 'block' : 'none';

            statusQrisConfirm.disabled = !bisaKonfirmasiQris;
            statusQrisConfirm.style.opacity =
                bisaKonfirmasiQris ? '1' : '0.75';

            statusQrisConfirm.textContent = pembayaranQrisDiterima
                ? 'Pembayaran Sudah Diterima — Teruskan ke Dapur'
                : 'Saya Sudah Membayar QRIS';
        }

        if (statusQrisBox) {

            const tampilkanQris =
                metodeBayar === 'QRIS' &&
                pesanan.status === 'Menunggu Pembayaran' &&
                statusBayar !== 'Gagal' &&
                statusBayar !== 'Kadaluarsa';

            statusQrisBox.style.display =
                tampilkanQris ? 'block' : 'none';

            if (tampilkanQris) {

                if (statusQrisTotal) {
                    statusQrisTotal.textContent =
                        'Rp ' +
                        Number(
                            pesanan.total_bayar || 0
                        ).toLocaleString('id-ID');
                }

                if (statusQrisOrderCode) {
                    statusQrisOrderCode.textContent =
                        pesanan.kode_pesanan || '-';
                }

                if (statusQrisStatus) {
                    const sudahBayar =
                        statusBayar === 'Berhasil';

                    statusQrisStatus.textContent = sudahBayar
                        ? '✅ Pembayaran diterima — klik tombol untuk meneruskan ke dapur'
                        : 'Menunggu pembayaran';

                    statusQrisStatus.classList.toggle(
                        'paid',
                        sudahBayar
                    );
                }
            }
        }

        const notifikasi =
            result.notifikasi || null;

        if (statusReadyNotice) {

            const siapDiambil =
                pesanan.status === 'Siap Diambil';

            statusReadyNotice.style.display =
                siapDiambil ? 'block' : 'none';

            if (siapDiambil && statusReadyMessage) {

                const pesanNotif =
                    notifikasi && notifikasi.pesan
                        ? notifikasi.pesan
                        : 'Makanan Anda sudah siap. Silakan ambil makanan Anda di kasir.';

                statusReadyMessage.textContent =
                    pesanNotif;
            }
        }

        if (statusAmbilButton) {

            const bisaAmbil =
                pesanan.status === 'Siap Diambil';

            statusAmbilButton.style.display =
                bisaAmbil ? 'block' : 'none';

            if (bisaAmbil) {
                statusAmbilButton.disabled =
                    statusAmbilProcessing;
                statusAmbilButton.style.opacity =
                    statusAmbilProcessing ? '0.6' : '1';
                statusAmbilButton.textContent =
                    '✅ Saya Sudah Ambil Makanan';
            }
        }

        if (
            ['Selesai', 'Dibatalkan', 'Kadaluarsa'].includes(
                pesanan.status
            )
        ) {
            stopStatusPolling();
        }

        // Tampilkan info meja & pemesan
        if (statusOrderTable) {
            statusOrderTable.textContent = pesanan.nomor_meja || 'Meja -';
        }
        if (statusOrderCustomer) {
            statusOrderCustomer.textContent = 'Pemesan: ' + (pesanan.nama_customer || 'Pelanggan');
        }


        statusOrderTotal.textContent =
            statusFormatRupiah(
                Number(
                    pesanan.total_bayar || 0
                )
            );


        statusOrderDetails.innerHTML =
            '';


        if (detail.length === 0) {

            statusOrderDetails.innerHTML = `
                <div
                    style="
                        padding:18px;
                        text-align:center;
                        color:#777;
                    "
                >
                    Tidak ada detail pesanan.
                </div>
            `;

        } else {

            detail.forEach(
                function (item) {

                    const jumlah =
                        Number(
                            item.jumlah || 0
                        );


                    const harga =
                        Number(
                            item.harga_satuan ||
                            item.harga ||
                            0
                        );


                    const subtotal =
                        Number(
                            item.subtotal ||
                            harga * jumlah
                        );


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
                                    ${statusEscapeHtml(
                                        item.nama_menu ||
                                        'Menu'
                                    )}
                                </div>

                                <div
                                    style="
                                        font-size:13px;
                                        color:#777;
                                    "
                                >
                                    ${jumlah} x
                                    ${statusFormatRupiah(
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
                                                ${statusEscapeHtml(
                                                    item.catatan_khusus
                                                )}
                                            </div>
                                        `
                                        : ''
                                }

                            </div>


                            <strong>
                                ${statusFormatRupiah(
                                    subtotal
                                )}
                            </strong>

                        </div>

                    `;


                    statusOrderDetails.appendChild(
                        element
                    );

                }
            );

        }


        customerStatusLoading.style.display =
            'none';

        customerStatusError.style.display =
            'none';

        customerStatusEmpty.style.display =
            'none';

        customerStatusContent.style.display =
            'block';
    }


    async function loadCustomerStatus(
        kodePesanan = null,
        silent = false
    )
    {
        const kode =
            kodePesanan ||
            currentCustomerOrderCode ||
            getSavedOrderCode();


        if (!kode) {

            showStatusEmpty();

            return;

        }


        currentCustomerOrderCode =
            kode;


        if (!silent) {
            showStatusLoading();
        }


        try {

            const response =
                await fetch(
                    `${CUSTOMER_STATUS_API}/${encodeURIComponent(
                        kode
                    )}`,
                    {
                        method: 'GET',
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
                !result.status ||
                !result.data
            ) {

                throw new Error(
                    result.message ||
                    'Pesanan tidak ditemukan.'
                );

            }


            renderCustomerStatus(
                result.data
            );


        } catch (error) {

            if (!silent) {
                showStatusError(
                    error.message ||
                    'Gagal mengambil status pesanan.'
                );
            }

        }
    }


    function startStatusPolling()
    {
        stopStatusPolling();

        statusPollTimer = setInterval(
            function () {

                if (
                    customerStatusModal.style.display ===
                        'block' &&
                    currentCustomerOrderCode
                ) {
                    loadCustomerStatus(
                        currentCustomerOrderCode,
                        true
                    );
                }

            },
            15000
        );
    }


    function stopStatusPolling()
    {
        if (statusPollTimer) {
            clearInterval(statusPollTimer);
            statusPollTimer = null;
        }
    }


    function openCustomerStatus(
        kodePesanan = null
    )
    {
        customerStatusModal.style.display =
            'block';

        document.body.style.overflow =
            'hidden';

        // Minta izin notifikasi browser sekali saja
        // (dipicu oleh klik pengguna).
        if (
            'Notification' in window &&
            Notification.permission === 'default'
        ) {
            Notification
                .requestPermission()
                .catch(function () {});
        }


        currentCustomerOrderCode =
            kodePesanan ||
            getSavedOrderCode();


        loadCustomerStatus(
            currentCustomerOrderCode
        );

        startStatusPolling();
    }


    function closeCustomerStatus()
    {
        stopStatusPolling();

        stopStatusPaymentCountdown();

        if (statusPaymentCountdownRow) {
            statusPaymentCountdownRow.style.display =
                'none';
        }

        customerStatusModal.style.display =
            'none';

        document.body.style.overflow =
            '';
    }


    document
        .getElementById(
            'closeCustomerStatus'
        )
        .addEventListener(
            'click',
            closeCustomerStatus
        );


    customerStatusModal.addEventListener(
        'click',
        function (event) {

            if (
                event.target ===
                customerStatusModal
            ) {

                closeCustomerStatus();

            }

        }
    );


    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Escape' &&
                customerStatusModal.style.display ===
                    'block'
            ) {

                closeCustomerStatus();

            }

        }
    );


    document
        .getElementById(
            'refreshCustomerStatus'
        )
        .addEventListener(
            'click',
            function () {

                loadCustomerStatus(
                    currentCustomerOrderCode
                );

            }
        );


    async function konfirmasiQrisStatus()
    {
        if (
            statusQrisConfirming ||
            !currentCustomerOrderCode
        ) {
            return;
        }

        statusQrisConfirming = true;

        if (statusQrisConfirm) {
            statusQrisConfirm.disabled = true;
            statusQrisConfirm.style.opacity = '0.6';
            statusQrisConfirm.textContent = 'Memproses...';
        }

        try {

            const response = await fetch(
                `${CUSTOMER_STATUS_API}/${encodeURIComponent(
                    currentCustomerOrderCode
                )}/bayar-qris`,
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

            await loadCustomerStatus(
                currentCustomerOrderCode
            );

        } catch (error) {

            alert(
                error.message ||
                'Konfirmasi pembayaran QRIS gagal.'
            );

            if (statusQrisConfirm) {
                statusQrisConfirm.disabled = false;
                statusQrisConfirm.style.opacity = '1';
                statusQrisConfirm.textContent =
                    'Saya Sudah Membayar QRIS';
            }

        } finally {

            statusQrisConfirming = false;

        }
    }


    if (statusQrisConfirm) {
        statusQrisConfirm.addEventListener(
            'click',
            konfirmasiQrisStatus
        );
    }


    async function ambilPesananStatus()
    {
        if (
            statusAmbilProcessing ||
            !currentCustomerOrderCode
        ) {
            return;
        }

        if (
            !confirm(
                'Konfirmasi bahwa Anda sudah mengambil makanan ini?'
            )
        ) {
            return;
        }

        statusAmbilProcessing = true;

        if (statusAmbilButton) {
            statusAmbilButton.disabled = true;
            statusAmbilButton.style.opacity = '0.6';
            statusAmbilButton.textContent = 'Memproses...';
        }

        try {

            const response = await fetch(
                `${CUSTOMER_STATUS_API}/${encodeURIComponent(
                    currentCustomerOrderCode
                )}/ambil`,
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
                    'Gagal menandai pesanan selesai.'
                );
            }

            alert(
                'Terima kasih! Pesanan sudah ditandai selesai.'
            );

            await loadCustomerStatus(
                currentCustomerOrderCode
            );

        } catch (error) {

            alert(
                error.message ||
                'Gagal menandai pesanan selesai.'
            );

            if (statusAmbilButton) {
                statusAmbilButton.disabled = false;
                statusAmbilButton.style.opacity = '1';
                statusAmbilButton.textContent =
                    '✅ Saya Sudah Ambil Makanan';
            }

        } finally {

            statusAmbilProcessing = false;

        }
    }


    if (statusAmbilButton) {
        statusAmbilButton.addEventListener(
            'click',
            ambilPesananStatus
        );
    }


    window.openCustomerStatus =
        openCustomerStatus;


    window.closeCustomerStatus =
        closeCustomerStatus;

</script>
