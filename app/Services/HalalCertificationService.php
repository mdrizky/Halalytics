<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\HalalProduct;

class HalalCertificationService
{
    private $muiApiUrl;
    private $muiApiKey;

    public function __construct()
    {
        // Konfigurasi API
        $this->muiApiUrl = config('services.mui.api_url');
        $this->muiApiKey = config('services.mui.api_key');
    }

    /**
     * Cek produk dari database MUI
     */
    public function checkMUIDatabase($productName, $brand = null)
    {
        try {
            // Contoh integrasi dengan API MUI (sesuaikan dengan API actual)
            // Jika API Key tidak ada, return mock response untuk testing
            if (empty($this->muiApiKey)) {
                // MOCK logic: jika nama mengandung 'chicken' atau 'beef' return unknown/non-halal
                // jika 'water', 'tea' -> halal
                // Ini hanya placeholder
                return [
                    'success' => false,
                    'message' => 'API Key not configured'
                ];
            }

            $response = Http::withHeaders([
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

        // Jika tidak ditemukan, tandai sebagai unknown
        // KECUALI jika kita punya logika lain. Untuk sekarang unknown.
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
