<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('objection_reason')->nullable()->after('re_evaluation_count');
            $table->unsignedBigInteger('objection_reason_id')->nullable()->after('objection_reason');
            $table->timestamp('objected_at')->nullable()->after('objection_reason_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['objection_reason', 'objection_reason_id', 'objected_at']);
        });
    }
};
