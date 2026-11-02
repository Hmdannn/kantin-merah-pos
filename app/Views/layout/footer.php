<footer class="main-footer">
    <div class="footer-container">
        <div class="footer-brand">
            <strong>Kantin Merah POS</strong>
            <span>Point of Sale System</span>
        </div>

        <div class="footer-copy">
            &copy; <?= date('Y') ?> Kantin Merah. All rights reserved.
        </div>
    </div>
</footer>

<style>
    .main-footer {
        margin-top: 40px;
        background: #ffffff;
        border-top: 1px solid #e5e7eb;
    }

    .footer-container {
        width: min(1200px, calc(100% - 32px));
        min-height: 72px;
        margin: 0 auto;

        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .footer-brand {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .footer-brand strong {
        color: #dc2626;
        font-size: 14px;
    }

    .footer-brand span {
        color: #9ca3af;
        font-size: 13px;
    }

    .footer-copy {
        color: #9ca3af;
        font-size: 13px;
        text-align: right;
    }

    @media (max-width: 600px) {
        .footer-container {
            width: min(100% - 20px, 1200px);
            padding: 14px 0;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }

        .footer-brand {
            flex-direction: column;
            gap: 3px;
        }

        .footer-copy {
            text-align: center;
        }
    }
</style>