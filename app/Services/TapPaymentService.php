<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TapPaymentService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = "https://api.tap.company/v2";
        $this->apiKey  = config('services.tap.secret_key');
    }

    /**
     * إنشاء عملية دفع
     */
    public function createPayment($amount, $currency, $customer, $urls)
    {
        // تأكد إن كل باراميتر موجود
        $payload = [
            'amount'   => $amount,
            'currency' => $currency,
            'customer' => $customer,
            'source'   => ['id' => 'src_all'],
            'redirect' => ['url' => $urls['redirect'] ?? ''],
            'post'     => ['url' => $urls['callback'] ?? ''],
        ];

        // استخدم Http facade مع Authorization
        $response = Http::withToken($this->apiKey)
            ->post($this->baseUrl . '/charges', $payload);

        return $response->json();
    }

    /**
     * استعلام عن حالة الدفع
     */
    public function getPaymentStatus($chargeId)
    {
        $response = Http::withToken($this->apiKey)
            ->get($this->baseUrl . '/charges/' . $chargeId);

        return $response->json();
    }
}
