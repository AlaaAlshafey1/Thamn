<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderFiles;
use App\Mail\ValuationResultMail;
use App\Services\ClaudeService;
use App\Http\Traits\FCMOperation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ThamnEvaluationService
{
    use FCMOperation;

    /**
     * Run AI Evaluation with Vision support
     */
    public function runAiEvaluation(Order $order): array
    {
        // ─── بناء ملخص الأسئلة والأجوبة ───────────────────────────────────────
        $qaLines = [];
        foreach ($order->details as $detail) {
            $question = $detail->question->question_ar
                ?? $detail->question->question_en
                ?? null;

            $answer = $detail->option->option_ar
                ?? $detail->option->option_en
                ?? $detail->value
                ?? null;

            if ($question && $answer) {
                $qaLines[] = "• {$question}: {$answer}";
            }
        }
        $qaText = implode("\n", $qaLines) ?: 'لا توجد بيانات وصفية مرفقة.';

        // ─── معالجة الصور ──────────────────────────────────────────────────────
        $imageFiles = $order->files->where('type', 'image');
        $hasImages  = $imageFiles->isNotEmpty();
        $imageCount = $imageFiles->count();

        // ─── جلب السياق المتخصص للفئة باستخدام Strategy Pattern (OCP) ──────────
        $categoryAr = $order->category->name_ar ?? $order->category->name_en ?? 'غير محدد';
        $categoryEn = $order->category->name_en ?? $order->category->name_ar ?? 'unknown';
        
        $resolver = new \App\Services\Evaluation\CategoryContextResolver();
        $ctx = $resolver->resolve($categoryEn, $categoryAr);

        $imageSection = $hasImages
            ? "يوجد ({$imageCount}) صورة مرفقة. قم بتحليل كل صورة بعناية:\n  - قيّم الحالة العامة (ممتازة / جيدة جداً / جيدة / مقبولة / رديئة).\n  - حدد أي عيوب ظاهرة (خدوش، كسور، بهتان، تلف).\n  - لاحظ مدى نظافة وصيانة السلعة.\n  - عامل الصور كدليل أساسي لتعديل السعر لأعلى أو لأسفل.\n{$ctx->getImageAnalysisTips()}"
            : "لا توجد صور مرفقة. اعتمد فقط على البيانات الوصفية، مع افتراض حالة (متوسطة إلى جيدة) وليس ممتازة — انظر تعليمات غياب الصور أدناه.";

        $noImageSection = !$hasImages ? "
━━━ تعليمات خاصة بغياب الصور (إلزامية) ━━━
1. افترض أن حالة السلعة (متوسطة إلى جيدة) لا (ممتازة) — غياب الصور يعني عدم التحقق من الحالة الفعلية.
2. لا ترفع السعر بسبب تخمين إيجابي — الأصل التحفظ وليس المبالغة.
3. {$ctx->getNoImageDataTips()}
4. قارن بمتوسط الأسعار في السوق السعودي لنفس المنتج، وخذ المتوسط لا الأعلى.
5. confidence يجب أن يكون بين 0.4 و 0.65 كحد أقصى عند غياب الصور.
6. لا تبالغ في التسعير — العميل يريد سعراً عادلاً يعكس الواقع السوقي." : "";

        // ─── بناء الـ Prompt ────────────────────────────────────────────────────
        $today = now()->locale('ar')->translatedFormat('d F Y');

        $reEvaluationNote = $order->re_evaluation_count > 0 
            ? "تنبيه هام جداً: العميل قدم احتجاجاً على التقييم السابق لأنه يرى أن القيمة التي وضعتها غير عادلة أو غير صحيحة. يرجى إعادة النظر بدقة ومراجعة كافة التفاصيل والصور من جديد وتعديل السعر ليكون أكثر إنصافاً ودقة حسب السوق الحالي. واذكر للعميل صراحة في بداية الـ reasoning أنك قمت بإعادة التقييم والمراجعة." 
            : "";

        $prompt = <<<PROMPT
{$ctx->getRole()}
تاريخ التقييم: {$today}
{$ctx->getMarketReferences()}
{$reEvaluationNote}

━━━ بيانات السلعة ━━━
الفئة: {$categoryAr}
{$qaText}

━━━ تعليمات خاصة بهذه الفئة ({$categoryAr}) ━━━
{$ctx->getPricingTips()}

━━━ تعليمات الصور ━━━
{$imageSection}
{$noImageSection}

━━━ قواعد التسعير الإلزامية ━━━
1. أرجع أرقاماً حقيقية قابلة للمقارنة بأسعار السوق السعودي اليوم — لا أصفاراً ولا قيماً وهمية.
2. قاعدة الفجوة (إلزامية رياضياً):
   - احسب recommended_price أولاً.
   - ثم: min_price = round(recommended_price × 0.93)
   - ثم: max_price = round(recommended_price × 1.07)
   - هذا يضمن أن الفجوة بين min و max لا تتجاوز 14% دائماً.
3. recommended_price يجب أن يقع دائماً بين min_price و max_price.
4. confidence يعبّر عن مدى ثقتك بالتقييم من 0.0 إلى 1.0 (1.0 = ثقة تامة بوجود صور وبيانات كاملة).
5. إذا كانت البيانات غير كافية لتقييم دقيق، خفّض confidence فقط — لا تغيّر منطق الفجوة.
6. استخرج أبرز 6 خصائص للسلعة كـ features بناء على البيانات.
7. يجب أن تكون جميع الردود والقيم المستخرجة (بما فيها reasoning و features) باللغة العربية الفصحى حصراً.
8. إذا لم توجد صور للسلعة، قم بإنشاء وصف دقيق (باللغة الإنجليزية) لصورة واقعية بخلفية بيضاء في حقل image_prompt.
9. منهجية التسعير الصحيحة:
   - قارن بمتوسط الـ 50% الوسطى من الإعلانات المشابهة (تجاهل الأعلى 25% والأدنى 25%).
   - لا تأخذ أرخص سعر ولا أغلى سعر — خذ المتوسط العادل.
   - السعر العادل هو ما يرضى به بائع معقول ومشتري معقول في السوق السعودي اليوم.
   - غياب الصور لا يعني انخفاض السعر — يعني فقط انخفاض الـ confidence.
10. إذا كان بالمنتج عيوب أو حوادث موثّقة، اخصم من recommended_price بشكل واضح ومناسب لحجم العيب، ثم احسب min/max بالصيغة أعلاه.

━━━ هيكل الرد (JSON فقط — لا نص خارجه) ━━━
{
  "min_price": <رقم صحيح بالريال السعودي>,
  "max_price": <رقم صحيح بالريال السعودي>,
  "recommended_price": <رقم صحيح بالريال السعودي>,
  "currency": "SAR",
  "confidence": <رقم عشري من 0.0 إلى 1.0>,
  "reasoning": "<تحليل احترافي مباشر: سبب التسعير، المراجع السوقية المستخدمة، تأثير الحالة، مقارنة السوق، وأثر الصور إن وجدت — بلا عبارات احتمالية>",
  "features": ["خاصية 1", "خاصية 2", "خاصية 3", "خاصية 4", "خاصية 5", "خاصية 6"],
  "image_prompt": "<وصف بالإنجليزية للصورة إن لم تكن هناك صور، أو null>"
}
PROMPT;

        // ─── مسارات الصور على الـ Server ───────────────────────────────────────
        $imagePaths = $imageFiles
            ->map(fn($f) => storage_path('app/public/' . $f->file_path))
            ->filter(fn($path) => file_exists($path))   // تأكد أن الملف موجود فعلاً
            ->values()
            ->toArray();

        $aiResult = app(ClaudeService::class)->evaluateProduct($prompt, $imagePaths);

        $order->update([
            // التثمين الذكي المنفرد → تم التثمين مباشرة
            // التثمين الاحترافي/الهجين → يفضل في beingEstimated لحد ما الخبير يقيم ثم الأدمن يوافق
            'status' => $order->evaluation_type === 'ai'
                ? ($order->status === 'beingReEstimated' ? 'reEstimated' : 'estimated')
                : ($order->status === 'beingReEstimated' ? 'beingReEstimated' : 'beingEstimated'),
            'ai_min_price' => $aiResult['min_price'] ?? null,
            'ai_max_price' => $aiResult['max_price'] ?? null,
            'ai_price'     => $aiResult['recommended_price'] ?? null,
            'total_price'  => $aiResult['recommended_price'] ?? null, // sync total_price with AI result
            'ai_confidence'=> $aiResult['confidence'] ?? null,
            'ai_reasoning' => $aiResult['reasoning'] ?? null,
            'ai_features'  => $aiResult['features'] ?? null,
            'evaluated_at' => $order->evaluated_at ?? now(),
        ]);

        // Generate Virtual Image if no images exist and prompt provided
        if (!$hasImages && !empty($aiResult['image_prompt'])) {
            try {
                $imageUrl = app(ClaudeService::class)->generateImage($aiResult['image_prompt']);
                if ($imageUrl) {
                    $imageContents = file_get_contents($imageUrl);
                    $filename = 'ai_generated_' . Str::random(10) . '.png';
                    $path = 'orders/images/' . $filename;
                    
                    Storage::disk('public')->put($path, $imageContents);

                    OrderFiles::create([
                        'order_id' => $order->id,
                        'file_path' => $path,
                        'file_name' => $filename,
                        'type' => 'image',
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('Failed to generate virtual image via DALL-E', ['error' => $e->getMessage()]);
            }
        } // end if (!$hasImages)

        $tokens = $order->user->getFcmTokens();

        if ($order->evaluation_type === 'ai') {
            // ── التثمين الذكي: بشّر العميل فقط — لا رسائل للأدمن ──────────
            if (!empty($tokens)) {
                $this->notifyByFirebase(
                    lang('اكتمل تثمين منتجك 🎉', 'Your evaluation is ready! 🎉', request()),
                    lang("تم تثمين منتجك رقم #{$order->id} بنجاح. تفضل اطلع على النتيجة الآن.", "Your product #{$order->id} has been evaluated. Check the result now!", request()),
                    $tokens,
                    ['data' => ['user_id' => $order->user_id, 'order_id' => $order->id, 'type' => 'order_evaluated_ai']]
                );
            }

            // Email to Customer
            try {
                if ($order->user?->email) {
                    Mail::to($order->user->email)->send(new ValuationResultMail($order, 'ai'));
                }
            } catch (\Throwable $e) {
                Log::error('AI Valuation Customer Email Failed: ' . $e->getMessage());
            }

        } else {
            // ── التثمين الاحترافي/الهجين: اطلب من العميل الانتظار فقط ──

            // FCM: اطلب من العميل الانتظار
            if (!empty($tokens)) {
                $this->notifyByFirebase(
                    lang('أوشكنا على النهاية ⏳', 'Almost done ⏳', request()),
                    lang('أوشكنا على النهاية، أرجو منك الصبر. طلبك الآن في المراجعة النهائية.', 'We are almost done, please be patient. Your order is in final review.', request()),
                    $tokens,
                    ['data' => ['user_id' => $order->user_id, 'order_id' => $order->id, 'type' => 'order_waiting_admin']]
                );
            }
        }

        return $aiResult;
    }

    /**
     * Calculate Thamn (Best) Fair Price
     */
    public function runThamnValuation(Order $order): void
    {
        // Based on AI and Expert valuations if available
        $aiPrice = $order->ai_price;
        $expertPrice = $order->expert_price;

        $thamnPrice = null;
        $minPrice = null;
        $maxPrice = null;

        if ($aiPrice && $expertPrice) {
            $thamnPrice = round(($aiPrice + $expertPrice) / 2, 2);
            $minPrice = max(0, $thamnPrice - 5000);
            $maxPrice = $thamnPrice + 5000;
        } elseif ($aiPrice) {
            $thamnPrice = $aiPrice;
            $minPrice = $order->ai_min_price;
            $maxPrice = $order->ai_max_price;
        } elseif ($expertPrice) {
            $thamnPrice = $expertPrice;
            $minPrice = $order->expert_min_price;
            $maxPrice = $order->expert_max_price;
        }

        $order->update([
            'thamn_price' => $thamnPrice,
            'thamn_min_price' => $minPrice,
            'thamn_max_price' => $maxPrice,
            'thamn_by' => auth()->id() ?? null,
            'thamn_at' => now(),
            // 'status' => 'estimated', // Set to estimated when result is ready
        ]);

        // Send Notification if price is now calculated
        if ($thamnPrice) {
            $user = $order->user;
            $tokens = $user->getFcmTokens();
            if (!empty($tokens)) {
                $this->notifyByFirebase(
                    lang('تم حساب السعر العادل', 'Fair Price Calculated', request()),
                    lang('لقد قام فريق ثمن بحساب السعر العادل لمنتجك: ' . $thamnPrice . ' ريال', 'Thamn team calculated the fair price for your product: ' . $thamnPrice . ' SAR', request()),
                    $tokens,
                    ['data' => ['user_id' => $order->user_id, 'order_id' => $order->id, 'type' => 'thamn_ready']]
                );
            }

            // Send Email with Thamn Valuation Result
            try {
                if ($user->email) {
                    Mail::to($user->email)->send(new ValuationResultMail($order, 'thamn'));
                }
            } catch (\Throwable $e) {
                Log::error('Thamn Valuation Result Email Failed: ' . $e->getMessage());
            }
        }
    }

    // Note: FCM notification is sent by the caller (OrderController::aiEvaluate or PaymentController)
    // to avoid duplicate notifications.

    /**
     * Notify all experts and admins about a new Professional Valuation order.
     */
    public function sendBestOrderToExperts(Order $order): void
    {
        $order->update([
            'status' => 'beingEstimated',
            'expert_evaluated' => 0,
        ]);

        $whatsapp = app(\App\Services\WhatsAppService::class);

        // Notify ALL Experts via Email, WhatsApp, and DB Notification
        $experts = \App\Models\User::role('expert')->get();
        foreach ($experts as $expert) {
            // Send WhatsApp
            if ($expert->phone) {
                $whatsapp->sendMessage(
                    $expert->phone,
                    "هلا بك خبير 👋 وصل طلب تثمين احترافي جديد رقم {$order->id} وهو متاح الآن في منصة الخبراء في ثمن. نرجو منك الدخول وتقييم الطلب في أسرع وقت."
                );
            }
            // Send Email
            if ($expert->email) {
                try {
                    Mail::to($expert->email)->send(new \App\Mail\SystemNotificationMail(
                        "طلب تثمين احترافي جديد بانتظارك رقم #{$order->id}",
                        "هلا بك خبير 👋 وصل طلب تثمين احترافي جديد رقم {$order->id} وهو متاح الآن في منصة الخبراء في ثمن. نرجو منك الدخول وتقييم الطلب في أسرع وقت.",
                        route('orders.show', $order->id)
                    ));
                } catch (\Throwable $e) {
                    Log::error("Failed to send expert email for best order: " . $e->getMessage());
                }
            }
        }
    }
}
