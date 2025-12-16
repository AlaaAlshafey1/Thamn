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
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();

            $table->foreignId('question_id')
                ->constrained()
                ->cascadeOnDelete();

            // 👇 لدعم sub options
            $table->foreignId('parent_option_id')
                ->nullable()
                ->constrained('question_options')
                ->nullOnDelete();

            $table->string('option_ar');
            $table->string('option_en')->nullable();

            // 👇 min / max للاختيارات اللي محتاجاها
            $table->decimal('min', 10, 2)->nullable();
            $table->decimal('max', 10, 2)->nullable();

            $table->string('image')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_options');
    }
};
