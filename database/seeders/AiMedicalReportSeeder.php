<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AiMedicalReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = DB::table('users')->get();
        if ($users->isEmpty()) return;

        foreach ($users as $user) {
            $reports = [
                [
                    'id_user' => $user->id_user,
                    'type' => 'bmi',
                    'input_data' => json_encode(['weight_kg' => 85, 'height_cm' => 170, 'bmi' => 29.4]),
                    'ai_response' => json_encode([
                        'status_fisik' => 'Berdasarkan BMI 29.4, Anda masuk dalam kategori Overweight. Fokus utama saat ini adalah menurunkan kadar lemak tubuh secara bertahap demi kesehatan jantung.',
                        'target_2_bulan' => 'Turunkan berat badan 4-5 kg dalam 60 hari.',
                        'saran_nutrisi' => [
                            'Ganti nasi putih dengan nasi merah atau karbohidrat kompleks lainnya.',
                            'Perbanyak protein dari sumber nabati seperti tempe dan tahu rebus.',
                            'Hindari minuman manis dan gorengan.'
                        ],
                        'saran_olahraga' => [
                            'Jalan cepat 30-45 menit setiap pagi.',
                            'Lakukan latihan beban ringan 2x seminggu.'
                        ],
                        'pesan_motivasi' => 'Setiap langkah kecil membawamu lebih dekat pada tujuan sehatmu. Semangat!'
                    ]),
                    'created_at' => now()->subDays(2),
                    'updated_at' => now()->subDays(2),
                ],
                [
                    'id_user' => $user->id_user,
                    'type' => 'symptom',
                    'input_data' => json_encode(['symptoms' => 'Pusing, mual, dan lemas setelah makan makanan bersantan.']),
                    'ai_response' => json_encode([
                        'status_fisik' => 'Gejala pusing dan mual setelah mengonsumsi santan mungkin mengindikasikan lonjakan kolesterol atau asam lambung yang naik (GERD).',
                        'target_2_bulan' => 'Meningkatkan kesehatan lambung dan menstabilkan profil lipid darah.',
                        'saran_nutrisi' => [
                            'Hindari makanan tinggi lemak jenuh seperti santan kental, jeroan, dan daging berlemak.',
                            'Tingkatkan asupan serat dari sayuran berdaun hijau.',
                            'Minum air hangat dengan jahe untuk meredakan mual.'
                        ],
                        'saran_olahraga' => [
                            'Lakukan yoga atau peregangan ringan setelah makan.',
                            'Hindari berbaring segera setelah makan, tunggu minimal 2 jam.'
                        ],
                        'pesan_motivasi' => 'Pola makan yang tepat adalah kunci kesehatan sejatimu!'
                    ]),
                    'created_at' => now()->subDays(10),
                    'updated_at' => now()->subDays(10),
                ]
            ];

            DB::table('ai_medical_reports')->insert($reports);
        }
    }
}
