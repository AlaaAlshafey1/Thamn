<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arbitrator_declarations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('token', 64)->unique(); // رابط مميز
            $table->string('full_name')->nullable();
            $table->string('national_id')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->longText('signature')->nullable(); // base64 image
            $table->string('pdf_path')->nullable(); // مسار PDF المحفوظ
            $table->timestamp('signed_at')->nullable(); // وقت التوقيع
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arbitrator_declarations');
    }
};
