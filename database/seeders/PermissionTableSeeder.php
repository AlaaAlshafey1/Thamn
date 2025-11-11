<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'users_view',
            'users_create',
            'users_edit',
            'users_delete',
            'roles_view',
            'roles_create',
            'roles_edit',
            'roles_delete',
            'permissions_view',
            'permissions_create',
            'permissions_edit',
            'permissions_delete',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }
    }
}
