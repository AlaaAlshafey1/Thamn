<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Intro extends Model
{
    protected $fillable = [
        'page',
        'title_ar',
        'title_en',
        'sub_title_ar',
        'sub_title_en',
        'description_ar',
        'description_en',
        'image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
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
    public function getDescription($lang = 'en')
    {
        return $lang === 'ar' ? $this->description_ar : $this->description_en;
    }
}
