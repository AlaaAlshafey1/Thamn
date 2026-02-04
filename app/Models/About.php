<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    protected $fillable = ['type', 'content_ar', 'content_en'];

    // Scope لجلب الصفحة حسب النوع
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // جلب المحتوى حسب اللغة
    public function getContentByLang($lang = 'en')
    {
        return $lang === 'ar' ? $this->content_ar : $this->content_en;
    }

}
