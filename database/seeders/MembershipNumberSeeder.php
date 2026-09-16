<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class MembershipNumberSeeder extends Seeder
{
    /**
     * يعمل update لكل المستخدمين اللي ملهمش membership_number
     * ويعمل لهم رقم عضوية فريد عشوائي.
     */
    public function run(): void
    {
        $users = User::whereNull('membership_number')
            ->withTrashed()  // يشمل المحذوفين soft-delete
            ->get();

        if ($users->isEmpty()) {
            $this->command->info('All users already have a membership number. Nothing to update.');
            return;
        }

        $this->command->info("Found {$users->count()} user(s) without a membership number. Updating...");

        $bar = $this->command->getOutput()->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            $user->membership_number = User::generateMembershipNumber();
            $user->saveQuietly(); // بدون إطلاق events أو timestamps
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info("Done! Assigned membership numbers to {$users->count()} user(s).");
    }
}
