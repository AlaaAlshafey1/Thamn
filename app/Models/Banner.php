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
    public function getTitle($lang = 'ar')
    {
        $val = $lang === 'ar' ? $this->title_ar : $this->title_en;
        $result = !empty($val) ? $val : $this->title_ar;
        if ($lang === 'ar' && !empty($result)) {
            return preg_replace('/(?<![^\s\p{P}])([وبفلك]?)(ث[\x{064E}]?م[\x{064E}]?ن)(?![^\s\p{P}])/u', '$1ثمّن', $result);
        }
        return $result;
    }
}
