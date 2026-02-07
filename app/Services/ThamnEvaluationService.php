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

        $prompt = <<<PROMPT
أنت خبير تثمين محترف (Appraiser) معتمد في منطقة الخليج العربي، وتحديداً في المملكة العربية السعودية. مهمتك هي تقديم تثمين دقيق وواقعي للسلعة بناءً على البيانات المقدمة والصور المرفقة.

السياق:
- الدولة: المملكة العربية السعودية (KSA)
- العملة: ريال سعودي (SAR)
- الفئة المختارة: {$order->category->name_ar}

البيانات المقدمة من المستخدم:
{$qaText}

المطلوب:
1. تحليل السلعة بناءً على الصور المرفقة لتقييم (الحالة الفيزيائية، النظافة، وجود خدوش أو عيوب، أصالة الماركة إن وجدت) بالإضافة إلى الندرة والطلب الحالي في السوق السعودي.
2. تقديم ثلاثة قيم واقعية:
   - "min_price": الحد الأدنى للسعر في حالة البيع السريع.
   - "max_price": الحد الأعلى للسعر الذي يمكن أن تصل إليه السلعة في حالة ممتازة ومشتري مهتم.
   - "recommended_price": السعر العادل (Fair Market Value) الذي تنصح به للبيع.
3. كتابة "reasoning" (السبب) باللغة العربية بأسلوب احترافي يشرح العوامل البصرية والفنية التي أثرت على هذا التقييم.

الشروط:
- الرد يجب أن يكون بصيغة JSON فقط.
- ممنوع كتابة أي كلمات خارج ملف JSON.
- تأكد من أن الأسعار واقعية وبالريال السعودي.

{
"min_price": number,
"max_price": number,
"recommended_price": number,
"currency": "SAR",
"confidence": number,
"reasoning": "شرح احترافي بالعربية يوضح ما تم رؤيته في الصور والتحليل الفني"
}
PROMPT;

        $imagePaths = $order->files->where('type', 'image')->map(fn($f) => storage_path('app/public/' . $f->file_path))->toArray();

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
            $tokens = array_filter([$order->user->fcm_token_android, $order->user->fcm_token_ios]);
            if (!empty($tokens)) {
                $this->notifyByFirebase(
                    lang('تم حساب السعر العادل', 'Fair Price Calculated', request()),
                    lang('لقد قام فريق ثمن بحساب السعر العادل لمنتجك: ' . $thamnPrice . ' ريال', 'Thamn team calculated the fair price for your product: ' . $thamnPrice . ' SAR', request()),
                    $tokens,
                    ['data' => ['user_id' => $order->user_id, 'order_id' => $order->id, 'type' => 'thamn_ready']]
                );
            }
        }
    }

    private function sendEvaluationNotification(Order $order)
    {
        $user = $order->user;
        if ($user->fcm_token_android || $user->fcm_token_ios) {
            $tokens = array_filter([$user->fcm_token_android, $user->fcm_token_ios]);
            $this->notifyByFirebase(
                lang('تم تقييم منتجك بنجاح', 'Product Evaluated Successfully', request()),
                lang('تم الانتهاء من تقييم طلبك رقم ' . $order->id . ' يمكنك الاطلاع على النتائج الآن', 'Evaluation for order #' . $order->id . ' is finished, check the results now', request()),
                $tokens,
                ['data' => ['user_id' => $user->id, 'order_id' => $order->id, 'type' => 'evaluation_ready']]
            );
        }
    }
}
