<?php

namespace App\Services\Evaluation\Contexts;

use App\Services\Evaluation\Contracts\CategoryEvaluationContext;

class WatchesContext implements CategoryEvaluationContext
{
    public static function getKeywords(): array
    {
        return ['ساعة', 'ساعات', 'watch', 'watches', 'روليكس', 'rolex'];
    }

    public function getRole(): string
    {
        return 'أنت مثمّن ساعات فاخرة ومتخصص في السوق السعودي والخليجي، ولديك خبرة في تقييم الساعات السويسرية والفاخرة.';
    }

    public function getMarketReferences(): string
    {
        return 'المراجع السوقية: منصة حراج (قسم الساعات)، Chrono24، موقع كرونو العرب، محلات الساعات المعتمدة في السعودية.';
    }

    public function getPricingTips(): string
    {
        return '- خذ بعين الاعتبار: الماركة، الموديل، المادة (ستيل/ذهب/تيتانيوم)، حجم الكيس، سنة الإنتاج، الحركة (أوتوماتيك/كوارتز).
- وجود الأوراق والكرتونة الأصلية يرفع السعر بشكل كبير (10-25%).
- الساعات المحدودة الإصدار لها علاوة سعرية خاصة.
- تاريخ آخر صيانة من الوكيل يؤثر على السعر.';
    }

    public function getNoImageDataTips(): string
    {
        return 'احسب السعر بناءً على: الماركة، الموديل، الرقم المرجعي، المادة، سنة الإنتاج.';
    }

    public function getImageAnalysisTips(): string
    {
        return '- لاحظ حالة الميناء (dial) والعقارب.
- افحص حالة السوار/الحزام.
- تحقق من وجود خدوش على الزجاج أو الكيس.
- لاحظ إذا كان هناك تآكل أو بهتان.';
    }
}
