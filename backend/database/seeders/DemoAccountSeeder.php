<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoAccountSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure core role accounts always exist in users table.
        $accounts = [
            [
                'email' => 'admin@halalytics.com',
                'name' => 'Admin Halalytics',
                'username' => 'admin',
                'password' => 'admin123',
                'role' => 'admin',
            ],
            [
                'email' => 'nutritionist@halalytics.com',
                'name' => 'Ahli Gizi Halalytics',
                'username' => 'ahli_gizi',
                'password' => '12345678',
                'role' => 'ahli_gizi',
            ],
            [
                'email' => 'user@halalytics.com',
                'name' => 'User Halalytics',
                'username' => 'user_demo',
                'password' => '12345678',
                'role' => 'user',
            ],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'username' => $account['username'],
                    'password' => Hash::make($account['password']),
                    'role' => $account['role'],
                    'email_verified_at' => now(),
                ]
            );
        }

        // Backfill existing known accounts if they were created as regular user before role support.
        User::query()
            ->whereIn('email', ['admin@halalytics.com', 'nutritionist@halalytics.com'])
            ->orWhereIn('username', ['admin', 'ahli_gizi'])
            ->get()
            ->each(function (User $user): void {
                if ($user->email === 'admin@halalytics.com' || $user->username === 'admin') {
                    $user->role = 'admin';
                }

                if ($user->email === 'nutritionist@halalytics.com' || $user->username === 'ahli_gizi') {
                    $user->role = 'ahli_gizi';
                }

                $user->email_verified_at = $user->email_verified_at ?? now();
                $user->save();
            });
    }
}
