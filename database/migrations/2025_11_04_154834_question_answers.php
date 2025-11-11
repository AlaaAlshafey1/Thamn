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
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->string('option_ar'); // النص بالعربية
            $table->string('option_en')->nullable(); // النص بالإنجليزية
            $table->string('image')->nullable(); // صورة الاختيار
            $table->integer('order')->default(0); // ترتيب الخيار
            $table->boolean('is_active')->default(true); // تفعيل/تعطيل الخيار
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_answers');
    }
};
