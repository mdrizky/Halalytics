<?php

namespace App\Jobs;

use App\Models\ProductModel;
use App\Models\User;
use App\Models\ProductAnalysisResult;
use App\Notifications\ScanResultReady;
use App\Services\AIAnalysisService;
use App\Services\HalalAnalysisService;
use App\Services\HealthAnalysisService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AnalyzeProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(
        public readonly int $productId,
        public readonly int $userId
    ) {}

    public function handle(
        AIAnalysisService     $ai,
        HalalAnalysisService  $halal,
        HealthAnalysisService $health
    ): void {
        $product = ProductModel::findOrFail($this->productId);
        $user    = User::findOrFail($this->userId);

        // 1. Halal analysis
        $halalPrompt  = $halal->buildHalalPrompt(
            json_decode($product->komposisi, true) ?? [],
            $product->halal_certificate
        );
        $halalResult  = $ai->analyzeWithGroq($halalPrompt);

        // 2. Health analysis (only for food)
        $healthResult = null;
        if ($product->kategori_id !== null) { // Simple category check
            $healthPrompt = $health->buildHealthPrompt(
                [
                    'product_name' => $product->nama_product,
                    'calories' => $product->calories,
                    'sugar' => $product->sugar_g,
                    'fat' => $product->fat_g,
                    'saturated_fat' => $product->saturated_fat_g ?? 0,
                    'sodium' => $product->sodium_mg ?? 0,
                    'protein' => $product->protein_g,
                    'carbs' => $product->carbs_g,
                ],
                [
                    'age' => $user->age,
                    'weight_kg' => $user->weight,
                    'height_cm' => $user->height,
                    'gender' => $user->gender ?? 'unknown',
                    'health_conditions' => explode(',', $user->medical_history ?? ''),
                    'diet_goal' => $user->goal,
                    'activity_level' => $user->activity_level,
                ]
            );
            $healthResult = $ai->analyzeWithGroq($healthPrompt);
        }

        // 3. Save result to ProductAnalysisResult
        $result = ProductAnalysisResult::updateOrCreate(
            ['product_id' => $this->productId, 'user_id' => $this->userId],
            [
                'input_type' => 'barcode',
                'raw_input' => $product->barcode,
                'halal_verdict' => $halalResult['verdict'] ?? 'SYUBHAT',
                'halal_data' => $halalResult,
                'health_verdict' => $healthResult['consumption_verdict'] ?? null,
                'health_data' => $healthResult,
                'nutri_score' => $healthResult['nutri_score'] ?? null,
            ]
        );

        // 4. Notify user that results are ready
        $user->notify(new ScanResultReady($result->id));

        Log::info("Analysis completed for product {$this->productId}");
    }

    public function failed(\Throwable $e): void
    {
        Log::error("AI analysis failed for product {$this->productId}: " . $e->getMessage());
    }
}
