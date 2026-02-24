<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
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
        $favorites = Favorite::where('user_id', $request->user()->id)
            ->with('favoritable')
            ->orderBy('created_at', 'desc')
            ->get();

        // Check for status changes & append barcode
        $favorites->transform(function ($favorite) {
            $favorite->checkStatusChange();
            // Append barcode from related model (Product/BpomData) if available
            $favorite->barcode = $favorite->favoritable->barcode ?? $favorite->favoritable->code ?? null;
            return $favorite;
        });

        return response()->json([
            'success' => true,
            'data' => $favorites
        ]);
    }

    /**
     * Add to favorites
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'favoritable_type' => 'required|string',
            'favoritable_id' => 'required|integer',
            'product_name' => 'required|string',
            'product_image' => 'nullable|string',
            'halal_status' => 'required|string',
            'category' => 'nullable|string',
            'user_notes' => 'nullable|string',
        ]);

        // Check if already favorited
        $exists = Favorite::where('user_id', $request->user()->id)
            ->where('favoritable_type', $validated['favoritable_type'])
            ->where('favoritable_id', $validated['favoritable_id'])
            ->first();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Product already in favorites'
            ], 409);
        }

        $favorite = Favorite::create([
            ...$validated,
            'user_id' => $request->user()->id,
            'last_known_status' => $validated['halal_status'],
        ]);

        // Sync to Firebase
        $this->firebaseService->syncFavorite($favorite);

        return response()->json([
            'success' => true,
            'message' => 'Added to favorites',
            'data' => $favorite
        ], 201);
    }

    /**
     * Remove from favorites
     */
    public function destroy($id, Request $request)
    {
        $favorite = Favorite::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $favorite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Removed from favorites'
        ]);
    }

    /**
     * Update notes
     */
    public function updateNotes($id, Request $request)
    {
        $validated = $request->validate([
            'user_notes' => 'required|string'
        ]);

        $favorite = Favorite::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $favorite->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Notes updated',
            'data' => $favorite
        ]);
    }
}
