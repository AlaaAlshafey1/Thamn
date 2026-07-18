<?php

namespace App\Services\Evaluation\Contexts;

use App\Services\Evaluation\Contracts\CategoryEvaluationContext;

class PhonesContext implements CategoryEvaluationContext
{
    public static function getKeywords(): array
    {
        return ['جوال', 'هاتف', 'موبايل', 'phone', 'mobile', 'جهاز ذكي', 'آيفون', 'iphone', 'سامسونج', 'samsung', 'هواوي'];
    }

    public function getRole(): string
    {
        return 'أنت مثمّن أجهزة إلكترونية وجوالات معتمد في السوق السعودي، ولديك خبرة في تقييم الهواتف الذكية المستعملة والجديدة.';
    }

    public function getMarketReferences(): string
    {
        return 'المراجع السوقية: منصة حراج (قسم الأجهزة)، متجر جرير (للأسعار الجديدة وحساب الإهلاك)، متجر اكسترا، أمازون السعودية.
ملاحظة: سعر الجوال المستعمل عادةً يكون بين 40% إلى 75% من سعر الجديد حسب الحالة وعمر الجهاز.';
    }

    public function getPricingTips(): string
    {
        return '- خذ بعين الاعتبار: الماركة، الموديل، مساحة التخزين، حالة البطارية (صحة البطارية)، الحالة العامة، الملحقات المتوفرة.
- أجهزة أبل (iPhone) تحتفظ بقيمتها أكثر من أندرويد.
- وجود الكرتونة الأصلية والملحقات يرفع السعر بـ 5-10%.
- الأجهزة المكسورة الشاشة تنخفض قيمتها بـ 30-50%.
- تحقق من إصدار الجهاز (سعودي / دولي) لأنه يؤثر على السعر.';
    }

    public function getNoImageDataTips(): string
    {
        return 'احسب السعر بناءً على: الماركة، الموديل، مساحة التخزين، عمر الجهاز، الحالة العامة.';
    }

    public function getImageAnalysisTips(): string
    {
        return '- لاحظ حالة الشاشة (خدوش، كسور، بيكسلات ميتة).
- افحص حالة الهيكل الخارجي (خدوش، انبعاجات).
- تحقق من حالة الكاميرا والأزرار.
- لاحظ إذا كان الجهاز أصلياً أو مجدداً (refurbished).';
    }
}
