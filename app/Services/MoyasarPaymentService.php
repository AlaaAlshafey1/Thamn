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
    // خدمة التحويلات البنكية (Moyasar Payouts)
    // =========================================================

    /**
     * جلب قائمة حسابات الصرف المرتبطة بحساب ميسر
     */
    public function getPayoutAccounts(): array
    {
        if (empty($this->secretKey)) {
            return [];
        }

        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->get($this->baseUrl . '/payout_accounts');

            if ($response->successful()) {
                return $response->json('payout_accounts') ?? [];
            }
        } catch (\Exception $e) {
            Log::error('[Moyasar Payouts] Error fetching payout accounts: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * تحويل الأموال آلياً إلى الحساب البنكي للخبير
     *
     * @param float $amount المبلغ بالريال
     * @param string $iban رقم الآيبان
     * @param string $accountName اسم المستفيد الرباعي
     * @param string|null $phone رقم الجوال
     * @param string|null $sourceId معرف حساب الصرف (اختياري)
     * @param array $metadata بيانات إضافية
     * @return array ['success' => bool, 'message' => string, 'data' => ?array]
     */
    public function createPayout(
        float $amount,
        string $iban,
        string $accountName,
        ?string $phone = null,
        ?string $sourceId = null,
        array $metadata = []
    ): array {
        if (empty($this->secretKey)) {
            return [
                'success' => false,
                'message' => 'مفتاح الربط السري الخاص ببوابة ميسر (MOYASAR_SECRET_KEY) غير محدد في إعدادات النظام.'
            ];
        }

        $cleanIban = strtoupper(str_replace([' ', '-'], '', trim($iban)));
        if (empty($cleanIban) || strlen($cleanIban) < 15) {
            return [
                'success' => false,
                'message' => 'رقم الآيبان (IBAN) الخاص بالخبير غير متوفر أو غير صحيح.'
            ];
        }

        // جلب source_id إذا لم يُمرر صراحة
        $resolvedSourceId = $sourceId ?: config('services.moyasar.payout_source_id');
        if (empty($resolvedSourceId)) {
            $accounts = $this->getPayoutAccounts();
            if (!empty($accounts) && isset($accounts[0]['id'])) {
                $resolvedSourceId = $accounts[0]['id'];
            }
        }

        if (empty($resolvedSourceId)) {
            return [
                'success' => false,
                'message' => 'لم يتم العثور على حساب صرف (Payout Account) مسجل في حساب ميسر الخاص بك. يرجى تفعيل ميزة التحويلات (Payouts) وربط حساب الصرف من لوحة تحكم ميسر أولاً.'
            ];
        }

        $amountInHalala = (int) round($amount * 100);

        $payload = [
            'source_id' => $resolvedSourceId,
            'amount' => $amountInHalala,
            'purpose' => 'expenses_services',
            'destination' => [
                'type' => 'bank',
                'iban' => $cleanIban,
                'name' => trim($accountName) ?: 'Expert',
                'mobile' => $phone ?? '',
                'country' => 'SA',
            ],
            'comment' => 'تحويل مستحقات خبير - منصة ثمن #' . ($metadata['withdrawal_id'] ?? ''),
            'metadata' => $metadata,
        ];

        Log::info('[Moyasar Payouts] Creating payout', ['payload' => $payload]);

        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->post($this->baseUrl . '/payouts', $payload);

            $data = $response->json();
            Log::info('[Moyasar Payouts] Response', [
                'status' => $response->status(),
                'body' => $data
            ]);

            if ($response->successful() && !empty($data['id'])) {
                return [
                    'success' => true,
                    'message' => 'تم التحويل البنكي الآلي للخبير عبر ميسر بنجاح.',
                    'payout_id' => $data['id'],
                    'data' => $data
                ];
            }

            // استخراج رسالة الخطأ المفصلة من ميسر
            $errorMsg = $data['message'] ?? 'فشلت عملية التحويل في ميسر.';
            if (!empty($data['errors']) && is_array($data['errors'])) {
                $errParts = [];
                foreach ($data['errors'] as $field => $errs) {
                    $errParts[] = $field . ': ' . (is_array($errs) ? implode(', ', $errs) : $errs);
                }
                $errorMsg .= ' (' . implode(' | ', $errParts) . ')';
            }

            return [
                'success' => false,
                'message' => $errorMsg,
                'data' => $data
            ];
        } catch (\Exception $e) {
            Log::error('[Moyasar Payouts] Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'حدث خطأ في الاتصال بسيرفر ميسر: ' . $e->getMessage()
            ];
        }
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
