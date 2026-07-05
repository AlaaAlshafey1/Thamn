<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'title_ar',
        'title_en',
        'file',
        'file_type',
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
}
