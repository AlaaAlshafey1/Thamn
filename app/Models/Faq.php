<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = ['question_ar','question_en','answer_ar','answer_en','category'];

    public function getQuestionByLang($lang = 'ar') {
        $val = $lang === 'ar' ? $this->question_ar : $this->question_en;
        return !empty($val) ? $val : $this->question_ar;
    }

    public function getAnswerByLang($lang = 'ar') {
        $val = $lang === 'ar' ? $this->answer_ar : $this->answer_en;
        return !empty($val) ? $val : $this->answer_ar;
    }
}

