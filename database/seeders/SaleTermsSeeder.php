<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TermCondition;

class SaleTermsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $checkboxTextAr = "أتعهد وأقسم بالله العظيم أن أجيب عن كافة الأسئلة بدقة ومصداقية وحيادية وبدون مبالغة أو تحيز .\nوأقر بأنني أرغب الاستفادة من خدمات التطبيق في معرفة السعر التقديري العادل من غير مسؤولية تلحق بالتطبيق في حال البيع أو الشراء أو المقايضة أو ماشابه ذلك .";
        
        $checkboxTextEn = "I swear by Almighty Allah that I will answer all questions accurately, truthfully, objectively, and without exaggeration or bias.\nI also acknowledge my desire to benefit from the application's services to know the fair estimated price, without any liability attaching to the application in the event of sale, purchase, barter, or similar transactions.";

        TermCondition::updateOrCreate(
            ['type' => 'sale_terms'],
            [
                'title_ar' => 'اتفاقية الرسوم',
                'title_en' => 'Fees Agreement',
                'content_ar' => 'هذه هي الشروط والأحكام الخاصة بالتثمين والبيع...',
                'content_en' => 'These are the terms and conditions for valuation and sale...',
                'is_active' => 1,
                'sort_order' => 1,
                'checkbox_label_ar' => $checkboxTextAr,
                'checkbox_label_en' => $checkboxTextEn,
            ]
        );
    }
}
