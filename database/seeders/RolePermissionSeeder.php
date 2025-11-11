<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        $permissionsArray = config('permissions');
        $allPermissions = collect($permissionsArray)->flatten()->toArray();

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }


        $superAdmin = Role::firstOrCreate(['name' => 'superadmin']);

        $superAdmin->syncPermissions(Permission::all());



        $user = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'first_name' => 'Super Administrator',
                'last_name' => 'Super Administrator',
                'password' => bcrypt('12345678'),
            ]
        );

        $user->assignRole($superAdmin);
    }
}
