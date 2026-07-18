<?php

namespace App\Services\Evaluation\Contexts;

use App\Services\Evaluation\Contracts\CategoryEvaluationContext;

class DefaultContext implements CategoryEvaluationContext
{
    protected string $categoryName;

    public function __construct(string $categoryName = 'هذا المنتج')
    {
        $this->categoryName = $categoryName;
    }

    public static function getKeywords(): array
    {
        return []; // Default matches everything else
    }

    public function getRole(): string
    {
        return "أنت مثمّن معتمد ومتخصص في السوق السعودي، ولديك خبرة واسعة في تقييم {$this->categoryName}.";
    }

    public function getMarketReferences(): string
    {
        return "المراجع السوقية: منصة حراج، المتاجر الإلكترونية السعودية المتخصصة، أمازون السعودية، نون.
ابحث عن أسعار نفس المنتج أو منتجات مشابهة في السوق السعودي للوصول لسعر عادل.";
    }

    public function getPricingTips(): string
    {
        return "- خذ بعين الاعتبار جميع المواصفات والتفاصيل المذكورة في البيانات.
- قارن بمنتجات مشابهة في السوق السعودي.
- احسب نسبة الإهلاك بناءً على عمر المنتج وحالته.
- الماركة والجودة عاملان أساسيان في التسعير.";
    }

    public function getNoImageDataTips(): string
    {
        return 'احسب السعر بناءً على جميع البيانات الوصفية المتاحة.';
    }

    public function getImageAnalysisTips(): string
    {
        return '- قيّم الحالة العامة للمنتج.
- لاحظ أي عيوب أو تلف ظاهر.
- تحقق من مدى النظافة والصيانة.
- قارن المظهر بالمنتج الجديد.';
    }
}
