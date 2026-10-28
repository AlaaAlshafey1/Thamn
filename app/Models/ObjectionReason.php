<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObjectionReason extends Model
{
    use HasFactory;

    protected $table = 'objection_reasons';

    protected $fillable = [
        'type',
        'title_ar',
        'title_en',
        'subtitle_ar',
        'subtitle_en',
        'icon',
        'options',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = [
        'title',
        'subtitle',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }

    public function getTitleAttribute()
    {
        $locale = app()->getLocale();
        return $this->getTitle($locale);
    }

    public function getSubtitleAttribute()
    {
        $locale = app()->getLocale();
        return $this->getSubtitle($locale);
    }

    public function getTitle($lang = 'ar')
    {
        if ($lang === 'en') {
            return !empty($this->title_en) ? $this->title_en : $this->title_ar;
        }
        return !empty($this->title_ar) ? $this->title_ar : $this->title_en;
    }

    public function getSubtitle($lang = 'ar')
    {
        if ($lang === 'en') {
            return !empty($this->subtitle_en) ? $this->subtitle_en : ($this->subtitle_ar ?? '');
        }
        return !empty($this->subtitle_ar) ? $this->subtitle_ar : ($this->subtitle_en ?? '');
    }

    public function getDefaultIcon(): string
    {
        return match ($this->type) {
            'cat_with_sub_cat' => 'price_estimation',
            'input_modification' => 'input_modification',
            default => 'other_reason',
        };
    }

    public function getLocalizedOptions($lang = 'ar'): array
    {
        if (empty($this->options) || !is_array($this->options)) {
            return [];
        }

        return collect($this->options)->values()->map(function ($opt, $idx) use ($lang) {
            $optId = isset($opt['id']) ? (int) $opt['id'] : (($this->id * 1000) + $idx + 1);
            $titleAr = $opt['title_ar'] ?? $opt['title'] ?? '';
            $titleEn = $opt['title_en'] ?? $opt['title'] ?? $titleAr;

            return [
                'id' => $optId,
                'title' => $lang === 'en' ? ($titleEn ?: $titleAr) : ($titleAr ?: $titleEn),
            ];
        })->all();
    }

    public function toApiArray($lang = 'ar'): array
    {
        $item = [
            'id' => $this->id,
            'type' => $this->type ?: 'other_reason',
            'title' => $this->getTitle($lang),
            'subtitle' => $this->getSubtitle($lang),
            'icon' => $this->icon ?: $this->getDefaultIcon(),
        ];

        $localizedOptions = $this->getLocalizedOptions($lang);
        if ($this->type === 'cat_with_sub_cat' || !empty($localizedOptions)) {
            $item['options'] = $localizedOptions;
        }

        return $item;
    }

    public static function getFormTitle($lang = 'ar'): string
    {
        if ($lang === 'en') {
            return Setting::where('key', 'objection_form_title_en')->value('value')
                ?: 'Select the reason for your dissatisfaction with the valuation';
        }

        return Setting::where('key', 'objection_form_title_ar')->value('value')
            ?: 'اختر سبب عدم رضاك عن التثمين';
    }

    public static function getFormattedData($lang = 'ar'): array
    {
        $types = self::active()->get()->map(fn($reason) => $reason->toApiArray($lang))->values()->all();

        return [
            'formTitle' => self::getFormTitle($lang),
            'types' => $types,
        ];
    }

    /**
     * Resolve either a main reason ID (e.g. 1, 2, 3) or a sub-option ID (e.g. 1001, 1002).
     * Returns ['model' => ?ObjectionReason, 'text' => ?string, 'type' => ?string]
     */
    public static function resolveSelection($reasonId = null, $optionId = null, $lang = 'ar'): array
    {
        $reasonModel = null;
        $selectedOptionTitle = null;

        if ($reasonId) {
            $reasonModel = self::find($reasonId);
        }

        // If reasonId wasn't found as a top-level row, check if reasonId is actually an option ID (like 1001 or 1002)
        if (!$reasonModel && $reasonId) {
            foreach (self::all() as $candidate) {
                if (is_array($candidate->options)) {
                    foreach ($candidate->options as $idx => $opt) {
                        $optId = isset($opt['id']) ? (int) $opt['id'] : (($candidate->id * 1000) + $idx + 1);
                        if ($optId === (int) $reasonId) {
                            $reasonModel = $candidate;
                            $titleAr = $opt['title_ar'] ?? $opt['title'] ?? '';
                            $titleEn = $opt['title_en'] ?? $opt['title'] ?? $titleAr;
                            $selectedOptionTitle = $lang === 'en' ? ($titleEn ?: $titleAr) : ($titleAr ?: $titleEn);
                            break 2;
                        }
                    }
                }
            }
        }

        // If explicit optionId was passed alongside reasonId
        if ($optionId) {
            $searchModels = $reasonModel ? [$reasonModel] : self::all();
            foreach ($searchModels as $candidate) {
                if (is_array($candidate->options)) {
                    foreach ($candidate->options as $idx => $opt) {
                        $optId = isset($opt['id']) ? (int) $opt['id'] : (($candidate->id * 1000) + $idx + 1);
                        if ($optId === (int) $optionId) {
                            $reasonModel = $reasonModel ?: $candidate;
                            $titleAr = $opt['title_ar'] ?? $opt['title'] ?? '';
                            $titleEn = $opt['title_en'] ?? $opt['title'] ?? $titleAr;
                            $selectedOptionTitle = $lang === 'en' ? ($titleEn ?: $titleAr) : ($titleAr ?: $titleEn);
                            break 2;
                        }
                    }
                }
            }
        }

        $textParts = [];
        if ($reasonModel) {
            $textParts[] = $reasonModel->getTitle($lang);
        }
        if ($selectedOptionTitle) {
            $textParts[] = $selectedOptionTitle;
        }

        return [
            'model' => $reasonModel,
            'text' => !empty($textParts) ? implode(' - ', array_unique($textParts)) : null,
            'type' => $reasonModel?->type,
        ];
    }
}
