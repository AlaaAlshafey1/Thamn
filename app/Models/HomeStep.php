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
    public function getTitle($lang = 'en')
    {
        return $lang === 'ar' ? $this->title_ar : $this->title_en;
    }

    /**
     * Get localized subtitle
     */
    public function getSubTitle($lang = 'en')
    {
        return $lang === 'ar' ? $this->sub_title_ar : $this->sub_title_en;
    }

    /**
     * Get localized description
     */
    public function getDesc($lang = 'en')
    {
        return $lang === 'ar' ? $this->desc_ar : $this->desc_en;
    }

    /**
     * Get localized items
     */
    public function getLocalizedItems($lang = 'en')
    {
        if (!$this->items) {
            return [];
        }

        return array_map(function ($item) use ($lang) {
            // Support both simple and multi-language item formats
            return [
                'label' => $item['label_' . $lang] ?? $item['label'] ?? '',
                'value' => $item['value_' . $lang] ?? $item['value'] ?? '',
                'image' => $item['image'] ?? null,
            ];
        }, $this->items);
    }
}
