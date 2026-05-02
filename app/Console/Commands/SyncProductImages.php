<?php

namespace App\Console\Commands;

use App\Models\ProductModel;
use App\Services\ProductImageService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SyncProductImages extends Command
{
    protected $signature = 'products:sync-images
        {--audit-only : Hanya audit tanpa mengubah database}
        {--only-missing : Hanya proses produk yang image-nya kosong}
        {--force-refresh : Refresh semua produk walau image saat ini valid}
        {--limit= : Batasi jumlah produk yang diproses}';

    protected $description = 'Audit, verify, dan sinkronkan gambar produk ke sumber lokal yang stabil.';

    public function handle(ProductImageService $productImageService): int
    {
        $query = ProductModel::with('kategori')->orderBy('id_product');

        if ($this->option('only-missing')) {
            $query->where(function ($builder) {
                $builder->whereNull('image')->orWhere('image', '');
            });
        }

        if ($this->option('limit')) {
            $query->limit((int) $this->option('limit'));
        }

        $products = $query->get();

        if ($products->isEmpty()) {
            $this->warn('Tidak ada produk yang perlu diproses.');
            return self::SUCCESS;
        }

        $isAuditOnly = (bool) $this->option('audit-only');
        $forceRefresh = (bool) $this->option('force-refresh');

        $summary = [
            'total' => $products->count(),
            'missing' => 0,
            'broken' => 0,
            'ok' => 0,
            'updated' => 0,
            'generated' => 0,
            'fallback' => 0,
            'kept' => 0,
        ];

        $rows = [];

        foreach ($products as $product) {
            $audit = $productImageService->auditProduct($product);

            if ($audit['state'] === 'missing') {
                $summary['missing']++;
            } elseif (str_contains($audit['state'], 'broken')) {
                $summary['broken']++;
            } else {
                $summary['ok']++;
            }

            if ($isAuditOnly) {
                $rows[] = [
                    $product->id_product,
                    Str::limit($product->nama_product, 28),
                    $audit['state'],
                    $audit['category_key'],
                    $audit['resolved_image'],
                ];
                continue;
            }

            $result = $productImageService->syncProduct($product, $forceRefresh);

            if ($result['status'] === 'updated') {
                $summary['updated']++;
            } elseif ($result['status'] === 'generated') {
                $summary['generated']++;
            } elseif ($result['status'] === 'fallback') {
                $summary['fallback']++;
            } else {
                $summary['kept']++;
            }

            $rows[] = [
                $product->id_product,
                Str::limit($product->nama_product, 28),
                $result['status'],
                $result['source'],
                $result['resolved_image'],
            ];
        }

        $this->table(
            ['ID', 'Produk', $isAuditOnly ? 'Audit' : 'Status', 'Source/Category', 'Resolved image'],
            $rows
        );

        $this->newLine();
        $this->info('Ringkasan sync gambar:');
        foreach ($summary as $label => $value) {
            $this->line(str_pad($label, 12, ' ', STR_PAD_RIGHT) . ': ' . $value);
        }

        return self::SUCCESS;
    }
}
