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
    public function runAiEvaluation(Order $order, ?float $previousPrice = null): array
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

        // ─── معالجة الصور (استبعاد الصور الافتراضية المولدة بالـ AI من تحليل Vision الحقيقي) ──
        $realUserImages = $order->files->where('type', 'image')->filter(fn($f) => !str_starts_with($f->file_name, 'ai_generated_'));
        $hasImages = $realUserImages->isNotEmpty();
        $imageCount = $realUserImages->count();
        $imageFiles = $hasImages ? $realUserImages : $order->files->where('type', 'image');

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

        // ─── حسم وتوثيق السعر السابق قبل الاعتراض ──────────────────────────────
        $resolvedPreviousPrice = $previousPrice ?: ($order->previous_price ?: (float) ($order->total_price ?: ($order->ai_price ?: 0)));
        if ($resolvedPreviousPrice > 0 && empty($order->previous_price)) {
            $order->update(['previous_price' => $resolvedPreviousPrice]);
        }

        // ─── المنهجية المرجعية المعتمدة لكبرى المنصات السعودية ─────────────────
        $saudiMarketGuide = <<<SAUDI
━━━ المنهجية المرجعية لأسواق ومنصات المملكة العربية السعودية ━━━
أنت خبير معتمد في تثمين السلع في المملكة العربية السعودية (وفق معايير الهيئة السعودية للمقيّمين المعتمدين - تقييم).
يعتمد تسعيرك حصراً على محاكاة واقع البيع والشراء والصفقات المنفذة في كبرى المنصات السعودية:
1. منصة حراج (Haraj.com.sa) ومنصة مستعمل وجديد (Mstaml.com):
   - المرجع الأكبر لأسعار السلع المستعملة في المدن السعودية (الرياض، جدة، مكة، المدينة، الشرقية، القصيم، إلخ).
   - مبدأ "السوم الناجز vs الحد": المعلنون يضعون أسعار "حد" تفاوضية، لكن الصفقات تُبرم دائماً بأقل منها بنسبة 10% إلى 15% (سعر السوم الصامل). السعر العادل هو سعر التنفيذ الحقيقي لا أرقام العرض المبالغ فيها.
2. منصات الأجهزة والإلكترونيات (مكتبة جرير Jarir، إكسترا eXtra، أمازون السعودية Amazon.sa، نون Noon.sa):
   - تمثل السقف الأعلى (سعر السلعة جديدة ومغلفة مع الضمان الرسمي سنتين). لا يجوز أبداً أن يقترب سعر المستعمل من سعر الجديد.
   - معدل الإهلاك التقني: تفقد السلعة 25% إلى 35% من قيمتها بمجرد فتح الكرتون، وتفقد 15% إلى 20% إضافية لكل سنة استخدام مع الأخذ بصحة البطارية ووجود الملحقات الأصلية.
3. منصات ومعارض السيارات (تطبيق سيارة Syarah، تقارير موجز Mojaz، كارسويتش CarSwitch، حراج السيارات):
   - استخراج متوسط الصفقات المتطابقة (الماركة، الموديل، سنة الصنع، الفئة، الكيلومترات بمتوسط 20-25 ألف كم/سنة).
   - خصم واضح ومحدد لأي صدمات أو رش بالبودي أو فحص دوري منتهي أو استهلاك زائد.
4. الساعات والمقتنيات الثمينة (أسواق الذهب والساعات في الرياض وجدة، والمنصات المعتمدة):
   - التمييز الصارم بين الساعات السويسرية الفاخرة التي تحتفظ بقيمتها، وبين ساعات الموضة التي تفقد معظم قيمتها بعد الشراء.
