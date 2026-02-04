<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = ['question_ar','question_en','answer_ar','answer_en','category'];

    public function getQuestionByLang($lang = 'en') {
        return $lang === 'ar' ? $this->question_ar : $this->question_en;
    }

    public function getAnswerByLang($lang = 'en') {
        return $lang === 'ar' ? $this->answer_ar : $this->answer_en;
    }
}

