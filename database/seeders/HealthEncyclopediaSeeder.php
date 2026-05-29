<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HealthEncyclopedia;

class HealthEncyclopediaSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'type' => 'obat',
                'alphabet' => 'P',
                'title' => 'Paracetamol',
                'summary' => 'Obat pereda nyeri dan penurun demam.',
                'content' => 'Paracetamol adalah obat yang digunakan untuk meredakan nyeri ringan hingga sedang, seperti sakit kepala, nyeri haid, sakit gigi, dan nyeri sendi, serta untuk menurunkan demam.',
                'source_link' => 'https://www.alodokter.com/paracetamol',
            ],
            [
                'type' => 'penyakit',
                'alphabet' => 'D',
                'title' => 'Diabetes Melitus',
                'summary' => 'Penyakit jangka panjang yang ditandai dengan kadar gula darah tinggi.',
                'content' => 'Diabetes melitus adalah gangguan metabolisme yang ditandai dengan tingginya kadar gula darah karena tubuh tidak dapat memproduksi atau menggunakan insulin dengan efektif.',
                'source_link' => 'https://www.alodokter.com/diabetes-melitus',
            ],
            [
                'type' => 'hidup_sehat',
                'alphabet' => 'P',
                'title' => 'Pola Makan Sehat',
                'summary' => 'Panduan mengonsumsi makanan bergizi seimbang.',
                'content' => 'Pola makan sehat mencakup konsumsi berbagai jenis makanan yang mengandung nutrisi penting seperti protein, karbohidrat, lemak sehat, vitamin, dan mineral dalam jumlah yang seimbang.',
                'source_link' => 'https://www.alodokter.com/hidup-sehat',
            ],
            [
                'type' => 'keluarga',
                'alphabet' => 'I',
                'title' => 'Imunisasi Anak',
                'summary' => 'Pemberian vaksin untuk meningkatkan kekebalan tubuh anak.',
                'content' => 'Imunisasi adalah upaya untuk memberikan kekebalan kepada bayi dan anak terhadap penyakit tertentu dengan memasukkan vaksin ke dalam tubuh.',
                'source_link' => 'https://www.alodokter.com/imunisasi',
            ],
        ];

        foreach ($data as $item) {
            HealthEncyclopedia::updateOrCreate(['title' => $item['title']], $item);
        }

        // Import from alodokter_data.json
        $jsonPath = base_path('alodokter_data.json');
        if (file_exists($jsonPath)) {
            $jsonContent = file_get_contents($jsonPath);
            $alodokterData = json_decode($jsonContent, true);
            
            if (is_array($alodokterData)) {
                foreach ($alodokterData as $item) {
                    // Simple parsing for causes, symptoms, etc.
                    $content = $item['content'] ?? '';
                    $causes = '';
                    $symptoms = '';
                    $treatments = '';
                    $halalNotes = 'Belum ada catatan khusus halal untuk kondisi ini.';

                    // Try to split content if it has specific headers (common in scraping Alodokter/Halodoc)
                    if (preg_match('/Penyebab(.*?)(?=Gejala|Diagnosis|Pengobatan|$)/si', $content, $matches)) {
                        $causes = trim(strip_tags($matches[1]));
                    }
                    if (preg_match('/Gejala(.*?)(?=Penyebab|Diagnosis|Pengobatan|$)/si', $content, $matches)) {
                        $symptoms = trim(strip_tags($matches[1]));
                    }
                    if (preg_match('/Pengobatan(.*?)(?=Penyebab|Gejala|Diagnosis|$)/si', $content, $matches)) {
                        $treatments = trim(strip_tags($matches[1]));
                    }

                    HealthEncyclopedia::create([
                        'type'        => $item['type'] ?? 'disease',
                        'alphabet'    => $item['alphabet'] ?? 'A',
                        'title'       => $item['title'] ?? 'Unknown',
                        'summary'     => $item['summary'] ?? '',
                        'content'     => $content,
                        'causes'      => $causes ?: null,
                        'symptoms'    => $symptoms ?: null,
                        'treatments'  => $treatments ?: null,
                        'halal_notes' => $halalNotes,
                        'source_link' => $item['source_link'] ?? null,
                    ]);
                }
            }
        }
    }
}
