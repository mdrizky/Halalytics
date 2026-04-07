<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\MarketplaceProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MarketplaceController extends Controller
{
    public function merchants(Request $request)
    {
        $query = Merchant::query();

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->lat && $request->lng) {
            $lat = $request->lat;
            $lng = $request->lng;
            $radius = $request->radius ?? 5; // km

            $query->select('*', DB::raw("
                (6371 * acos(cos(radians($lat))
                * cos(radians(latitude))
                * cos(radians(longitude) - radians($lng))
                + sin(radians($lat))
                * sin(radians(latitude)))) AS distance
            "))
            ->having('distance', '<=', $radius)
            ->orderBy('distance');
        }

        return response()->json(['success' => true, 'data' => $query->paginate(20)]);
    }

    public function merchantDetail($id)
    {
        $merchant = Merchant::with('products')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $merchant]);
    }

    public function products(Request $request)
    {
        $query = MarketplaceProduct::with('merchant:id,name,type');

        if ($request->category) {
            $query->where('category', $request->category);
        }
        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        if ($request->boolean('halal_only')) {
            $query->where('is_halal_certified', true);
        }

        return response()->json(['success' => true, 'data' => $query->paginate(20)]);
    }

    public function nearbyHealthFacilities(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $apiKey = config('services.google.places_key');
        $lat = $request->lat;
        $lng = $request->lng;
        $radius = ($request->radius ?? 3) * 1000; // convert km to meters
        $type = $request->type ?? 'hospital';

        // Jika Google Places API key tersedia
        if ($apiKey) {
            try {
                $response = Http::get('https://maps.googleapis.com/maps/api/place/nearbysearch/json', [
                    'location' => "$lat,$lng",
                    'radius'   => $radius,
                    'type'     => $type,
                    'key'      => $apiKey,
                    'language' => 'id',
                ]);

                if ($response->successful()) {
                    $results = collect($response->json('results'))->map(fn($p) => [
                        'name'      => $p['name'],
                        'address'   => $p['vicinity'] ?? '',
                        'latitude'  => $p['geometry']['location']['lat'],
                        'longitude' => $p['geometry']['location']['lng'],
                        'rating'    => $p['rating'] ?? null,
                        'place_id'  => $p['place_id'],
                        'is_open'   => $p['opening_hours']['open_now'] ?? null,
                        'type'      => $type,
                    ])->take(20);

                    return response()->json(['success' => true, 'data' => $results]);
                }
            } catch (\Exception $e) {
                // Fallback to local DB
            }
        }

        // Fallback: database lokal
        $facilities = Merchant::whereIn('type', ['klinik', 'rs', 'apotek', 'puskesmas'])
            ->select('*', DB::raw("
                (6371 * acos(cos(radians($lat))
                * cos(radians(latitude))
                * cos(radians(longitude) - radians($lng))
                + sin(radians($lat))
                * sin(radians(latitude)))) AS distance
            "))
            ->having('distance', '<=', $request->radius ?? 5)
            ->orderBy('distance')
            ->limit(20)
            ->get();

        return response()->json(['success' => true, 'data' => $facilities]);
    }
}
