<?php

namespace App\Services\External;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenFDAService
{
    private string $baseUrl = 'https://api.fda.gov';

    public function getDrugByBarcode(string $barcode): ?array
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/drug/ndc.json", [
                'search' => "package_ndc:{$barcode}",
                'limit' => 1,
            ]);

            $result = $response->json('results.0');
            if (is_array($result)) {
                return [
                    'name' => $result['brand_name'] ?? ($result['generic_name'] ?? 'Unknown Drug Product'),
                    'ingredients' => '',
                    'allergens' => [],
                    'additives' => [],
                    'nutriments' => [
                        'sugars' => 0,
                        'sodium' => 0,
                        'fat' => 0,
                        'protein' => 0,
                        'calories' => 0,
                    ],
                    'source' => 'OpenFDA',
                ];
            }
        } catch (\Throwable $e) {
            Log::warning('OpenFDA barcode fetch failed: ' . $e->getMessage());
        }

        return null;
    }
}
