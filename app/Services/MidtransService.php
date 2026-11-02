<?php

namespace App\Services;

use Config\Midtrans;

class MidtransService
{
    protected $config;

    public function __construct()
    {
        $this->config = new Midtrans();
    }

    public function createQrisTransaction(
        string $orderId,
        float $grossAmount
    ): array {
        $serverKey = $this->config->serverKey;

        if (empty($serverKey)) {
            throw new \Exception('Midtrans Server Key belum dikonfigurasi.');
        }

        $url = rtrim($this->config->apiBaseUrl, '/')
            . '/v2/charge';

        $payload = [
            'payment_type' => 'qris',

            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],

            'qris' => [
                'acquirer' => 'gopay',
            ],
        ];

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,

            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Basic '
                    . base64_encode($serverKey . ':'),
            ],

            CURLOPT_POSTFIELDS => json_encode($payload),

            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);

            throw new \Exception(
                'Gagal menghubungi Midtrans: ' . $error
            );
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $result = json_decode($response, true);

        if ($httpCode < 200 || $httpCode >= 300) {
            $message = $result['status_message']
                ?? 'Gagal membuat transaksi QRIS di Midtrans.';

            throw new \Exception($message);
        }

        return $result;
    }
}