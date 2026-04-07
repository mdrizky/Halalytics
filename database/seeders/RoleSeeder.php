<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create roles
        foreach (['user', 'expert', 'admin'] as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }

        User::query()
            ->select(['id_user', 'role'])
            ->whereNotNull('role')
            ->chunkById(100, function ($users) {
                foreach ($users as $user) {
                    if (in_array($user->role, ['user', 'expert', 'admin'], true)) {
                        $user->syncRoles([$user->role]);
                    }
                }
            }, 'id_user');
    }
}
