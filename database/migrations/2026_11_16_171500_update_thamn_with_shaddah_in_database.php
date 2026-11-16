<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update intros table
        if (Schema::hasTable('intros')) {
            $intros = DB::table('intros')->get();
            foreach ($intros as $intro) {
                $updates = [];
                if (!empty($intro->title_ar)) {
                    $formatted = $this->formatShaddah($intro->title_ar);
                    if ($formatted !== $intro->title_ar) {
                        $updates['title_ar'] = $formatted;
                    }
                }
                if (!empty($intro->sub_title_ar)) {
                    $formatted = $this->formatShaddah($intro->sub_title_ar);
                    if ($formatted !== $intro->sub_title_ar) {
                        $updates['sub_title_ar'] = $formatted;
                    }
                }
                if (!empty($intro->description_ar)) {
                    $formatted = $this->formatShaddah($intro->description_ar);
                    if ($formatted !== $intro->description_ar) {
                        $updates['description_ar'] = $formatted;
                    }
                }
                if (!empty($updates)) {
                    DB::table('intros')->where('id', $intro->id)->update($updates);
                }
            }
        }

        // Update banners table
        if (Schema::hasTable('banners')) {
            $banners = DB::table('banners')->get();
            foreach ($banners as $banner) {
                if (!empty($banner->title_ar)) {
                    $formatted = $this->formatShaddah($banner->title_ar);
                    if ($formatted !== $banner->title_ar) {
                        DB::table('banners')->where('id', $banner->id)->update([
                            'title_ar' => $formatted,
                        ]);
                    }
                }
            }
        }

        // Update home_steps table
        if (Schema::hasTable('home_steps')) {
            $steps = DB::table('home_steps')->get();
            foreach ($steps as $step) {
                $updates = [];
                if (!empty($step->title_ar)) {
                    $formatted = $this->formatShaddah($step->title_ar);
                    if ($formatted !== $step->title_ar) {
                        $updates['title_ar'] = $formatted;
                    }
                }
                if (!empty($step->sub_title_ar)) {
                    $formatted = $this->formatShaddah($step->sub_title_ar);
                    if ($formatted !== $step->sub_title_ar) {
                        $updates['sub_title_ar'] = $formatted;
                    }
                }
                if (!empty($updates)) {
                    DB::table('home_steps')->where('id', $step->id)->update($updates);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Shaddah formatting is standard
    }

    private function formatShaddah(?string $text): ?string
    {
        if (empty($text)) {
            return $text;
        }
        return preg_replace('/(?<![^\s\p{P}])([وبفلك]?)(ث[\x{064E}]?م[\x{064E}]?ن)(?![^\s\p{P}])/u', '$1ثمّن', $text);
    }
};

