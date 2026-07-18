<?php

namespace App\Services\Evaluation\Contexts;

use App\Services\Evaluation\Contracts\CategoryEvaluationContext;

class FurnitureContext implements CategoryEvaluationContext
{
    public static function getKeywords(): array
    {
        return ['أثاث', 'مفروشات', 'furniture', 'كنب', 'طاولة', 'سرير'];
    }

    public function getRole(): string
    {
        return 'أنت مثمّن أثاث ومفروشات متخصص في السوق السعودي، ولديك خبرة في تقييم الأثاث المنزلي والمكتبي.';
    }

    public function getMarketReferences(): string
    {
        return 'المراجع السوقية: منصة حراج (قسم الأثاث)، متجر هوم سنتر، ايكيا السعودية، ميداس.';
    }

    public function getPricingTips(): string
    {
        return '- خذ بعين الاعتبار: نوع الأثاث، الماركة، المادة (خشب طبيعي/صناعي/MDF)، الحالة، العمر.
- الأثاث المستعمل عادةً يفقد 40-70% من قيمته الأصلية.
- الأثاث الفاخر (من ماركات عالمية) يحتفظ بقيمته أكثر.
- الأطقم الكاملة أغلى نسبياً من القطع المنفردة.';
    }

    public function getNoImageDataTips(): string
    {
        return 'احسب السعر بناءً على: نوع القطعة، الماركة، المادة، العمر، الحالة العامة.';
    }

    public function getImageAnalysisTips(): string
    {
        return '- قيّم حالة التنجيد والقماش.
- لاحظ أي خدوش أو كسور في الهيكل.
- افحص حالة الأدراج والمفصلات.
- تحقق من مدى النظافة العامة.';
    }
}