SAUDI;

        // ─── بناء الـ Prompt ────────────────────────────────────────────────────
        $today = now()->locale('ar')->translatedFormat('d F Y');

        $reEvaluationNote = $order->re_evaluation_count > 0
            ? "ملاحظة للنظام: هذا طلب إعادة تقييم رسمي بعد مراجعة العميل. راجع كافة المعطيات من جديد وأعط السعر الأدق والمنصف — دون ذكر ذلك نصاً في reasoning."
            : "";

        $objectionSection = "";
        if (!empty($order->objection_reason)) {
            $objectionReason = trim($order->objection_reason);
            $reasonLower = mb_strtolower($objectionReason);

            $isHigher = str_contains($reasonLower, 'أعلى من المتوقع') 
                || str_contains($reasonLower, 'مرتفع') 
                || str_contains($reasonLower, 'غالي') 
                || str_contains($reasonLower, 'مبالغ')
                || str_contains($reasonLower, 'overpriced')
                || str_contains($reasonLower, 'higher');

            $isLower = str_contains($reasonLower, 'أقل من المتوقع') 
                || str_contains($reasonLower, 'منخفض') 
                || str_contains($reasonLower, 'رخيص') 
                || str_contains($reasonLower, 'بخس')
                || str_contains($reasonLower, 'undervalued')
                || str_contains($reasonLower, 'lower');

            $isImageObjection = str_contains($reasonLower, 'صورة') 
                || str_contains($reasonLower, 'صوره') 
                || str_contains($reasonLower, 'image') 
                || str_contains($reasonLower, 'photo');

            $isInputMod = str_contains($reasonLower, 'تعديل المدخلات') || str_contains($reasonLower, 'input_modification');

            $previousPriceFormatted = $resolvedPreviousPrice > 0 ? number_format($resolvedPreviousPrice, 0) . ' ريال سعودي' : 'غير محدد';

            if ($isHigher && $resolvedPreviousPrice > 0) {
                $targetLow = round($resolvedPreviousPrice * 0.74);
                $targetHigh = round($resolvedPreviousPrice * 0.86);
                $objectionSpecifics = <<<SPEC
• مغزى الاعتراض: العميل يعترض رسمياً بأن السعر السابق ({$previousPriceFormatted}) كان أعلى من المتوقع ومن أسعار السوق الواقعية (مبالغ فيه).
• قاعدة تسعير إلزامية حاسمة لا تقبل النقاش:
  1. يجب حتماً أن يكون السعر الجديد (recommended_price) أقل بوضوح من السعر السابق ({$previousPriceFormatted}). رفع السعر أو إبقاؤه كما هو خطأ جسيم ومرفوض كلياً.
  2. النطاق العادل والمستهدف للتسعير الجديد: ما بين {$targetLow} و {$targetHigh} ريال تقريباً، بما يعكس متوسط صفقات البيع السريع ("السوم الناجز") والشريحة السعرية الدنيا المتداولة في حراج ومستعمل وجديد لنفس الحالة والمواصفات.
  3. صياغة التقرير (reasoning): اكتب تقريراً تحليلياً موضوعياً يشرح بلباقة (دون ذكر كلمة "اعتراض") أنه بناءً على إعادة تدقيق حركة التداول والصفقات السريعة المنفذة في السوق السعودي وأثر الاستهلاك العادي، تم اعتماد السعر العادل المحدث.
SPEC;
            } elseif ($isLower && $resolvedPreviousPrice > 0) {
                $targetLow = round($resolvedPreviousPrice * 1.12);
                $targetHigh = round($resolvedPreviousPrice * 1.25);
                $objectionSpecifics = <<<SPEC
• مغزى الاعتراض: العميل يعترض رسمياً بأن السعر السابق ({$previousPriceFormatted}) أقل من القيمة الحقيقية للسلعة في السوق (بخس لقيمة السلعة).
• قاعدة تسعير إلزامية حاسمة لا تقبل النقاش:
  1. يجب حتماً أن يكون السعر الجديد (recommended_price) أعلى من السعر السابق ({$previousPriceFormatted}) لإنصاف العميل، مع مراعاة عدم تجاوز سقف سعر السلعة جديدة في جرير/أمازون.
  2. النطاق العادل والمستهدف للتسعير الجديد: ما بين {$targetLow} و {$targetHigh} ريال تقريباً، بافتراض أفضلية النظافة واكتمال الملحقات وأسعار المعروض الممتاز في حراج ومستعمل.
  3. تخفيض السعر هنا مرفوض تماماً ويُعد خطأ جسيماً.
  4. صياغة التقرير (reasoning): اشرح بموضوعية واحترافية (دون ذكر كلمة "اعتراض") أنه بناءً على مراجعة حالة السلعة ونظافتها ومقارنتها بالمعروض عالي الجودة في السوق السعودي، تم تحسين التقدير ليعكس القيمة السوقية العادلة.
SPEC;
            } elseif ($isImageObjection) {
                $objectionSpecifics = <<<SPEC
• مغزى الاعتراض: العميل يعترض على صورة السلعة الافتراضية الظاهرة (مثلاً عدم تطابق شكل الجهاز في الصورة مع الموديل المحدد بالمدخلات).
• توجيه حاسم: يقوم النظام الآن بتوليد صورة افتراضية جديدة مطابقة تماماً للموديل المحدد في المدخلات.
• قاعدة التسعير: طالما أن مواصفات السلعة صحيحة، حافظ على السعر العادل السابق ({$previousPriceFormatted}) دون زيادة أو خفض عشوائي، إلا إذا استدعى الموديل الفعلي تصحيحاً موضوعياً في السعر.
• صياغة التقرير: اكتب تحليلاً دقيقاً لمواصفات الموديل وتأكيد مطابقته لأسعار السوق السعودي.
SPEC;
            } elseif ($isInputMod) {
                $objectionSpecifics = <<<SPEC
• مغزى الاعتراض: العميل قام بتعديل مواصفات أو مدخلات أو صور السلعة.
• قاعدة التسعير: قيّم السلعة بدقة تامة بناءً على المدخلات الجديدة المرفقة وقارنها بأسعار السوق السعودي الحالي، واشرح أثر المواصفات المعدلة في تقرير الـ reasoning.
SPEC;
            } else {
                $objectionSpecifics = <<<SPEC
• نص وملاحظات العميل في الاعتراض: "{$objectionReason}"
• قاعدة التسعير: ادرس ملاحظة العميل بعناية وقارنها بالسعر السابق ({$previousPriceFormatted}). إذا ذكر العميل عيوباً أو نقص ملحقات أو أعطالاً، اخفض السعر دون السعر السابق. وإذا ذكر مميزات وفواتير وضماناً ممتداً، ارفع السعر بما يعكس قيمتها الحقيقية.
SPEC;
            }

            $objectionSection = <<<OBJECTION

━━━ توجيه إلزامي حاسم: إعادة التثمين بناءً على اعتراض العميل ━━━
العميل قام بتقديم اعتراض رسمي على التقييم السابق:
• السعر السابق الذي اعترض عليه العميل: {$previousPriceFormatted}
• سبب الاعتراض المسجل: "{$objectionReason}"

تعليمات المعالجة الإلزامية:
{$objectionSpecifics}
OBJECTION;
        }

        $prompt = <<<PROMPT
{$ctx->getRole()}
تاريخ التقييم: {$today}
{$saudiMarketGuide}
{$ctx->getMarketReferences()}
{$reEvaluationNote}
{$objectionSection}

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
   - عند وجود صور واضحة وبيانات كاملة وبيانات مطابقة للسوق: confidence لا يقل عن 0.90.
   - عند وجود بيانات كاملة بدون صور لكن السلعة شائعة ومعروفة: confidence لا يقل عن 0.80.
   - عند وجود صور فقط بدون بيانات وافية: confidence لا يقل عن 0.75.
   - عند غياب الصور كلياً مع بيانات كافية: confidence بين 0.70 و 0.85.
   - عند غياب الصور كلياً مع بيانات شحيحة: confidence بين 0.60 و 0.75.
   - قيمة confidence أقل من 0.60 ممنوعة تماماً مهما كانت الظروف — استخدم بيانات السوق العامة للوصول للحد الأدنى.
   - قيمة confidence = 0.01 أو أقل من 0.50 خطأ جسيم لا يُقبل ولن يُقبل.
