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
        Schema::table('users', function (Blueprint $table) {
        $table->string('bank_name')->nullable();
        $table->string('iban')->nullable();
        $table->string('account_number')->nullable();
        $table->string('swift')->nullable();
        $table->text('experience')->nullable(); // شهادات الخبرة
        $table->text('certificates')->nullable(); // شهادات/دورات
        $table->text('notes')->nullable(); // ملاحظات إضافية
        $table->decimal('balance', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
