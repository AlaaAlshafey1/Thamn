<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderDetails;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $user = \App\Models\User::first();

        $order = Order::create([
            'user_id' => $user->id,
            'category_id' => 1, // سيارات
            'total_price' => 0, // سيتم حسابه
            'status' => 'pending',
        ]);

        $totalPrice = 0;
        $questions = Question::with('options.suboptions')->where('is_active', 1)->get();

        foreach ($questions as $question) {
            $selectedOptionId = null;
            $price = 0;

            if ($question->options->count()) {
                // إذا كان هذا سؤال نوع التقييم (rateTypeSelection)، نختار AI
                if ($question->type === 'rateTypeSelection') {
                    $option = $question->options->where('badge', 'ai')->first() ?? $question->options->first();
                } else {
                    $option = $question->options->random();
                }

                $selectedOptionId = $option->id;
                $price = $option->price ?? 0;

                if ($option->suboptions && $option->suboptions->count()) {
                    $subOption = $option->suboptions->random();
                    $selectedOptionId = $subOption->id;
                    $price += $subOption->price ?? 0;
                }
            }

            OrderDetails::create([
                'order_id' => $order->id,
                'question_id' => $question->id,
                'option_id' => $selectedOptionId,
                'value' => null,
                'price' => $price,
            ]);
        }

        // إيجاد سعر باقة التقييم المختارة
        $rateTypeAnswer = $order->details()->whereHas('question', function ($q) {
            $q->where('type', 'rateTypeSelection');
        })->first();

        if ($rateTypeAnswer) {
            $totalPrice = $rateTypeAnswer->option->price ?? 0;
        }

        // بما أن العميل (Seeder) لم يرفع صورة، يتم إضافة رسوم الصورة (5 ريال)
        $totalPrice += env('IMAGE_GENERATION_FEE', 5);

        $order->update([
            'total_price' => $totalPrice,
            'status' => 'orderReceived' // تم الدفع
        ]);

        $this->command->info("تم إنشاء الطلب #{$order->id} بنجاح. جاري توليد الصورة وإجراء تقييم الذكاء الاصطناعي...");

        // === محاكاة ما يحدث بعد الدفع (توليد الصورة ديناميكياً) ===
        // التوليد اليدوي للصورة يتم فقط للخبراء، أما AI فيولدها بنفسه أثناء التقييم
        $evaluationType = $rateTypeAnswer?->option?->badge ?? $rateTypeAnswer?->value;
        if ($evaluationType !== 'ai') {
            try {
                $qaLines = [];
                foreach ($order->details as $detail) {
                    $qText = $detail->question->question_en ?? $detail->question->question_ar;
                    $aText = $detail->option->option_en ?? $detail->option->option_ar ?? $detail->value;
                    if ($qText && $aText) {
                        $qaLines[] = "{$qText}: {$aText}";
                    }
                }
                $qaText = implode(", ", $qaLines);
                $category = $order->category->name_en ?? 'Car';

                $prompt = "A highly realistic, professional studio photograph of a {$category} with the following specifications: {$qaText}. Pure white background, centered, well lit, high quality.";
                
                $imageUrl = app(\App\Services\OpenAIService::class)->generateImage($prompt);
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
                    
                    $order->load('files');
                    $this->command->info("تم توليد الصورة وإرفاقها بالطلب بنجاح.");
                }
            } catch (\Exception $e) {
                $this->command->error("فشل في توليد الصورة: " . $e->getMessage());
            }
        }

        // === محاكاة توجيه التقييم إلى AI ===
        try {
            app(\App\Services\ThamnEvaluationService::class)->runAiEvaluation($order);
            $this->command->info("تم التقييم بواسطة AI بنجاح!");
        } catch (\Exception $e) {
            $this->command->error("فشل في تقييم AI: " . $e->getMessage());
        }
    }
}
