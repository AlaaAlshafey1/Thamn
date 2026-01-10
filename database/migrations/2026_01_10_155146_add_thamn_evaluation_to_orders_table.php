<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('thamn_price', 10, 2)->nullable()->after('expert_price');
            $table->text('thamn_reasoning')->nullable()->after('thamn_price');
            $table->foreignId('thamn_by')->nullable()
                  ->constrained('users')->nullOnDelete()
                  ->after('thamn_reasoning');
            $table->timestamp('thamn_at')->nullable()->after('thamn_by');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'thamn_price',
                'thamn_reasoning',
                'thamn_by',
                'thamn_at'
            ]);
        });
    }
};

