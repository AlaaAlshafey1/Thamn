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

        // إنشاء طلب جديد
        $order = Order::create([
            'user_id' => $user->id,
            'category_id' => 1, // ممكن تختار أي فئة افتراضية
            'total_price' => rand(100, 1000), // سعر افتراضي
            'status' => 'pending',
        ]);

        // جلب كل الأسئلة النشطة
        $questions = Question::with('options.suboptions')
                        ->where('is_active', 1)
                        ->get();

        foreach ($questions as $question) {

            $selectedOptionId = null;

            // لو السؤال ليه خيارات
            if ($question->options->count()) {

                $option = $question->options->random(); // اختيار عشوائي للخيار

                $selectedOptionId = $option->id;

                // لو فيه sub-options نختار منهم كمان
                if ($option->suboptions->count()) {
                    $subOption = $option->suboptions->random();
                    $selectedOptionId = $subOption->id;
                }

            }

            // إنشاء detail للطلب
            OrderDetails::create([
                'order_id' => $order->id,
                'question_id' => $question->id,
                'option_id' => $selectedOptionId,
                'value' => null, // لو السؤال input أو slider ممكن تحط قيمة افتراضية هنا
                'price' => $selectedOptionId ? $option->price ?? 0 : 0,
            ]);
        }

        $this->command->info('تم إنشاء طلب افتراضي للمستخدم رقم 2 مع كل الأسئلة!');
    }
}
