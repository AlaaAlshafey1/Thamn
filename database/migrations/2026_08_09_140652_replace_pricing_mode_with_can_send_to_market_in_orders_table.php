<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop old fields
            $table->dropColumn('pricing_mode');
            $table->dropColumn('sale_terms_accepted');
            
            // Add new field
            $table->boolean('can_send_to_market')->default(false)->after('status')->comment('هل الأوردر ينفع يتبعت للسوق ولا لا');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('can_send_to_market');
            $table->enum('pricing_mode', ['valuation_only', 'valuation_and_sale'])->default('valuation_only');
            $table->boolean('sale_terms_accepted')->default(false);
        });
    }
};
