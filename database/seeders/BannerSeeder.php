<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Banner::firstOrCreate(
            ['id' => 1],
            [
                'title_ar' => 'بانر إعلاني',
                'title_en' => 'Promotional Banner',
                'file' => 'https://placehold.co/1200x400/2563eb/ffffff?text=Banner',
                'file_type' => 'image',
                'is_active' => true,
                'sort_order' => 0,
            ]
        );
    }
}
