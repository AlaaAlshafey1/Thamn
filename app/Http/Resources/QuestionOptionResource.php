<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionOptionResource extends JsonResource
{
public function toArray(Request $request): array
{
    return [
        'id'          => $this->id,
        'name'        => lang($this->option_ar, $this->option_en, $request),
        'image'       => $this->image ? asset('storage/' . $this->image) : null,
        'description' => "desc",
        'order'       => $this->order,
        'min'         => $this->min ?? 0,
        'max'         => $this->max ?? 0,
        'sub_options' => $this->parent_option_id === null
            ? QuestionOptionResource::collection($this->subOptions)
            : null,
    ];
}

}
