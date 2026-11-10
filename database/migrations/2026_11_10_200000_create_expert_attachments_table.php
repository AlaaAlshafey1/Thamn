<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('expert_attachments')) {
            Schema::create('expert_attachments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('file_path');
                $table->string('original_name')->nullable();
                $table->string('attachment_type'); // شهادة, دورة, وثيقة عمل حر, سجل تجاري, مشهد, تعريف عمل, خبرة, اعتماد, تزكية, أخرى
                $table->unsignedBigInteger('file_size')->nullable();
                $table->string('mime_type')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('expert_attachments');
    }
};

