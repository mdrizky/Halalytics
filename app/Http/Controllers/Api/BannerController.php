<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    /**
     * Get all active banners
     */
    public function index()
    {
        try {
            $banners = Banner::where('is_active', true)
                ->orderBy('position', 'asc')
                ->get(['id', 'title', 'description', 'image', 'position']);

            $fallbackImages = [
                1 => 'https://via.placeholder.com/1200x500/16a34a/ffffff?text=Halalytics+Promo',
                2 => 'https://via.placeholder.com/1200x500/2563eb/ffffff?text=Edukasi+Halal',
                3 => 'https://via.placeholder.com/1200x500/f59e0b/ffffff?text=Tips+Sehat',
            ];

            $banners = $banners->map(function ($banner) use ($fallbackImages) {
                if (empty($banner->image)) {
                    $banner->image = $fallbackImages[$banner->position] ?? $fallbackImages[1];
                }
                return $banner;
            })->values();

            return response()->json([
                'success' => true,
                'data' => $banners,
                'message' => 'Banners retrieved successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve banners: ' . $e->getMessage()
            ], 500);
        }
    }
}
