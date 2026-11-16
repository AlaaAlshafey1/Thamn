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
     * Ensure 'ثمّن' always has shaddah on the meem
     */
    public static function formatThamnWithShaddah(?string $text): ?string
    {
        if (empty($text)) {
            return $text;
        }
        return preg_replace('/(?<![^\s\p{P}])([وبفلك]?)(ث[\x{064E}]?م[\x{064E}]?ن)(?![^\s\p{P}])/u', '$1ثمّن', $text);
    }

    /**
     * Get localized title
     */
    public function getTitle($lang = 'ar')
    {
        $val = $lang === 'ar' ? $this->title_ar : $this->title_en;
        $result = !empty($val) ? $val : $this->title_ar;
        return $lang === 'ar' ? self::formatThamnWithShaddah($result) : $result;
    }

    /**
     * Get localized subtitle
     */
    public function getSubTitle($lang = 'ar')
    {
        $val = $lang === 'ar' ? $this->sub_title_ar : $this->sub_title_en;
        $result = !empty($val) ? $val : $this->sub_title_ar;
        return $lang === 'ar' ? self::formatThamnWithShaddah($result) : $result;
    }

    /**
     * Get localized description
     */
    public function getDescription($lang = 'ar')
    {
        $val = $lang === 'ar' ? $this->description_ar : $this->description_en;
        $result = !empty($val) ? $val : $this->description_ar;
        return $lang === 'ar' ? self::formatThamnWithShaddah($result) : $result;
    }
}
