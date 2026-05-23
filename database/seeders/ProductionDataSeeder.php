<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class ProductionDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Roles exist (Spatie)
        $roles = ['admin', 'ahli_gizi', 'user'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // 2. Create Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@halalytics.com'],
            [
                'username' => 'admin',
                'full_name' => 'Administrator Halalytics',
                'password' => 'AdminHalal2026!',
                'role' => 'admin',
                'active' => true,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // 3. Create Nutritionist User (Ahli Gizi)
        $nutritionist = User::updateOrCreate(
            ['email' => 'pakar@halalytics.com'],
            [
                'username' => 'pakar_gizi',
                'full_name' => 'Dr. Siti Aminah, S.Gz',
                'password' => 'PakarHalal2026!',
                'role' => 'ahli_gizi',
                'active' => true,
                'email_verified_at' => now(),
                'bio' => 'Ahli gizi bersertifikat dengan pengalaman 10 tahun.',
                'medical_history' => 'Spesialis Nutrisi Klinis',
            ]
        );
        $nutritionist->assignRole('ahli_gizi');

        // 4. Create dummy data for Admin/Nutritionist functionality if needed
        // (e.g., initial consultations, etc.)
    }
}
