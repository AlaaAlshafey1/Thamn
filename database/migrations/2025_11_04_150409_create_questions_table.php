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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('question_ar');
            $table->string('question_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->unsignedInteger('stageing')
                ->default(1);

            $table->enum('type', [
                'singleChoiceCard',
                'singleChoiceChip',
                'singleChoiceChipWithImage',
                'singleChoiceDropdown',
                'multiSelection',
                'counterInput',
                'dateCountInput',
                'singleSelectionSlider',
                'valueRangeSlider',
                'rating',
                'price',
                'progress',
                'productAges'
            ]);
            $table->json('settings')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
