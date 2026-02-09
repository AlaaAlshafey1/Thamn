<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\TapPayment;
use App\Services\TapPaymentService;
use App\Services\OpenAIService;
use App\Services\ThamnEvaluationService;
use Illuminate\Support\Facades\Log;
use App\Notifications\OrderReadyForExpertsNotification;
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
        $fcmToken = $order->user->fcm_token ?? $order->user->fcm_token_android ?? $order->user->fcm_token_ios;
        if ($fcmToken) {
            $this->notifyByFirebase(
                lang('تم استلام طلبك', 'Order Received', $request),
                lang('بدأنا العمل على طلب التقييم رقم ' . $order->id, 'We started working on evaluation order #' . $order->id, $request),
                [$fcmToken],
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
                    app(ThamnEvaluationService::class)->runAiEvaluation($order);
                    break;

                case 'expert':
                    $this->sendToExperts($order); // هنعملها بعدين
                    break;

                case 'best':
                    app(ThamnEvaluationService::class)->runThamnValuation($order); // هنعملها بعدين
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
        app(ThamnEvaluationService::class)->runAiEvaluation($order);
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

        // Broadcast to ALL Experts
        $experts = \App\Models\User::role('expert')->get();
        foreach ($experts as $expert) {
            $expert->notify(new OrderReadyForExpertsNotification($order));

            // Push Notification to Expert
            $expertToken = $expert->fcm_token ?? $expert->fcm_token_android ?? $expert->fcm_token_ios;
            if ($expertToken) {
                $this->notifyByFirebase(
                    'طلب تقييم جديد متاح',
                    'يوجد طلب تقييم جديد رقم ' . $order->id . ' متاح الآن في السوق.',
                    [$expertToken],
                    ['data' => ['user_id' => $expert->id, 'order_id' => $order->id, 'type' => 'new_expert_order']]
                );
            }
        }

        // إرسال Notification للمستخدم
        $order->user->notify(new \App\Notifications\OrderSentForExpertEvaluation($order));

        // Push Notification to User: We will get back to you soon
        $userToken = $order->user->fcm_token ?? $order->user->fcm_token_android ?? $order->user->fcm_token_ios;
        if ($userToken) {
            $this->notifyByFirebase(
                lang('تم تحويل طلبك للخبراء', 'Order sent to experts', request()),
                lang('طلبك رقم ' . $order->id . ' قيد المراجعة الآن من قبل خبرائنا، سنقوم بالرد عليك في أقرب وقت', 'Your order #' . $order->id . ' is now being reviewed by our experts, we will get back to you as soon as possible', request()),
                [$userToken],
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
        app(ThamnEvaluationService::class)->runThamnValuation($order);
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
