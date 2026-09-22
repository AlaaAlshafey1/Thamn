<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\AiValuationRating;

class AiValuationRatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ratings = [
            [
                'name_ar' => 'أدنى من المقبول',
                'name_en' => 'Below Acceptable',
                'bg_color' => '#FADBD8',
                'icon_color' => '#E74C3C',
                'filename' => 'rating_lowest.svg'
            ],
            [
                'name_ar' => 'مقبول',
                'name_en' => 'Acceptable',
                'bg_color' => '#FDEBD0',
                'icon_color' => '#F39C12',
                'filename' => 'rating_low.svg'
            ],
            [
                'name_ar' => 'عادل',
                'name_en' => 'Fair',
                'bg_color' => '#D5F5E3',
                'icon_color' => '#27AE60',
                'filename' => 'rating_fair.svg'
            ],
            [
                'name_ar' => 'أعلى من المقبول',
                'name_en' => 'Above Acceptable',
                'bg_color' => '#D6EAF8',
                'icon_color' => '#2980B9',
                'filename' => 'rating_high.svg'
            ],
            [
                'name_ar' => 'مرتفع',
                'name_en' => 'High',
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
                ['name_ar' => $item['name_ar']],
                [
                    'name_en' => $item['name_en'],
                    'color' => $item['bg_color'],
                    'icon' => $path,
                    'is_active' => true,
                ]
            );
        }
    }
}
