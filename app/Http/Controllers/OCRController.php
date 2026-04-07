<?php

namespace App\Http\Controllers;

use App\Models\OCRProduct;
use App\Models\ScanHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OCRController extends Controller
{
    /**
     * List all OCR products with pagination (admin web)
     */
    public function index()
    {
        $products = OCRProduct::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Upload and process OCR image
     */
    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
                'step' => 'required|in:front,back,processing',
                'user_id' => 'nullable|exists:users,id'
            ]);

            $userId = $request->user_id ?? Auth::id();
            if (!$userId) {
                throw new \Exception('No user identified');
            }
            $user = User::findOrFail($userId);
            $image = $request->file('image');
            $step = $request->step;

            // Generate unique filename
            $filename = 'ocr_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('ocr-images', $filename, 'public');

            // Process image with mock OCR (Vision API fallback)
            $ocrResult = $this->getMockOCRResult();

            // Create or update OCR product record
            $ocrProduct = OCRProduct::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'barcode' => $request->barcode ?? null,
                    'status' => 'pending'
                ],
                [
                    'front_image' => $step === 'front' ? $path : null,
                    'back_image' => $step === 'back' ? $path : null,
                    'extracted_text' => $ocrResult['text'] ?? '',
                    'ingredients' => $ocrResult['ingredients'] ?? [],
                    'confidence_score' => $ocrResult['confidence'] ?? 0,
                    'processing_step' => $step,
                    'processed_at' => now()
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Image processed successfully',
                'data' => [
                    'ocr_product_id' => $ocrProduct->id,
                    'extracted_text' => $ocrResult['text'] ?? '',
                    'ingredients' => $ocrResult['ingredients'] ?? [],
                    'confidence_score' => $ocrResult['confidence'] ?? 0,
                    'processing_step' => $step,
                    'next_step' => $this->getNextStep($step)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get next processing step
     */
    private function getNextStep($currentStep)
    {
        switch ($currentStep) {
            case 'front': return 'back';
            case 'back': return 'processing';
            case 'processing': return 'complete';
            default: return 'front';
        }
    }

    /**
     * Mock OCR result for testing
     */
    private function getMockOCRResult()
    {
        return [
            'text' => 'Ingredients: Wheat flour, vegetable oil, salt, sugar, yeast extract, natural flavors, spices. Contains wheat and soy.',
            'ingredients' => [
                'Wheat flour', 'vegetable oil', 'salt', 'sugar',
                'yeast extract', 'natural flavors', 'spices', 'soy'
            ],
            'confidence' => 0.87
        ];
    }

    /**
     * Get OCR products for admin review (pending)
     */
    public function getPendingProducts()
    {
        $products = OCRProduct::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Show single OCR product detail
     */
    public function show($id)
    {
        $product = OCRProduct::with('user')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * Approve OCR product
     */
    public function approve(Request $request, $id)
    {
        try {
            $product = OCRProduct::findOrFail($id);

            $product->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'notes' => $request->notes
            ]);

            // Add to scan history if barcode exists
            if ($product->barcode) {
                ScanHistory::create([
                    'user_id' => $product->user_id,
                    'product_name' => $this->extractProductName($product->extracted_text),
                    'barcode' => $product->barcode,
                    'ingredients' => $product->ingredients,
                    'halal_status' => $this->determineHalalStatus($product->ingredients ?? []),
                    'scan_date' => now()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product approved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject OCR product
     */
    public function reject(Request $request, $id)
    {
        try {
            $product = OCRProduct::findOrFail($id);

            $product->update([
                'status' => 'rejected',
                'rejected_by' => Auth::id(),
                'rejected_at' => now(),
                'rejection_reason' => $request->reason
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product rejected successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk approve OCR products
     */
    public function bulkApprove(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:ocr_products,id'
            ]);

            OCRProduct::whereIn('id', $request->ids)
                ->where('status', 'pending')
                ->update([
                    'status' => 'approved',
                    'approved_by' => Auth::id(),
                    'approved_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' products approved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk approve: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk reject OCR products
     */
    public function bulkReject(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:ocr_products,id',
                'reason' => 'nullable|string'
            ]);

            OCRProduct::whereIn('id', $request->ids)
                ->where('status', 'pending')
                ->update([
                    'status' => 'rejected',
                    'rejected_by' => Auth::id(),
                    'rejected_at' => now(),
                    'rejection_reason' => $request->reason ?? 'Bulk rejected'
                ]);

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' products rejected'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk reject: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * List approved products
     */
    public function getApprovedProducts()
    {
        $products = OCRProduct::with('user')
            ->where('status', 'approved')
            ->orderBy('approved_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * List rejected products
     */
    public function getRejectedProducts()
    {
        $products = OCRProduct::with('user')
            ->where('status', 'rejected')
            ->orderBy('rejected_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Export OCR data to CSV
     */
    public function export()
    {
        $products = OCRProduct::with('user')->get();

        $csvData = "ID,User,Barcode,Status,Extracted Text,Confidence,Created At\n";
        foreach ($products as $product) {
            $csvData .= implode(',', [
                $product->id,
                '"' . ($product->user->username ?? 'N/A') . '"',
                '"' . ($product->barcode ?? '') . '"',
                $product->status,
                '"' . str_replace('"', '""', substr($product->extracted_text ?? '', 0, 100)) . '"',
                $product->confidence_score ?? 0,
                $product->created_at
            ]) . "\n";
        }

        return response($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ocr_products_export_' . date('Y-m-d') . '.csv"'
        ]);
    }

    /**
     * Get OCR statistics
     */
    public function statistics()
    {
        $stats = [
            'total' => OCRProduct::count(),
            'pending' => OCRProduct::where('status', 'pending')->count(),
            'approved' => OCRProduct::where('status', 'approved')->count(),
            'rejected' => OCRProduct::where('status', 'rejected')->count(),
            'today_total' => OCRProduct::whereDate('created_at', today())->count(),
            'today_approved' => OCRProduct::where('status', 'approved')
                ->whereDate('approved_at', today())->count(),
            'today_rejected' => OCRProduct::where('status', 'rejected')
                ->whereDate('rejected_at', today())->count(),
            'processing_accuracy' => $this->calculateAccuracy()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Helper methods
     */
    private function extractProductName($text)
    {
        $lines = explode("\n", $text ?? '');
        return trim($lines[0] ?? 'Unknown Product');
    }

    private function determineHalalStatus($ingredients)
    {
        $haramIngredients = [
            'pork', 'alcohol', 'gelatin', 'lard', 'carmine', 'blood',
            'non-halal', 'wine', 'beer', 'bacon', 'ham'
        ];

        if (!is_array($ingredients)) {
            return 'Halal';
        }

        foreach ($ingredients as $ingredient) {
            foreach ($haramIngredients as $haram) {
                if (stripos($ingredient, $haram) !== false) {
                    return 'Haram';
                }
            }
        }

        return 'Halal';
    }

    private function calculateAccuracy()
    {
        $total = OCRProduct::where('status', '!=', 'pending')->count();
        $approved = OCRProduct::where('status', 'approved')->count();

        return $total > 0 ? round(($approved / $total) * 100, 2) : 0;
    }
}
