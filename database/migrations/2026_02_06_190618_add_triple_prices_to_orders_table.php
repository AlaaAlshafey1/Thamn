<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('expert_min_price', 15, 2)->nullable()->after('expert_price');
            $table->decimal('expert_max_price', 15, 2)->nullable()->after('expert_min_price');
            $table->decimal('thamn_min_price', 15, 2)->nullable()->after('thamn_price');
            $table->decimal('thamn_max_price', 15, 2)->nullable()->after('thamn_min_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
