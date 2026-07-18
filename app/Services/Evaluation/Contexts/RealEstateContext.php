<?php

namespace App\Services\Evaluation\Contexts;

use App\Services\Evaluation\Contracts\CategoryEvaluationContext;

class RealEstateContext implements CategoryEvaluationContext
{
    public static function getKeywords(): array
    {
        return ['عقار', 'شقة', 'فيلا', 'أرض', 'بيت', 'real_estate', 'property', 'apartment', 'villa', 'land', 'estate', 'منزل', 'دوبلكس', 'real estate'];
    }

    public function getRole(): string
    {
        return 'أنت مثمّن عقاري معتمد ومتخصص في السوق العقاري السعودي، ولديك خبرة في تقييم الشقق، الفلل، الأراضي، والمباني التجارية.';
    }

    public function getMarketReferences(): string
    {
        return 'المراجع السوقية: تطبيق عقار، منصة حراج (قسم العقارات)، مؤشرات وزارة الإسكان السعودية، بوابة عقار ماب، تطبيق ديل.
ملاحظة: الأسعار العقارية تتفاوت بشكل كبير حسب المدينة والحي والموقع الدقيق. الموقع هو العامل الأهم في التسعير العقاري.';
    }

    public function getPricingTips(): string
    {
        return '- خذ بعين الاعتبار: المدينة، الحي، المساحة (م²)، عدد الغرف، عمر العقار، الطابق، الاتجاه، وجود مصعد/مواقف/حديقة.
- سعر المتر المربع يختلف جذرياً بين المدن (الرياض ≠ جدة ≠ الدمام) وبين الأحياء في نفس المدينة.
- العقارات الجديدة (أقل من 5 سنوات) لها علاوة سعرية مقارنة بالقديمة.
- وجود صك إلكتروني وضمانات يرفع القيمة.
- القرب من الخدمات (مدارس، مساجد، طرق رئيسية) يؤثر إيجابياً.
- العقارات التجارية تُقيّم بناءً على العائد الإيجاري السنوي أيضاً.';
    }

    public function getNoImageDataTips(): string
    {
        return 'احسب السعر بناءً على: نوع العقار، المدينة، الحي، المساحة، عدد الغرف، عمر البناء.';
    }

    public function getImageAnalysisTips(): string
    {
        return '- قيّم جودة التشطيبات (فاخرة / عادية / شعبية).
- لاحظ حالة الصيانة العامة والنظافة.
- افحص جودة المطبخ والحمامات (مؤشر رئيسي لمستوى العقار).
- لاحظ الإطلالة والإضاءة الطبيعية.
- تحقق من وجود تشققات أو رطوبة أو عيوب إنشائية.';
    }
}
