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
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('ai_valuation_rating_id')->nullable()->after('ai_reasoning');
            $table->foreign('ai_valuation_rating_id')->references('id')->on('ai_valuation_ratings')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['ai_valuation_rating_id']);
            $table->dropColumn('ai_valuation_rating_id');
        });
    }
};
