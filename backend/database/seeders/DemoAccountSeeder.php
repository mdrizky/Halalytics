<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoAccountSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@halalytics.com'], [
            'name' => 'admin',
            'username' => 'admin',
            'password' => Hash::make('Admin123!'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        User::updateOrCreate(['email' => 'user@halalytics.com'], [
            'name' => 'daffa',
            'username' => 'daffa',
            'password' => Hash::make('User123!'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        User::updateOrCreate(['email' => 'nutritionist@halalytics.com'], [
            'name' => 'nutritionist',
            'username' => 'nutritionist',
            'password' => Hash::make('Nutrition123!'),
            'role' => 'ahli_gizi',
            'email_verified_at' => now(),
        ]);
    }
}
