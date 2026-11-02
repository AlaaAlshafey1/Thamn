<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoryExpertOpinionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultLabelAr = 'رأي الخبير المعتمد';
        $defaultLabelEn = 'Certified Expert Opinion';
        $defaultSubtitleAr = 'لدى الهيئة السعودية للمقيمين المعتمدين';
        $defaultSubtitleEn = 'Accredited by the Saudi Authority for Accredited Valuers';

        Category::all()->each(function ($cat) use ($defaultLabelAr, $defaultLabelEn, $defaultSubtitleAr, $defaultSubtitleEn) {
            $cat->update([
                'expert_opinion_label_ar' => $cat->expert_opinion_label_ar ?: $defaultLabelAr,
                'expert_opinion_label_en' => $cat->expert_opinion_label_en ?: $defaultLabelEn,
                'expert_opinion_subtitle_ar' => $defaultSubtitleAr,
                'expert_opinion_subtitle_en' => $defaultSubtitleEn,
            ]);
        });
    }
}

