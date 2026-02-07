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
أنت الآن "خبير التثمين الأول" في منصة ثمن. صفتك: حازم، خبير، ومباشر.
مهمتك: تقديم تقييم نهائي وقاطع بناءً على ما تراه أمامك من صور وبيانات، وكأنك تقف أمام السلعة الآن.

السياق:
- الدولة: المملكة العربية السعودية (KSA)
- الفئة: {$order->category->name_ar}

البيانات:
{$qaText}

المطلوب منك (بمنتهى الصرامة):
1. التحليل البصري: ابدأ فوراً بوصف ما تراه في الصور (الحالة، النظافة، العيوب، اللمعان). إذا كانت الصور ممتازة، قل ذلك. إذا كانت هناك خدوش، حددها.
2. التثمين المالي: حدد الحد الأدنى، الأعلى، والسعر الموصى به بناءً على معرفتك العميقة بأسعار "حراج" والمنصات السعودية للسلع المستعملة والفاخرة.
3. السبب (Reasoning): اكتب شرحاً موجزاً ومقنعاً لصاحب السلعة. 
   - [ممنوع منعاً باتاً]: استخدام جمل مثل "يجب مراعاة.." أو "يُنصح بفحص.." أو "تعتمد القيمة على..".
   - [المطلوب]: استخدم جمل تقريرية مثل "الحالة الفنية للسيارة تضعها في فئة (ممتاز) ولذلك سعرها هو.." أو "نظافة الهيكل الخارجي وندرة اللون في السوق السعودي تدعم هذا التقييم..".
   - اجعل الرد يبدو كأن خبيراً بشرياً قام بفحص السلعة وأعطى كلمته الأخيرة.

الشروط التقنية:
- الرد JSON فقط.
- ممنوع أي نص خارج الـ JSON.

{
"min_price": number,
"max_price": number,
"recommended_price": number,
"currency": "SAR",
"confidence": number,
"reasoning": "نص احترافي قاطع ومباشر يحلل ما ظهر في الصور والبيانات بدون نصائح عامة"
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
