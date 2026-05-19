<?php

namespace App\Services\External;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenFDAService
{
    private string $baseUrl = 'https://api.fda.gov';

    public function searchDrug(string $query): array
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/drug/label.json", [
                'search' => 'openfda.brand_name:"' . addslashes($query) . '"',
                'limit' => 5,
            ]);

            return $response->json('results', []) ?? [];
        } catch (\Throwable $e) {
            Log::error('OpenFDA search error: ' . $e->getMessage());

            return [];
        }
    }

    public function getDrugByBarcode(string $barcode): ?array
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/drug/ndc.json", [
                'search' => "package_ndc:{$barcode}",
                'limit' => 1,
            ]);
            $results = $response->json('results', []);

            return ! empty($results) ? $results[0] : null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
