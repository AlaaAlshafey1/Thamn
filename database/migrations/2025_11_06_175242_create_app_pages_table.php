<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_pages', function (Blueprint $table) {
            $table->id();

            // 🧩 الاسم والمعلومات العامة
            $table->string('name')->unique(); // المفتاح الداخلي (مثلاً splash_screen)
            $table->string('title_ar')->nullable();
            $table->string('title_en')->nullable();

            // 📝 الوصف (محتوى الصفحة)
            $table->longText('description_ar')->nullable();
            $table->longText('description_en')->nullable();

            // 🪟 نوع الصفحة: splash, screen, section, popup...
            $table->string('type')->default('screen');

            // 🎨 الخلفية
            $table->string('background_color')->nullable();
            $table->string('background_image')->nullable(); // لو الصفحة فيها صورة خلفية

            // 🖼️ اللوجو (مفيد للسبلاش)
            $table->string('logo')->nullable();

            // 🎯 ألوان النص والأزرار
            $table->string('text_color')->nullable();
            $table->string('button_color')->nullable();
            $table->string('button_text_color')->nullable();

            // 🏞️ إعدادات البانر
            $table->boolean('has_banner')->default(false);
            $table->string('banner_image')->nullable();
            $table->string('banner_color')->nullable();
            $table->string('banner_text')->nullable();

            // 🧱 تخطيط الصفحة (Layout JSON)
            $table->json('layout_json')->nullable();

            // ⚙️ حالة التفعيل
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_pages');
    }
};
