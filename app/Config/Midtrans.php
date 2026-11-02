<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Midtrans extends BaseConfig
{
    public string $serverKey = '';
    public string $clientKey = '';

    public bool $isProduction = false;

    public string $apiBaseUrl = 'https://app.sandbox.midtrans.com';

    public string $notificationUrl = '';
}