5. إذا كانت البيانات غير كافية لتقييم دقيق، خفّض confidence بحد أقصى إلى 0.70 فقط — لا تغيّر منطق الفجوة.
6. يجب أن تكون جميع الردود والقيم المستخرجة (بما فيها reasoning و infographic_cards) باللغة العربية الفصحى حصراً.
7. هام جداً — أسلوب كتابة `reasoning` (اقرأ بعناية واتبع بدقة):
   - اكتب تقرير قصير ومباشر يتكلم عن السلعة كما لو كنت تصفها لشخص يريد معرفة قيمتها اليوم.
   - ابدأ مباشرة بوصف حالة السلعة العامة والخصائص الأساسية التي تؤثر على سعرها.
   - اذكر السعر المناسب بصياغة محايدة مثل: "بناءً على أسعار السوق السعودي الحالية" أو "وفق متوسط أسعار السوق المحلي" — دون ذكر أي منصة أو موقع باسمه (ممنوع ذكر: حراج، نون، أمازون، جرير، مستعمل، إلخ).
   - لا تكتب قوائم خصومات أو زيادات بالأرقام (مثل: -200 ريال، +500 ريال). ممنوع تماماً.
   - لا تذكر "تناقضات" أو "بيانات غير واقعية" أو "تحذيرات" أو "ملاحظات تجريبية".
   - لا تعطِ العميل توجيهات أو نصائح للبيع — فقط الوصف والسعر.
   - الطول المثالي: فقرتان أو ثلاث جمل قصيرة كحد أقصى.
   - استخدم HTML بسيط: <b> للتمييز، <br> للفواصل إذا لزم. لا تستخدم ul/li في reasoning.
8. إذا لم توجد صور للسلعة، أو إذا طلب العميل توليد صورة بالذكاء الاصطناعي (create_image_ai)، قم بإنشاء وصف دقيق (باللغة الإنجليزية) لصورة واقعية بخلفية بيضاء في حقل image_prompt.
9. منهجية التسعير الصحيحة (أساسية وإلزامية جداً):
   - يجب عليك تقييم السلعة بناءً على المطابقة التامة للمواصفات المذكورة في الطلب (الماركة، الموديل، سنة الصنع، سعة التخزين، الحالة العامة، إلخ). لا تفترض مواصفات أقل أو أعلى.
   - قم بتحليل أسعار السوق السعودي لنفس المنتج المستعمل بذات المواصفات والحالة — بغض النظر عن اسم المنصة — واستخرج السعر العادل.
   - لا تعتمد على السعر الذي يذكره صاحب السلعة (إن وجد) فقد يكون مبالغاً فيه أو كاذباً، اعتمد فقط على المعطيات الفعلية للسلعة وأسعار السوق.
   - إذا كانت السلعة شائعة (مثل الهواتف أو السيارات المعروفة)، يجب أن يكون السعر دقيقاً ومطابقاً للأسعار المتداولة حالياً في السوق السعودي.
   - قارن بمتوسط الـ 50% الوسطى من الإعلانات المشابهة للسلعة (تجاهل الأعلى 25% والأدنى 25%).
   - السعر العادل هو السعر الفعلي الذي يدفعه المشتري المعقول في السوق حالياً لهذه السلعة المحددة.
   - غياب الصور لا يعني انخفاض السعر — يعني فقط انخفاض طفيف في confidence.
