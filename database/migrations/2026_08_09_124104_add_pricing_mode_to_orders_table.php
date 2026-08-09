<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * يضيف:
     *  - pricing_mode: valuation_only | valuation_and_sale
     *  - sale_terms_accepted: هل المستخدم وافق على شروط البيع
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('pricing_mode', ['valuation_only', 'valuation_and_sale'])
                  ->default('valuation_only')
                  ->after('payment_type');

            $table->boolean('sale_terms_accepted')
                  ->default(false)
                  ->after('pricing_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['pricing_mode', 'sale_terms_accepted']);
        });
    }
};
