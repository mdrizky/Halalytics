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
                1 => 'https://picsum.photos/seed/halalytics-promo/1200/500',
                2 => 'https://picsum.photos/seed/halalytics-edukasi/1200/500',
                3 => 'https://picsum.photos/seed/halalytics-tips/1200/500',
            ];

            $banners = $banners->map(function ($banner) use ($fallbackImages) {
                if (empty($banner->image)) {
                    $banner->image = $fallbackImages[$banner->position] ?? $fallbackImages[1];
                }

                // Provide action mapping for Android user app.
                // Frontend can route by this pair without hardcoded assumptions.
                if ((int) $banner->position === 1) {
                    $banner->action_type = 'open_bpom';
                    $banner->action_value = 'bpom_scanner';
                } elseif ((int) $banner->position === 2) {
                    $banner->action_type = 'open_health_suite';
                    $banner->action_value = 'health_suite_hub';
                } else {
                    $banner->action_type = 'open_search';
                    $banner->action_value = 'search_external';
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
