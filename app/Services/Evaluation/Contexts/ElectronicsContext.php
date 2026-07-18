<?php

namespace App\Services\Evaluation\Contexts;

use App\Services\Evaluation\Contracts\CategoryEvaluationContext;

class ElectronicsContext implements CategoryEvaluationContext
{
    public static function getKeywords(): array
    {
        return ['إلكتروني', 'لابتوب', 'كمبيوتر', 'تلفزيون', 'شاشة', 'بلايستيشن', 'electronic', 'laptop', 'computer', 'tv', 'playstation', 'كاميرا', 'camera', 'تابلت', 'tablet', 'آيباد', 'ipad'];
    }

    public function getRole(): string
    {
        return 'أنت مثمّن إلكترونيات ومعدات تقنية متخصص في السوق السعودي، ولديك خبرة في تقييم الأجهزة الإلكترونية المتنوعة.';
    }

    public function getMarketReferences(): string
    {
        return 'المراجع السوقية: منصة حراج (قسم الأجهزة)، متجر جرير، متجر اكسترا، أمازون السعودية، نون.';
    }

    public function getPricingTips(): string
    {
        return '- خذ بعين الاعتبار: الماركة، الموديل، المواصفات، سنة الشراء، الحالة، الضمان المتبقي.
- الأجهزة الإلكترونية تفقد قيمتها بسرعة (20-30% سنوياً).
- وجود الكرتونة والفاتورة والضمان يرفع السعر.
- الأجهزة من ماركات معروفة (Apple, Sony, Samsung) تحتفظ بقيمتها أكثر.';
    }

    public function getNoImageDataTips(): string
    {
        return 'احسب السعر بناءً على: نوع الجهاز، الماركة، الموديل، المواصفات، عمر الجهاز.';
    }

    public function getImageAnalysisTips(): string
    {
        return '- لاحظ حالة الهيكل الخارجي.
- افحص حالة الشاشة (إن وجدت).
- تحقق من وجود جميع الملحقات.
- لاحظ إذا كان الجهاز أصلياً أو معدّلاً.';
    }
}
