<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat Role Dasar
        $roles = ['admin', 'user', 'ahli_gizi'];

        foreach ($roles as $roleName) {
            if (!Role::where('name', $roleName)->exists()) {
                Role::create(['name' => $roleName]);
            }
        }
    }
}
