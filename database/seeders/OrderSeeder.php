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
            'status' => 'waitingPayment',
        ]);

        $totalPrice = 0;
        $questions = Question::with('options.suboptions')->where('is_active', 1)->get();

        foreach ($questions as $question) {
            $selectedOptionId = null;
            $value = null;
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
            } else {
                // سؤال بدون options → يستخدم value نصية حسب النوع
                $value = match ($question->type ?? 'text') {
                    'number'   => rand(1, 100),
                    'price'    => rand(5000, 80000),
                    'year'     => rand(2010, 2024),
                    'location' => 'الرياض',
                    'textarea' => 'وصف تجريبي للسلعة',
                    default    => 'قيمة تجريبية',
                };
            }

            OrderDetails::create([
                'order_id'   => $order->id,
                'question_id'=> $question->id,
                'option_id'  => $selectedOptionId,
                'value'      => $value,
                'price'      => $price,
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

        // === تم إيقاف توليد الصورة وتقييم الـ AI هنا ===
        // سيتم تنفيذهم تلقائياً بعد نجاح الدفع من خلال الـ Controller

        $this->command->info("تم إنشاء الطلب #{$order->id} بنجاح. في انتظار الدفع (waitingPayment)...");
    }
}
