<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ObjectionReason;

class ObjectionReasonSeeder extends Seeder
{
    public function run(): void
    {
        $reasons = [
            [
                'title_ar' => 'السعر المقدر أقل بكثير من القيمة السوقية الحالية للسلعة',
                'title_en' => 'Estimated price is much lower than the current market value of the item',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title_ar' => 'السعر المقدر أعلى من القيمة الواقعية للسلعة',
                'title_en' => 'Estimated price is higher than the realistic value of the item',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'title_ar' => 'لم يتم مراعاة حالة السلعة والمواصفات المرفقة بدقة',
                'title_en' => 'Item condition and attached specifications were not accurately considered',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'title_ar' => 'تم تجاهل ملحقات أو إضافات مميزة تؤثر إيجاباً على التثمين',
                'title_en' => 'Accessories or special add-ons positively affecting valuation were overlooked',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'title_ar' => 'سبب آخر (سأقوم بكتابة التفاصيل في الملاحظات)',
                'title_en' => 'Other reason (I will provide details in the notes)',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($reasons as $reason) {
            ObjectionReason::firstOrCreate(
                ['title_ar' => $reason['title_ar']],
                $reason
            );
        }
    }
}
