<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\TapPayment;
use App\Services\TapPaymentService;
use App\Services\OpenAIService;
use App\Services\ThamnEvaluationService;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Mail;
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
        $order = Order::with(['user', 'details.question', 'details.option', 'files'])->findOrFail($order_id);
        $amount = (float) $order->total_price;

        // لو السعر = 0 (مثلاً بعد كنسل دفع سابق)، نحسبه تاني من إجابات الأوردر
        if ($amount <= 0) {
            $rateTypeAnswer = $order->details()
                ->whereHas('question', function ($q) {
                    $q->where('type', 'rateTypeSelection');
                })
                ->first();

            if ($rateTypeAnswer && $rateTypeAnswer->option) {
                $amount = (float) $rateTypeAnswer->option->price;
            }

            // نضيف رسوم الصورة لو مفيش صورة مرفوعة
            if ($order->files->where('type', 'image')->count() === 0) {
                $amount += (float) env('IMAGE_GENERATION_FEE', 5);
            }

            // لو لسه 0 بعد إعادة الحساب → رفض
            if ($amount <= 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'قيمة الطلب غير صالحة للدفع'
                ], 400);
            }

            // نحفظ السعر الصحيح في الـ DB عشان المرة الجاية
            $order->update(['total_price' => $amount]);
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
            'category',
            'files',   // ← مهم: الصور لازم تكون محملة عشان الـ AI يشوفها
            'user',    // ← مهم: للإشعارات
        ])->findOrFail($payment->order->id);

        $order->update([
            'status' => $payment->status,
        ]);

        // لو الدفع ما اكتمل بنجاح، ابعت إشعار للمستخدم وارجع
        if ($payment->status !== 'orderReceived') {
            $fcmToken = $order->user->fcm_token ?? $order->user->fcm_token_android ?? $order->user->fcm_token_ios;
            if ($fcmToken) {
                $this->notifyByFirebase(
                    lang('لم يتم الدفع', 'Payment Not Completed', $request),
                    lang(
                        'لم تكتمل عملية الدفع لطلبك رقم ' . $order->id . '. يمكنك المحاولة مرة أخرى.',
                        'Payment for order #' . $order->id . ' was not completed. You can try again.',
                        $request
                    ),
                    [$fcmToken],
                    ['data' => ['user_id' => $order->user_id, 'order_id' => $order->id, 'type' => 'payment_failed']]
                );
            }
            return response()->json($statusResponse);
        }

        // Send FCM: Order Received
        $this->notifyOrderReceived($order, $request);

        // Send FCM & Email & WhatsApp: Payment Success & Invoice
        try {
            $whatsapp = app(\App\Services\WhatsAppService::class);

            // Notify Customer
            $customerMsg = \App\Services\WhatsAppService::getTemplate('order_paid_customer', ['id' => $order->id]);
            $whatsapp->sendMessage($order->user->phone, $customerMsg);

            // Notify Experts of same category
            $experts = \App\Models\User::role('expert')
                ->where('category_id', $order->category_id)
                ->get();

            $expertMsg = \App\Services\WhatsAppService::getTemplate('new_order_expert', [
                'category' => $order->category->name_ar ?? $order->category->name_en
            ]);

            foreach ($experts as $expert) {
                if ($expert->phone) {
                    $whatsapp->sendMessage($expert->phone, $expertMsg);
                }
                // Notify Expert via Email
                Mail::to($expert->email)->send(new \App\Mail\SystemNotificationMail(
                    'جاك رزق! طلب تثمين جديد بقسمك',
                    "يا خبيرنا، فيه طلب تثمين جديد بقسمك لا يفوتك.\nادخل على لوحة التحكم واستلم الطلب الحين.",
                    route('orders.index')
                ));
            }

            // Notify Customer via Email
            Mail::to($order->user->email)->send(new \App\Mail\SystemNotificationMail(
                'وصلنا مبلغك.. وجاري العمل على طلبك!',
                "يا هلا والله! استلمنا مبلغك لطلبك رقم {$order->id}.\nطلبك الحين عند أفضل خبرائنا، خلك قريب وبنبشرك بالنتيجة.",
                route('orders.show', $order->id)
            ));

            // Notify Admin
            $adminEmail = 'thmmnapplic@gmail.com';
            Mail::to($adminEmail)->send(new \App\Mail\SystemNotificationMail(
                'يا مدير، فيه طلب تثمين جديد اندفع!',
                "بشرى سارة! العميل {$order->user->first_name} دفع قيمة طلب التثمين رقم {$order->id}.\nشيك على الطلب في لوحة التحكم.",
                route('orders.show', $order->id)
            ));

            // تم مسح الإشعار المكرر الثابت بالعربي من هنا، لأن الدالة notifyOrderReceived بتعمل نفس الشيء مع دعم اللغتين
            Mail::to($order->user->email)->send(new InvoiceMail($order));
        } catch (\Exception $e) {
            \Log::error('Payment Success Notification/Mail Failed: ' . $e->getMessage());
        }
        $rateTypeAnswer = $order->details()->whereHas('question', function ($q) { $q->where('type', 'rateTypeSelection'); })->first();
        $evaluationType = $rateTypeAnswer?->option?->badge ?? $rateTypeAnswer?->value;

        // توليد صورة ديناميكية إذا لم يرفع العميل صورة (فقط للخبراء وثمن، أما الـ AI فيولدها بنفسه من الـ prompt الخاص به)
        if ($payment->status === 'orderReceived' && $evaluationType !== 'ai' && $order->files->where('type', 'image')->count() === 0) {
            try {
                $qaLines = [];
                foreach ($order->details as $detail) {
                    $question = $detail->question->question_en ?? $detail->question->question_ar;
                    $answer = $detail->option->option_en ?? $detail->option->option_ar ?? $detail->value;
                    if ($question && $answer) {
                        $qaLines[] = "{$question}: {$answer}";
                    }
                }
                $qaText = implode(", ", $qaLines);
                $category = $order->category->name_en ?? 'product';

                $prompt = "A highly realistic, professional studio photograph of a {$category} with the following specifications: {$qaText}. Pure white background, centered, well lit, high quality.";
                
                $imageUrl = app(OpenAIService::class)->generateImage($prompt);
                if ($imageUrl) {
                    $imageContents = file_get_contents($imageUrl);
                    $filename = 'ai_generated_auto_' . \Illuminate\Support\Str::random(10) . '.png';
                    $path = 'orders/images/' . $filename;
                    
                    \Illuminate\Support\Facades\Storage::disk('public')->put($path, $imageContents);

                    \App\Models\OrderFiles::create([
                        'order_id' => $order->id,
                        'file_path' => $path,
                        'file_name' => $filename,
                        'type' => 'image',
                    ]);
                    
                    // Reload order files relation
                    $order->load('files');
                }
            } catch (\Exception $e) {
                Log::error('Dynamic Image Generation Failed in Callback: ' . $e->getMessage());
            }
        }

        $this->handleEvaluationRouting($order);

        return response()->json($statusResponse);
    }

    /**
     * Notify user that order is received
     */
    private function notifyOrderReceived(Order $order, Request $request): void
    {
        $fcmToken = $order->user->fcm_token ?? $order->user->fcm_token_android ?? $order->user->fcm_token_ios;
        if ($fcmToken) {
            $this->notifyByFirebase(
                lang('تم استلام طلبك', 'Order Received', $request),
                lang('بدأنا العمل على طلب التقييم رقم ' . $order->id, 'We started working on evaluation order #' . $order->id, $request),
                [$fcmToken],
                ['data' => ['user_id' => $order->user_id, 'order_id' => $order->id, 'type' => 'order_received']]
            );
        }
    }

    /**
     * Route order to appropriate evaluation service
     */
    private function handleEvaluationRouting(Order $order): void
    {
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
                    $this->sendToExperts($order);
                    break;

                case 'best':
                    $this->runAiEvaluation($order);
                    app(\App\Services\ThamnEvaluationService::class)->sendBestOrderToExperts($order);
                    break;

                default:
                    Log::warning('Unknown evaluation type, defaulting to Expert', [
                        'order_id' => $order->id,
                        'evaluation_type' => $evaluationType
                    ]);
                    $this->sendToExperts($order);
            }

            // Note: No extra FCM sent here — each evaluation type (AI/expert/best)
            // sends its own result notification when the evaluation is complete.

        } catch (\Throwable $e) {
            Log::error('Evaluation Routing Failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
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
                    lang('طلب تقييم جديد متاح', 'New Evaluation Request Available', request()),
                    lang('يوجد طلب تقييم جديد رقم ' . $order->id . ' متاح الآن في السوق.', 'A new evaluation request #' . $order->id . ' is now available.', request()),
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
