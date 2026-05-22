<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoAccountSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Keep existing user profile/password intact; only create if missing.
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
            $user = User::firstOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'username' => $account['username'],
                    'password' => Hash::make($account['password']),
                    'role' => $account['role'],
                    'email_verified_at' => $now,
                ]
            );

            if ($user->role !== $account['role']) {
                $user->role = $account['role'];
            }

            if (!$user->email_verified_at) {
                $user->email_verified_at = $now;
            }

            if ($user->isDirty()) {
                $user->save();
            }
        }

        // Legacy username backfill support from older seed data.
        User::query()
            ->whereIn('username', ['admin', 'ahli gizi'])
            ->get()
            ->each(function (User $user) use ($now): void {
                if ($user->username === 'admin') {
                    $user->role = 'admin';
                }

                if ($user->username === 'ahli gizi') {
                    $user->role = 'ahli_gizi';
                    $user->username = 'ahli_gizi';
                }

                if (!$user->email_verified_at) {
                    $user->email_verified_at = $now;
                }

                if ($user->isDirty()) {
                    $user->save();
                }
            });
    }
}
