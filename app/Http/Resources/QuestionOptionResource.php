<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionOptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $extraFee = 0;
        $wantsAiImage = $request->boolean('create_image_ai') || $request->input('create_image_ai') == 1;

        // إذا كان هذا الخيار يمثل إحدى باقات التقييم وطلب العميل توليد صورة بالذكاء الاصطناعي، يضاف 5 ريال
        $isPackage = in_array($this->badge, ['ai', 'expert', 'best'])
            || ($this->question && $this->question->type === 'rateTypeSelection');

        if ($wantsAiImage && $isPackage) {
            $extraFee = (float) env('IMAGE_GENERATION_FEE', 5);
        }

        $basePrice = $this->price !== null ? (float) $this->price : null;
        $finalPrice = $basePrice !== null ? (string) ($basePrice + $extraFee) : null;

        return [
            'id'              => $this->id,
            'name'            => lang($this->option_ar, $this->option_en, $request),
            'image'           => $this->image ? asset('storage/' . $this->image) : null,
            'description'     => lang($this->description_ar, $this->description_en, $request),
            'order'           => $this->order,
            'min'             => $this->min ?? 0,
            'max'             => $this->max ?? 0,
            'price'           => $finalPrice,
            'base_price'      => $basePrice,
            'badge'           => $this->badge ?? null,
            'subOptionsTitle' => $this->sub_options_title ?? null,
            'sub_options'     => $this->parent_option_id === null
                ? QuestionOptionResource::collection($this->subOptions)
                : null,
        ];
    }
}
