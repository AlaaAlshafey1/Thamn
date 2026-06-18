<?php

namespace App\Services;

use App\Models\Order;
use App\Services\OpenAIService;
use App\Http\Traits\FCMOperation;
use Illuminate\Support\Facades\Log;

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
            // الحقول الصحيحة في الـ Model هي question_ar و option_ar
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

        $imageSection = $hasImages
            ? "يوجد ({$imageCount}) صورة مرفقة. قم بتحليل كل صورة بعناية:
  - قيّم الحالة العامة (ممتازة / جيدة جداً / جيدة / مقبولة / رديئة).
  - حدد أي عيوب ظاهرة (خدوش، كسور، بهتان، تلف).
  - لاحظ مدى نظافة وصيانة السلعة.
  - عامل الصور كدليل أساسي لتعديل السعر لأعلى أو لأسفل."
            : "لا توجد صور مرفقة. قيّم السلعة بناءً على البيانات الوصفية فقط، بافتراض حالة (جيدة جداً) كسيناريو محايد.";

        // ─── بناء الـ Prompt ────────────────────────────────────────────────────
        $categoryAr = $order->category->name_ar ?? $order->category->name_en ?? 'غير محدد';
        $today      = now()->locale('ar')->translatedFormat('d F Y');

        $prompt = <<<PROMPT
أنت مثمّن معتمد ومتخصص في السوق السعودي للسلع المستعملة.
تاريخ التقييم: {$today}
المرجع السوقي: منصة حراج، سوق.com، أسعار الوكالات، ومزادات السلع المحلية.

━━━ بيانات السلعة ━━━
الفئة: {$categoryAr}
{$qaText}

━━━ تعليمات الصور ━━━
{$imageSection}

━━━ قواعد التسعير الإلزامية ━━━
1. أرجع أرقاماً حقيقية قابلة للمقارنة بأسعار السوق السعودي اليوم — لا أصفاراً ولا قيماً وهمية.
2. الفجوة بين min_price و max_price يجب ألا تتجاوز 25% من recommended_price (هامش واقعي).
3. recommended_price يجب أن يقع دائماً بين min_price و max_price.
4. confidence يعبّر عن مدى ثقتك بالتقييم من 0.0 إلى 1.0 (1.0 = ثقة تامة، 0.5 = بيانات ناقصة).
5. إذا كانت البيانات غير كافية لتقييم دقيق، أعطِ نطاقاً أوسع وخفّض confidence.

━━━ هيكل الرد (JSON فقط — لا نص خارجه) ━━━
{
  "min_price": <رقم صحيح بالريال السعودي>,
  "max_price": <رقم صحيح بالريال السعودي>,
  "recommended_price": <رقم صحيح بالريال السعودي>,
  "currency": "SAR",
  "confidence": <رقم عشري من 0.0 إلى 1.0>,
  "reasoning": "<تحليل احترافي مباشر: سبب التسعير، تأثير الحالة، مقارنة السوق، وأثر الصور إن وجدت — بلا عبارات احتمالية>"
}
PROMPT;

        // ─── مسارات الصور على الـ Server ───────────────────────────────────────
        $imagePaths = $imageFiles
            ->map(fn($f) => storage_path('app/public/' . $f->file_path))
            ->filter(fn($path) => file_exists($path))   // تأكد أن الملف موجود فعلاً
            ->values()
            ->toArray();

        $aiResult = app(OpenAIService::class)->evaluateProduct($prompt, $imagePaths);

        $order->update([
            'status' => "estimated",
            'ai_min_price' => $aiResult['min_price'] ?? null,
            'ai_max_price' => $aiResult['max_price'] ?? null,
            'ai_price' => $aiResult['recommended_price'] ?? null,
            'ai_confidence' => $aiResult['confidence'] ?? null,
            'ai_reasoning' => $aiResult['reasoning'] ?? null,
        ]);

        // Send FCM Notification
        $this->sendEvaluationNotification($order);

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
            $minPrice = round(($order->ai_min_price + $order->expert_min_price) / 2, 2);
            $maxPrice = round(($order->ai_max_price + $order->expert_max_price) / 2, 2);
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
            $fcmToken = $user->fcm_token ?? $user->fcm_token_android ?? $user->fcm_token_ios;
            if ($fcmToken) {
                $this->notifyByFirebase(
                    lang('تم حساب السعر العادل', 'Fair Price Calculated', request()),
                    lang('لقد قام فريق ثمن بحساب السعر العادل لمنتجك: ' . $thamnPrice . ' ريال', 'Thamn team calculated the fair price for your product: ' . $thamnPrice . ' SAR', request()),
                    [$fcmToken],
                    ['data' => ['user_id' => $order->user_id, 'order_id' => $order->id, 'type' => 'thamn_ready']]
                );
            }
        }
    }

    private function sendEvaluationNotification(Order $order)
    {
        $user = $order->user;
        $fcmToken = $user->fcm_token ?? $user->fcm_token_android ?? $user->fcm_token_ios;
        if ($fcmToken) {
            $this->notifyByFirebase(
                lang('تم تقييم منتجك بنجاح', 'Product Evaluated Successfully', request()),
                lang('تم الانتهاء من تقييم طلبك رقم ' . $order->id . ' يمكنك الاطلاع على النتائج الآن', 'Evaluation for order #' . $order->id . ' is finished, check the results now', request()),
                [$fcmToken],
                ['data' => ['user_id' => $user->id, 'order_id' => $order->id, 'type' => 'evaluation_ready']]
            );
        }
    }
}
