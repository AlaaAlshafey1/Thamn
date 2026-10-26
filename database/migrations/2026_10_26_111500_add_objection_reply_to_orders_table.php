<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('objection_admin_reply')->nullable()->after('objected_at');
            $table->timestamp('objection_replied_at')->nullable()->after('objection_admin_reply');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['objection_admin_reply', 'objection_replied_at']);
        });
    }
};
