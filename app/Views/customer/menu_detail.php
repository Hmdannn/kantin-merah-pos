<div
    id="menuDetailModal"
    style="
        display:none;
        position:fixed;
        inset:0;
        z-index:1000;
        background:rgba(0,0,0,.55);
        padding:20px;
        overflow-y:auto;
    "
>

    <div
        style="
            max-width:520px;
            margin:40px auto;
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
                padding:16px 20px;
                border-bottom:1px solid #eee;
            "
        >

            <h2
                id="detailMenuTitle"
                style="
                    font-size:20px;
                    margin:0;
                "
            >
                Detail Menu
            </h2>

            <button
                type="button"
                id="closeMenuDetail"
                style="
                    border:none;
                    background:none;
                    font-size:26px;
                    cursor:pointer;
                    color:#666;
                "
            >
                &times;
            </button>

        </div>


        <div id="menuDetailLoading"
            style="
                padding:40px 20px;
                text-align:center;
                color:#777;
            "
        >
            Memuat detail menu...
        </div>


        <div
            id="menuDetailError"
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
            id="menuDetailContent"
            style="display:none;"
        >

            <img
                id="detailMenuImage"
                src=""
                alt=""
                style="
                    width:100%;
                    aspect-ratio:4 / 3;
                    height:auto;
                    object-fit:cover;
                    background:#eee;
                "
            >


            <div style="padding:20px;">

                <div
                    id="detailMenuCategory"
                    style="
                        font-size:13px;
                        color:#777;
                        margin-bottom:8px;
                    "
                >
                </div>


                <h3
                    id="detailMenuName"
                    style="
                        font-size:24px;
                        margin-bottom:10px;
                    "
                >
                </h3>


                <div
                    id="detailMenuPrice"
                    style="
                        font-size:20px;
                        font-weight:700;
                        color:#b91c1c;
                        margin-bottom:8px;
                    "
                >
                </div>


                <div
                    id="detailMenuStock"
                    style="
                        font-size:14px;
                        color:#666;
                        margin-bottom:20px;
                    "
                >
                </div>


                <div style="margin-bottom:16px;">

                    <label
                        for="detailMenuQuantity"
                        style="
                            display:block;
                            font-weight:600;
                            margin-bottom:8px;
                        "
                    >
                        Jumlah
                    </label>


                    <div
                        style="
                            display:flex;
                            align-items:center;
                            gap:10px;
                        "
                    >

                        <button
                            type="button"
                            id="decreaseQuantity"
                            style="
                                width:40px;
                                height:40px;
                                border:1px solid #ddd;
                                background:#fff;
                                border-radius:8px;
                                font-size:20px;
                            "
                        >
                            −
                        </button>


                        <input
                            type="number"
                            id="detailMenuQuantity"
                            min="1"
                            value="1"
                            style="
                                width:80px;
                                height:40px;
                                border:1px solid #ddd;
                                border-radius:8px;
                                text-align:center;
                                font-size:16px;
                            "
                        >


                        <button
                            type="button"
                            id="increaseQuantity"
                            style="
                                width:40px;
                                height:40px;
                                border:1px solid #ddd;
                                background:#fff;
                                border-radius:8px;
                                font-size:20px;
                            "
                        >
                            +
                        </button>

                    </div>

                </div>


                <div style="margin-bottom:20px;">

                    <label
                        for="detailMenuNote"
                        style="
                            display:block;
                            font-weight:600;
                            margin-bottom:8px;
                        "
                    >
                        Catatan Khusus
                    </label>


                    <textarea
                        id="detailMenuNote"
                        rows="3"
                        placeholder="Contoh: tidak pedas, tanpa sambal..."
                        style="
                            width:100%;
                            resize:vertical;
                            border:1px solid #ddd;
                            border-radius:8px;
                            padding:10px;
                            font-family:inherit;
                        "
                    ></textarea>

                </div>


                <button
                    type="button"
                    id="addToCartButton"
                    style="
                        width:100%;
                        border:none;
                        background:#b91c1c;
                        color:#fff;
                        padding:13px;
                        border-radius:8px;
                        font-weight:600;
                        font-size:16px;
                    "
                >
                    Tambahkan ke Keranjang
                </button>

            </div>

        </div>

    </div>

