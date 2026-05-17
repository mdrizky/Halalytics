<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\ProductModel;
use App\Services\FirebaseRealtimeService;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseRealtimeService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Get user favorites
     */
    public function index(Request $request)
    {
        $favorites = Favorite::where('user_id', $request->user()->id_user)
            ->with('favoritable')
            ->orderBy('created_at', 'desc')
            ->get();

        $favorites->transform(function ($favorite) {
            try {
                $favorite->checkStatusChange();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Favorite status check skipped', [
                    'favorite_id' => $favorite->id,
                    'message' => $e->getMessage(),
                ]);
            }
            $rel = $favorite->favoritable;
            $favorite->barcode = $rel?->barcode ?? $rel?->code ?? null;

            return $favorite;
        });

        return response()->json([
            'success' => true,
            'data' => $favorites,
        ]);
    }

    /**
     * Add to favorites.
     * When "barcode" is sent, server resolves/creates ProductModel — client must not send favoritable_id=0.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'barcode' => 'nullable|string|max:128',
            'favoritable_type' => 'nullable|string',
            'favoritable_id' => 'nullable|integer|min:1',
            'product_name' => 'required|string',
            'product_image' => 'nullable|string',
            'halal_status' => 'required|string',
            'category' => 'nullable|string',
            'user_notes' => 'nullable|string',
        ]);

        if (empty($validated['barcode']) && empty($validated['favoritable_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Wajib kirim barcode atau favoritable_id yang valid.',
            ], 422);
        }

        if (! empty($validated['barcode'])) {
            $product = ProductModel::query()->firstOrCreate(
                ['barcode' => trim($validated['barcode'])],
                [
                    'nama_product' => $validated['product_name'],
                    'status' => $validated['halal_status'],
                    'active' => true,
                    'source' => 'favorite_sync',
                    'image' => $validated['product_image'] ?? null,
                ]
            );

            $validated['favoritable_type'] = ProductModel::class;
            $validated['favoritable_id'] = (int) $product->getKey();
        } else {
            if (empty($validated['favoritable_type'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'favoritable_type wajib jika tidak menggunakan barcode.',
                ], 422);
            }
            $validated['favoritable_type'] = $this->normalizeFavoritableType((string) $validated['favoritable_type']);
        }

        $exists = Favorite::where('user_id', $request->user()->id_user)
            ->where('favoritable_type', $validated['favoritable_type'])
            ->where('favoritable_id', $validated['favoritable_id'])
            ->first();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Product already in favorites',
            ], 409);
        }

        $favorite = Favorite::create([
            'favoritable_type' => $validated['favoritable_type'],
            'favoritable_id' => $validated['favoritable_id'],
            'product_name' => $validated['product_name'],
            'product_image' => $validated['product_image'] ?? null,
            'halal_status' => $validated['halal_status'],
            'category' => $validated['category'] ?? null,
            'user_notes' => $validated['user_notes'] ?? null,
            'user_id' => $request->user()->id_user,
            'last_known_status' => $validated['halal_status'],
        ]);

        $this->firebaseService->syncFavorite($favorite);

        return response()->json([
            'success' => true,
            'message' => 'Added to favorites',
            'data' => $favorite,
        ], 201);
    }

    /**
     * Remove from favorites
     */
    public function destroy($id, Request $request)
    {
        $favorite = Favorite::where('user_id', $request->user()->id_user)
            ->findOrFail($id);

        $favorite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Removed from favorites',
        ]);
    }

    /**
     * Update notes
     */
    public function updateNotes($id, Request $request)
    {
        $validated = $request->validate([
            'user_notes' => 'required|string',
        ]);

        $favorite = Favorite::where('user_id', $request->user()->id_user)
            ->findOrFail($id);

        $favorite->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Notes updated',
            'data' => $favorite,
        ]);
    }

    private function normalizeFavoritableType(string $value): string
    {
        $normalized = strtolower(trim($value));

        return match ($normalized) {
            'product', 'app\\models\\productmodel', 'app/models/productmodel' => ProductModel::class,
            default => ProductModel::class,
        };
    }
}
