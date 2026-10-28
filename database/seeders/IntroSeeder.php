<?php

namespace Database\Seeders;

use App\Models\Intro;
use Illuminate\Database\Seeder;

class IntroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $intros = [
            [
                'id' => 1,
                'page' => 'welcome',
                'title_ar' => 'حياك في ثمن',
                'title_en' => 'Welcome to Thmmn',
                'sub_title_ar' => 'منصتك الموثوقة لتثمين المقتنيات والسلع المستعملة بدقة واحترافية',
                'sub_title_en' => 'Your trusted platform for accurate and professional valuation',
                'description_ar' => null,
                'description_en' => null,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'id' => 2,
                'page' => 'signup',
                'title_ar' => 'سجل الآن وخلك جاهز للتثمين',
                'title_en' => 'Create your account today and get ready for accurate valuations',
                'sub_title_ar' => 'انضم إلى مجتمع ثمن واستمتع بتجربة تثمين متكاملة وذكية',
                'sub_title_en' => 'Join the Thmmn community for an integrated smart valuation experience',
                'description_ar' => null,
                'description_en' => null,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'id' => 3,
                'page' => 'verify',
                'title_ar' => 'بس خطوة بسيطة وندخلك !',
                'title_en' => 'Just one quick step and you are all set !',
                'sub_title_ar' => 'أدخل رمز التحقق المرسل إلى هاتفك لمتابعة حسابك بأمان',
                'sub_title_en' => 'Enter the verification code sent to your phone to continue securely',
                'description_ar' => null,
                'description_en' => null,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'id' => 4,
                'page' => 'login',
                'title_ar' => 'هلا برجعتك !',
                'title_en' => 'Welcome back !',
                'sub_title_ar' => 'سعداء برؤيتك مرة أخرى في ثمن',
                'sub_title_en' => 'We are glad to see you again on Thmmn',
                'description_ar' => null,
                'description_en' => null,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'id' => 7,
                'page' => 'valuation_export',
                'title_ar' => 'تمام عليك ..',
                'title_en' => 'Well done ..',
                'sub_title_ar' => 'أنت على بعد خطوة واحدة من تصدير بطاقة التثمين لسلعتك !!',
                'sub_title_en' => 'You are one step away from exporting your item valuation card !!',
                'description_ar' => null,
                'description_en' => null,
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'id' => 8,
                'page' => 'imaginary_image_warning',
                'title_ar' => 'تنبيه',
                'title_en' => 'Notice',
                'sub_title_ar' => 'في حال عدم رفعكم لصورة حقيقية للسلعة سيتم توليد صورة تخيلية افتراضية برسوم رمزية 5 ريال',
                'sub_title_en' => 'If you do not upload a real image of the item, a virtual imaginary image will be generated for a nominal fee of 5 SAR',
                'description_ar' => null,
                'description_en' => null,
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'id' => 9,
                'page' => 'valuation_disclaimer',
                'title_ar' => 'حابين نذكرك',
                'title_en' => 'Just a reminder',
                'sub_title_ar' => 'التثمين لك مو لغيرك وراح يعطيك تصور تقديري مو سعر بيع ( ملزم )',
                'sub_title_en' => 'The valuation is for your guidance and provides an estimated conception, not a binding selling price',
                'description_ar' => null,
                'description_en' => null,
                'is_active' => true,
                'sort_order' => 9,
            ],
        ];

        foreach ($intros as $data) {
            // نتحقق من الصفحة أولاً أو الـ ID
            $existing = Intro::where('page', $data['page'])->first() ?? Intro::find($data['id']);
            if ($existing) {
                $existing->update($data);
            } else {
                Intro::create($data);
            }
        }
    }
}
