<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('orders') && !Schema::hasColumn('orders', 'create_image_ai')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->boolean('create_image_ai')->default(false)->after('can_send_to_market');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'create_image_ai')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('create_image_ai');
            });
        }
    }
};

