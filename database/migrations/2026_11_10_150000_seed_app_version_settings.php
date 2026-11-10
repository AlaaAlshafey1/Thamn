<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $defaultSettings = [
            ['key' => 'android_app_version', 'value' => '1.0.0'],
            ['key' => 'ios_app_version', 'value' => '1.0.0'],
            ['key' => 'android_store_url', 'value' => 'https://play.google.com/store/apps/details?id=com.thamn.app'],
            ['key' => 'ios_store_url', 'value' => 'https://apps.apple.com/app/id6470000000'],
            ['key' => 'app_update_message_ar', 'value' => 'يتوفر إصدار جديد من تطبيق ثمن، يرجى التحديث الآن للاستمرار في الاستمتاع بأفضل تجربة.'],
            ['key' => 'app_update_message_en', 'value' => 'A new version of Thamn app is available. Please update now for the best experience.'],
            ['key' => 'android_force_update', 'value' => '0'],
            ['key' => 'ios_force_update', 'value' => '0'],
        ];

        foreach ($defaultSettings as $setting) {
            DB::table('settings')->insertOrIgnore($setting);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'android_app_version',
            'ios_app_version',
            'android_store_url',
            'ios_store_url',
            'app_update_message_ar',
            'app_update_message_en',
            'android_force_update',
            'ios_force_update',
        ])->delete();
    }
};

