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
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        User::updateOrCreate(['email' => 'user@halalytics.com'], [
            'name' => 'daffa',
            'username' => 'daffa',
            'password' => Hash::make('12345678'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        User::updateOrCreate(['email' => 'nutritionist@halalytics.com'], [
            'name' => 'ahli gizi',
            'username' => 'ahli gizi',
            'password' => Hash::make('12345678'),
            'role' => 'ahli_gizi',
            'email_verified_at' => now(),
        ]);
    }
}
