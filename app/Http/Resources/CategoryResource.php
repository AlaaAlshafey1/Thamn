<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'name'           => lang($this->name_ar, $this->name_en, $request),
            'image'          => $this->image ? asset('storage/' . $this->image) : null,
            'questions_image'=> $this->questions_image ? asset('storage/' . $this->questions_image) : null,
            "is_active"      => $this->is_active,
            'created_at'     => $this->created_at->format('Y-m-d'),
        ];
    }
}
