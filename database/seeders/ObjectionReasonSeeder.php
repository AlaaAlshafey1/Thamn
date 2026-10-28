<?php

namespace Database\Seeders;

use App\Models\ObjectionReason;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ObjectionReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // حفظ عنوان نافذة الاحتجاج (formTitle) في جدول الإعدادات
        Setting::updateOrCreate(
            ['key' => 'objection_form_title_ar'],
            ['value' => 'اختر سبب عدم رضاك عن التثمين']
        );

        Setting::updateOrCreate(
            ['key' => 'objection_form_title_en'],
            ['value' => 'Select the reason for your dissatisfaction with the valuation']
        );

        // تفريغ الجدول وإعادة ضبط الأسباب بالمعرفات (IDs) المطلوبة من تطبيق الموبايل
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ObjectionReason::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $reasons = [
            [
                'id' => 1,
                'type' => 'cat_with_sub_cat',
                'title_ar' => 'تقدير السعر',
                'title_en' => 'Price Estimation',
                'subtitle_ar' => 'السعر أعلى أو أقل من المتوقع',
                'subtitle_en' => 'Price is higher or lower than expected',
                'icon' => 'price_estimation',
                'options' => [
                    [
                        'id' => 1001,
                        'title_ar' => 'السعر أعلى من المتوقع',
                        'title_en' => 'Price is higher than expected',
                    ],
                    [
                        'id' => 1002,
                        'title_ar' => 'السعر أقل من المتوقع',
                        'title_en' => 'Price is lower than expected',
                    ],
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'id' => 2,
                'type' => 'input_modification',
                'title_ar' => 'تعديل المدخلات',
                'title_en' => 'Input Modification',
                'subtitle_ar' => 'طلب تعديل المدخلات الخاصة بالتثمين',
                'subtitle_en' => 'Request to modify valuation inputs',
                'icon' => 'input_modification',
                'options' => null,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'id' => 3,
                'type' => 'other_reason',
                'title_ar' => 'سبب آخر',
                'title_en' => 'Other Reason',
                'subtitle_ar' => 'اختر سبب آخر غير المذكور',
                'subtitle_en' => 'Choose another unlisted reason',
                'icon' => 'other_reason',
                'options' => null,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($reasons as $reason) {
            ObjectionReason::updateOrCreate(['id' => $reason['id']], $reason);
        }
    }
}
