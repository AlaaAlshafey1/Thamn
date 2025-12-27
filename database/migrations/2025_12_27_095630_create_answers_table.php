<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('question_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('option_id')
                ->nullable()
                ->constrained('question_options')
                ->nullOnDelete();

            $table->foreignId('sub_option_id')
                ->nullable()
                ->constrained('question_options')
                ->nullOnDelete();

            $table->string('value')->nullable();

            // للسعر (price type)
            $table->decimal('price', 10, 2)->nullable();

            $table->tinyInteger('status')
                ->default(1)
                ->comment('0=pending, 1=completed, 2=skipped');

            $table->timestamps();

            $table->unique(['user_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};

