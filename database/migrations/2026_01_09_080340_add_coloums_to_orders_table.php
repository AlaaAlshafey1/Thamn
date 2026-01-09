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
            $table->decimal('ai_min_price',10,2)->nullable();
            $table->decimal('ai_max_price',10,2)->nullable();
            $table->decimal('ai_price',10,2)->nullable();
            $table->integer('ai_confidence')->nullable();
            $table->text(column: 'ai_reasoning')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

         $table->dropColumn(['ai_min_price', 'ai_max_price', 'ai_price','ai_confidence','ai_reasoning']);

        });
    }
};
