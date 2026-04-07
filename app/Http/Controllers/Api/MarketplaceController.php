<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceProduct;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class MarketplaceController extends Controller
{
    public function nearbyMerchants(Request $request)
    {
        $validated = $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:50',
            'type' => 'nullable|string|max:100',
        ]);

        $lat = (float) $validated['lat'];
        $lng = (float) $validated['lng'];
        $radius = (float) ($validated['radius'] ?? 10);
        $type = $validated['type'] ?? null;

        $merchants = Merchant::query()
            ->select('merchants.*')
            ->selectRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                [$lat, $lng, $lat]
            )
            ->where('is_verified', true)
            ->when($type, fn ($query) => $query->where('type', $type))
            ->having('distance', '<=', $radius)
            ->orderBy('distance')
            ->get()
            ->map(fn (Merchant $merchant) => $this->merchantPayload($merchant));

        return $this->successResponse($merchants, 'Merchant terdekat berhasil diambil.');
    }

    public function merchants(Request $request)
    {
        if ($request->filled('lat') && $request->filled('lng')) {
            return $this->nearbyMerchants($request);
        }

        $merchants = Merchant::query()
            ->where('is_verified', true)
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->input('type')))
            ->orderBy('name')
            ->get()
            ->map(fn (Merchant $merchant) => $this->merchantPayload($merchant));

        return $this->successResponse($merchants, 'Daftar merchant berhasil diambil.');
    }

    public function merchantDetail($id)
    {
        $merchant = Merchant::with('products')->findOrFail($id);

        return $this->successResponse([
            'merchant' => $this->merchantPayload($merchant),
            'products' => $merchant->products->map(fn (MarketplaceProduct $product) => $this->productPayload($product))->values(),
        ], 'Detail merchant berhasil diambil.');
    }

    public function nearbyHealthFacilities(Request $request)
    {
        $validated = $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:50',
        ]);

        $lat = (float) $validated['lat'];
        $lng = (float) $validated['lng'];
        $radius = (float) ($validated['radius'] ?? 5);
        $apiKey = config('services.google.places_key');

        if (! empty($apiKey)) {
            try {
                $response = Http::get('https://maps.googleapis.com/maps/api/place/nearbysearch/json', [
                    'location' => $lat . ',' . $lng,
                    'radius' => (int) ($radius * 1000),
                    'type' => 'hospital',
                    'keyword' => 'hospital|pharmacy|doctor',
                    'language' => 'id',
                    'key' => $apiKey,
                ]);

                if ($response->successful()) {
                    $results = collect($response->json('results', []))
                        ->map(fn (array $place) => [
                            'place_id' => $place['place_id'] ?? null,
                            'name' => $place['name'] ?? 'Fasilitas Kesehatan',
                            'address' => $place['vicinity'] ?? ($place['formatted_address'] ?? null),
                            'latitude' => data_get($place, 'geometry.location.lat'),
                            'longitude' => data_get($place, 'geometry.location.lng'),
                            'types' => $place['types'] ?? [],
                            'rating' => isset($place['rating']) ? (float) $place['rating'] : null,
                            'is_open' => data_get($place, 'opening_hours.open_now'),
                            'phone_number' => null,
                        ])
                        ->values();

                    return $this->successResponse($results, 'Fasilitas kesehatan berhasil diambil dari Google Places.');
                }
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        $facilities = Merchant::query()
            ->select('merchants.*')
            ->selectRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                [$lat, $lng, $lat]
            )
            ->whereIn('type', ['klinik', 'apotek', 'rs', 'puskesmas'])
            ->having('distance', '<=', $radius)
            ->orderBy('distance')
            ->get()
            ->map(fn (Merchant $merchant) => [
                'place_id' => $merchant->google_place_id,
                'name' => $merchant->name,
                'address' => $merchant->address,
                'latitude' => $merchant->latitude,
                'longitude' => $merchant->longitude,
                'types' => [$merchant->type],
                'rating' => null,
                'is_open' => null,
                'phone_number' => $merchant->phone,
            ]);

        return $this->successResponse($facilities, 'Fasilitas kesehatan lokal berhasil diambil.');
    }

    public function products(Request $request)
    {
        $products = MarketplaceProduct::query()
            ->with('merchant:id,name,type,address,is_verified')
            ->when($request->filled('merchant_id'), fn ($query) => $query->where('merchant_id', $request->integer('merchant_id')))
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->input('category')))
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%' . $request->input('search') . '%'))
            ->orderBy('name')
            ->get()
            ->map(fn (MarketplaceProduct $product) => $this->productPayload($product));

        return $this->successResponse($products, 'Daftar produk marketplace berhasil diambil.');
    }

    public function productDetail($id)
    {
        $product = MarketplaceProduct::with('merchant')->findOrFail($id);

        return $this->successResponse($this->productPayload($product), 'Detail produk berhasil diambil.');
    }

    private function merchantPayload(Merchant $merchant): array
    {
        return [
            'id' => $merchant->id,
            'name' => $merchant->name,
            'type' => $merchant->type,
            'address' => $merchant->address,
            'latitude' => $merchant->latitude,
            'longitude' => $merchant->longitude,
            'phone' => $merchant->phone,
            'website' => $merchant->website,
            'affiliate_link' => $merchant->affiliate_link,
            'is_verified' => (bool) $merchant->is_verified,
            'distance' => isset($merchant->distance) ? round((float) $merchant->distance, 2) : null,
            'opening_hours' => $merchant->opening_hours,
        ];
    }

    private function productPayload(MarketplaceProduct $product): array
    {
        return [
            'id' => $product->id,
            'merchant_id' => $product->merchant_id,
            'merchant_name' => $product->merchant?->name,
            'name' => $product->name,
            'description' => $product->description,
            'price' => (int) $product->price,
            'image_url' => $product->image_path ? Storage::disk('public')->url($product->image_path) : null,
            'category' => $product->category,
            'is_halal_certified' => (bool) $product->is_halal_certified,
            'halal_cert_number' => $product->halal_cert_number,
            'stock' => (int) $product->stock,
        ];
    }
}
