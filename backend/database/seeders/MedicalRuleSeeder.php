<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicalRuleSeeder extends Seeder
{
    public function run(): void
    {
        $symptomRules = [
            ['keyword' => 'demam', 'drug_name' => 'Paracetamol', 'drug_type' => 'OTC', 'indication' => 'Demam dan nyeri ringan-sedang', 'severity_score' => 2, 'warnings' => json_encode(['Hindari melebihi dosis kemasan', 'Waspada bila ada gangguan hati'])],
            ['keyword' => 'sakit kepala', 'drug_name' => 'Paracetamol', 'drug_type' => 'OTC', 'indication' => 'Sakit kepala ringan', 'severity_score' => 2, 'warnings' => json_encode(['Hindari penggunaan berlebihan'])],
            ['keyword' => 'batuk', 'drug_name' => 'Guaifenesin', 'drug_type' => 'OTC', 'indication' => 'Membantu mengencerkan dahak', 'severity_score' => 2, 'warnings' => json_encode(['Minum air cukup'])],
            ['keyword' => 'maag', 'drug_name' => 'Antasida', 'drug_type' => 'OTC', 'indication' => 'Keluhan maag ringan', 'severity_score' => 2, 'warnings' => json_encode(['Pisahkan 2 jam dari obat lain'])],
            ['keyword' => 'nyeri dada', 'drug_name' => 'NONE', 'drug_type' => 'REFER', 'indication' => 'Butuh evaluasi medis segera', 'severity_score' => 5, 'warnings' => json_encode(['Red-flag: segera ke IGD'])],
        ];

        foreach ($symptomRules as $row) {
            DB::table('medical_symptom_rules')->updateOrInsert(
                ['keyword' => $row['keyword'], 'drug_name' => $row['drug_name']],
                array_merge($row, ['updated_at' => now(), 'created_at' => now()])
            );
        }

        $contra = [
            ['drug_name' => 'Paracetamol', 'condition_keyword' => 'penyakit hati', 'warning_text' => 'Riwayat penyakit hati: konsultasi dokter sebelum memakai Paracetamol.'],
            ['drug_name' => 'Antasida', 'condition_keyword' => 'gagal ginjal', 'warning_text' => 'Riwayat gangguan ginjal: gunakan antasida hanya atas arahan dokter.'],
            ['drug_name' => 'Guaifenesin', 'condition_keyword' => 'alergi obat', 'warning_text' => 'Riwayat alergi obat: cek komposisi, hentikan jika muncul reaksi alergi.'],
        ];

        foreach ($contra as $row) {
            DB::table('medical_contraindication_rules')->updateOrInsert(
                ['drug_name' => $row['drug_name'], 'condition_keyword' => $row['condition_keyword']],
                array_merge($row, ['updated_at' => now(), 'created_at' => now()])
            );
        }

        $interactions = [
            ['drug_a' => 'Paracetamol', 'drug_b' => 'Alcohol', 'risk_level' => 'high', 'warning_text' => 'Hindari konsumsi alkohol berlebihan saat memakai paracetamol (risiko hati).'],
            ['drug_a' => 'Antasida', 'drug_b' => 'Tetracycline', 'risk_level' => 'high', 'warning_text' => 'Antasida dapat menurunkan absorpsi tetracycline, beri jeda pemakaian.'],
        ];

        foreach ($interactions as $row) {
            DB::table('drug_interaction_blacklists')->updateOrInsert(
                ['drug_a' => $row['drug_a'], 'drug_b' => $row['drug_b']],
                array_merge($row, ['updated_at' => now(), 'created_at' => now()])
            );
        }
    }
}
