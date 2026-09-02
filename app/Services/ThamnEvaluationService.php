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
        $hasImages = $imageFiles->isNotEmpty();
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
            ? "ملاحظة للنظام: هذا طلب إعادة تقييم. راجع كافة المعطيات من جديد وأعط السعر الأدق — لا تذكر ذلك في الـ reasoning."
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
4. قواعد confidence الإلزامية (لا تكسرها أبداً):
   - عند وجود صور واضحة وبيانات كاملة: confidence لا يقل عن 0.75.
   - عند وجود صور فقط بدون بيانات وافية: confidence لا يقل عن 0.60.
   - عند غياب الصور كلياً: confidence بين 0.40 و 0.65 كحد أقصى.
   - قيمة confidence أقل من 0.40 ممنوعة تماماً مهما كانت الظروف.
   - قيمة confidence = 0.01 أو أقل من 0.10 خطأ جسيم لا يُقبل.
5. إذا كانت البيانات غير كافية لتقييم دقيق، خفّض confidence فقط — لا تغيّر منطق الفجوة.
6. يجب أن تكون جميع الردود والقيم المستخرجة (بما فيها reasoning و infographic_cards) باللغة العربية الفصحى حصراً.
7. هام جداً — أسلوب كتابة `reasoning` (اقرأ بعناية):
   - اكتب تقرير قصير ومباشر يتكلم عن السلعة كما لو كنت تصفها لشخص يريد معرفة قيمتها اليوم.
   - ابدأ مباشرة بوصف حالة السلعة العامة والخصائص الأساسية التي تؤثر على سعرها.
   - اذكر السعر المناسب ومصدره من السوق باختصار (مثل: "بناءً على أسعار حراج الحالية...").
   - لا تكتب قوائم خصومات أو زيادات بالأرقام (مثل: -200 ريال، +500 ريال). ممنوع تماماً.
   - لا تذكر "تناقضات" أو "بيانات غير واقعية" أو "تحذيرات" أو "ملاحظات تجريبية".
   - لا تعطِ العميل توجيهات أو نصائح — فقط الوصف والسعر.
   - الطول المثالي: فقرتان أو ثلاث جمل قصيرة كحد أقصى.
   - استخدم HTML بسيط: <b> للتمييز، <br> للفواصل إذا لزم. لا تستخدم ul/li في reasoning.
8. إذا لم توجد صور للسلعة، قم بإنشاء وصف دقيق (باللغة الإنجليزية) لصورة واقعية بخلفية بيضاء في حقل image_prompt.
9. منهجية التسعير الصحيحة (أساسية وإلزامية جداً):
   - يجب عليك تقييم السلعة بناءً على المطابقة التامة للمواصفات المذكورة في الطلب (الماركة، الموديل، سنة الصنع، سعة التخزين، الحالة العامة، إلخ). لا تفترض مواصفات أقل أو أعلى.
   - قم بمحاكاة البحث الفعلي في منصات البيع والشراء السعودية الشهيرة (مثل: حراج، نون، أمازون السعودية، جرير، وغيرها) لاستخراج السعر العادل للسلعة المستعملة بنفس حالتها اليوم.
   - لا تعتمد على السعر الذي يذكره صاحب السلعة (إن وجد) فقد يكون مبالغاً فيه أو كاذباً، اعتمد فقط على المعطيات الفعلية للسلعة وأسعار السوق.
   - إذا كانت السلعة شائعة (مثل الهواتف أو السيارات المعروفة)، يجب أن يكون السعر دقيقاً ومطابقاً للأسعار المتداولة حالياً.
   - قارن بمتوسط الـ 50% الوسطى من الإعلانات المشابهة للسلعة (تجاهل الأعلى 25% والأدنى 25%).
   - السعر العادل هو السعر الفعلي الذي يدفعه المشتري المعقول في السوق حالياً لهذه السلعة المحددة.
   - غياب الصور لا يعني انخفاض السعر — يعني فقط انخفاض الـ confidence.
10. إذا كان بالمنتج عيوب أو حوادث موثّقة، اخصم من recommended_price بشكل واضح ومناسب لحجم العيب، ثم احسب min/max بالصيغة أعلاه.
11. هام جداً — بطاقات الـ Infographic:
   - استخرج أبرز 6-8 بطاقات معلوماتية مرئية عن السلعة في مصفوفة `infographic_cards`.
   - كل بطاقة تحتوي على: icon (إيموجي مناسب)، title (اسم المعلومة بالعربية)، value (القيمة بالعربية)، description (وصف مختصر مفيد جداً بالعربية لا يتجاوز 10 كلمات).
   - اختر البطاقات حسب نوع السلعة:
     * السيارات: العمر/السنة، الكيلومترات، الحوادث، الضمان، عدد الملاك، الحالة العامة، التاريخ الدولي، سجلات الصيانة.
     * الجوالات: الموديل، مساحة التخزين، حالة البطارية، الشاشة، الحالة العامة، وجود الكرتونة، لون الجهاز.
     * الإلكترونيات العامة: الماركة، السنة، الحالة، المواصفات الرئيسية، الملحقات، حالة الجهاز الخارجية.
     * الأثاث/العقارات/الساعات: أهم المواصفات المنطقية لتلك الفئة.

