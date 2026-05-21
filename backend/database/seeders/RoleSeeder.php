<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'description' => 'Administrator full access'],
            ['name' => 'user', 'description' => 'Regular user access'],
            ['name' => 'ahli_gizi', 'description' => 'Nutritionist access'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']],
                ['description' => $role['description'], 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
