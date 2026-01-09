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

    /**
     * إنشاء عملية الدفع
     */
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

    /**
     * Tap CALLBACK (Server to Server)
     */
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
        $status = strtoupper($statusResponse['status'] ?? 'FAILED');

        $payment->status = $status === 'CAPTURED' ? 'paid' : 'failed';
        $payment->response_data = json_encode($statusResponse);
        $payment->save();

        $order = $payment->order;
        $order->update([
            'payment_status' => $payment->status
        ]);

        // ================= Evaluation =================
        if ($payment->status === 'paid') {

            $order->load([
                'details.question',
                'details.option',
                'category'
            ]);

            // البحث عن سؤال التقييم
            $rateQuestion = $order->details->firstWhere('question.type', 'rateTypeSelection');

            if ($rateQuestion) {
                $selectedBadge = $rateQuestion->option->badge ?? null;

                if ($selectedBadge === 'ai') {
                    // ===== AI Evaluation =====
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

    تفاصيل السلعة كما أدخلها العميل:
    {$qaText}

    المطلوب:
    1- تحديد السعر العادل الحالي في السوق السعودي.
    2- أقل سعر وأعلى سعر منطقي.
    3- سعر نهائي موصى به.
    4- شرح مختصر لأسباب التقييم.
    5- نسبة ثقة من 0 إلى 100.

    الرد يجب أن يكون JSON فقط:
    {
    "min_price": رقم,
    "max_price": رقم,
    "recommended_price": رقم,
    "currency": "SAR",
    "confidence": رقم,
    "reasoning": "شرح مختصر"
    }
    PROMPT;

                    try {
                        $aiResult = app(OpenAIService::class)->evaluateProduct($prompt);

                        $order->update([
                            'ai_min_price'  => $aiResult['min_price'] ?? null,
                            'ai_max_price'  => $aiResult['max_price'] ?? null,
                            'ai_price'      => $aiResult['recommended_price'] ?? null,
                            'ai_confidence' => $aiResult['confidence'] ?? null,
                            'ai_reasoning'  => $aiResult['reasoning'] ?? null,
                        ]);
                    } catch (\Throwable $e) {
                        Log::error('AI Evaluation Failed', [
                            'order_id' => $order->id,
                            'error'    => $e->getMessage()
                        ]);
                    }

                } else {
                    // ===== Expert / Badge Evaluation =====
                    // إرسال إشعار بالـ Email بأن التقييم سيتم من خبير
                    try {
                        $user = $order->user;
                        \Mail::to($user->email)->send(new \App\Mail\ExpertEvaluationNotification($order));
                        Log::info("Expert evaluation email sent to {$user->email}");
                    } catch (\Throwable $e) {
                        Log::error("Failed to send expert evaluation email", [
                            'order_id' => $order->id,
                            'error'    => $e->getMessage()
                        ]);
                    }
                }
            }
        }

        return response()->json(['status' => true]);
    }


    /**
     * المستخدم بعد الدفع
     */
    public function redirect($orderId)
    {
        return redirect()->to("/payment-success?order_id={$orderId}");
    }
}
