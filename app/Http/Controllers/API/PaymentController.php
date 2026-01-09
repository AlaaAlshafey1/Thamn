<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\TapPayment;
use App\Services\TapPaymentService;
use App\Services\OpenAIService;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private TapPaymentService $tapPaymentService;

    public function __construct(TapPaymentService $tapPaymentService)
    {
        $this->tapPaymentService = $tapPaymentService;
    }

    // ===============================
    // إنشاء عملية الدفع
    // ===============================
    public function payOrder($order_id)
    {
        $order = Order::with('user')->findOrFail($order_id);

        $amount = (float) $order->total_price;
        if ($amount <= 0) {
            return response()->json([
                'status'  => false,
                'message' => 'قيمة الطلب غير صالحة للدفع'
            ], 400);
        }

        $payment = $this->tapPaymentService->createPayment(
            $amount,
            'SAR',
            [
                'first_name' => $order->user->name ?? 'Customer',
                'email'      => $order->user->email ?? 'test@test.com',
                'phone'      => [
                    'country_code' => '966',
                    'number'       => $order->user->phone ?? '500000000',
                ],
            ],
            [
                'redirect' => route('payment.redirect', $order->id),
                'callback' => route('payment.callback'),
            ]
        );

        TapPayment::create([
            'order_id'      => $order->id,
            'charge_id'     => $payment['id'] ?? null,
            'amount'        => $amount,
            'status'        => $payment['status'] ?? 'INITIATED',
            'response_data' => json_encode($payment),
        ]);

        return response()->json([
            'status'  => true,
            'payment' => $payment
        ]);
    }

    // ===============================
    // Tap CALLBACK (Server to Server)
    // ===============================
    public function callback(Request $request)
    {
        Log::info('Tap Callback', $request->all());

        $chargeId = $request->input('id') ?? $request->input('tap_id');
        if (!$chargeId) {
            return response()->json(['status' => false, 'message' => 'Missing charge id'], 400);
        }

        $payment = TapPayment::where('charge_id', $chargeId)->first();
        if (!$payment) {
            return response()->json(['status' => false, 'message' => 'Payment not found'], 404);
        }

        $statusResponse = $this->tapPaymentService->getPaymentStatus($chargeId);
        $status = strtoupper($statusResponse['status'] ?? 'INITIATED');
        $payment->status = in_array($status, ['CAPTURED', 'INITIATED'])
            ? 'paid'
            : 'failed';

        $payment->response_data = json_encode($statusResponse);
        $payment->save();

        $payment->order->update([
            'status' => $payment->status
        ]);

        return response()->json(['status' => true]);
    }

    // ===============================
    // USER REDIRECT (هنا AI)
    // ===============================
    public function redirect($orderId)
    {
        $order = Order::with([
            'details.question',
            'details.option',
            'category'
        ])->findOrFail($orderId);

        // ✅ لازم يكون مدفوع
        if ($order->status !== 'paid') {
            return response()->json([
                'status' => false,
                'message' => 'Payment Failed'
            ], 400);

        }

        // ✅ لو AI اتعمل قبل كده
        if ($order->ai_price) {
            return response()->json([
                'status' => true,
                'message' => 'Payment Success',
                'order_id' => $order->id,
                'ai_price' => $order->ai_price,
                'ai_confidence' => $order->ai_confidence,
                'ai_reasoning' => $order->ai_reasoning,
            ]);
        }


            try {
                $this->runAiEvaluation($order);
            } catch (\Throwable $e) {
                Log::error('AI Evaluation Failed on Redirect', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }


            return response()->json([
                'status' => true,
                'message' => 'Payment Success',
                'order_id' => $order->id,
                'ai_price' => $order->ai_price,
                'ai_confidence' => $order->ai_confidence,
                'ai_reasoning' => $order->ai_reasoning,
            ]);
    }

    // ===============================
    // AI CORE FUNCTION (Reusable)
    // ===============================
    private function runAiEvaluation(Order $order): void
    {
        $qaText = '';

        foreach ($order->details as $detail) {
            $question = $detail->question->question_ar ?? null;
            $answer   = $detail->option->option_ar ?? $detail->value ?? null;

            if ($question && $answer) {
                $qaText .= "- {$question}: {$answer}\n";
            }
        }
        $prompt = <<<PROMPT
أنت خبير محترف في تثمين السلع في السوق السعودي.

الدولة: المملكة العربية السعودية
العملة: ريال سعودي (SAR)
فئة السلعة: {$order->category->name_ar}

تفاصيل السلعة:
{$qaText}

ممنوع كتابة أي نص خارج JSON.

{
"min_price": رقم,
"max_price": رقم,
"recommended_price": رقم,
"currency": "SAR",
"confidence": رقم,
"reasoning": "شرح مختصر"
}
PROMPT;

        $aiResult = app(OpenAIService::class)->evaluateProduct($prompt);

        $order->update([
            'ai_min_price'  => $aiResult['min_price'] ?? null,
            'ai_max_price'  => $aiResult['max_price'] ?? null,
            'ai_price'      => $aiResult['recommended_price'] ?? null,
            'ai_confidence' => $aiResult['confidence'] ?? null,
            'ai_reasoning'  => $aiResult['reasoning'] ?? null,
        ]);
    }

    // ===============================
    // TEST AI (زي ما هو)
    // ===============================
    public function testAiEvaluation($orderId)
    {
        $order = Order::findOrFail($orderId);
        $this->runAiEvaluation($order);

        return response()->json([
            'status' => true,
            'order' => $order->fresh()
        ]);
    }

    public function success(Request $request)
    {
        $orderId = $request->query('order_id');
        $order = Order::findOrFail($orderId);

        return response()->json([
            'status' => true,
            'message' => 'Payment Success',
            'order_id' => $order->id,
            'ai_price' => $order->ai_price,
            'ai_confidence' => $order->ai_confidence,
            'ai_reasoning' => $order->ai_reasoning,
        ]);
    }

    public function failed()
    {
        return response()->json([
            'status' => false,
            'message' => 'Payment Failed'
        ], 400);
    }



}
