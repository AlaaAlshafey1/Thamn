<?php

namespace App\Services\Evaluation\Contexts;

use App\Services\Evaluation\Contracts\CategoryEvaluationContext;

class CarsContext implements CategoryEvaluationContext
{
    public static function getKeywords(): array
    {
        return ['سيار', 'car', 'vehicle', 'auto', 'مركب'];
    }

    public function getRole(): string
    {
        return 'أنت مثمّن سيارات معتمد ومتخصص في السوق السعودي، ولديك خبرة واسعة في تقييم السيارات الجديدة والمستعملة.';
    }

    public function getMarketReferences(): string
    {
        return 'المراجع السوقية: منصة حراج (قسم السيارات)، تطبيق موجز، تطبيق نجم، وموقع سيارة.
تنبيه مهم عن حراج: أسعار حراج تمثّل عروض البيع الفردية وعادةً تكون أقل بـ 5-10% من القيمة السوقية الحقيقية بسبب التفاوض. لذا استخدمها كحد أدنى للمقارنة، لا كسعر مرجعي وحيد.';
    }

    public function getPricingTips(): string
    {
        return '- ارجع لأسعار نفس الموديل والسنة والشكل (فيس ليفت / قبل فيس ليفت) في السوق السعودي الحالي.
- خذ بعين الاعتبار: الماركة، الموديل، سنة الصنع، المسافة المقطوعة (الكيلومترات)، نوع الوقود، ناقل الحركة، اللون، الحوادث، الصيانة.
- السيارات المستوردة الأمريكية (سلفج/كلين) لها أسعار مختلفة عن الوكالة.
- خذ متوسط الأسعار لا أدناها ولا أعلاها.';
    }

    public function getNoImageDataTips(): string
    {
        return 'احسب السعر بناءً على: السنة، الماركة، الموديل، الفئة، المسافة المقطوعة، نوع الجير.';
    }

    public function getImageAnalysisTips(): string
    {
        return '- قيّم حالة البودي الخارجي (خدوش، رشات دهان، صدمات).
- لاحظ حالة الإطارات والجنوط.
- افحص حالة المقصورة الداخلية (المقاعد، الطبلون، عداد المسافة).
- تحقق من وجود تعديلات أو إضافات.';
    }
}
