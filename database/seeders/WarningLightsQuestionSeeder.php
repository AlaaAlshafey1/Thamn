<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuestionStep;
use Illuminate\Database\Seeder;

/**
 * سؤال "لمبات التنبيه" (warningLights) — فئة السيارات / مرحلة "حالته واستخدامه".
 *
 * نوع السؤال الجديد:  warningLights
 *  - الموبايل يعرض أولاً سؤال (نعم / لا).
 *  - لو الإجابة "نعم" تظهر شبكة لمبات التنبيه ويختار المستخدم منها (اختيار متعدد).
 *
 * كل خيار (option):
 *  - badge  => مفتاح الأيقونة (icon key) يستخدمه الموبايل لعرض الأيقونة المناسبة.
 *  - image  => يمكن رفع الأيقونة من الداشبورد (تعديل السؤال) وستظهر في حقل image بالـ API.
 *
 * Seeder آمن للتشغيل أكثر من مرة (updateOrCreate) ولن يكرر السؤال أو الخيارات.
 *
 * التشغيل:  php artisan db:seed --class=WarningLightsQuestionSeeder
 */
class WarningLightsQuestionSeeder extends Seeder
{
    public const TYPE = 'warningLights';

    public function run(): void
    {
        // فئة السيارات
        $category = Category::where('name_ar', 'السيارات')->first()
            ?? Category::where('name_en', 'like', 'car%')->first();

        if (!$category) {
            $this->command?->error('لم يتم العثور على فئة "السيارات" — تم إلغاء الـ Seeder.');
            return;
        }

        // مرحلة "حالته واستخدامه" (قسم الاستهلاك / الاستخدام)
        $step = QuestionStep::where('name_en', 'Condition and Usage')->first()
            ?? QuestionStep::where('name_ar', 'like', '%استخدام%')->first();

        if (!$step) {
            $this->command?->error('لم يتم العثور على مرحلة "حالته واستخدامه" — تم إلغاء الـ Seeder.');
            return;
        }

        $questionAr = 'هل لاحظت إضاءة (وميض) تنبيه متكرر من التنبيهات التالية نتيجة خلل محتمل ؟';
        $questionEn = 'Have you noticed a repeated warning light (flashing) from the following indicators due to a possible fault?';

        $question = Question::updateOrCreate(
            [
                'category_id' => $category->id,
                'stageing'    => $step->id,
                'type'        => self::TYPE,
            ],
            [
                'question_ar'    => $questionAr,
                'question_en'    => $questionEn,
                'description_ar' => null,
                'description_en' => null,
                'is_required'    => false,
                'is_active'      => true,
                'flow'           => 'valuation',
                'group_type'     => 'first',
                // يظهر بعد سؤال "مستوى استهلاكك لسلعتك"
                'order'          => 3,
                'settings'       => [
                    'hint' => [
                        'ar' => 'حالة السيارة',
                        'en' => 'Car condition',
                    ],
                    'titleDescription' => [
                        'ar' => $questionAr,
                        'en' => $questionEn,
                    ],
                ],
            ]
        );

        $noOption = QuestionOption::updateOrCreate(
            [
                'question_id'      => $question->id,
                'option_en'        => 'No',
            ],
            [
                'parent_option_id' => null,
                'option_ar' => 'لا',
                'order'     => 1,
                'is_active' => true,
            ]
        );

        $yesOption = QuestionOption::updateOrCreate(
            [
                'question_id'      => $question->id,
                'option_en'        => 'Yes',
            ],
            [
                'parent_option_id' => null,
                'option_ar' => 'نعم',
                'order'     => 2,
                'is_active' => true,
            ]
        );

        foreach ($this->lights() as $index => [$key, $ar, $en]) {
            QuestionOption::updateOrCreate(
                [
                    'question_id'      => $question->id,
                    'badge'            => $key,
                ],
                [
                    'parent_option_id' => $yesOption->id,
                    'option_ar' => $ar,
                    'option_en' => $en,
                    'image'     => null,
                    'order'     => $index + 1,
                    'is_active' => true,
                ]
            );
        }

        $this->command?->info("تمت إضافة/تحديث سؤال لمبات التنبيه (ID: {$question->id}) بعدد " . count($this->lights()) . ' خيار.');
    }

    /**
     * [icon_key, الاسم عربي, الاسم إنجليزي] — بنفس ترتيب الصور (صف بصف).
     */
    private function lights(): array
    {
        return [
            // الصف 1
            ['check_engine',    'لمبة المحرك',                 'Check Engine'],
            ['glow_plug',       'شمعات التسخين (ديزل)',        'Glow Plug (Diesel)'],
            ['brake_system',    'نظام الفرامل',                'Brake System'],
            ['headlight',       'المصابيح الأمامية',           'Headlights'],
            ['washer_fluid',    'سائل مسح الزجاج',             'Washer Fluid'],
            // الصف 2
            ['battery',         'البطارية / الشحن',            'Battery / Charging'],
            ['coolant_temp',    'حرارة المحرك',                'Engine Temperature'],
            ['brake_pad_wear',  'تآكل فحمات الفرامل',          'Brake Pad Wear'],
            ['high_beam',       'الإضاءة العالية',             'High Beam'],
            ['cruise_control',  'مثبت السرعة',                 'Cruise Control'],
            // الصف 3
            ['tire_pressure',   'ضغط الإطارات',                'Tire Pressure'],
            ['light_failure',   'عطل المصابيح',                'Lamp Failure'],
            ['abs',             'نظام ABS',                    'ABS'],
            ['turn_signal',     'إشارات الانعطاف',             'Turn Signals'],
            ['seat_belt',       'حزام الأمان',                 'Seat Belt'],
            // الصف 4
            ['rear_defroster',  'مزيل الضباب الخلفي',          'Rear Defroster'],
            ['position_lights', 'أضواء الموقف',                'Position Lights'],
            ['parking_brake',   'فرامل اليد',                  'Parking Brake'],
            ['dpf',             'فلتر الجسيمات (DPF)',         'Particulate Filter (DPF)'],
            ['lane_departure',  'الحيود عن المسار',            'Lane Departure'],
            // الصف 5
            ['oil_pressure',    'ضغط الزيت',                   'Oil Pressure'],
            ['daytime_lights',  'إضاءة النهار',                'Daytime Running Lights'],
            ['ebs',             'نظام EBS',                    'EBS'],
            ['oil_level',       'مستوى الزيت',                 'Oil Level'],
            ['wipers',          'مساحات الزجاج',               'Windshield Wipers'],
            // الصف 6
            ['low_fuel',        'انخفاض الوقود',               'Low Fuel'],
            ['power_steering',  'نظام التوجيه',                'Steering System'],
            ['brake_fault',     'عطل الفرامل',                 'Brake Fault'],
            ['exhaust_overheat', 'ارتفاع حرارة العادم',        'Exhaust Overheat'],
            ['trunk_open',      'الصندوق الخلفي مفتوح',        'Trunk Open'],
            // الصف 7
            ['airbag',          'الوسادة الهوائية',            'Airbag'],
            ['door_open',       'باب مفتوح',                   'Door Open'],
            ['icy_road',        'تحذير الطريق الجليدي',        'Icy Road Warning'],
            ['low_temperature', 'انخفاض درجة الحرارة',         'Low Temperature'],
            ['hood_open',       'غطاء المحرك مفتوح',           'Hood Open'],
        ];
    }
}
