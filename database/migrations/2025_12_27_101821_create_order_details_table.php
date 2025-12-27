<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('option_id')->nullable()->constrained('question_options')->nullOnDelete();
            $table->foreignId('sub_option_id')->nullable()->constrained('question_options')->nullOnDelete();
            $table->string('value')->nullable();  // للقيم النصية/slider
            $table->decimal('price', 10, 2)->nullable(); // سعر الإجابة لو type=price
            $table->string('status')->nullable();
            $table->string('stageing')->nullable()->comment('Stage or step of the question');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
