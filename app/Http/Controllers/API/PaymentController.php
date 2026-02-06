<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\TapPayment;
use App\Services\TapPaymentService;
use App\Services\OpenAIService;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\FCMOperation;

class PaymentController extends Controller
{
    use FCMOperation;
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
                'status' => false,
                'message' => 'قيمة الطلب غير صالحة للدفع'
            ], 400);
        }

        $customerName = $order->user->name ?? 'Unknown Customer';
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
            'order_id' => $order->id,
            'charge_id' => $payment['id'] ?? null,
            'amount' => $amount,
            'status' => $payment['status'] ?? 'INITIATED',
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

        // Send FCM: Order Received
        if ($order->user->fcm_token_android || $order->user->fcm_token_ios) {
            $tokens = array_filter([$order->user->fcm_token_android, $order->user->fcm_token_ios]);
            $this->notifyByFirebase(
                lang('تم استلام طلبك', 'Order Received', $request),
                lang('بدأنا العمل على طلب التقييم رقم ' . $order->id, 'We started working on evaluation order #' . $order->id, $request),
                $tokens,
                ['data' => ['user_id' => $order->user_id, 'order_id' => $order->id, 'type' => 'order_received']]
            );
        }


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
        if ($tap_pay->status == 'INITIATED') {
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
            $question = $detail->question->name_ar ?? null;
            $answer = $detail->option->name_ar ?? $detail->value ?? null;

            if ($question && $answer) {
                $qaText .= "- {$question}: {$answer}\n";
            }
        }

        $prompt = <<<PROMPT
أنت خبير تثمين محترف (Appraiser) معتمد في منطقة الخليج العربي، وتحديداً في المملكة العربية السعودية. مهمتك هي تقديم تثمين دقيق وواقعي للسلعة بناءً على البيانات المقدمة.

السياق:
- الدولة: المملكة العربية السعودية (KSA)
- العملة: ريال سعودي (SAR)
- الفئة المختارة: {$order->category->name_ar}

البيانات المقدمة من المستخدم:
{$qaText}

المطلوب:
1. تحليل السلعة بناءً على ندرتها، حالتها، الطلب الحالي عليها في السوق السعودي (مثل حراج، منصات البيع الفاخرة، أو أسواق المستعمل).
2. تقديم ثلاثة قيم:
   - "min_price": الحد الأدنى للسعر في حالة البيع السريع.
   - "max_price": الحد الأعلى للسعر الذي يمكن أن تصل إليه السلعة في حالة ممتازة ومشتري مهتم.
   - "recommended_price": السعر العادل (Fair Market Value) الذي تنصح به للبيع.
3. كتابة "reasoning" (السبب) باللغة العربية بأسلوب احترافي يشرح العوامل التي أثرت على هذا التقييم (مثل: الحالة، البراند، اتجاهات السوق).

الشروط:
- الرد يجب أن يكون بصيغة JSON فقط.
- ممنوع كتابة أي كلمات خارج ملف JSON.
- تأكد من أن الأسعار واقعية وبالريال السعودي.

{
"min_price": number,
"max_price": number,
"recommended_price": number,
"currency": "SAR",
"confidence": number (from 0 to 100),
"reasoning": "شرح احترافي بالعربية"
}
PROMPT;

        $aiResult = app(OpenAIService::class)->evaluateProduct($prompt);

        $order->update([
            'status' => "estimated",
            'ai_min_price' => $aiResult['min_price'] ?? null,
            'ai_max_price' => $aiResult['max_price'] ?? null,
            'ai_price' => $aiResult['recommended_price'] ?? null,
            'ai_confidence' => $aiResult['confidence'] ?? null,
            'ai_reasoning' => $aiResult['reasoning'] ?? null,
        ]);

        // Send FCM: Evaluation Ready
        $user = $order->user;
        if ($user->fcm_token_android || $user->fcm_token_ios) {
            $tokens = array_filter([$user->fcm_token_android, $user->fcm_token_ios]);
            $this->notifyByFirebase(
                lang('تم تقييم منتجك', 'Product Evaluated', request()),
                lang('التقييم الذكي لطلبك رقم ' . $order->id . ' جاهز الآن', 'AI Evaluation for your order #' . $order->id . ' is now ready', request()),
                $tokens,
                ['data' => ['user_id' => $user->id, 'order_id' => $order->id, 'type' => 'evaluation_ready']]
            );
        }
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

            // Push Notification to Expert
            if ($expert->fcm_token_android || $expert->fcm_token_ios) {
                $tokens = array_filter([$expert->fcm_token_android, $expert->fcm_token_ios]);
                $this->notifyByFirebase(
                    'طلب تقييم جديد',
                    'لديك طلب تقييم جديد رقم ' . $order->id,
                    $tokens,
                    ['data' => ['user_id' => $expert->id, 'order_id' => $order->id, 'type' => 'new_expert_order']]
                );
            }
        }

        // إرسال Notification للمستخدم
        $order->user->notify(new \App\Notifications\OrderSentForExpertEvaluation($order));

        // Push Notification to User: We will get back to you soon
        if ($order->user->fcm_token_android || $order->user->fcm_token_ios) {
            $tokens = array_filter([$order->user->fcm_token_android, $order->user->fcm_token_ios]);
            $this->notifyByFirebase(
                lang('تم تحويل طلبك للخبراء', 'Order sent to experts', request()),
                lang('طلبك رقم ' . $order->id . ' قيد المراجعة الآن من قبل خبرائنا، سنقوم بالرد عليك في أقرب وقت', 'Your order #' . $order->id . ' is now being reviewed by our experts, we will get back to you as soon as possible', request()),
                $tokens,
                ['data' => ['user_id' => $order->user_id, 'order_id' => $order->id, 'type' => 'expert_pending']]
            );
        }

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
            'thamn_min_price' => $order->ai_min_price ?? $order->expert_min_price,
            'thamn_max_price' => $order->ai_max_price ?? $order->expert_max_price,
            'thamn_by' => auth()->id() ?? null,
            'thamn_at' => now(),
            'status' => 'beingEstimated',
        ]);

        // إرسال Notification للمستخدم
        $order->user->notify(new \App\Notifications\OrderThamnPriceCalculated($order));

        // Push Notification to User
        if ($order->user->fcm_token_android || $order->user->fcm_token_ios) {
            $tokens = array_filter([$order->user->fcm_token_android, $order->user->fcm_token_ios]);
            $this->notifyByFirebase(
                lang('تم استلام طلب تقييم ثمن', 'Thamn request received', request()),
                lang('طلبك رقم ' . $order->id . ' قيد المراجعة، سيتم تزويدك بالتقييم النهائي قريباً', 'Your order #' . $order->id . ' is under review, final evaluation will be provided soon', request()),
                $tokens,
                ['data' => ['user_id' => $order->user_id, 'order_id' => $order->id, 'type' => 'thamn_pending']]
            );
        }

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
