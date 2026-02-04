<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'question_ar',
        'question_en',
        'description_ar',
        'description_en',
        'type',
        'is_required',
        'is_active',
        'order',
        'min_value',
        'max_value',
        'step',
        "stageing",
        "addSearch",
        "useCupertinoPicker",
        'flow',
        "settings",
        'group_type'
    ];

    protected $casts = [
        'settings'    => 'array',
        'is_required' => 'boolean',
        'is_active'   => 'boolean',
    ];
    public function options()
    {
        return $this->hasMany(QuestionOption::class)
                    ->whereNull('parent_option_id')
                    ->where('is_active', 1)
                    ->orderBy('order');
    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function step()
    {
        return $this->belongsTo(QuestionStep::class);
    }

}
