<?php
$currentPath = trim(uri_string(), '/');
?>

<nav class="main-navbar">
    <div class="navbar-container">

        <a href="<?= site_url('/') ?>" class="navbar-brand">
            <span class="brand-name">Kantin Merah</span>
            <span class="brand-subtitle">POS</span>
        </a>

        <button
            type="button"
            class="navbar-toggle"
            onclick="toggleNavbar()"
            aria-label="Buka menu"
        >
            ☰
        </button>

        <div class="navbar-menu" id="navbarMenu">

            <a
                href="<?= site_url('kasir') ?>"
                class="navbar-link <?= str_starts_with($currentPath, 'kasir') ? 'active' : '' ?>"
            >
                Kasir
            </a>

            <a
                href="<?= site_url('pesanan') ?>"
                class="navbar-link <?= str_starts_with($currentPath, 'pesanan') ? 'active' : '' ?>"
            >
                Pesanan
            </a>

            <a
                href="<?= site_url('dapur') ?>"
                class="navbar-link <?= str_starts_with($currentPath, 'dapur') ? 'active' : '' ?>"
            >
                Dapur
            </a>

            <a
                href="<?= site_url('manajerial') ?>"
                class="navbar-link <?= str_starts_with($currentPath, 'manajerial') ? 'active' : '' ?>"
            >
                Manajerial
            </a>

            <a
                href="<?= site_url('auth/logout') ?>"
                class="navbar-link navbar-logout"
            >
                Keluar
            </a>

        </div>
    </div>
</nav>

<style>
    .main-navbar {
        position: sticky;
        top: 0;
        z-index: 1000;
        background: #ffffff;
        border-bottom: 1px solid #e5e7eb;
    }

    .navbar-container {
        width: min(1200px, calc(100% - 32px));
        min-height: 64px;
        margin: 0 auto;

        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
    }

    .navbar-brand {
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    .brand-name {
        font-size: 20px;
        font-weight: 800;
        color: #dc2626;
    }

    .brand-subtitle {
        padding: 3px 7px;
        border-radius: 5px;
        background: #dc2626;
        color: #ffffff;
        font-size: 11px;
        font-weight: 700;
    }

    .navbar-menu {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .navbar-link {
        padding: 9px 12px;
        border-radius: 7px;
        color: #4b5563;
        font-size: 14px;
        font-weight: 600;
        transition: 0.15s ease;
    }

    .navbar-link:hover {
        background: #fef2f2;
        color: #dc2626;
    }

    .navbar-link.active {
        background: #fee2e2;
        color: #b91c1c;
    }

    .navbar-link.navbar-logout {
        margin-left: 8px;
        color: #dc2626;
    }

    .navbar-toggle {
        display: none;
        padding: 8px 10px;
        border: 1px solid #d1d5db;
        border-radius: 7px;
        background: #ffffff;
        color: #374151;
        font-size: 18px;
    }

    @media (max-width: 900px) {
        .navbar-container {
            width: min(100% - 20px, 1200px);
            min-height: 58px;
        }

        .navbar-toggle {
            display: block;
        }

        .navbar-menu {
            position: absolute;
            top: 58px;
            left: 0;
            right: 0;

            display: none;
            flex-direction: column;
            align-items: stretch;

            padding: 10px;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.06);
        }

        .navbar-menu.show {
            display: flex;
        }

        .navbar-link {
            width: 100%;
        }

        .navbar-link.navbar-logout {
            margin-left: 0;
        }
    }
</style>

<script>
    function toggleNavbar() {
        const menu = document.getElementById('navbarMenu');

        if (!menu) {
            return;
        }

        menu.classList.toggle('show');
    }
</script>
