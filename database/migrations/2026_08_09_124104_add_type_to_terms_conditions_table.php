<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * يضيف حقل type لتمييز أنواع الشروط:
     *   - general: شروط عامة (الافتراضي)
     *   - sale_terms: شروط التثمين والبيع
     */
    public function up(): void
    {
        Schema::table('terms_conditions', function (Blueprint $table) {
            $table->string('type')->default('general')->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('terms_conditions', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
