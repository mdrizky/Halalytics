<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoAccountSeeder extends Seeder
{
    private const LEGACY_NUTRITIONIST_USERNAME = 'ahli gizi';
    private const CANONICAL_NUTRITIONIST_USERNAME = 'ahli_gizi';

    public function run(): void
    {
        $now = now();

        DB::transaction(function () use ($now): void {
            foreach ($this->demoAccounts() as $account) {
                $user = User::firstOrCreate(
                    ['email' => $account['email']],
                    [
                        'full_name' => $account['full_name'],
                        'username' => $account['username'],
                        'password' => Hash::make($account['password']),
                        'role' => $account['role'],
                        'active' => true,
                        'weight' => $account['weight'] ?? null,
                        'blood_type' => $account['blood_type'] ?? null,
                    ]
                );

                if (method_exists($user, 'assignRole')) {
                    try {
                        $user->assignRole($account['role']);
                    } catch (\Throwable $e) {
                        // Role system not active
                    }
                }
            }

            $this->backfillLegacyUsers($now);
        });
    }

    private function demoAccounts(): array
    {
        return [
            [
                'full_name' => 'Admin Halalytics',
                'username' => 'admin',
                'email' => 'admin@halalytics.com',
                'password' => 'admin123',
                'role' => 'admin',
                'weight' => 70,
                'blood_type' => 'O',
            ],
            [
                'full_name' => 'Ahli Gizi Halalytics',
                'username' => self::CANONICAL_NUTRITIONIST_USERNAME,
                'email' => 'nutritionist@halalytics.com',
                'password' => '12345678',
                'role' => 'ahli_gizi',
                'weight' => 60,
                'blood_type' => 'B',
            ],
            [
                'full_name' => 'User Halalytics',
                'username' => 'user_demo',
                'email' => 'user@halalytics.com',
                'password' => '12345678',
                'role' => 'user',
                'weight' => 65,
                'blood_type' => 'A',
            ],
        ];
    }

    private function backfillLegacyUsers(CarbonInterface $now): void
    {
        $canonicalUsernameTakenByOtherUser = User::query()
            ->where('username', self::CANONICAL_NUTRITIONIST_USERNAME)
            ->where('email', '!=', 'nutritionist@halalytics.com')
            ->exists();

        User::query()
            ->where(function (Builder $query): void {
                $query
                    ->where('email', 'admin@halalytics.com')
                    ->orWhere('email', 'nutritionist@halalytics.com');
            })
            ->orWhereIn('username', ['admin', self::LEGACY_NUTRITIONIST_USERNAME])
            ->get()
            ->each(function (User $user) use ($now, $canonicalUsernameTakenByOtherUser): void {
                if ($user->email === 'admin@halalytics.com' || $user->username === 'admin') {
                    $user->role = 'admin';
                }

                if ($user->email === 'nutritionist@halalytics.com' || $user->username === self::LEGACY_NUTRITIONIST_USERNAME) {
                    $user->role = 'ahli_gizi';
                }

                if (
                    $user->username === self::LEGACY_NUTRITIONIST_USERNAME
                    && !$canonicalUsernameTakenByOtherUser
                ) {
                    $user->username = self::CANONICAL_NUTRITIONIST_USERNAME;
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
