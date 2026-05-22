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
                        'name' => $account['name'],
                        'username' => $account['username'],
                        'password' => Hash::make($account['password']),
                        'role' => $account['role'],
                        'email_verified_at' => $now,
                    ]
                );

                $this->syncRoleAndVerification($user, $account['role'], $now);
            }

            $this->backfillLegacyUsers($now);
        });
    }

    /**
     * @return array<int, array{name: string, username: string, email: string, password: string, role: string}>
     */
    private function demoAccounts(): array
    {
        return [
            ['email' => 'admin@halalytics.com', 'name' => 'Admin Halalytics', 'username' => 'admin', 'password' => 'admin123', 'role' => 'admin'],
            ['email' => 'nutritionist@halalytics.com', 'name' => 'Ahli Gizi Halalytics', 'username' => self::CANONICAL_NUTRITIONIST_USERNAME, 'password' => '12345678', 'role' => 'ahli_gizi'],
            ['email' => 'user@halalytics.com', 'name' => 'User Halalytics', 'username' => 'user_demo', 'password' => '12345678', 'role' => 'user'],
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

                $this->syncRoleAndVerification($user, $user->role, $now);
            });
    }

    private function syncRoleAndVerification(User $user, string $expectedRole, CarbonInterface $now): void
    {
        if ($user->role !== $expectedRole) {
            $user->role = $expectedRole;
        }

        if (!$user->email_verified_at) {
            $user->email_verified_at = $now;
        }

        if ($user->isDirty()) {
            $user->save();
        }
    }
}
