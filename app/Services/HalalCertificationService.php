<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\HalalProduct;
use App\Services\ExternalApiService;

class HalalCertificationService
{
    private $muiApiUrl;
    private $muiApiKey;
    private $externalApiService;

    public function __construct(ExternalApiService $externalApiService)
    {
        // Konfigurasi API
        $this->muiApiUrl = config('services.mui.api_url');
        $this->muiApiKey = config('services.mui.api_key');
        $this->externalApiService = $externalApiService;
    }

    /**
     * Cek produk dari database MUI
     */
    public function checkMUIDatabase($productName, $brand = null)
    {
        try {
            // Jika API Key tidak ada, return response false agar lanjut ke fallback
            if (empty($this->muiApiKey)) {
                return [
                    'success' => false,
                    'message' => 'API Key not configured'
                ];
            }

            $response = Http::timeout(5)->withHeaders([
                'Authorization' => 'Bearer ' . $this->muiApiKey,
                'Accept' => 'application/json'
            ])->get($this->muiApiUrl . '/products/search', [
                'name' => $productName,
                'brand' => $brand
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'Product not found in MUI database'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Cek dan simpan ke database lokal
     */
    public function verifyAndStore($barcode, $productName, $brand)
    {
        // Cek di database lokal dulu
        $existing = HalalProduct::where('product_barcode', $barcode)
            ->where('last_checked_at', '>', now()->subDays(30)) // cache 30 hari
            ->first();

        if ($existing) {
            return [
                'success' => true,
                'data' => $existing,
                'source' => 'cache'
            ];
        }

        // Cek ke MUI API
        $muiResult = $this->checkMUIDatabase($productName, $brand);

        if ($muiResult['success'] && !empty($muiResult['data'])) {
            $productData = $muiResult['data'][0] ?? $muiResult['data'];

            // Simpan atau update
            $halalProduct = HalalProduct::updateOrCreate(
                ['product_barcode' => $barcode],
                [
                    'product_name' => $productName,
                    'brand' => $brand,
                    'halal_certificate_number' => $productData['certificate_number'] ?? null,
                    'halal_status' => 'halal',
                    'certification_body' => 'MUI',
                    'certificate_valid_until' => $productData['valid_until'] ?? null,
                    'certificate_data' => $productData,
                    'last_checked_at' => now()
                ]
            );

            return [
                'success' => true,
                'data' => $halalProduct,
                'source' => 'mui_api'
            ];
        }

        // FALLBACK: Cek ke Open Food Facts jika MUI gagal/tidak ada API
        $offResult = $this->externalApiService->searchOpenFoodFacts($barcode);
        
        if ($offResult['found']) {
            $labels = strtolower($offResult['labels'] ?? '');
            $status = 'unknown';
            $certBody = null;

            if (str_contains($labels, 'halal')) {
                $status = 'halal';
                $certBody = 'Internasional (via OpenFoodFacts)';
            } elseif (str_contains($labels, 'haram') || str_contains($labels, 'pork')) {
                $status = 'haram';
            }

            if ($status !== 'unknown') {
                $halalProduct = HalalProduct::updateOrCreate(
                    ['product_barcode' => $barcode],
                    [
                        'product_name' => $offResult['nama_produk'] ?? $productName,
                        'brand' => $offResult['merk'] ?? $brand,
                        'halal_status' => $status,
                        'certification_body' => $certBody,
                        'last_checked_at' => now()
                    ]
                );

                return [
                    'success' => true,
                    'data' => $halalProduct,
                    'source' => 'open_food_facts_fallback'
                ];
            }
        }

        // Jika tidak ditemukan, tandai sebagai unknown
        $halalProduct = HalalProduct::updateOrCreate(
            ['product_barcode' => $barcode],
            [
                'product_name' => $productName,
                'brand' => $brand,
                'halal_status' => 'unknown',
                'last_checked_at' => now()
            ]
        );

        return [
            'success' => true,
            'data' => $halalProduct,
            'source' => 'not_found'
        ];
    }
}
