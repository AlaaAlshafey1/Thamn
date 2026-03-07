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
        $qaText = '';
        foreach ($order->details as $detail) {
            $question = $detail->question->name_ar ?? null;
            $answer = $detail->option->name_ar ?? $detail->value ?? null;

            if ($question && $answer) {
                $qaText .= "- {$question}: {$answer}\n";
            }
        }

        $imageFiles = $order->files->where('type', 'image');
        $hasImages = $imageFiles->isNotEmpty();

        $imagePromptSection = $hasImages
            ? "التحليل البصري: ابدأ فوراً بوصف ما تراه في الصور (الحالة، النظافة، العيوب، اللمعان). إذا كانت الصور ممتازة، قل ذلك. إذا كانت هناك خدوش، حددها."
            : "ملاحظة هامة: لا توجد صور مرفقة لهذا الطلب. مهمتك هي 'تخيل' السلعة بناءً على الوصف والبيانات التقنية المرفقة، وتقديم تقييم افتراضي دقيق بافتراض حالة السلعة (جيدة جداً) ما لم يذكر الوصف غير ذلك. اجعل وصفك يبدو واقعياً وكأنك ترى السلعة فعلاً بناءً على خبرتك في هذا الموديل/الفئة.";

        $prompt = <<<PROMPT
أنت الآن "كبير مثمني السلع" وخبير معتمد في السوق السعودي. صفتك: حازم، تقني، مباشر، وصارم جداً.
مهمتك: تقديم تثمين نهائي وقاطع بناءً على المعطيات، وكأنك تقف أمام السلعة في مزاد علني رفيع المستوى.

السياق السوقي:
- المنطقة: المملكة العربية السعودية (المقارنة بأسعار: حراج، منصات السلع الفاخرة، والوكالات المحلية).
- الفئة المستهدفة: {$order->category->name_ar}

البيانات الوصفية المستلمة:
{$qaText}

المطلوب منك (بمنتهى الصرامة المهنية):
1. {$imagePromptSection}
2. التسعير الاحترافي: حدد (الحد الأدنى، الحد الأعلى، والسعر العادل الموصى به) بالريال السعودي. يجب أن تعكس الأرقام القيمة الفعلية الحالية في السوق السعودي المستعمل.
3. التحليل التبريري (Reasoning):
   - استخدم لغة تقريرية جازمة (مثال: "بناءً على المواصفات الفنية المذكورة، فإن هذه السلعة تصنف ضمن..." أو "ندرة هذه النسخة في السوق السعودي ترفع قيمتها التقديرية بنسبة...").
   - [ممنوع منعاً باتاً]: استخدام عبارات احتمالية مثل "ربما"، "يمكن أن يكون"، "قد تختلف"، "يُفضل الفحص".
   - [ممنوع]: النصائح العامة. نريد حكماً فنياً ومالياً.
   - إذا لم توجد صور، صف السلعة وصفاً 'افتراضياً' يقرب الصورة لصاحبها بناءً على البيانات (مثال: "بالنظر لمواصفات هذا الموديل المتوفرة لدينا، غالباً ما تكون حالة الهيكل...").

الشروط التقنية:
- الرد يجب أن يكون بصيغة JSON حصراً.
- ممنوع أي نص ترحيبي أو ختامي خارج الـ JSON.

{
"min_price": number,
"max_price": number,
"recommended_price": number,
"currency": "SAR",
"confidence": number,
"reasoning": "نص احترافي قاطع ومباشر يحلل السلعة مالياً وفنياً بلهجة الخبير الواثق"
}
PROMPT;

        $imagePaths = $imageFiles->map(fn($f) => storage_path('app/public/' . $f->file_path))->toArray();

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