10. إذا كان بالمنتج عيوب أو حوادث موثّقة، اخصم من recommended_price بشكل واضح ومناسب لحجم العيب، ثم احسب min/max بالصيغة أعلاه.
11. هام جداً — بطاقات الـ Infographic:
   - استخرج أبرز 6-8 بطاقات معلوماتية مرئية عن السلعة في مصفوفة `infographic_cards`.
   - كل بطاقة تحتوي على: icon (إيموجي مناسب)، title (اسم المعلومة بالعربية)، value (القيمة بالعربية)، description (وصف مختصر مفيد جداً بالعربية لا يتجاوز 10 كلمات).
   - اختر البطاقات حسب نوع السلعة:
     * السيارات: العمر/السنة، الكيلومترات، الحوادث، الضمان، عدد الملاك، الحالة العامة، التاريخ الدولي، سجلات الصيانة.
     * الجوالات: الموديل، مساحة التخزين، حالة البطارية، الشاشة، الحالة العامة، وجود الكرتونة، لون الجهاز.
     * الإلكترونيات العامة: الماركة، السنة، الحالة، المواصفات الرئيسية، الملحقات، حالة الجهاز الخارجية.
     * الأثاث/العقارات/الساعات: أهم المواصفات المنطقية لتلك الفئة.
   - ممنوع ذكر أسماء المنصات أو المواقع في قيم البطاقات أو أوصافها.

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

        // ─── كود الحماية الصارم للاعتراضات (Safety Guard for Objections) ───
        if ($resolvedPreviousPrice > 0 && !empty($order->objection_reason)) {
            $reasonLower = mb_strtolower($order->objection_reason);
            $recommended = (float) ($aiResult['recommended_price'] ?? 0);

            $isHigher = str_contains($reasonLower, 'أعلى من المتوقع') 
                || str_contains($reasonLower, 'مرتفع') 
                || str_contains($reasonLower, 'غالي') 
                || str_contains($reasonLower, 'مبالغ')
                || str_contains($reasonLower, 'overpriced')
                || str_contains($reasonLower, 'higher');

            $isLower = str_contains($reasonLower, 'أقل من المتوقع') 
                || str_contains($reasonLower, 'منخفض') 
                || str_contains($reasonLower, 'رخيص') 
                || str_contains($reasonLower, 'بخس')
                || str_contains($reasonLower, 'undervalued')
                || str_contains($reasonLower, 'lower');

            if ($isHigher && ($recommended >= $resolvedPreviousPrice || $recommended <= 0)) {
                // منع رفع السعر في حالة اعتراض "أعلى من المتوقع": خفض السعر بنسبة عادلة 18%
                $corrected = round($resolvedPreviousPrice * 0.82);
                Log::warning("AI Objection Safety Guard: Customer objected price was too high (Previous: {$resolvedPreviousPrice}), but AI returned {$recommended}. Correcting downwards to {$corrected}.");
                $aiResult['recommended_price'] = $corrected;
                $aiResult['min_price'] = round($corrected * 0.93);
                $aiResult['max_price'] = round($corrected * 1.07);
            } elseif ($isLower && ($recommended <= $resolvedPreviousPrice || $recommended <= 0)) {
                // منع خفض السعر في حالة اعتراض "أقل من المتوقع": رفع السعر بنسبة عادلة 16%
                $corrected = round($resolvedPreviousPrice * 1.16);
                Log::warning("AI Objection Safety Guard: Customer objected price was too low (Previous: {$resolvedPreviousPrice}), but AI returned {$recommended}. Correcting upwards to {$corrected}.");
                $aiResult['recommended_price'] = $corrected;
                $aiResult['min_price'] = round($corrected * 0.93);
                $aiResult['max_price'] = round($corrected * 1.07);
            }
        }

        // ─── ضبط الفجوة 14% تلقائياً لأي نتيجة لضمان سلامة الأرقام ───
        if (!empty($aiResult['recommended_price'])) {
            $rec = (float) $aiResult['recommended_price'];
            $aiResult['min_price'] = round($rec * 0.93);
            $aiResult['max_price'] = round($rec * 1.07);
        }

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

        $isAiObjection = $order->evaluation_type === 'ai' && !empty($order->objection_reason);

        $newStatus = $order->status;
        $isExpertEvaluated = $order->expert_evaluated ? 1 : 0;

        if ($isAiObjection) {
            $newStatus = 'reEstimated';
            $isExpertEvaluated = 1;
        } elseif (!in_array($order->status, ['estimated', 'reEstimated', 'completed'])) {
            $newStatus = $order->status === 'beingReEstimated' ? 'beingReEstimated' : 'beingEstimated';
        }

        $order->update([
            'status' => $newStatus,
            'previous_price' => $resolvedPreviousPrice > 0 ? $resolvedPreviousPrice : $order->previous_price,
            'expert_evaluated' => $isExpertEvaluated,
            'ai_min_price' => $aiResult['min_price'] ?? null,
            'ai_max_price' => $aiResult['max_price'] ?? null,
            'ai_price' => $aiResult['recommended_price'] ?? null,
            'total_price' => $aiResult['recommended_price'] ?? null,
            'ai_confidence' => $aiResult['confidence'] ?? null,
            'ai_reasoning' => $fullReasoningHtml,
            'ai_features' => !empty($cards) ? $cards : null, // للداشبورد
            'evaluated_at' => now(),
        ]);

        // Generate Virtual Image if no images exist OR if create_image_ai was requested OR on objection
        $this->generateVirtualImageForOrder($order, $aiResult['image_prompt'] ?? null, (bool) $isAiObjection);

        // ── إشعار العميل بالانتظار (في التقييم العادي فقط، أما اعتراض الـ AI فالسعر جاهز فوراً) ──
        if (!$isAiObjection) {
            $tokens = $order->user->getFcmTokens();
            if (!empty($tokens)) {
                $isAr = ($order->user->preferred_language ?? 'ar') === 'ar';
                $this->notifyByFirebase(
                    $isAr ? 'أوشكنا على النهاية' : 'Almost done',
                    $isAr 
                        ? 'أوشكنا على النهاية.. طلبك الآن في المراجعة النهائية' 
                        : 'We are almost done.. Your order is now in final review',
                    $tokens,
                    ['data' => ['user_id' => $order->user_id, 'order_id' => $order->id, 'type' => 'order_waiting_admin']]
                );
            }
        }

        // ── إرسال الطلب لخبراء نفس القسم لطلب المصادقة أو مراجعة الاعتراض ──
        $whatsapp = app(\App\Services\WhatsAppService::class);
        $expertsQuery = \App\Models\User::role('expert');
        if ($order->category_id) {
            $expertsQuery->where('category_id', $order->category_id);
        }
        $experts = $expertsQuery->get();
        
        $categoryName = $order->category->name_ar ?? $order->category->name_en ?? '';
        $categoryText = $categoryName ? " بقسم [{$categoryName}]" : "";

        if ($isAiObjection) {
            // إشعار الخبراء بالاعتراض الجديد لتمكينهم من المراجعة والتعليق أو اتخاذ قرار
            $objectionMsg = "تنبيه خبير ثمن ⚖️\nوصل اعتراض جديد من العميل على الطلب{$categoryText} رقم {$order->id}\n📌 سبب الاحتجاج: {$order->objection_reason}\nتم تحديث معطيات التقييم وإعادة توليد الصورة، وبإمكانك الآن الدخول لمراجعة الطلب والتعليق أو اعتماد التعديل أو تثبيت السعر السابق وإغلاق الطلب.";
            foreach ($experts as $expert) {
                $expertLink = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                    'expert.auto-login',
                    now()->addDays(7),
                    ['user' => $expert->id, 'redirect' => route('orders.show', $order->id)]
                );
                if ($expert->phone) {
                    $whatsappMsg = $objectionMsg . \App\Services\WhatsAppService::formatActionLink('مراجعة الاعتراض واتخاذ إجراء', $expertLink, '🔍');
                    $whatsapp->sendMessage($expert->phone, $whatsappMsg);
                }
            }
        } elseif ($order->evaluation_type === 'ai' && !$order->expert_id) {
            $msg = "ياهلا خبير التثمين\nوصلك طلب تثمين ذكي{$categoryText} رقم {$order->id}\nالمرجو منك سرعة الدخول على الطلب والاطلاع على التقرير واعتماد السعر وفق رؤيتكم واعتمادكم\n\nشاكرين تعاونكم\nفريق تطبيق ثمن";

            foreach ($experts as $expert) {
                // رابط دخول تلقائي آمن للخبير للداشبورد مباشرة
                $expertLink = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                    'expert.auto-login',
                    now()->addDays(7),
                    ['user' => $expert->id, 'redirect' => route('orders.show', $order->id)]
                );

                if ($expert->phone) {
                    $whatsappMsg = $msg . \App\Services\WhatsAppService::formatActionLink('عرض الطلب واعتماد التثمين', $expertLink, '🔍');
                    $whatsapp->sendMessage($expert->phone, $whatsappMsg);
                }
                if ($expert->email) {
                    try {
                        Mail::to($expert->email)->send(new \App\Mail\SystemNotificationMail(
                            "مطلوب المصادقة على طلب التثمين الذكي رقم {$order->id}",
                            $msg,
                            $expertLink,
                            'عرض الطلب'
                        ));
                    } catch (\Throwable $e) {
                        Log::error("Failed to send expert email for AI order approval: " . $e->getMessage());
                    }
                }
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
                $isAr = ($user->preferred_language ?? 'ar') === 'ar';
                $this->notifyByFirebase(
                    $isAr ? 'تم حساب السعر العادل' : 'Fair price calculated',
                    $isAr 
                        ? "قام فريق ثمن بحساب السعر العادل لمنتجك {$thamnPrice} ريال" 
                        : "Thamn team has calculated the fair price for your product: {$thamnPrice} SAR",
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

        // Notify Experts of the same category via Email, WhatsApp, and DB Notification
        $expertsQuery = \App\Models\User::role('expert');
        if ($order->category_id) {
            $expertsQuery->where('category_id', $order->category_id);
        }
        $experts = $expertsQuery->get();

        $categoryName = $order->category->name_ar ?? $order->category->name_en ?? '';
        $categoryText = $categoryName ? " بقسم [{$categoryName}]" : "";

        foreach ($experts as $expert) {
            $expertLink = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                'expert.auto-login',
                now()->addDays(7),
                ['user' => $expert->id, 'redirect' => route('orders.show', $order->id)]
            );

            // Send WhatsApp
            if ($expert->phone) {
                $expertMsg = "هلا بك خبير ثمن .. وصل طلب تثمين احترافي جديد{$categoryText} رقم {$order->id} وهو متاح الآن في منصة الخبراء .. نرجو منك الدخول وتقييم الطلب في أسرع وقت";
                $expertMsg .= \App\Services\WhatsAppService::formatActionLink('تسجيل الدخول واستلام الطلب', $expertLink, '🔐');
                $whatsapp->sendMessage($expert->phone, $expertMsg);
            }
            // Send Email
            if ($expert->email) {
                try {
                    Mail::to($expert->email)->send(new \App\Mail\SystemNotificationMail(
                        'طلب تقييم جديد بقسمك',
                        "يوجد طلب جديد رقم {$order->id} متاح الآن للتقييم .. بادر بالدخول لمنصة الخبراء في ثمن لاستلامه قبل الخبراء الآخرين",
                        $expertLink,
                        'عرض الطلب واستلامه'
                    ));
                } catch (\Throwable $e) {
                    Log::error("Failed to send expert email for best order: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Generate Virtual AI Image for an order if needed, requested via create_image_ai, or on objection
     */
    public function generateVirtualImageForOrder(Order $order, ?string $imagePrompt = null, bool $forceRegenerate = false): ?OrderFiles
    {
        $order->refresh();
        $order->loadMissing(['files', 'details.question', 'details.option', 'category']);

        // فحص الصور المرفوعة فعلياً من العميل (استبعاد الصور الافتراضية السابقة المولدة بالـ AI)
        $hasUserImages = $order->files->where('type', 'image')->filter(function ($f) {
            return !str_starts_with($f->file_name, 'ai_generated_');
        })->count() > 0;

        $hasExistingAiImage = $order->files->where('type', 'image')->filter(function ($f) {
            return str_starts_with($f->file_name, 'ai_generated_');
        })->count() > 0;

        $wantsAiImage = (bool) ($order->create_image_ai || request('create_image_ai', false));

        // توليد/إعادة توليد الصورة إذا:
        // 1. تم الإجبار ($forceRegenerate)
        // 2. طلب العميل صورة بالذكاء الاصطناعي ($wantsAiImage)
        // 3. الطلب لا يحتوي على أي صور حقيقية من العميل (!$hasUserImages)
        // 4. الطلب كان يحتوي على صورة AI سابقة ويتم مراجعته ($hasExistingAiImage)
        if ($forceRegenerate || !$hasUserImages || $wantsAiImage || $hasExistingAiImage) {
            if (empty($imagePrompt)) {
                $imagePrompt = $this->buildAccurateImagePrompt($order);
            }

            // إذا كان هناك صور سابقة مولدة بالذكاء الاصطناعي لنفس الطلب، نحذفها لنضع الصورة الجديدة المحدثة
            $oldAiFiles = $order->files()->where('type', 'image')->where('file_name', 'like', 'ai_generated_%')->get();
            foreach ($oldAiFiles as $oldFile) {
                Storage::disk('public')->delete($oldFile->file_path);
                $oldFile->delete();
            }

            try {
                Log::info("Generating virtual AI image for order #{$order->id} with prompt: {$imagePrompt}");
                $imageUrl = app(ClaudeService::class)->generateImage($imagePrompt);
                if ($imageUrl) {
                    if (str_starts_with($imageUrl, 'data:image')) {
                        $parts = explode(',', $imageUrl);
                        $imageContents = base64_decode($parts[1] ?? '');
                    } else {
                        $imageContents = \Illuminate\Support\Facades\Http::timeout(30)->get($imageUrl)->body();
                    }

                    if ($imageContents) {
                        $filename = 'ai_generated_' . Str::random(10) . '.png';
                        $path = 'orders/' . $order->id . '/images/' . $filename;

                        Storage::disk('public')->put($path, $imageContents);

                        $fileRecord = OrderFiles::create([
                            'order_id' => $order->id,
                            'file_path' => $path,
                            'file_name' => $filename,
                            'type' => 'image',
                        ]);

                        $order->load('files');
                        Log::info("Virtual image generated and attached to order #{$order->id}: {$path}");

                        return $fileRecord;
                    }
                }
            } catch (\Throwable $e) {
                Log::error('Failed to generate virtual image via AI', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return null;
    }

    /**
     * بناء وصف إنجليزي فائق الدقة والاستوديو التجاري الاحترافي لـ DALL-E / OpenAI
     */
    public function buildAccurateImagePrompt(Order $order, ?string $customPrompt = null): string
    {
        if (!empty($customPrompt)) {
            return $customPrompt;
        }

        $order->loadMissing(['details.question', 'details.option', 'category']);

        $categoryAr = $order->category->name_ar ?? 'منتج';
        $categoryEn = $order->category->name_en ?? 'Product';

        $brand = '';
        $model = '';
        $color = '';

        // استخراج الموديل من نص الاعتراض إن وجد (مثل اعتراض: ظهور صورة ايفون 13 وهو ايفون 17 برو)
        $objectionText = $order->objection_reason ?? '';
        if (preg_match('/(iphone\s*1[1-9]\s*(?:pro\s*max|pro|plus|mini)?)/i', $objectionText, $matches)) {
            $model = $matches[1];
        } elseif (preg_match('/(ايفون|آيفون)\s*([0-9]{1,2})\s*(برو\s*ماكس|برو|بلاس|ميني)?/u', $objectionText, $matches)) {
            $num = $matches[2];
            $tier = trim($matches[3] ?? '');
            $tierEn = match ($tier) {
                'برو ماكس' => 'Pro Max',
                'برو' => 'Pro',
                'بلاس', 'بلس' => 'Plus',
                'ميني' => 'Mini',
                default => ''
            };
            $model = "iPhone {$num}" . ($tierEn ? " {$tierEn}" : '');
        }

        foreach ($order->details as $d) {
            $qAr = mb_strtolower($d->question?->question_ar ?? '');
            $qEn = mb_strtolower($d->question?->question_en ?? '');
            $valAr = trim($d->option?->option_ar ?: ($d->value ?: ''));
            $valEn = trim($d->option?->option_en ?: '');
            $val = $valEn ?: $valAr;

            if (empty($val) || $d->question?->type === 'rateTypeSelection') {
                continue;
            }

            // فحص واستخراج الماركة
            if (empty($brand) && (
                str_contains($qAr, 'ماركة') || str_contains($qAr, 'شركة') || str_contains($qAr, 'مصنعة') ||
                str_contains($qEn, 'brand') || str_contains($qEn, 'make') || str_contains($qEn, 'manufacturer')
            )) {
                $brand = $this->translateBrandToEnglish($val);
            }

            // فحص واستخراج الموديل (إن لم يُستخرج من الاعتراض)
            if (empty($model) && (
                str_contains($qAr, 'موديل') || str_contains($qAr, 'طراز') || str_contains($qAr, 'تعريف') ||
                str_contains($qAr, 'نوع') || str_contains($qEn, 'model') || str_contains($qEn, 'type')
            )) {
                if (preg_match('/(iphone\s*[0-9]{1,2}(?:\s*(?:pro\s*max|pro|plus|mini))?)/i', $val, $m)) {
                    $model = $m[1];
                } elseif (preg_match('/(ايفون|آيفون)\s*([0-9]{1,2})/u', $val, $m)) {
                    $model = 'iPhone ' . $m[2];
                    if (str_contains(mb_strtolower($val), 'برو ماكس')) $model .= ' Pro Max';
                    elseif (str_contains(mb_strtolower($val), 'برو')) $model .= ' Pro';
                    elseif (str_contains(mb_strtolower($val), 'بلس')) $model .= ' Plus';
                } else {
                    $model = $val;
                }
            }

            // فحص واستخراج اللون
            if (empty($color) && (
                str_contains($qAr, 'لون') || str_contains($qAr, 'مادة') || str_contains($qAr, 'خامة') ||
                str_contains($qEn, 'color') || str_contains($qEn, 'material')
            )) {
                $color = $this->translateColorToEnglish($val);
            }
        }

        // فحص بديل لاستخراج الماركة إن لم تلتقط من الأسئلة
        if (empty($brand)) {
            foreach ($order->details as $d) {
                $txt = ($d->option?->option_ar ?: '') . ' ' . ($d->value ?: '');
                $detected = $this->detectBrandFromText($txt);
                if ($detected) {
                    $brand = $detected;
                    break;
                }
            }
        }

        // فحص بديل للموديل
        if (empty($model)) {
            foreach ($order->details as $d) {
                $txt = ($d->option?->option_ar ?: '') . ' ' . ($d->value ?: '');
                if (preg_match('/(iphone\s*[0-9]{1,2}(?:\s*(?:pro\s*max|pro|plus|mini))?)/i', $txt, $m)) {
                    $model = $m[1];
                    break;
                }
            }
        }

        if (empty($brand) && !empty($model) && str_contains(mb_strtolower($model), 'iphone')) {
            $brand = 'Apple';
        }

        $brand = $brand ?: 'Apple';
        $model = $model ?: ($categoryEn ?: 'flagship product');
        $model = preg_replace('/[^\w\s\-\+]/u', '', $model);

        $isPhone = str_contains(mb_strtolower($categoryEn), 'phone') 
            || str_contains(mb_strtolower($categoryAr), 'جوال') 
            || str_contains(mb_strtolower($model), 'iphone')
            || str_contains(mb_strtolower($model), 'galaxy');

        $isWatch = str_contains(mb_strtolower($categoryEn), 'watch') 
            || str_contains(mb_strtolower($categoryAr), 'ساعة') 
            || str_contains(mb_strtolower($categoryAr), 'ساعات');

        $isCar = str_contains(mb_strtolower($categoryEn), 'car') 
            || str_contains(mb_strtolower($categoryAr), 'سيار');

        $colorPhrase = $color ? "in {$color} finish" : "in sleek premium finish";

        if ($isPhone) {
            return "A realistic ultra-high-resolution commercial studio product photography of authentic {$brand} {$model} flagship smartphone {$colorPhrase}. Showcasing the genuine modern device with ultra-thin bezels, dynamic screen, precision metal/titanium edges, and rear camera module, centered, pure clean seamless white background, professional studio lighting, product catalog photography, 8k resolution, crisp details, sharp focus, no distortion, no text, no watermark.";
        }

        if ($isWatch) {
            return "A realistic ultra-high-resolution commercial studio product photography of authentic {$brand} {$model} luxury watch {$colorPhrase}. Centered composition on pure clean seamless white background, elegant professional studio lighting, horology catalog photography, 8k resolution, sharp focus, crisp details, no watermark.";
        }

        if ($isCar) {
            return "A realistic high-resolution commercial automotive studio photograph of authentic {$brand} {$model} {$colorPhrase}. Pure white showroom studio background, professional studio lighting, sharp crisp details, 8k, no watermark.";
        }

        return "A realistic high-resolution commercial studio product photograph of authentic {$brand} {$model} ({$categoryEn}) {$colorPhrase}. Centered on pure clean white background, professional commercial studio lighting, 8k resolution, sharp focus, photorealistic catalog photo, no watermark.";
    }

    private function translateBrandToEnglish(string $brand): string
    {
        $b = mb_strtolower(trim($brand));
        $map = [
            'أبل' => 'Apple',
            'ابل' => 'Apple',
            'apple' => 'Apple',
            'سامسونج' => 'Samsung',
            'سامسونغ' => 'Samsung',
            'samsung' => 'Samsung',
            'هواوي' => 'Huawei',
            'huawei' => 'Huawei',
            'شاومي' => 'Xiaomi',
            'xiaomi' => 'Xiaomi',
            'سوني' => 'Sony',
            'sony' => 'Sony',
            'رولكس' => 'Rolex',
            'rolex' => 'Rolex',
            'كارتييه' => 'Cartier',
            'cartier' => 'Cartier',
            'أوميغا' => 'Omega',
            'اوميغا' => 'Omega',
            'omega' => 'Omega',
            'تويوتا' => 'Toyota',
            'toyota' => 'Toyota',
            'مرسيدس' => 'Mercedes-Benz',
            'mercedes' => 'Mercedes-Benz',
            'بي ام دبليو' => 'BMW',
            'bmw' => 'BMW',
            'لكزس' => 'Lexus',
            'lexus' => 'Lexus',
            'هوندا' => 'Honda',
            'honda' => 'Honda',
            'نيسان' => 'Nissan',
            'nissan' => 'Nissan',
            'فورد' => 'Ford',
            'ford' => 'Ford',
            'هيونداي' => 'Hyundai',
            'hyundai' => 'Hyundai',
            'كيا' => 'Kia',
            'kia' => 'Kia',
        ];
        return $map[$b] ?? $brand;
    }

    private function translateColorToEnglish(string $color): string
    {
        $c = mb_strtolower(trim($color));
        $map = [
            'تيتانيوم أسود' => 'Black Titanium',
            'تيتانيوم اسود' => 'Black Titanium',
            'تيتانيوم طبيعي' => 'Natural Titanium',
            'تيتانيوم أبيض' => 'White Titanium',
            'تيتانيوم ابيض' => 'White Titanium',
            'تيتانيوم أزرق' => 'Blue Titanium',
            'تيتانيوم ازرق' => 'Blue Titanium',
            'تيتانيوم صحراوي' => 'Desert Titanium',
            'أسود' => 'Black',
            'اسود' => 'Black',
            'أبيض' => 'White',
            'ابيض' => 'White',
            'أزرق' => 'Deep Blue',
            'ازرق' => 'Deep Blue',
            'كحلي' => 'Navy Blue',
            'فضي' => 'Silver',
            'ذهبي' => 'Gold',
            'رمادي' => 'Space Gray',
            'أخضر' => 'Green',
            'اخضر' => 'Green',
            'أحمر' => 'Red',
            'احمر' => 'Red',
        ];
        return $map[$c] ?? $color;
    }

    private function detectBrandFromText(string $text): ?string
    {
        $t = mb_strtolower($text);
        if (str_contains($t, 'أبل') || str_contains($t, 'ابل') || str_contains($t, 'apple') || str_contains($t, 'iphone')) return 'Apple';
        if (str_contains($t, 'سامسونج') || str_contains($t, 'samsung') || str_contains($t, 'galaxy')) return 'Samsung';
        if (str_contains($t, 'هواوي') || str_contains($t, 'huawei')) return 'Huawei';
        if (str_contains($t, 'شاومي') || str_contains($t, 'xiaomi')) return 'Xiaomi';
        if (str_contains($t, 'رولكس') || str_contains($t, 'rolex')) return 'Rolex';
        if (str_contains($t, 'تويوتا') || str_contains($t, 'toyota')) return 'Toyota';
        return null;
    }
}