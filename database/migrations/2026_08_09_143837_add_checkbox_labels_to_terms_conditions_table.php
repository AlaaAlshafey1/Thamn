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
        Schema::table('terms_conditions', function (Blueprint $table) {
            if (!Schema::hasColumn('terms_conditions', 'checkbox_label_ar')) {
                $table->text('checkbox_label_ar')->nullable();
            }
            if (!Schema::hasColumn('terms_conditions', 'checkbox_label_en')) {
                $table->text('checkbox_label_en')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('terms_conditions', function (Blueprint $table) {
            if (Schema::hasColumn('terms_conditions', 'checkbox_label_ar')) {
                $table->dropColumn('checkbox_label_ar');
            }
            if (Schema::hasColumn('terms_conditions', 'checkbox_label_en')) {
                $table->dropColumn('checkbox_label_en');
            }
        });
    }
};