━━━ هيكل الرد (JSON فقط — لا نص خارجه) ━━━
{
  "min_price": <رقم صحيح بالريال السعودي>,
  "max_price": <رقم صحيح بالريال السعودي>,
  "recommended_price": <رقم صحيح بالريال السعودي>,
  "currency": "SAR",
  "confidence": <رقم عشري من 0.0 إلى 1.0>,
  "reasoning": "<كود HTML منسق وأنيق يحتوي على: سبب التسعير، المراجع السوقية المستخدمة، تأثير الحالة، ومقارنة السوق>",
  "infographic_cards": [
    {"icon": "🚗", "title": "سنة الصنع", "value": "2020", "description": "سيارة عمرها 5 سنوات"},
    {"icon": "📍", "title": "الكيلومترات", "value": "85,000 كم", "description": "استهلاك طبيعي للعمر"}
  ],
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

        // ─── حارس السعر الأدنى (Safety Guard) ────────────────────────────────
        // Claude قد يُخطئ في التسعير — هذا الكود يُصحح تلقائياً إذا كان السعر أقل من الحد المنطقي
        if (!empty($aiResult['recommended_price'])) {
            // استخرج سعر الشراء من البيانات (إن وُجد)
            $purchasePrice = null;
            foreach ($order->details as $detail) {
                $qAr = mb_strtolower($detail->question->question_ar ?? '');
                $qEn = mb_strtolower($detail->question->question_en ?? '');
                $val = $detail->value ?? $detail->option->option_ar ?? $detail->option->option_en ?? null;
                if (
                    $val && is_numeric($val) && (
                        str_contains($qAr, 'سعر') || str_contains($qAr, 'جديد') ||
                        str_contains($qEn, 'price') || str_contains($qEn, 'cost')
                    )
                ) {
                    $purchasePrice = (float) $val;
                    break;
                }
            }

            // تحديد الحد الأدنى حسب وجود الضمان
            $hasWarranty = $order->details->contains(function ($detail) {
                $val = mb_strtolower($detail->option->option_ar ?? $detail->value ?? '');
                return str_contains($val, 'ساري') || str_contains($val, 'ضمان');
            });

            if ($purchasePrice && $purchasePrice > 5000) {
                $floorRate = $hasWarranty ? 0.55 : 0.40; // 55% بضمان / 40% بدون
                $priceFloor = round($purchasePrice * $floorRate);
                $recommended = $aiResult['recommended_price'];

                if ($recommended < $priceFloor) {
                    Log::warning("AI Safety Guard: price {$recommended} below floor {$priceFloor} (purchase: {$purchasePrice}, warranty: " . ($hasWarranty ? 'yes' : 'no') . "). Correcting.");
                    $corrected = $priceFloor;
                    $aiResult['recommended_price'] = $corrected;
                    $aiResult['min_price'] = round($corrected * 0.93);
                    $aiResult['max_price'] = round($corrected * 1.07);
                    $aiResult['reasoning'] = $aiResult['reasoning'] ?? '';
                }
            }
        }

        // ─── بناء HTML الـ Infographic ودمجه مع الـ Reasoning ──────────────────
        $cards = $aiResult['infographic_cards'] ?? [];
        $reasoningText = $aiResult['reasoning'] ?? '';
        $fullReasoningHtml = $this->buildInfographicHtml($cards, $reasoningText);

        $order->update([
            // التثمين الذكي المنفرد → تم التثمين مباشرة
            // التثمين الاحترافي/الهجين → يفضل في beingEstimated لحد ما الخبير يقيم ثم الأدمن يوافق
            'status' => $order->evaluation_type === 'ai'
                ? ($order->status === 'beingReEstimated' ? 'reEstimated' : 'estimated')
                : ($order->status === 'beingReEstimated' ? 'beingReEstimated' : 'beingEstimated'),
            'ai_min_price' => $aiResult['min_price'] ?? null,
            'ai_max_price' => $aiResult['max_price'] ?? null,
            'ai_price' => $aiResult['recommended_price'] ?? null,
            'total_price' => $aiResult['recommended_price'] ?? null,
            'ai_confidence' => $aiResult['confidence'] ?? null,
            'ai_reasoning' => $fullReasoningHtml,
            'ai_features' => !empty($cards) ? $cards : null, // للداشبورد
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

    /* ═══════════════════════════════════════════════════════════════════════
     |  buildInfographicHtml
     |  يحوّل مصفوفة البطاقات + نص الـ reasoning إلى HTML واحد متكامل
     |  يعرضه الموبايل مباشرة دون أي معالجة إضافية
     ══════════════════════════════════════════════════════════════════════ */
    private function buildInfographicHtml(array $cards, string $reasoningText): string
    {
        if (empty($cards) && empty($reasoningText)) {
            return '';
        }

        $html = '<div style="font-family:\'Avenir Arabic\',\'SF Pro Text\',\'Segoe UI\',Arial,sans-serif;direction:rtl;text-align:right;color:#1a1a1a;background:#f5f0e8;border-radius:16px;padding:14px;">';

        // ── قسم الـ Cards ───────────────────────────────────────────────────
        if (!empty($cards)) {
            $html .= '<table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom:12px;">';

            $pairs = array_chunk($cards, 2);
            foreach ($pairs as $pairIndex => $pair) {
                $html .= '<tr>';
                foreach ($pair as $card) {
                    $icon = htmlspecialchars($card['icon'] ?? '●', ENT_QUOTES);
                    $title = htmlspecialchars($card['title'] ?? '', ENT_QUOTES);
                    $value = htmlspecialchars($card['value'] ?? '—', ENT_QUOTES);
                    $desc = htmlspecialchars($card['description'] ?? '', ENT_QUOTES);

                    $html .= '
<td width="50%" style="padding:4px;vertical-align:top;">
  <table cellpadding="0" cellspacing="0" border="0" width="100%"
    style="background:#ffffff;border:1px solid #e2d5b8;border-radius:12px;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
    <tr><td style="padding:12px 8px;text-align:center;">
      <div style="font-size:26px;line-height:1;margin-bottom:6px;">' . $icon . '</div>
      <div style="font-size:9px;font-weight:700;color:#9a7840;letter-spacing:0.4px;margin-bottom:4px;text-transform:uppercase;">' . $title . '</div>
      <div style="font-size:14px;font-weight:900;color:#1a1a1a;margin-bottom:' . ($desc ? '3px' : '0') . ';">' . $value . '</div>
      ' . ($desc ? '<div style="font-size:9px;color:#a89060;line-height:1.4;">' . $desc . '</div>' : '') . '
    </td></tr>
  </table>
</td>';
                }
                // خلية فاضية لو عدد البطاقات فردي
                if (count($pair) === 1) {
                    $html .= '<td width="50%" style="padding:4px;"></td>';
                }
                $html .= '</tr>';
                // مسافة صغيرة بين الصفوف
                $html .= '<tr><td colspan="2" style="height:2px;"></td></tr>';
            }

            $html .= '</table>';
        }

        // ── قسم الـ Reasoning النصي ─────────────────────────────────────────
        if (!empty($reasoningText)) {
            $html .= '<div style="background:#ffffff;border-radius:12px;padding:13px 14px;font-size:13px;line-height:1.85;color:#3d3020;">';
            // إذا لم يحتوِ على HTML → نحوّله مباشرة
            if (!preg_match('/<[a-z][\s\S]*>/i', $reasoningText)) {
                $html .= nl2br(htmlspecialchars($reasoningText, ENT_QUOTES));
            } else {
                $html .= $reasoningText;
            }
            $html .= '</div>';
        }

        $html .= '</div>';
        return $html;
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
                    "هلا بك خبير ( التثمين ) 👋 وصل طلب تثمين احترافي جديد رقم {$order->id} وهو متاح الآن في منصة الخبراء في ثمن. نرجو منك الدخول وتقييم الطلب في أسرع وقت."
                );
            }
            // Send Email
            if ($expert->email) {
                try {
                    Mail::to($expert->email)->send(new \App\Mail\SystemNotificationMail(
                        "هلا بك خبير ( التثمين ) 👋 طلب تثمين احترافي جديد بانتظارك رقم #{$order->id}",
                        "هلا بك خبير ( التثمين ) 👋 وصل طلب تثمين احترافي جديد رقم {$order->id} وهو متاح الآن في منصة الخبراء في ثمن. نرجو منك الدخول وتقييم الطلب في أسرع وقت.",
                        route('orders.show', $order->id)
                    ));
                } catch (\Throwable $e) {
                    Log::error("Failed to send expert email for best order: " . $e->getMessage());
                }
            }
        }
    }
}
