<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DemoAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            [
                'full_name' => 'Admin Demo',
                'username' => 'admin_demo',
                'email' => 'admin@halalytics.com',
                'password' => Hash::make('Admin123!'),
                'role' => 'admin',
                'active' => 1,
                'weight_kg' => 70,
                'blood_type' => 'O',
            ],
            [
                'full_name' => 'User Demo',
                'username' => 'user_demo',
                'email' => 'user@halalytics.com',
                'password' => Hash::make('User123!'),
                'role' => 'user',
                'active' => 1,
                'weight_kg' => 65,
                'blood_type' => 'A',
            ],
            [
                'full_name' => 'Nutritionist Demo',
                'username' => 'nutritionist_demo',
                'email' => 'nutritionist@halalytics.com',
                'password' => Hash::make('Nutrition123!'),
                'role' => 'ahli_gizi',
                'active' => 1,
                'weight_kg' => 60,
                'blood_type' => 'B',
            ]
        ];

        foreach ($accounts as $account) {
            $user = User::updateOrCreate(
                ['email' => $account['email']],
                $account
            );
            if (method_exists($user, 'assignRole')) {
                try {
                    $user->assignRole($account['role']);
                } catch (\Throwable $e) {}
            }
        }
    }
}