</div>


<script>

    const MENU_DETAIL_API =
        <?= json_encode(site_url('customer/menu')) ?>;

    const MENU_CART_STORAGE =
        'kantin_merah_cart';


    let currentMenu =
        null;


    const menuDetailModal =
        document.getElementById(
            'menuDetailModal'
        );

    const menuDetailLoading =
        document.getElementById(
            'menuDetailLoading'
        );

    const menuDetailError =
        document.getElementById(
            'menuDetailError'
        );

    const menuDetailContent =
        document.getElementById(
            'menuDetailContent'
        );

    const detailMenuImage =
        document.getElementById(
            'detailMenuImage'
        );

    const detailMenuTitle =
        document.getElementById(
            'detailMenuTitle'
        );

    const detailMenuName =
        document.getElementById(
            'detailMenuName'
        );

    const detailMenuCategory =
        document.getElementById(
            'detailMenuCategory'
        );

    const detailMenuPrice =
        document.getElementById(
            'detailMenuPrice'
        );

    const detailMenuStock =
        document.getElementById(
            'detailMenuStock'
        );

    const detailMenuQuantity =
        document.getElementById(
            'detailMenuQuantity'
        );

    const detailMenuNote =
        document.getElementById(
            'detailMenuNote'
        );

    const addToCartButton =
        document.getElementById(
            'addToCartButton'
        );


    function menuDetailFormatRupiah(
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


    function menuDetailEscapeHtml(
        value
    )
    {
        const div =
            document.createElement('div');

        div.textContent =
            value ?? '';

        return div.innerHTML;
    }


    function getMenuCart()
    {
        try {

            const saved =
                localStorage.getItem(
                    MENU_CART_STORAGE
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


    function saveMenuCart(cart)
    {
        localStorage.setItem(
            MENU_CART_STORAGE,
            JSON.stringify(cart)
        );

        updateMenuCartCount();
    }


    function updateMenuCartCount()
    {
        const cart =
            getMenuCart();

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


    function showMenuDetailLoading()
    {
        menuDetailLoading.style.display =
            'block';

        menuDetailError.style.display =
            'none';

        menuDetailContent.style.display =
            'none';
    }


    function showMenuDetailError(
        message
    )
    {
        menuDetailLoading.style.display =
            'none';

        menuDetailContent.style.display =
            'none';

        menuDetailError.textContent =
            message;

        menuDetailError.style.display =
            'block';
    }


    function renderMenuDetail(
        menu
    )
    {
        currentMenu =
            menu;


        menuDetailLoading.style.display =
            'none';

        menuDetailError.style.display =
            'none';

        menuDetailContent.style.display =
            'block';


        detailMenuTitle.textContent =
            menu.nama_menu;


        detailMenuName.textContent =
            menu.nama_menu;


        detailMenuCategory.textContent =
            menu.nama_kategori || '';


        detailMenuPrice.textContent =
            menuDetailFormatRupiah(
                menu.harga
            );


        detailMenuStock.textContent =
            `Stok tersedia: ${Number(
                menu.jumlah_stok
            )}`;


        detailMenuQuantity.value =
            1;


        detailMenuQuantity.max =
            Number(menu.jumlah_stok);


        detailMenuNote.value =
            '';


        if (menu.foto) {

            detailMenuImage.src =
                menu.foto;

            detailMenuImage.alt =
                menu.nama_menu;

            detailMenuImage.style.display =
                'block';

        } else {

            detailMenuImage.removeAttribute(
                'src'
            );

            detailMenuImage.alt =
                'Tidak ada foto';

            detailMenuImage.style.display =
                'none';

        }


        const stok =
            Number(menu.jumlah_stok);


        if (
            stok <= 0 ||
            menu.status_stok !== 'Tersedia'
        ) {

            addToCartButton.disabled =
                true;

            addToCartButton.textContent =
                'Stok Tidak Tersedia';

            addToCartButton.style.opacity =
                '0.6';

        } else {

            addToCartButton.disabled =
                false;

            addToCartButton.textContent =
                'Tambahkan ke Keranjang';

            addToCartButton.style.opacity =
                '1';

        }

    }


    async function openCustomerMenuDetail(
        menuId
    )
    {
        menuDetailModal.style.display =
            'block';

        document.body.style.overflow =
            'hidden';


        showMenuDetailLoading();


        try {

            const response =
                await fetch(
                    `${MENU_DETAIL_API}/${menuId}`,
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
                    'Menu tidak ditemukan.'
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
                    'Detail menu tidak tersedia.'
                );

            }


            renderMenuDetail(
                result.data
            );


        } catch (error) {

            showMenuDetailError(
                error.message ||
                'Gagal memuat detail menu.'
            );

        }

    }


    function closeCustomerMenuDetail()
    {
        menuDetailModal.style.display =
            'none';

        document.body.style.overflow =
            '';

        currentMenu =
            null;
    }


    document
        .getElementById(
            'closeMenuDetail'
        )
        .addEventListener(
            'click',
            closeCustomerMenuDetail
        );


    menuDetailModal.addEventListener(
        'click',
        function (event) {

            if (
                event.target ===
                menuDetailModal
            ) {

                closeCustomerMenuDetail();

            }

        }
    );


    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Escape' &&
                menuDetailModal.style.display ===
                    'block'
            ) {

                closeCustomerMenuDetail();

            }

        }
    );


    document
        .getElementById(
            'decreaseQuantity'
        )
        .addEventListener(
            'click',
            function () {

                let quantity =
                    Number(
                        detailMenuQuantity.value
                    ) || 1;


                quantity =
                    Math.max(
                        1,
                        quantity - 1
                    );


                detailMenuQuantity.value =
                    quantity;

            }
        );


    document
        .getElementById(
            'increaseQuantity'
        )
        .addEventListener(
            'click',
            function () {

                if (!currentMenu) {
                    return;
                }


                const maxStock =
                    Number(
                        currentMenu.jumlah_stok
                    );


                let quantity =
                    Number(
                        detailMenuQuantity.value
                    ) || 1;


                quantity =
                    Math.min(
                        maxStock,
                        quantity + 1
                    );


                detailMenuQuantity.value =
                    quantity;

            }
        );


    detailMenuQuantity.addEventListener(
        'input',
        function () {

            if (!currentMenu) {
                return;
            }


            const maxStock =
                Number(
                    currentMenu.jumlah_stok
                );


            let quantity =
                Number(
                    detailMenuQuantity.value
                );


            if (
                !Number.isFinite(quantity) ||
                quantity < 1
            ) {

                quantity = 1;

            }


            if (quantity > maxStock) {

                quantity =
                    maxStock;

            }


            detailMenuQuantity.value =
                quantity;

        }
    );


    addToCartButton.addEventListener(
        'click',
        function () {

            if (!currentMenu) {
                return;
            }


            const maxStock =
                Number(
                    currentMenu.jumlah_stok
                );


            let quantity =
                Number(
                    detailMenuQuantity.value
                );


            if (
                !Number.isInteger(quantity) ||
                quantity < 1
            ) {

                quantity = 1;

            }


            if (quantity > maxStock) {

                quantity =
                    maxStock;

            }


            const note =
                detailMenuNote.value.trim();


            const cart =
                getMenuCart();


            const existingIndex =
                cart.findIndex(
                    function (item) {

                        return Number(
                            item.id_menu
                        ) === Number(
                            currentMenu.id_menu
                        );

                    }
                );


            if (existingIndex >= 0) {

                const existingItem =
                    cart[existingIndex];


                const newQuantity =
                    Number(
                        existingItem.jumlah || 0
                    ) + quantity;


                existingItem.jumlah =
                    Math.min(
                        newQuantity,
                        maxStock
                    );


                if (note) {

                    existingItem.catatan_khusus =
                        note;

                }


            } else {

                cart.push({

                    id_menu:
                        Number(
                            currentMenu.id_menu
                        ),

                    jumlah:
                        quantity,

                    catatan_khusus:
                        note

                });

            }


            saveMenuCart(cart);


            closeCustomerMenuDetail();


            if (
                typeof window.openCustomerCart ===
                'function'
            ) {

                window.openCustomerCart();

            }

        }
    );


    window.openCustomerMenuDetail =
        openCustomerMenuDetail;


    window.closeCustomerMenuDetail =
        closeCustomerMenuDetail;


    updateMenuCartCount();

</script>