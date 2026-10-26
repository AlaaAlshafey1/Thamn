<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObjectionReason extends Model
{
    use HasFactory;

    protected $table = 'objection_reasons';

    protected $fillable = [
        'title_ar',
        'title_en',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = [
        'title',
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

    public function getTitle($lang = 'ar')
    {
        if ($lang === 'en') {
            return !empty($this->title_en) ? $this->title_en : $this->title_ar;
        }
        return !empty($this->title_ar) ? $this->title_ar : $this->title_en;
    }
}
