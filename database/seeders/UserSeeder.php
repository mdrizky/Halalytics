<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create regular user
        User::firstOrCreate(
            ['username' => 'daffa'],
            [
                'full_name' => 'Daffa Rizky',
                'email' => 'daffa@example.com',
                'password' => Hash::make('12345678'),
                'phone' => '08123456789',
                'blood_type' => 'A+',
                'allergy' => 'Seafood',
                'medical_history' => 'No significant medical history',
                'role' => 'user',
                'active' => true,
                'goal' => 'Maintain healthy lifestyle',
                'diet_preference' => 'Balanced',
                'activity_level' => 'Active',
                'address' => 'Jakarta, Indonesia',
                'language' => 'id',
                'age' => 25,
                'height' => 170.5,
                'weight' => 65.0,
                'bmi' => 22.4,
                'notif_enabled' => true,
                'dark_mode' => false,
            ]
        );

        // Create admin user (only if not exists)
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'full_name' => 'Administrator',
                'email' => 'admin@halalytics.com',
                'password' => Hash::make('admin123'),
                'phone' => '08123456780',
                'blood_type' => 'O+',
                'allergy' => null,
                'medical_history' => null,
                'role' => 'admin',
                'active' => true,
                'goal' => 'System administration',
                'diet_preference' => null,
                'activity_level' => 'Moderate',
                'address' => 'Jakarta, Indonesia',
                'language' => 'id',
                'age' => 30,
                'height' => 175.0,
                'weight' => 70.0,
                'bmi' => 22.9,
                'notif_enabled' => true,
                'dark_mode' => false,
            ]
        );

        // Create test user
        User::firstOrCreate(
            ['username' => 'testuser'],
            [
                'full_name' => 'Test User',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'phone' => '08123456788',
                'blood_type' => 'B+',
                'allergy' => 'Peanuts',
                'medical_history' => 'Asthma',
                'role' => 'user',
                'active' => true,
                'goal' => 'Weight loss',
                'diet_preference' => 'Low carb',
                'activity_level' => 'Sedentary',
                'address' => 'Bandung, Indonesia',
                'language' => 'id',
                'age' => 28,
                'height' => 165.0,
                'weight' => 75.0,
                'bmi' => 27.5,
                'notif_enabled' => true,
                'dark_mode' => true,
            ]
        );
    }
}
