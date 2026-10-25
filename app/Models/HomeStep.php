<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeStep extends Model
{
    protected $fillable = [
        'title_ar',
        'title_en',
        'sub_title_ar',
        'sub_title_en',
        'desc_ar',
        'desc_en',
        'type',
        'items',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'items' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get localized title
     */
    public function getTitle($lang = 'ar')
    {
        $val = $lang === 'ar' ? $this->title_ar : $this->title_en;
        return !empty($val) ? $val : $this->title_ar;
    }

    /**
     * Get localized subtitle
     */
    public function getSubTitle($lang = 'ar')
    {
        $val = $lang === 'ar' ? $this->sub_title_ar : $this->sub_title_en;
        return !empty($val) ? $val : $this->sub_title_ar;
    }

    /**
     * Get localized description
     */
    public function getDesc($lang = 'ar')
    {
        $val = $lang === 'ar' ? $this->desc_ar : $this->desc_en;
        return !empty($val) ? $val : $this->desc_ar;
    }

    /**
     * Get localized items
     */
    public function getLocalizedItems($lang = 'ar')
    {
        if (!$this->items) {
            return [];
        }

        return array_map(function ($item) use ($lang) {
            // Support both simple and multi-language item formats with Arabic fallback
            $label = $item['label_' . $lang] ?? ($lang === 'ar' ? ($item['label_ar'] ?? $item['label'] ?? '') : ($item['label_en'] ?? $item['label'] ?? ''));
            if (empty($label)) {
                $label = $item['label_ar'] ?? $item['label'] ?? '';
            }

            $value = $item['value_' . $lang] ?? ($lang === 'ar' ? ($item['value_ar'] ?? $item['value'] ?? '') : ($item['value_en'] ?? $item['value'] ?? ''));
            if (empty($value)) {
                $value = $item['value_ar'] ?? $item['value'] ?? '';
            }

            return [
                'label' => $label,
                'value' => $value,
                'image' => $item['image'] ?? null,
            ];
        }, $this->items);
    }
}
