<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BloodEvent;
use App\Models\BloodStock;
use App\Models\BloodEmergencyRequest;
use Carbon\Carbon;

class BloodEventSeeder extends Seeder
{
    public function run(): void
    {
        $admin = \App\Models\User::where('role', 'admin')->first();
        $adminId = $admin ? $admin->id_user : null;

        // Create 3 Blood Events
        $events = [
            [
                'title' => 'Aksi Donor Darah Peduli Sesama - PMI Pusat',
                'location' => 'Gedung PMI Pusat, Jakarta',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'address' => 'Jl. Gatot Subroto, Jakarta Selatan',
                'event_date' => Carbon::today()->addDays(2),
                'start_time' => '08:00:00',
                'end_time' => '13:00:00',
                'quota' => 150,
                'registered_count' => 12,
                'organizer' => 'PMI Jakarta',
                'contact_phone' => '021-1234567',
                'image_url' => 'https://images.unsplash.com/photo-1615461066841-6116e61058f4?auto=format&fit=crop&q=80&w=800',
                'status' => 'active',
                'created_by' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Donor Darah Masal Universitas Indonesia',
                'location' => 'Balairung UI, Depok',
                'latitude' => -6.3606,
                'longitude' => 106.8271,
                'address' => 'Kampus UI Depok, Jawa Barat',
                'event_date' => Carbon::today()->addDays(5),
                'start_time' => '09:00:00',
                'end_time' => '15:00:00',
                'quota' => 250,
                'registered_count' => 85,
                'organizer' => 'BEM UI & RSUI',
                'contact_phone' => '081299998888',
                'image_url' => 'https://images.unsplash.com/photo-1579154204601-01588f351e67?auto=format&fit=crop&q=80&w=800',
                'status' => 'active',
                'created_by' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Hari Donor Darah Sedunia - RS Hasan Sadikin',
                'location' => 'RS Hasan Sadikin, Bandung',
                'latitude' => -6.8966,
                'longitude' => 107.5969,
                'address' => 'Jl. Pasteur No.38, Bandung',
                'event_date' => Carbon::today()->addDays(14),
                'start_time' => '07:30:00',
                'end_time' => '12:00:00',
                'quota' => 100,
                'registered_count' => 5,
                'organizer' => 'PMI Bandung',
                'contact_phone' => '022-2034953',
                'image_url' => 'https://images.unsplash.com/photo-1536856136534-bb679c52a9aa?auto=format&fit=crop&q=80&w=800',
                'status' => 'active',
                'created_by' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($events as $event) {
            BloodEvent::create($event);
        }

        // Create initial Blood Stocks
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        
        foreach ($bloodTypes as $type) {
            $count = rand(5, 30); // Random count for each type
            if ($type == 'O-') $count = rand(1, 5); // Rare type
            if ($type == 'AB-') $count = rand(0, 3); // Very rare
            
            BloodStock::create([
                'blood_type' => $type,
                'volume_ml' => $count * 450,
                'bags_count' => $count,
                'collected_date' => Carbon::today()->subDays(rand(1, 15)),
                'expiry_date' => Carbon::today()->addDays(rand(15, 30)),
                'location' => 'Bank Darah Pusat',
                'status' => 'available',
            ]);
        }

        // Create 1 Emergency Request
        BloodEmergencyRequest::create([
            'hospital_name' => 'RS Medika Utama',
            'blood_type_needed' => 'O-',
            'bags_needed' => 3,
            'urgency_level' => 'critical',
            'contact_person' => 'Dr. Andi',
            'contact_phone' => '081233334444',
            'notes' => 'Pasien kecelakaan kritis, butuh darah O- segera dalam 2 jam.',
            'is_fulfilled' => false,
            'created_by' => $adminId,
        ]);
    }
}
