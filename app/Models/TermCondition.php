<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TermCondition extends Model
{
    public $table = "terms_conditions";
    protected $fillable = [
        'title_ar',
        'title_en',
        'content_ar',
        'content_en',
        'sort_order',
        'is_active'
    ];
}
