<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('objection_reasons', function (Blueprint $table) {
            if (!Schema::hasColumn('objection_reasons', 'type')) {
                $table->string('type')->default('other_reason')->after('id');
            }
            if (!Schema::hasColumn('objection_reasons', 'subtitle_ar')) {
                $table->string('subtitle_ar')->nullable()->after('title_en');
            }
            if (!Schema::hasColumn('objection_reasons', 'subtitle_en')) {
                $table->string('subtitle_en')->nullable()->after('subtitle_ar');
            }
            if (!Schema::hasColumn('objection_reasons', 'icon')) {
                $table->string('icon')->nullable()->after('subtitle_en');
            }
            if (!Schema::hasColumn('objection_reasons', 'options')) {
                $table->json('options')->nullable()->after('icon');
            }
        });
    }

    public function down(): void
    {
        Schema::table('objection_reasons', function (Blueprint $table) {
            $columns = ['type', 'subtitle_ar', 'subtitle_en', 'icon', 'options'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('objection_reasons', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

