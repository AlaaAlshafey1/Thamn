<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppPage extends Model
{
    use HasFactory;

protected $fillable = [
    'name',
    'type',
    'title_ar',
    'title_en',
    'description_ar', // ✅ لازم تكون هنا
    'description_en', // ✅ ولازم دي كمان
    'background_color',
    'background_image',
    'logo',
    'text_color',
    'button_color',
    'button_text_color',
    'has_banner',
    'banner_image',
    'banner_color',
    'banner_text',
    'layout_json',
    'is_active',
];


    protected $casts = [
        'has_banner' => 'boolean',
        'is_active' => 'boolean',
        'layout_json' => 'array',
    ];
}
