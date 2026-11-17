<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city', 100)->nullable()->after('nationality');
            }
        });

        // Copy existing city from arbitrator_declarations if available
        try {
            if (Schema::hasTable('arbitrator_declarations')) {
                $declarations = DB::table('arbitrator_declarations')
                    ->whereNotNull('city')
                    ->where('city', '!=', '')
                    ->get(['user_id', 'city']);

                foreach ($declarations as $dec) {
                    if ($dec->user_id) {
                        DB::table('users')
                            ->where('id', $dec->user_id)
                            ->where(function ($q) {
                                $q->whereNull('city')->orWhere('city', '');
                            })
                            ->update(['city' => $dec->city]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignore if copy fails
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'city')) {
                $table->dropColumn('city');
            }
        });
    }
};
