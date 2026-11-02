<div
    id="customerCartModal"
    style="
        display:none;
        position:fixed;
        inset:0;
        z-index:1100;
        background:rgba(0,0,0,.55);
        padding:20px;
        overflow-y:auto;
    "
>

    <div
        style="
            max-width:700px;
            margin:30px auto;
            background:#fff;
            border-radius:14px;
            overflow:hidden;
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

            <h2 style="margin:0;">
                Keranjang
            </h2>

            <button
                type="button"
                id="closeCustomerCart"
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
            id="customerCartLoading"
            style="
                display:none;
                padding:40px;
                text-align:center;
                color:#777;
            "
        >
            Memuat keranjang...
        </div>


        <div
            id="customerCartEmpty"
            style="
                display:none;
                padding:50px 20px;
                text-align:center;
                color:#777;
            "
        >
            <div
                style="
                    font-size:40px;
                    margin-bottom:12px;
                "
            >
                🛒
            </div>

            <div
                style="
                    font-size:18px;
                    font-weight:600;
                    margin-bottom:6px;
                "
            >
                Keranjang masih kosong
            </div>

            <div>
                Silakan pilih menu terlebih dahulu.
            </div>
        </div>


        <div
            id="customerCartError"
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
            id="customerCartContent"
            style="display:none;"
        >

            <div
                id="customerCartItems"
                style="
                    padding:20px;
                "
            >
            </div>


            <div
                style="
                    border-top:1px solid #eee;
                    padding:20px;
                "
            >

                <div
                    style="
                        display:flex;
                        justify-content:space-between;
                        align-items:center;
                        margin-bottom:16px;
                    "
                >

                    <span
                        style="
                            font-size:16px;
                            color:#666;
                        "
                    >
                        Total
                    </span>

                    <strong
                        id="customerCartTotal"
                        style="
                            font-size:22px;
                            color:#b91c1c;
                        "
                    >
                        Rp0
                    </strong>

                </div>


                <button
                    type="button"
                    id="customerCartCheckout"
                    style="
                        width:100%;
                        border:none;
                        background:#b91c1c;
                        color:#fff;
                        padding:13px;
                        border-radius:8px;
                        font-weight:600;
                        font-size:16px;
                        cursor:pointer;
                    "
                >
                    Lanjut ke Checkout
                </button>

            </div>

        </div>

    </div>

</div>


