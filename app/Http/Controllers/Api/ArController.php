<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Illuminate\Http\Request;

class ArController extends Controller
{
    public function nearbyForAr(Request $request)
    {
        $validated = $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:10',
        ]);

        $lat = (float) $validated['lat'];
        $lng = (float) $validated['lng'];
        $radius = (float) ($validated['radius'] ?? 2);

        $merchants = Merchant::query()
            ->select('merchants.*')
            ->selectRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                [$lat, $lng, $lat]
            )
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('is_verified', true)
            ->having('distance', '<=', $radius)
            ->orderBy('distance')
            ->limit(10)
            ->get()
            ->map(fn (Merchant $merchant) => [
                'id' => $merchant->id,
                'name' => $merchant->name,
                'type' => $merchant->type,
                'latitude' => (float) $merchant->latitude,
                'longitude' => (float) $merchant->longitude,
                'distance' => round((float) $merchant->distance, 3),
                'rating' => null,
                'phone' => $merchant->phone,
            ]);

        return $this->successResponse($merchants, 'Lokasi AR terdekat berhasil diambil.');
    }
}
