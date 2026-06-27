<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('evaluated_at')->nullable()->after('accepted_at');
            $table->unsignedTinyInteger('re_evaluation_count')->default(0)->after('evaluated_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['evaluated_at', 're_evaluation_count']);
        });
    }
};
