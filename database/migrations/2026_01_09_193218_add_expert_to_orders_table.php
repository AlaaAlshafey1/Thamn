<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // الخبير اللي قيم الطلب
            $table->unsignedBigInteger('expert_id')->nullable()->after('ai_reasoning');

            // هل التقييم اتعمل من قبل الخبير
            $table->boolean('expert_evaluated')->default(false)->after('expert_id');

            // لو تحب تعمل علاقة مع جدول users (الخبراء)
            $table->foreign('expert_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['expert_id']);
            $table->dropColumn(['expert_id', 'expert_evaluated']);
        });
    }
};
