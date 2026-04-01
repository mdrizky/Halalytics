<?php

namespace App\Services;

use App\Models\BpomData;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BpomService
{
    private const BPOM_SEARCH_URL = 'https://cekbpom.pom.go.id/search';
    private const CACHE_TTL_SEARCH = 604800; // 7 days

    /**
     * Best-effort probe endpoints. Jika endpoint resmi tidak tersedia, service
     * otomatis turun ke scraping dan database lokal.
     */
    private array $publicApiEndpoints = [
        'https://api-pom.go.id/v1/products',
        'https://api-pom.go.id/v1/product',
        'https://api-pom.go.id/v1/search',
    ];

    public function search(string $query, ?string $kategori = null): array
    {
        $normalizedQuery = trim($query);

        if ($normalizedQuery === '') {
            return $this->buildFallbackResponse('Query BPOM tidak boleh kosong.', $normalizedQuery);
        }

        $cacheKey = 'bpom_search_' . md5(Str::lower($normalizedQuery . '|' . ($kategori ?? '')));

        return Cache::remember($cacheKey, self::CACHE_TTL_SEARCH, function () use ($normalizedQuery, $kategori) {
            $localResults = $this->searchLocal($normalizedQuery, $kategori);
            if ($localResults->isNotEmpty()) {
                return $this->buildSuccessResponse('database_lokal', $localResults);
            }

            $apiResults = collect($this->queryPublicApi($normalizedQuery, $kategori));
            if ($apiResults->isNotEmpty()) {
                $saved = $this->saveToLocal($apiResults->all(), 'bpom_public_api');
                return $this->buildSuccessResponse('bpom_public_api', collect($saved));
            }

            $scrapedResults = collect($this->scrapeBpom($normalizedQuery, $kategori));
            if ($scrapedResults->isNotEmpty()) {
                $saved = $this->saveToLocal($scrapedResults->all(), 'bpom_scraping');
                return $this->buildSuccessResponse('bpom_scraping', collect($saved));
            }

            return $this->buildFallbackResponse(
                'Data BPOM sedang dimuat dari sumber resmi. Coba lagi beberapa saat atau gunakan nomor registrasi yang lebih spesifik.',
                $normalizedQuery
            );
        });
    }

    public function checkRegistration(string $nomorReg): array
    {
        $normalizedCode = strtoupper(trim($nomorReg));

        if ($normalizedCode === '') {
            return [
                'found' => false,
                'source' => 'fallback',
                'data' => $this->buildPlaceholderRecord($normalizedCode),
                'message' => 'Nomor registrasi BPOM tidak boleh kosong.',
            ];
        }

        $cacheKey = 'bpom_reg_' . md5($normalizedCode);

        return Cache::remember($cacheKey, self::CACHE_TTL_SEARCH, function () use ($normalizedCode) {
            $existing = BpomData::query()
                ->where('nomor_reg', $normalizedCode)
                ->first();

            if ($existing) {
                return [
                    'found' => true,
                    'source' => 'database_lokal',
                    'data' => $this->formatBpomData($existing),
                    'message' => 'Data BPOM ditemukan di database lokal.',
                ];
            }

            $apiMatches = collect($this->queryPublicApi($normalizedCode));
            $exactApi = $apiMatches->first(fn (array $item) => strtoupper((string) ($item['nomor_reg'] ?? '')) === $normalizedCode);
            if ($exactApi) {
                $saved = $this->saveOneToLocal($exactApi, 'bpom_public_api');
                return [
                    'found' => true,
                    'source' => 'bpom_public_api',
                    'data' => $this->formatBpomData($saved),
                    'message' => 'Data BPOM ditemukan dari API publik.',
                ];
            }

            $scrapedMatches = collect($this->scrapeBpom($normalizedCode));
            $exactScrape = $scrapedMatches->first(fn (array $item) => strtoupper((string) ($item['nomor_reg'] ?? '')) === $normalizedCode)
                ?? $scrapedMatches->first();

            if ($exactScrape) {
                $saved = $this->saveOneToLocal($exactScrape, 'bpom_scraping');
                return [
                    'found' => true,
                    'source' => 'bpom_scraping',
                    'data' => $this->formatBpomData($saved),
                    'message' => 'Data BPOM ditemukan dari situs resmi.',
                ];
            }

            return [
                'found' => false,
                'source' => 'fallback',
                'data' => $this->buildPlaceholderRecord($normalizedCode),
                'message' => 'Nomor registrasi tidak ditemukan dalam database lokal maupun sumber resmi saat ini.',
            ];
        });
    }

    public function syncLatest(int $limit = 100): array
    {
        $keywords = ['obat', 'makanan', 'kosmetik', 'suplemen', 'vitamin', 'sirup', 'cream'];
        $synced = 0;
        $sources = [];
        $errors = [];

        foreach ($keywords as $keyword) {
            try {
                $publicResults = $this->queryPublicApi($keyword);
                if (!empty($publicResults)) {
                    $saved = $this->saveToLocal(array_slice($publicResults, 0, $limit), 'bpom_public_api');
                    $synced += count($saved);
                    $sources['bpom_public_api'] = ($sources['bpom_public_api'] ?? 0) + count($saved);
                }

                if ($synced >= $limit) {
                    break;
                }

                $scrapedResults = $this->scrapeBpom($keyword);
                if (!empty($scrapedResults)) {
                    $saved = $this->saveToLocal(array_slice($scrapedResults, 0, $limit - $synced), 'bpom_scraping');
                    $synced += count($saved);
                    $sources['bpom_scraping'] = ($sources['bpom_scraping'] ?? 0) + count($saved);
                }

                if ($synced >= $limit) {
                    break;
                }
            } catch (\Throwable $throwable) {
                $errors[] = sprintf('%s: %s', $keyword, $throwable->getMessage());
                Log::warning('BPOM sync keyword failed', [
                    'keyword' => $keyword,
                    'error' => $throwable->getMessage(),
                ]);
            }
        }

        $summary = [
            'success' => true,
            'synced_count' => $synced,
            'sources' => $sources,
            'errors' => $errors,
            'ran_at' => now()->toIso8601String(),
        ];

        $this->notifyAdmins($summary);

        return $summary;
    }

    private function searchLocal(string $query, ?string $kategori = null): Collection
    {
        return BpomData::query()
            ->when($kategori, fn ($builder) => $builder->where('kategori', $kategori))
            ->where(function ($builder) use ($query) {
                $builder->where('nomor_reg', 'like', "%{$query}%")
                    ->orWhere('nama_produk', 'like', "%{$query}%")
                    ->orWhere('merk', 'like', "%{$query}%")
                    ->orWhere('pendaftar', 'like', "%{$query}%");
            })
            ->orderByDesc('is_verified_manually')
            ->orderByDesc('verification_status')
            ->orderBy('nama_produk')
            ->limit(20)
            ->get()
            ->map(fn (BpomData $item) => $this->formatBpomData($item));
    }

    private function queryPublicApi(string $query, ?string $kategori = null): array
    {
        foreach ($this->publicApiEndpoints as $endpoint) {
            try {
                $response = Http::timeout(12)
                    ->acceptJson()
                    ->withHeaders([
                        'User-Agent' => 'Halalytics/1.0 (+https://halalytics.app)',
                    ])
                    ->get($endpoint, array_filter([
                        'q' => $query,
                        'query' => $query,
                        'keyword' => $query,
                        'kategori' => $kategori,
                    ]));

                if (!$response->successful()) {
                    continue;
                }

                $rows = $this->extractPublicApiRows($response->json());
                if (!empty($rows)) {
                    return $rows;
                }
            } catch (\Throwable $throwable) {
                Log::debug('BPOM public API probe failed', [
                    'endpoint' => $endpoint,
                    'query' => $query,
                    'error' => $throwable->getMessage(),
                ]);
            }
        }

        return [];
    }

    private function extractPublicApiRows(array $payload): array
    {
        $data = $payload['data'] ?? $payload['results'] ?? $payload['items'] ?? $payload;

        if (!is_array($data)) {
            return [];
        }

        $rows = [];
        foreach ($data as $item) {
            if (!is_array($item)) {
                continue;
            }

            $rows[] = $this->normalizeBpomRow([
                'nomor_reg' => $item['nomor_reg'] ?? $item['registration_number'] ?? $item['no_registrasi'] ?? $item['nomorRegistrasi'] ?? null,
                'nama_produk' => $item['nama_produk'] ?? $item['product_name'] ?? $item['nama'] ?? null,
                'merk' => $item['merk'] ?? $item['brand'] ?? null,
                'pendaftar' => $item['pendaftar'] ?? $item['company'] ?? $item['produsen'] ?? null,
                'kategori' => $item['kategori'] ?? $item['category'] ?? null,
                'status_registrasi' => $item['status_registrasi'] ?? $item['status'] ?? 'Terdaftar',
                'tanggal' => $item['tanggal'] ?? $item['registered_at'] ?? $item['created_at'] ?? null,
                'source_label' => 'bpom_public_api',
            ]);
        }

        return array_values(array_filter($rows, fn (array $row) => !empty($row['nomor_reg']) || !empty($row['nama_produk'])));
    }

    private function scrapeBpom(string $query, ?string $kategori = null): array
    {
        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml',
                ])
                ->get(self::BPOM_SEARCH_URL, array_filter([
                    'query' => $query,
                    'q' => $query,
                    'kategori' => $kategori,
                ]));

            if (!$response->successful()) {
                return [];
            }

            return $this->parseBpomHtml($response->body());
        } catch (\Throwable $throwable) {
            Log::warning('BPOM scraping failed', [
                'query' => $query,
                'error' => $throwable->getMessage(),
            ]);

            return [];
        }
    }

    private function parseBpomHtml(string $html): array
    {
        $results = [];

        if (preg_match_all('/<tr[^>]*>(.*?)<\/tr>/si', $html, $rows)) {
            foreach ($rows[1] as $row) {
                if (!preg_match_all('/<td[^>]*>(.*?)<\/td>/si', $row, $cells)) {
                    continue;
                }

                $cellData = array_map(fn ($cell) => trim(strip_tags(html_entity_decode($cell))), $cells[1]);
                if (count($cellData) < 2) {
                    continue;
                }

                $results[] = $this->normalizeBpomRow([
                    'nomor_reg' => $cellData[0] ?? null,
                    'nama_produk' => $cellData[1] ?? null,
                    'pendaftar' => $cellData[2] ?? null,
                    'kategori' => $cellData[3] ?? $this->inferCategoryFromRegNumber($cellData[0] ?? ''),
                    'status_registrasi' => $cellData[4] ?? 'Terdaftar',
                    'tanggal' => $cellData[5] ?? null,
                    'source_label' => 'bpom_scraping',
                ]);
            }
        }

        return array_values(array_filter($results, fn (array $row) => !empty($row['nomor_reg']) || !empty($row['nama_produk'])));
    }

    private function normalizeBpomRow(array $row): array
    {
        $statusRegistrasi = trim((string) ($row['status_registrasi'] ?? 'Terdaftar'));
        $nomorReg = trim((string) ($row['nomor_reg'] ?? ''));

        return [
            'nomor_reg' => $nomorReg !== '' ? $nomorReg : null,
            'nama_produk' => trim((string) ($row['nama_produk'] ?? '')) ?: null,
            'merk' => trim((string) ($row['merk'] ?? $row['nama_produk'] ?? '')) ?: null,
            'pendaftar' => trim((string) ($row['pendaftar'] ?? '')) ?: null,
            'kategori' => trim((string) ($row['kategori'] ?? $this->inferCategoryFromRegNumber($nomorReg))) ?: 'umum',
            'status_registrasi' => $statusRegistrasi,
            'tanggal' => $row['tanggal'] ?? null,
            'status_keamanan' => Str::contains(Str::lower($statusRegistrasi), 'batal') ? 'bahaya' : 'aman',
            'status_halal' => 'belum_diverifikasi',
            'source_label' => $row['source_label'] ?? 'bpom_scraping',
        ];
    }

    private function saveToLocal(array $results, string $source): array
    {
        $saved = [];
        foreach ($results as $row) {
            $saved[] = $this->formatBpomData($this->saveOneToLocal($row, $source));
        }

        return $saved;
    }

    private function saveOneToLocal(array $row, string $source): BpomData
    {
        $normalized = $this->normalizeBpomRow($row);
        $lookup = ['nomor_reg' => $normalized['nomor_reg'] ?: 'UNKNOWN_' . md5(($normalized['nama_produk'] ?? '') . '|' . $source)];

        return BpomData::updateOrCreate(
            $lookup,
            [
                'nama_produk' => $normalized['nama_produk'] ?? 'Tidak diketahui',
                'merk' => $normalized['merk'],
                'pendaftar' => $normalized['pendaftar'],
                'kategori' => $normalized['kategori'],
                'status_keamanan' => $normalized['status_keamanan'],
                'status_halal' => $normalized['status_halal'],
                'sumber_data' => $source,
                'verification_status' => $source === 'manual_entry' ? 'verified' : 'pending',
                'is_verified_manually' => $source === 'manual_entry',
                'last_synced_at' => now(),
            ]
        );
    }

    private function buildSuccessResponse(string $source, Collection $results): array
    {
        return [
            'found' => true,
            'source' => $source,
            'results' => $results->values()->all(),
            'total' => $results->count(),
            'message' => 'Data BPOM berhasil ditemukan.',
        ];
    }

    private function buildFallbackResponse(string $message, string $query): array
    {
        return [
            'found' => false,
            'source' => 'fallback',
            'results' => [$this->buildPlaceholderRecord($query)],
            'total' => 1,
            'message' => $message,
        ];
    }

    private function buildPlaceholderRecord(string $query): array
    {
        return [
            'id' => null,
            'nomor_reg' => $query ?: null,
            'nama_produk' => 'Data BPOM sedang diverifikasi',
            'merk' => null,
            'kategori' => $this->inferCategoryFromRegNumber($query),
            'pendaftar' => null,
            'alamat_produsen' => null,
            'kemasan' => null,
            'bentuk_sediaan' => null,
            'tanggal_terbit' => null,
            'masa_berlaku' => null,
            'status_keamanan' => 'unknown',
            'skor_keamanan' => null,
            'status_halal' => 'belum_diverifikasi',
            'status_registrasi' => 'Perlu pengecekan manual',
            'is_expired' => false,
            'source' => 'fallback',
            'image_url' => null,
            'is_verified_manually' => false,
            'last_synced_at' => null,
        ];
    }

    private function notifyAdmins(array $summary): void
    {
        $admins = User::query()
            ->where('role', 'admin')
            ->get(['id_user']);

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id_user,
                'title' => 'Sync BPOM selesai',
                'message' => sprintf(
                    'Sinkronisasi BPOM selesai. %d data diproses, %d error.',
                    (int) ($summary['synced_count'] ?? 0),
                    count($summary['errors'] ?? [])
                ),
                'type' => 'system',
                'extra_data' => $summary,
                'action_type' => 'open_screen',
                'action_value' => 'admin_bpom',
            ]);
        }
    }

    private function formatBpomData(BpomData $item): array
    {
        return [
            'id' => $item->id,
            'nomor_reg' => $item->nomor_reg,
            'nama_produk' => $item->nama_produk,
            'merk' => $item->merk,
            'kategori' => $item->kategori,
            'pendaftar' => $item->pendaftar,
            'alamat_produsen' => $item->alamat_produsen,
            'kemasan' => $item->kemasan,
            'bentuk_sediaan' => $item->bentuk_sediaan,
            'tanggal_terbit' => optional($item->tanggal_terbit)->toDateString(),
            'masa_berlaku' => optional($item->masa_berlaku)->toDateString(),
            'status_keamanan' => $item->status_keamanan,
            'skor_keamanan' => $item->skor_keamanan,
            'status_halal' => $item->status_halal,
            'status_registrasi' => $item->verification_status === 'verified' ? 'Terverifikasi' : 'Terdaftar',
            'is_expired' => $item->isExpired(),
            'source' => $item->sumber_data,
            'image_url' => $item->image_url,
            'is_verified_manually' => (bool) $item->is_verified_manually,
            'last_synced_at' => optional($item->last_synced_at)->toIso8601String(),
        ];
    }

    private function inferCategoryFromRegNumber(string $nomorReg): string
    {
        $prefix = strtoupper(substr(trim($nomorReg), 0, 2));

        return match ($prefix) {
            'NA', 'NC', 'ND' => 'kosmetik',
            'MD', 'ML', 'FF' => 'pangan',
            'TR', 'TI' => 'obat_tradisional',
            'SD', 'SI' => 'suplemen',
            'DB', 'DK', 'DT', 'DL' => 'obat',
            default => 'umum',
        };
    }
}
