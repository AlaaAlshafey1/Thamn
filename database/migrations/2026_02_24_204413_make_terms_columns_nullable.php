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
            
            $table->string('title_ar')->nullable()->change();
            $table->string('title_en')->nullable()->change();
            $table->text('content_ar')->nullable()->change();
            $table->text('content_en')->nullable()->change();
            $table->string('file')->nullable()->change();
            $table->integer('sort_order')->nullable()->change();
            $table->boolean('is_active')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('terms_conditions', function (Blueprint $table) {
            $table->string('title_ar')->change();
            $table->string('title_en')->change();
            $table->text('content_ar')->change();
            $table->text('content_en')->change();
            $table->string('file')->change();
            $table->integer('sort_order')->change();
            $table->boolean('is_active')->change();
        });
    }
};