<script>

    const CUSTOMER_CART_API =
        <?= json_encode(site_url('customer/menu')) ?>;

    const CUSTOMER_CART_STORAGE =
        'kantin_merah_cart';


    let customerCartMenus =
        {};


    const customerCartModal =
        document.getElementById(
            'customerCartModal'
        );

    const customerCartLoading =
        document.getElementById(
            'customerCartLoading'
        );

    const customerCartEmpty =
        document.getElementById(
            'customerCartEmpty'
        );

    const customerCartError =
        document.getElementById(
            'customerCartError'
        );

    const customerCartContent =
        document.getElementById(
            'customerCartContent'
        );

    const customerCartItems =
        document.getElementById(
            'customerCartItems'
        );

    const customerCartTotal =
        document.getElementById(
            'customerCartTotal'
        );


    function cartFormatRupiah(value)
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


    function cartEscapeHtml(value)
    {
        const div =
            document.createElement('div');

        div.textContent =
            value ?? '';

        return div.innerHTML;
    }


    function getCustomerCart()
    {
        try {

            const saved =
                localStorage.getItem(
                    CUSTOMER_CART_STORAGE
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


    function saveCustomerCart(cart)
    {
        localStorage.setItem(
            CUSTOMER_CART_STORAGE,
            JSON.stringify(cart)
        );


        updateCustomerCartCount();
    }


    function updateCustomerCartCount()
    {
        const cart =
            getCustomerCart();


        const total =
            cart.reduce(
                function (sum, item) {

                    return sum +
                        Number(
                            item.jumlah || 0
                        );

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


    function showCartLoading()
    {
        customerCartLoading.style.display =
            'block';

        customerCartEmpty.style.display =
            'none';

        customerCartError.style.display =
            'none';

        customerCartContent.style.display =
            'none';
    }


    function showCartError(message)
    {
        customerCartLoading.style.display =
            'none';

        customerCartEmpty.style.display =
            'none';

        customerCartContent.style.display =
            'none';

        customerCartError.textContent =
            message;

        customerCartError.style.display =
            'block';
    }


    function renderCart()
    {
        const cart =
            getCustomerCart();


        customerCartLoading.style.display =
            'none';

        customerCartError.style.display =
            'none';


        if (cart.length === 0) {

            customerCartContent.style.display =
                'none';

            customerCartEmpty.style.display =
                'block';

            return;

        }


        customerCartEmpty.style.display =
            'none';

        customerCartContent.style.display =
            'block';

        customerCartItems.innerHTML = '';


        let total =
            0;


        cart.forEach(
            function (item, index) {

                const menu =
                    customerCartMenus[
                        Number(item.id_menu)
                    ];


                if (!menu) {
                    return;
                }


                const jumlah =
                    Number(item.jumlah || 0);


                const harga =
                    Number(menu.harga || 0);


                const subtotal =
                    harga * jumlah;


                total += subtotal;


                const itemElement =
                    document.createElement(
                        'div'
                    );


                itemElement.style.cssText = `
                    display:flex;
                    gap:14px;
                    align-items:flex-start;
                    padding:16px 0;
                    border-bottom:1px solid #eee;
                `;


                const image =
                    menu.foto
                        ? `
                            <img
                                src="${cartEscapeHtml(
                                    menu.foto
                                )}"
                                alt="${cartEscapeHtml(
                                    menu.nama_menu
                                )}"
                                style="
                                    width:96px;
                                    aspect-ratio:4 / 3;
                                    height:auto;
                                    object-fit:cover;
                                    border-radius:8px;
                                    background:#eee;
                                    flex-shrink:0;
                                "
                            >
                        `
                        : `
                            <div
                                style="
                                    width:96px;
                                    aspect-ratio:4 / 3;
                                    height:auto;
                                    border-radius:8px;
                                    background:#eee;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    color:#999;
                                    font-size:12px;
                                    text-align:center;
                                    flex-shrink:0;
                                "
                            >
                                Tidak ada foto
                            </div>
                        `;


                itemElement.innerHTML = `

                    ${image}

                    <div
                        style="
                            flex:1;
                            min-width:0;
                        "
                    >

                        <div
                            style="
                                font-weight:700;
                                font-size:17px;
                                margin-bottom:5px;
                            "
                        >
                            ${cartEscapeHtml(
                                menu.nama_menu
                            )}
                        </div>


                        <div
                            style="
                                font-size:13px;
                                color:#777;
                                margin-bottom:5px;
                            "
                        >
                            ${cartEscapeHtml(
                                menu.nama_kategori || ''
                            )}
                        </div>


                        <div
                            style="
                                color:#b91c1c;
                                font-weight:600;
                                margin-bottom:10px;
                            "
                        >
                            ${cartFormatRupiah(
                                harga
                            )}
                        </div>


                        <div
                            style="
                                display:flex;
                                align-items:center;
                                gap:8px;
                            "
                        >

                            <button
                                type="button"
                                class="cart-minus"
                                data-index="${index}"
                                style="
                                    width:32px;
                                    height:32px;
                                    border:1px solid #ddd;
                                    background:#fff;
                                    border-radius:6px;
                                    cursor:pointer;
                                "
                            >
                                −
                            </button>


                            <span
                                style="
                                    min-width:30px;
                                    text-align:center;
                                    font-weight:600;
                                "
                            >
                                ${jumlah}
                            </span>


                            <button
                                type="button"
                                class="cart-plus"
                                data-index="${index}"
                                style="
                                    width:32px;
                                    height:32px;
                                    border:1px solid #ddd;
                                    background:#fff;
                                    border-radius:6px;
                                    cursor:pointer;
                                "
                            >
                                +
                            </button>


                            <button
                                type="button"
                                class="cart-remove"
                                data-index="${index}"
                                style="
                                    margin-left:auto;
                                    border:none;
                                    background:none;
                                    color:#b91c1c;
                                    cursor:pointer;
                                    font-size:13px;
                                "
                            >
                                Hapus
                            </button>

                        </div>


                        ${
                            item.catatan_khusus
                                ? `
                                    <div
                                        style="
                                            margin-top:10px;
                                            padding:8px;
                                            background:#f7f7f7;
                                            border-radius:6px;
                                            font-size:13px;
                                            color:#555;
                                        "
                                    >
                                        Catatan:
                                        ${cartEscapeHtml(
                                            item.catatan_khusus
                                        )}
                                    </div>
                                `
                                : ''
                        }

                    </div>


                    <div
                        style="
                            font-weight:700;
                            white-space:nowrap;
                            align-self:flex-start;
                        "
                    >
                        ${cartFormatRupiah(
                            subtotal
                        )}
                    </div>

                `;


                customerCartItems.appendChild(
                    itemElement
                );

            }
        );


        customerCartTotal.textContent =
            cartFormatRupiah(total);

    }


    async function loadCartMenus()
    {
        const cart =
            getCustomerCart();


        if (cart.length === 0) {

            customerCartMenus =
                {};

            renderCart();

            return;

        }


        showCartLoading();


        try {

            const uniqueIds =
                [
                    ...new Set(
                        cart.map(
                            function (item) {
                                return Number(
                                    item.id_menu
                                );
                            }
                        )
                    )
                ];


            const results =
                await Promise.all(
                    uniqueIds.map(
                        async function (idMenu) {

                            const response =
                                await fetch(
                                    `${CUSTOMER_CART_API}/${idMenu}`,
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


                            return result.data;

                        }
                    )
                );


            customerCartMenus =
                {};


            results.forEach(
                function (menu) {

                    customerCartMenus[
                        Number(menu.id_menu)
                    ] = menu;

                }
            );


            renderCart();


        } catch (error) {

            showCartError(
                error.message ||
                'Gagal memuat isi keranjang.'
            );

        }
    }


    function openCustomerCart()
    {
        customerCartModal.style.display =
            'block';

        document.body.style.overflow =
            'hidden';


        loadCartMenus();
    }


    function closeCustomerCart()
    {
        customerCartModal.style.display =
            'none';

        document.body.style.overflow =
            '';
    }


    customerCartItems.addEventListener(
        'click',
        function (event) {

            const minusButton =
                event.target.closest(
                    '.cart-minus'
                );

            const plusButton =
                event.target.closest(
                    '.cart-plus'
                );

            const removeButton =
                event.target.closest(
                    '.cart-remove'
                );


            if (
                !minusButton &&
                !plusButton &&
                !removeButton
            ) {

                return;

            }


            const button =
                minusButton ||
                plusButton ||
                removeButton;


            const index =
                Number(button.dataset.index);


            const cart =
                getCustomerCart();


            if (
                !cart[index]
            ) {

                return;

            }


            const item =
                cart[index];


            const menu =
                customerCartMenus[
                    Number(item.id_menu)
                ];


            const maxStock =
                menu
                    ? Number(menu.jumlah_stok)
                    : null;


            if (minusButton) {

                item.jumlah =
                    Number(item.jumlah) - 1;


                if (item.jumlah <= 0) {

                    cart.splice(
                        index,
                        1
                    );

                }

            }


            if (plusButton) {

                const current =
                    Number(item.jumlah);


                if (
                    maxStock !== null &&
                    current >= maxStock
                ) {

                    return;

                }


                item.jumlah =
                    current + 1;

            }


            if (removeButton) {

                cart.splice(
                    index,
                    1
                );

            }


            saveCustomerCart(cart);

            renderCart();

        }
    );


    document
        .getElementById(
            'closeCustomerCart'
        )
        .addEventListener(
            'click',
            closeCustomerCart
        );


    customerCartModal.addEventListener(
        'click',
        function (event) {

            if (
                event.target ===
                customerCartModal
            ) {

                closeCustomerCart();

            }

        }
    );


    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Escape' &&
                customerCartModal.style.display ===
                    'block'
            ) {

                closeCustomerCart();

            }

        }
    );


    document
        .getElementById(
            'customerCartCheckout'
        )
        .addEventListener(
            'click',
            function () {

                const cart =
                    getCustomerCart();


                if (cart.length === 0) {

                    alert(
                        'Keranjang masih kosong.'
                    );

                    return;

                }


                if (
                    typeof window.openCustomerCheckout ===
                    'function'
                ) {

                    closeCustomerCart();

                    window.openCustomerCheckout();

                } else {

                    alert(
                        'Halaman checkout belum tersedia.'
                    );

                }

            }
        );


    window.openCustomerCart =
        openCustomerCart;


    window.closeCustomerCart =
        closeCustomerCart;


    updateCustomerCartCount();

</script>