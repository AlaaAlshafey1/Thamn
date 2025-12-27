<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TapPaymentService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = "https://api.tap.company/v2";
        $this->apiKey = config('services.tap.secret_key');
    }

    public function createPayment($amount, $currency = "SAR", $customer = [], $redirectUrl = "")
    {
        $response = Http::withToken($this->apiKey)
            ->post($this->baseUrl . '/charges', [
                "amount" => $amount,
                "currency" => $currency,
                "customer" => $customer,
                "source" => [
                    "id" => "src_all"
                ],
                "redirect" => [
                    "url" => $redirectUrl
                ]
            ]);

        return $response->json();
    }

    public function getPaymentStatus($chargeId)
    {
        $response = Http::withToken($this->apiKey)
            ->get($this->baseUrl . '/charges/' . $chargeId);

        return $response->json();
    }
}
