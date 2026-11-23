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
        if (!Schema::hasTable('order_ratings')) {
            Schema::create('order_ratings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete()->unique();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('expert_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
                $table->tinyInteger('rating')->comment('1 to 5 stars');
                $table->text('feedback')->nullable()->comment('Customer written feedback');
                $table->string('channel')->default('whatsapp_chat')->comment('whatsapp_chat, quick_link, app');
                $table->boolean('is_resolved')->default(false)->comment('For low ratings 1 or 2, marked if CS contacted customer');
                $table->text('admin_notes')->nullable()->comment('Notes by CS or Admin upon following up');
                $table->timestamp('resolved_at')->nullable();
                $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['rating', 'is_resolved']);
                $table->index('expert_id');
            });
        }

        // Add survey_sent_at to orders table to prevent duplicate survey sends
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'survey_sent_at')) {
                $table->timestamp('survey_sent_at')->nullable()->after('customer_progress_notified');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_ratings');

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'survey_sent_at')) {
                $table->dropColumn('survey_sent_at');
            }
        });
    }
};
