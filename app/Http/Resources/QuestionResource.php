<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use  App\Helpers\Helper;

class QuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = strtolower($request->header('Accept-Language', 'en'));

        return [
            'id'            => $this->id,
            'category_id'   => $this->category_id,
            'category_name' => $locale === 'ar' ? ($this->category->name_ar ?? '') : ($this->category->name_en ?? ''),

            'question'      => $locale === 'ar' ? $this->question_ar : $this->question_en,
            'description'   => $locale === 'ar' ? $this->description_ar : $this->description_en,

            'type'          => $this->type,
            'is_required'   => (bool) $this->is_required,
            'order'         => (int) $this->order,

            // slider
            'min_value'     => $this->min_value,
            'max_value'     => $this->max_value,
            'step'          => $this->step,

            // options
            'options' => QuestionOptionResource::collection($this->options()->get() ?? collect()),
        ];
    }
}
