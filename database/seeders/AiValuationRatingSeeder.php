<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\AiValuationRating;
use App\Models\Category;

class AiValuationRatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ratings = [
            [
                'id' => 1,
                'name_ar' => 'أدنى من المعقول',
                'name_en' => 'Below Reasonable',
                'subtitle_ar' => 'أقل من المتوقع',
                'subtitle_en' => 'Below Expected',
                'bg_color' => '#FADBD8',
                'icon_color' => '#E74C3C',
                'filename' => 'rating_lowest.svg'
            ],
            [
                'id' => 2,
                'name_ar' => 'معقول',
                'name_en' => 'Reasonable',
                'subtitle_ar' => 'ضمن النطاق المنخفض',
                'subtitle_en' => 'Within Lower Range',
                'bg_color' => '#FDEBD0',
                'icon_color' => '#F39C12',
                'filename' => 'rating_low.svg'
            ],
            [
                'id' => 3,
                'name_ar' => 'عادل',
                'name_en' => 'Fair',
                'subtitle_ar' => 'سعر مناسب للسوق',
                'subtitle_en' => 'Fair Market Price',
                'bg_color' => '#D5F5E3',
                'icon_color' => '#27AE60',
                'filename' => 'rating_fair.svg'
            ],
            [
                'id' => 4,
                'name_ar' => 'أعلى من المعقول',
                'name_en' => 'Above Reasonable',
                'subtitle_ar' => 'ضمن النطاق المرتفع',
                'subtitle_en' => 'Within Upper Range',
                'bg_color' => '#D6EAF8',
                'icon_color' => '#2980B9',
                'filename' => 'rating_high.svg'
            ],
            [
                'id' => 5,
                'name_ar' => 'مرتفع',
                'name_en' => 'High',
                'subtitle_ar' => 'أعلى من المتوقع',
                'subtitle_en' => 'Above Expected',
                'bg_color' => '#EBDEF0',
                'icon_color' => '#8E44AD',
                'filename' => 'rating_highest.svg'
            ]
        ];

        Storage::disk('public')->makeDirectory('valuation_ratings');

        foreach ($ratings as $item) {
            $svgContent = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="' . $item['icon_color'] . '">
    <rect x="4" y="14" width="4" height="6" rx="1"/>
    <rect x="10" y="10" width="4" height="10" rx="1"/>
    <rect x="16" y="6" width="4" height="14" rx="1"/>
</svg>';
            
            $path = 'valuation_ratings/' . $item['filename'];
            Storage::disk('public')->put($path, $svgContent);

            AiValuationRating::updateOrCreate(
                ['id' => $item['id']],
                [
                    'name_ar' => $item['name_ar'],
                    'name_en' => $item['name_en'],
                    'subtitle_ar' => $item['subtitle_ar'],
                    'subtitle_en' => $item['subtitle_en'],
                    'color' => $item['bg_color'],
                    'icon' => $path,
                    'is_active' => true,
                ]
            );
        }

        // تحديث الوصف الفرعي لرأي الخبير لجميع الفئات الحالية
        $defaultSubtitleAr = 'لدى الهيئة السعودية للمقيمين المعتمدين';
        $defaultSubtitleEn = 'Accredited by the Saudi Authority for Accredited Valuers';

        Category::all()->each(function ($cat) use ($defaultSubtitleAr, $defaultSubtitleEn) {
            $cat->update([
                'expert_opinion_subtitle_ar' => $cat->expert_opinion_subtitle_ar ?: $defaultSubtitleAr,
                'expert_opinion_subtitle_en' => $cat->expert_opinion_subtitle_en ?: $defaultSubtitleEn,
                'expert_opinion_label_ar' => $cat->expert_opinion_label_ar ?: 'رأي الخبير المعتمد',
                'expert_opinion_label_en' => $cat->expert_opinion_label_en ?: 'Certified Expert Opinion',
            ]);
        });
    }
}
