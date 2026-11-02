<?php
$error = session()->getFlashdata('error');
$success = session()->getFlashdata('success');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kantin Merah POS</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: linear-gradient(135deg, #7a0f1d 0%, #a81c2d 50%, #dc2626 100%);
            color: #1f2937;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            padding: 36px 32px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.25);
        }

        .login-header {
            text-align: center;
            margin-bottom: 26px;
        }

        .logo {
            width: 68px;
            height: 68px;
            margin: 0 auto 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: linear-gradient(135deg, #b91c1c, #ef4444);
            color: #fff;
            font-size: 24px;
            font-weight: 800;
            box-shadow: 0 8px 18px rgba(185, 28, 28, 0.35);
        }

        h1 {
            margin: 0 0 6px;
            font-size: 24px;
            color: #111827;
            font-weight: 800;
        }

        .subtitle {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
        }

        .badge-campus {
            display: inline-block;
            margin-top: 8px;
            background: #fee2e2;
            color: #991b1b;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 9999px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .alert {
            margin-bottom: 18px;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
        }

        input {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid #d1d5db;
            border-radius: 10px;
            outline: none;
            font-size: 14px;
            transition: all .15s ease;
        }

        input:focus {
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, .15);
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 85px;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            color: #b91c1c;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 6px;
        }

        .submit-button {
            width: 100%;
            padding: 13px;
            border: 0;
            border-radius: 10px;
            background: linear-gradient(135deg, #b91c1c, #dc2626);
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(185, 28, 28, 0.3);
            transition: all .15s ease;
        }

        .submit-button:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }

        .submit-button:disabled {
            opacity: .7;
            cursor: wait;
        }

        /* Demo Account Quick Switcher */
        .demo-box {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px dashed #e5e7eb;
        }

        .demo-title {
            font-size: 12px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
            text-align: center;
        }

        .demo-buttons {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }

        .btn-demo {
            padding: 9px 6px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
            text-align: center;
            transition: all .15s ease;
        }

        .btn-demo:hover {
            background: #fee2e2;
            border-color: #fca5a5;
            color: #991b1b;
        }

        .btn-demo small {
            display: block;
            font-size: 10px;
            color: #6b7280;
            font-weight: normal;
            margin-top: 2px;
        }

        .footer {
            margin-top: 22px;
            text-align: center;
            color: #9ca3af;
            font-size: 12px;
        }

        .footer a {
            color: #b91c1c;
            text-decoration: none;
            font-weight: 600;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <main class="login-card">

        <header class="login-header">
            <div class="logo">KM</div>
            <h1>Kantin Merah POS</h1>
            <p class="subtitle">Sistem Point of Sale Self-Service Berbasis Web</p>
            <div class="badge-campus">Studi Kasus: UNP Kampus 1</div>
        </header>

        <?php if ($error): ?>
            <div class="alert alert-error" role="alert">
                <span>⚠️</span> <?= esc($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success" role="status">
                <span>✅</span> <?= esc($success) ?>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('login/attempt') ?>" method="post" id="loginForm">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="email">Alamat Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= esc(old('email') ?? '') ?>"
                    placeholder="nama@kantinmerah.com"
                    autocomplete="username"
                    required
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>

                <div class="password-wrapper">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        id="togglePassword"
                    >
                        Tampilkan
                    </button>
                </div>
            </div>

            <button type="submit" class="submit-button" id="submitButton">
                Masuk ke Sistem
            </button>
        </form>

        <!-- Quick Fill Role Selector (Sesuai PDF 4 Aktor) -->
        <div class="demo-box">
            <div class="demo-title">Pilih Akun Demo (1-Click Login Langsung)</div>
            <div class="demo-buttons">
                <button type="button" class="btn-demo" onclick="fillRole('kasir@kantinmerah.com', 'password123', true)">
                    💰 Kasir
                    <small>Shift & Bayar</small>
                </button>
                <button type="button" class="btn-demo" onclick="fillRole('dapur@kantinmerah.com', 'password123', true)">
                    🍳 Dapur
                    <small>FIFO & Masak</small>
                </button>
                <button type="button" class="btn-demo" onclick="fillRole('manajerial@kantinmerah.com', 'password123', true)">
                    📊 Manajer
                    <small>Laporan & Menu</small>
                </button>
            </div>
        </div>

        <footer class="footer">
            Customer / Pembeli? <a href="<?= site_url('customer') ?>" target="_blank">Buka Menu Pesan Mandiri &rarr;</a>
            <br><br>
            <small style="color: #cbd5e1;">Belum ada akun di database? <a href="<?= site_url('test/init-users') ?>" target="_blank" style="color: #94a3b8;">Inisialisasi User Demo</a></small>
        </footer>

    </main>

    <script>
        const passwordInput = document.getElementById('password');
        const emailInput = document.getElementById('email');
        const togglePassword = document.getElementById('togglePassword');
        const loginForm = document.getElementById('loginForm');
        const submitButton = document.getElementById('submitButton');

        togglePassword.addEventListener('click', function () {
            const show = passwordInput.type === 'password';
            passwordInput.type = show ? 'text' : 'password';
            togglePassword.textContent = show ? 'Sembunyikan' : 'Tampilkan';
        });

        loginForm.addEventListener('submit', function () {
            submitButton.disabled = true;
            submitButton.textContent = 'Memverifikasi...';
        });

        function fillRole(email, password, autoSubmit = false) {
            emailInput.value = email;
            passwordInput.value = password;
            if (autoSubmit) {
                submitButton.disabled = true;
                submitButton.textContent = 'Memverifikasi...';
                loginForm.submit();
            } else {
                emailInput.style.borderColor = '#dc2626';
                passwordInput.style.borderColor = '#dc2626';
                setTimeout(() => {
                    emailInput.style.borderColor = '';
                    passwordInput.style.borderColor = '';
                }, 600);
            }
        }
    </script>
</body>
</html>
