<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\TapPayment;
use App\Services\MoyasarPaymentService;
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

    private MoyasarPaymentService $moyasarPaymentService;
    private TapPaymentService     $tapPaymentService; // نبقيه للاستعلام عن المدفوعات القديمة

    public function __construct(
        MoyasarPaymentService $moyasarPaymentService,
        TapPaymentService     $tapPaymentService
    ) {
        $this->moyasarPaymentService = $moyasarPaymentService;
        $this->tapPaymentService     = $tapPaymentService;
    }

    // ===============================
    // إنشاء عملية الدفع (Moyasar)
    // ===============================
    public function payOrder(Request $request, $order_id)
    {
        $order = Order::with(['user', 'details.question', 'details.option', 'files'])->findOrFail($order_id);
        $amount = (float) $order->total_price;

        // لو السعر = 0، نحسبه من إجابات الأوردر
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

            if ($amount <= 0) {
                return response()->json([
                    'status'  => false,
                    'message' => 'قيمة الطلب غير صالحة للدفع'
                ], 400);
            }

            $order->update(['total_price' => $amount]);
        }

        // ── إرجاع رابط مشفر ومحمي لصفحة الدفع ──
        // الرابط هيكون مؤمن بـ Signature وصالح لمدة 30 دقيقة فقط، محدش يقدر يفتحه غير اللي معاه الرابط بالضبط
        $checkoutUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'moyasar.checkout',
            now()->addMinutes(30),
            ['orderId' => $order->id]
        );

        // نقوم بمحاكاة هيكل استجابة Tap (JSON) حتى لا يتعطل الموبايل أثناء قراءة الرابط
        return response()->json([
            'id'          => 'chg_moyasar_' . $order->id,
            'status'      => 'INITIATED',
            'transaction' => [
                'url' => $checkoutUrl
            ],
            // إبقاء الحقول الإضافية للتوافق
            'payment_url' => $checkoutUrl,
            'order_id'    => $order->id,
            'amount'      => $amount,
        ]);
    }

    // ===============================
    // Moyasar Webhook (Server → Server)
    // يُستدعى من Moyasar تلقائياً عند تغيير حالة الدفع
    // ===============================
    public function moyasarWebhook(Request $request)
    {
        // ── التحقق من التوقيع ──────────────────────────────
        $rawBody  = $request->getContent();
        $signature = $request->header('moyasar-signature', '');

        if (!$this->moyasarPaymentService->verifyWebhookSignature($rawBody, $signature)) {
            Log::warning('[Moyasar Webhook] Invalid signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = $request->all();

        Log::info('[Moyasar Webhook] Received', ['data' => $data]);

        // ── استخراج البيانات ───────────────────────────────
        $type      = $data['type']    ?? null;  // payment_paid, payment_failed, etc.
        $payment   = $data['data']    ?? [];
        $paymentId = $payment['id']   ?? null;
        $status    = strtolower($payment['status'] ?? 'failed'); // paid, failed, authorized

        if (!$paymentId) {
            return response()->json(['error' => 'Missing payment ID'], 400);
        }

        // ── جلب سجل الدفع أو إنشاؤه من الـ Metadata ────────────────────────────
        $orderId = $payment['metadata']['order_id'] ?? null;
        if (!$orderId) {
            Log::error('[Moyasar Webhook] Missing order_id in metadata', ['payment' => $payment]);
            return response()->json(['error' => 'Missing order_id'], 400);
        }

        $tapPayment = TapPayment::firstOrCreate(
            ['charge_id' => $paymentId],
            ['order_id' => $orderId, 'status' => 'pending']
        );

        // ── تجنب المعالجة المكررة ──────────────────────────
        if ($tapPayment->status === 'orderReceived') {
            Log::info('[Moyasar Webhook] Already processed, skipping', ['payment_id' => $paymentId]);
            return response()->json(['status' => 'already_processed']);
        }

        // ── تحديث حالة الدفع ──────────────────────────────
        $orderStatus = ($status === 'paid') ? 'orderReceived' : 'failed';

        $tapPayment->update([
            'status'        => $orderStatus,
            'response_data' => json_encode($payment),
        ]);

        $order = Order::with([
            'details.question',
            'details.option',
            'category',
            'files',
            'user',
        ])->findOrFail($tapPayment->order_id);

        $order->update(['status' => $orderStatus]);

        // ── لو الدفع فشل ──────────────────────────────────
        if ($orderStatus !== 'orderReceived') {
            $tokens = $order->user->getFcmTokens();
            if (!empty($tokens)) {
                $this->notifyByFirebase(
                    lang('لم يتم الدفع', 'Payment Not Completed', $request),
                    lang(
                        'لم تكتمل عملية الدفع لطلبك رقم ' . $order->id . '. يمكنك المحاولة مرة أخرى.',
                        'Payment for order #' . $order->id . ' was not completed. You can try again.',
                        $request
                    ),
                    $tokens,
                    ['data' => ['user_id' => $order->user_id, 'order_id' => $order->id, 'type' => 'payment_failed']]
                );
            }

            return response()->json(['status' => 'failed']);
        }

        // ── الدفع نجح ─────────────────────────────────────
        $this->processSuccessfulOrder($order, $request);

        return response()->json(['status' => 'ok']);
    }

    private function processSuccessfulOrder(Order $order, Request $request)
    {
        $this->notifyOrderReceived($order, $request);
        $this->sendPaymentSuccessNotifications($order);

        // توليد صورة إن لم يرفق المستخدم صورة
        $rateTypeAnswer  = $order->details()->whereHas('question', function ($q) { $q->where('type', 'rateTypeSelection'); })->first();
        $evaluationType  = $rateTypeAnswer?->option?->badge ?? $rateTypeAnswer?->value;

        if ($evaluationType !== 'ai' && $order->files->where('type', 'image')->count() === 0) {
            $this->generateAutoImage($order);
        }

        // توجيه الطلب للتقييم المناسب
        $this->handleEvaluationRouting($order);
    }

    // ===============================
    // User Redirect بعد الدفع (صفحة وسيطة)
    // Moyasar يوجّه المستخدم هنا
    // ===============================
    public function redirect(Request $request, $orderId)
    {
        $order      = Order::findOrFail($orderId);
        $paymentId  = $request->query('id');      // Moyasar يرجع ?id=pay_...&status=...
        $statusParam = $request->query('status'); // paid / failed / authorized

        if (!$paymentId) {
            // لا يوجد payment ID → خطأ
            return response()->json([
                'status'   => false,
                'message'  => 'لم يتم العثور على بيانات الدفع.',
                'order_id' => $orderId,
            ], 400);
        }

        // التحقق الفعلي من حالة الدفع عبر API
        $statusResponse = $this->moyasarPaymentService->getPaymentStatus($paymentId);
        $actualStatus   = strtolower($statusResponse['status'] ?? 'failed');

        $tapPayment = TapPayment::where('charge_id', $paymentId)->first();

        if ($actualStatus === 'paid') {
            $freshlyProcessed = false;

            // ─── نحدث الـ DB لو الـ webhook لم يصل بعد ───
            if ($tapPayment && $tapPayment->status !== 'orderReceived') {
                $tapPayment->update([
                    'status'        => 'orderReceived',
                    'response_data' => json_encode($statusResponse),
                ]);
                $order->update(['status' => 'orderReceived']);
                $freshlyProcessed = true;
            } elseif (!$tapPayment) {
                // حالة نادرة: ما في سجل دفع أصلاً — ننشئه ونعالج الطلب
                TapPayment::create([
                    'charge_id'     => $paymentId,
                    'order_id'      => $orderId,
                    'status'        => 'orderReceived',
                    'response_data' => json_encode($statusResponse),
                ]);
                $order->update(['status' => 'orderReceived']);
                $freshlyProcessed = true;
            }

            // ─── لو معالجة جديدة → بعت الإشعارات ────────────────────
            // (لو الـ webhook سبق وعالج الطلب $freshlyProcessed = false نتجنب التكرار)
            if ($freshlyProcessed) {
                $order->load(['details.question', 'details.option', 'category', 'files', 'user']);
                $this->processSuccessfulOrder($order, $request);
            }

            // ─── نرجع للتطبيق بنجاح ───────────────────────
            return response()->json([
                'status'     => true,
                'message'    => 'تم الدفع بنجاح',
                'order_id'   => $orderId,
                'payment_id' => $paymentId,
            ]);
        }

        // الدفع فشل أو ملغى
        if ($tapPayment && $tapPayment->status !== 'orderReceived') {
            $tapPayment->update([
                'status'        => 'failed',
                'response_data' => json_encode($statusResponse),
            ]);
            $order->update(['status' => 'failed']);
        }

        return response()->json([
            'status'     => false,
            'message'    => 'لم يكتمل الدفع.',
            'order_id'   => $orderId,
            'payment_id' => $paymentId,
        ], 400);
    }

    // ===============================
    // callback (مسار Tap القديم - الموبايل بيعتمد عليه)
    // ===============================
    public function callback(Request $request)
    {
        $paymentId = $request->query('id') ?? $request->query('tap_id');
        
        if ($paymentId) {
            // جلب تفاصيل الدفع من Moyasar لاستخراج order_id من الـ metadata
            $statusResponse = $this->moyasarPaymentService->getPaymentStatus($paymentId);
            $orderId = $statusResponse['metadata']['order_id'] ?? null;

            if ($orderId) {
                // إنشاء سجل الدفع في قاعدة البيانات إن لم يكن موجوداً لتتبع المعاملة
                $tapPayment = TapPayment::firstOrCreate(
                    ['charge_id' => $paymentId],
                    ['order_id' => $orderId, 'status' => 'pending']
                );

                $order = Order::find($orderId);
                $isPaid = false;

                if ($order && $order->status !== 'orderReceived') {
                    if (strtolower($statusResponse['status'] ?? '') === 'paid') {
                        $tapPayment->update(['status' => 'orderReceived', 'response_data' => json_encode($statusResponse)]);
                        $order->update(['status' => 'orderReceived']);
                        $isPaid = true;
                        
                        // تنفيذ إشعارات الدفع وتوليد الـ AI وتوجيه الطلب فوراً
                        $this->processSuccessfulOrder($order, $request);
                    }
                } else if ($order && $order->status === 'orderReceived') {
                    $isPaid = true; // Already paid
                }

                if ($isPaid) {
                    return response()->json([
                        'status' => true,
                        'message' => 'تم الدفع بنجاح',
                        'order_id' => $orderId,
                        'payment_id' => $paymentId
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'عملية الدفع فشلت أو تم رفضها.',
                        'order_id' => $orderId,
                        'payment_id' => $paymentId
                    ]);
                }
            }
        }
        
        return response()->json(['status' => 'received']);
    }

    public function callback_error(Request $request)
    {
        return response()->json([
            'status'  => false,
            'message' => 'عملية الدفع فشلت.',
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // Private Helpers
    // ──────────────────────────────────────────────────────────

    private function notifyOrderReceived(Order $order, Request $request): void
    {
        $tokens = $order->user->getFcmTokens();
        if (!empty($tokens)) {
            $this->notifyByFirebase(
                lang('تم استلام طلبك', 'Order Received', $request),
                lang('بدأنا العمل على طلب التقييم رقم ' . $order->id, 'We started working on evaluation order #' . $order->id, $request),
                $tokens,
                ['data' => ['user_id' => $order->user_id, 'order_id' => $order->id, 'type' => 'order_received']]
            );
        }
    }

    private function sendPaymentSuccessNotifications(Order $order): void
    {
        try {
            $whatsapp = app(\App\Services\WhatsAppService::class);

            // إشعار العميل
            $customerMsg = \App\Services\WhatsAppService::getTemplate('order_paid_customer', ['id' => $order->id]);
            $whatsapp->sendMessage($order->user->phone, $customerMsg);

            // إشعار الخبراء في نفس الفئة
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
                Mail::to($expert->email)->send(new \App\Mail\SystemNotificationMail(
                    'جاك رزق! طلب تثمين جديد بقسمك',
                    "يا خبيرنا، فيه طلب تثمين جديد بقسمك لا يفوتك.\nادخل على لوحة التحكم واستلم الطلب الحين.",
                    route('orders.index')
                ));
            }

            // بريد العميل
            Mail::to($order->user->email)->send(new \App\Mail\SystemNotificationMail(
                'وصلنا مبلغك.. وجاري العمل على طلبك!',
                "يا هلا والله! استلمنا مبلغك لطلبك رقم {$order->id}.\nطلبك الحين عند أفضل خبرائنا، خلك قريب وبنبشرك بالنتيجة.",
                route('orders.show', $order->id)
            ));

            // بريد الأدمن
            Mail::to('thmmnapplic@gmail.com')->send(new \App\Mail\SystemNotificationMail(
                'يا مدير، فيه طلب تثمين جديد اندفع!',
                "بشرى سارة! العميل {$order->user->first_name} دفع قيمة طلب التثمين رقم {$order->id}.\nشيك على الطلب في لوحة التحكم.",
                route('orders.show', $order->id)
            ));

            // الفاتورة
            Mail::to($order->user->email)->send(new InvoiceMail($order));

        } catch (\Exception $e) {
            Log::error('[Moyasar] Payment success notifications failed: ' . $e->getMessage());
        }
    }

    private function generateAutoImage(Order $order): void
    {
        try {
            $qaLines = [];
            foreach ($order->details as $detail) {
                $question = $detail->question->question_en ?? $detail->question->question_ar;
                $answer   = $detail->option->option_en ?? $detail->option->option_ar ?? $detail->value;
                if ($question && $answer) {
                    $qaLines[] = "{$question}: {$answer}";
                }
            }
            $qaText   = implode(', ', $qaLines);
            $category = $order->category->name_en ?? 'product';
            $prompt   = "A highly realistic, professional studio photograph of a {$category} with the following specifications: {$qaText}. Pure white background, centered, well lit, high quality.";

            $imageUrl = app(OpenAIService::class)->generateImage($prompt);
            if ($imageUrl) {
                $imageContents = file_get_contents($imageUrl);
                $filename = 'ai_generated_auto_' . \Illuminate\Support\Str::random(10) . '.png';
                $path     = 'orders/images/' . $filename;

                \Illuminate\Support\Facades\Storage::disk('public')->put($path, $imageContents);

                \App\Models\OrderFiles::create([
                    'order_id'  => $order->id,
                    'file_path' => $path,
                    'file_name' => $filename,
                    'type'      => 'image',
                ]);

                $order->load('files');
            }
        } catch (\Exception $e) {
            Log::error('[Moyasar] Auto image generation failed: ' . $e->getMessage());
        }
    }

    private function handleEvaluationRouting(Order $order): void
    {
        try {
            $rateTypeAnswer = $order->details()
                ->whereHas('question', function ($q) {
                    $q->where('type', 'rateTypeSelection');
                })
                ->first();

            $evaluationType = $rateTypeAnswer?->option?->badge
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
                    Log::warning('[Moyasar] Unknown evaluation type, defaulting to Expert', [
                        'order_id'        => $order->id,
                        'evaluation_type' => $evaluationType,
                    ]);
                    $this->sendToExperts($order);
            }

        } catch (\Throwable $e) {
            Log::error('[Moyasar] Evaluation routing failed', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
        }
    }

    private function runAiEvaluation(Order $order): void
    {
        app(ThamnEvaluationService::class)->runAiEvaluation($order);
    }

    private function sendToExperts(Order $order): void
    {
        $order->update([
            'status'           => 'beingEstimated',
            'expert_evaluated' => 0,
        ]);

        $experts = \App\Models\User::role('expert')->get();
        foreach ($experts as $expert) {
            $expert->notify(new OrderReadyForExpertsNotification($order));

            $expertTokens = $expert->getFcmTokens();
            if (!empty($expertTokens)) {
                $this->notifyByFirebase(
                    lang('هلا بك خبير ( التثمين ) 👋 - طلب تثمين احترافي جديد 🔔', 'Hello expert (Valuation) 👋 - New Professional Valuation Request 🔔', request()),
                    lang('هلا بك خبير ( التثمين ) 👋 وصل طلب تثمين احترافي جديد رقم ' . $order->id . ' وهو متاح الآن في منصة الخبراء في ثمن. نرجو منك الدخول وتقييم الطلب في أسرع وقت.', 'Hello expert (Valuation) 👋 A new professional valuation request #' . $order->id . ' is now available. Please login and evaluate as soon as possible.', request()),
                    $expertTokens,
                    ['data' => ['user_id' => $expert->id, 'order_id' => $order->id, 'type' => 'new_expert_order']]
                );
            }
        }

        $order->user->notify(new \App\Notifications\OrderSentForExpertEvaluation($order));

        $userTokens = $order->user->getFcmTokens();
        if (!empty($userTokens)) {
            $this->notifyByFirebase(
                lang('تم تحويل طلبك للخبراء', 'Order sent to experts', request()),
                lang('طلبك رقم ' . $order->id . ' قيد المراجعة الآن من قبل خبرائنا، سنقوم بالرد عليك في أقرب وقت', 'Your order #' . $order->id . ' is now being reviewed by our experts, we will get back to you as soon as possible', request()),
                $userTokens,
                ['data' => ['user_id' => $order->user_id, 'order_id' => $order->id, 'type' => 'expert_pending']]
            );
        }

        Log::info('[Moyasar] Order sent to expert', ['order_id' => $order->id]);
    }

    // ──────────────────────────────────────────────────────────
    // للاختبار فقط
    // ──────────────────────────────────────────────────────────
    public function testAiEvaluation($orderId)
    {
        $order = Order::findOrFail($orderId);
        $this->runAiEvaluation($order);

        return response()->json([
            'status' => true,
            'order'  => $order->fresh(),
        ]);
    }

    public function success(Request $request)
    {
        $orderId = $request->query('order_id');
        $order   = Order::findOrFail($orderId);

        return response()->json([
            'status'        => true,
            'message'       => 'Payment Success',
            'order_id'      => $order->id,
            'ai_price'      => $order->ai_price,
            'ai_confidence' => $order->ai_confidence,
            'ai_reasoning'  => $order->ai_reasoning,
        ]);
    }

    public function failed()
    {
        return response()->json([
            'status'  => false,
            'message' => 'Payment Failed',
        ], 400);
    }
}
