<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoyasarPaymentService
{
    protected string $baseUrl  = 'https://api.moyasar.com/v1';
    protected string $secretKey;
    protected string $webhookSecret;

    public function __construct()
    {
        $this->secretKey     = config('services.moyasar.secret_key');
        $this->webhookSecret = config('services.moyasar.webhook_secret', '');
    }

    // =========================================================
    // إنشاء عملية دفع جديدة
    // =========================================================
    /**
     * إنشاء Payment Request (تماماً زي Tap)
     * الموبايل يفتح الـ URL ، المستخدم يدفع على صفحة Moyasar ، ثم يرجع بـ callback
     *
     * @param float  $amount      المبلغ بالريال
     * @param string $currency    SAR
     * @param array  $metadata    بيانات إضافية (order_id, ...)
     * @param string $callbackUrl الـ URL بعد إتمام الدفع (ديب لينك أو URL)
     * @return array
     */
    public function createPayment(
        float  $amount,
        string $currency = 'SAR',
        array  $source = [],
        array  $metadata = [],
        string $callbackUrl = ''
    ): array {
        $amountInHalala = (int) round($amount * 100);

        $payload = [
            'amount'       => $amountInHalala,
            'currency'     => $currency,
            'description'  => $metadata['description'] ?? 'طلب تثمين رقم ' . ($metadata['order_id'] ?? ''),
            'callback_url' => $callbackUrl,
            'source'       => array_merge(['type' => 'creditcard'], $source),
            'metadata'     => $metadata,
        ];

        Log::info('[Moyasar] Creating payment', ['payload' => $payload]);

        $response = Http::withBasicAuth($this->secretKey, '')
            ->post($this->baseUrl . '/payments', $payload);

        $data = $response->json();

        Log::info('[Moyasar] Payment response', ['response' => $data]);

        return $data;
    }

    // =========================================================
    // جلب حالة الدفع
    // =========================================================
    public function getPaymentStatus(string $paymentId): array
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->get($this->baseUrl . '/payments/' . $paymentId);

        return $response->json();
    }

    // =========================================================
    // التحقق من توقيع الـ Webhook
    // =========================================================
    /**
     * Moyasar يرسل header: moyasar-signature
     * القيمة هي HMAC-SHA256 للـ request body بالـ webhook_secret
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        if (empty($this->webhookSecret)) {
            // لو ما في secret مضبوط، نقبل كل الطلبات (test mode)
            Log::warning('[Moyasar] Webhook secret not configured — skipping signature verification');
            return true;
        }

        $expected = hash_hmac('sha256', $payload, $this->webhookSecret);

        return hash_equals($expected, $signature);
    }

    // =========================================================
    // رد الاسترداد
    // =========================================================
    public function refundPayment(string $paymentId, int $amountInHalala): array
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->post($this->baseUrl . '/payments/' . $paymentId . '/refund', [
                'amount_cents' => $amountInHalala,
            ]);

        return $response->json();
    }
}
