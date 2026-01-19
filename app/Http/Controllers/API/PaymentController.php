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

        $customerName  = $order->user->name ?? 'Unknown Customer';
        $customerEmail = $order->user->email ?? 'noemail@example.com';
        $customerPhone = $order->user->phone ?? '0000000000';



                // إنشاء طلب الدفع في Tap
        $payment = $this->tapPaymentService->createPayment(
            $amount,
            "SAR",
            [
                "first_name" => $customerName,
                "email" => $customerEmail,
                "phone" => [
                    "country_code" => "966",
                    "number" => $customerPhone
                ]
            ],
            // 👇 USER REDIRECT
            url("/payment/order/{$order->id}"),

            // 👇 SERVER CALLBACK
            route('payment.callback')
        );

        TapPayment::create([
            'order_id'      => $order->id,
            'charge_id'     => $payment['id'] ?? null,
            'amount'        => $amount,
            'status'        => $payment['status'] ?? 'CAPTURED',
            'response_data' => json_encode($payment),
        ]);

        return response()->json($payment);
    }

    // ===============================
    // Tap CALLBACK (Server to Server)
    // ===============================
    // public function callback(Request $request)
    // {

    //     $chargeId = $request->tap_id; // Tap بترجع tap_id
    //     $statusResponse = $this->tapPaymentService->getPaymentStatus($chargeId);
    //     $payment = TapPayment::where('charge_id', $chargeId)->first();

    //     if ($payment) {
    //         $status = $statusResponse['status'] ?? 'FAILED';

    //         $payment->status = strtoupper($status) === 'CAPTURED' ? 'paid' : 'failed';
    //         $payment->response_data = json_encode($statusResponse);
    //         $payment->save();

    //         $payment->order->update([
    //             'payment_status' => $payment->status,
    //         ]);
    //     }
    //     $order = Order::with([
    //         'details.question',
    //         'details.option',
    //         'category'
    //     ])->findOrFail($payment->order->id);
    //         try {

    //             $this->runAiEvaluation($order);
    //         } catch (\Throwable $e) {
    //             Log::error('AI Evaluation Failed on Redirect', [
    //                 'order_id' => $order->id,
    //                 'error' => $e->getMessage()
    //             ]);
    //         }
    //     return response()->json($statusResponse);
    // }
    public function callback(Request $request)
    {
        $chargeId = $request->tap_id; // Tap بترجع tap_id
        $statusResponse = $this->tapPaymentService->getPaymentStatus($chargeId);
        $payment = TapPayment::where('charge_id', $chargeId)->first();

        if (!$payment) {
            return response()->json([
                'status' => false,
                'message' => 'Payment record not found'
            ], 404);
        }

        // تحديث حالة الدفع
        $status = $statusResponse['status'] ?? 'FAILED';

        $payment->status = strtoupper($status) === 'CAPTURED' ? 'orderReceived' : 'failed';
        $payment->response_data = json_encode($statusResponse);
        $payment->save();

        $order = Order::with([
            'details.question',
            'details.option',
            'category'
        ])->findOrFail($payment->order->id);

        $order->update([
            'status' => $payment->status,
        ]);

        if ($payment->status === 'paid') {
            try {
                // نجيب الإجابة على سؤال rateTypeSelection
                $rateTypeAnswer = $order->details()
                    ->whereHas('question', function ($q) {
                        $q->where('type', 'rateTypeSelection');
                    })
                    ->first();

                // قراءة القيمة من الخيار أو value مباشر
                $evaluationType = $rateTypeAnswer?->option?->badge // badge = 'ai', 'expert', 'best'
                                ?? $rateTypeAnswer?->value;

                switch ($evaluationType) {
                    case 'ai':
                        $this->runAiEvaluation($order);
                        break;

                    case 'expert':
                        $this->sendToExperts($order); // هنعملها بعدين
                        break;

                    case 'best':
                        $this->runPricingEvaluation($order); // هنعملها بعدين
                        break;

                    default:
                        Log::warning('Unknown evaluation type', [
                            'order_id' => $order->id,
                            'evaluation_type' => $evaluationType
                        ]);
                }

            } catch (\Throwable $e) {
                Log::error('Evaluation Failed on Callback', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return response()->json($statusResponse);
    }


    public function callback_error(Request $request)
    {
        $chargeId = $request->tap_id;
        return response()->json([
            'status' => false,
            'message' => 'عملية الدفع فشلت.',
            'charge_id' => $chargeId
        ]);
    }

    // ===============================
    // USER REDIRECT (هنا AI)
    // ===============================
public function redirect(Request $request, $orderId)
{
    $order = Order::findOrFail($orderId);

    // Tap بيرجع tap_id
    $tapId = $request->query('tap_id');
    $tap_pay = TapPayment::where('charge_id', $tapId)->first();
    if ($tap_pay->status == 'CAPTURED') {
        $order->status = "orderReceived";
        $order->save();
        return redirect()->to(
            url("/payment/callback/package_sucess?success=true&tap_id={$tapId}")
        );
    }

    return redirect()->to(
        url("/payment/callback/package_error?success=false&tap_id={$tapId}")
    );
}



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
            'status'  => "estimated" ?? null,
            'ai_min_price'  => $aiResult['min_price'] ?? null,
            'ai_max_price'  => $aiResult['max_price'] ?? null,
            'ai_price'      => $aiResult['recommended_price'] ?? null,
            'ai_confidence' => $aiResult['confidence'] ?? null,
            'ai_reasoning'  => $aiResult['reasoning'] ?? null,
        ]);
    }

// ===============================
// إرسال للأخصائيين للتقييم
// ===============================
private function sendToExperts(Order $order): void
{
    $order->update([
        'status' => 'beingEstimated',
        'expert_evaluated' => 0,
    ]);

    // مثال: اختيار أول خبير (يمكن تعديل حسب المنطق لديك)
    $expert = \App\Models\User::role('expert')->first();
    if ($expert) {
        $order->update([
            'expert_id' => $expert->id
        ]);

        // إرسال Notification للخبير
        $expert->notify(new \App\Notifications\OrderAssignedToExpert($order));
    }

    // إرسال Notification للمستخدم
    $order->user->notify(new \App\Notifications\OrderSentForExpertEvaluation($order));

    Log::info("Order sent to expert", [
        'order_id' => $order->id,
        'expert_id' => $order->expert_id ?? null
    ]);
}

// ===============================
// تثمين ثمن المنتج
// ===============================
private function runPricingEvaluation(Order $order): void
{
    // مثال: حساب متوسط بين AI و Expert إذا متوفرين
    $aiPrice = $order->ai_price ?? null;
    $expertPrice = $order->expert_price ?? null;

    $thamnPrice = null;

    if ($aiPrice && $expertPrice) {
        $thamnPrice = round(($aiPrice + $expertPrice) / 2, 2);
    } elseif ($aiPrice) {
        $thamnPrice = $aiPrice;
    } elseif ($expertPrice) {
        $thamnPrice = $expertPrice;
    }

    $order->update([
        'thamn_price' => $thamnPrice,
        'thamn_by' => auth()->id() ?? null,
        'thamn_at' => now(),
        'status' => 'beingEstimated',

    ]);

    // إرسال Notification للمستخدم
    $order->user->notify(new \App\Notifications\OrderThamnPriceCalculated($order));

    Log::info("Thamn price calculated", [
        'order_id' => $order->id,
        'thamn_price' => $thamnPrice
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
