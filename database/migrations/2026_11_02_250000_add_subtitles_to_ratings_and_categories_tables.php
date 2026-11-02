<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add subtitle_ar and subtitle_en to ai_valuation_ratings
        Schema::table('ai_valuation_ratings', function (Blueprint $table) {
            if (!Schema::hasColumn('ai_valuation_ratings', 'subtitle_ar')) {
                $table->string('subtitle_ar')->nullable()->after('name_en');
            }
            if (!Schema::hasColumn('ai_valuation_ratings', 'subtitle_en')) {
                $table->string('subtitle_en')->nullable()->after('subtitle_ar');
            }
        });

        // 2. Add expert_opinion_subtitle_ar and expert_opinion_subtitle_en to categories
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'expert_opinion_subtitle_ar')) {
                $table->string('expert_opinion_subtitle_ar')->nullable()->after('expert_opinion_label_en');
            }
            if (!Schema::hasColumn('categories', 'expert_opinion_subtitle_en')) {
                $table->string('expert_opinion_subtitle_en')->nullable()->after('expert_opinion_subtitle_ar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_valuation_ratings', function (Blueprint $table) {
            if (Schema::hasColumn('ai_valuation_ratings', 'subtitle_ar')) {
                $table->dropColumn('subtitle_ar');
            }
            if (Schema::hasColumn('ai_valuation_ratings', 'subtitle_en')) {
                $table->dropColumn('subtitle_en');
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'expert_opinion_subtitle_ar')) {
                $table->dropColumn('expert_opinion_subtitle_ar');
            }
            if (Schema::hasColumn('categories', 'expert_opinion_subtitle_en')) {
                $table->dropColumn('expert_opinion_subtitle_en');
            }
        });
    }
};

