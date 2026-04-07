<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductModel;
use App\Models\HalalProduct;
use Illuminate\Http\Request;

class FoodSearchController extends Controller
{
    /**
     * Search food/products by query string
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);

        $query = $request->q;
        $perPage = $request->get('per_page', 20);

        // Search in products table
        $products = ProductModel::where('nama_product', 'LIKE', "%{$query}%")
            ->orWhere('brand', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id_product,
                    'name' => $p->nama_product ?? $p->name,
                    'brand' => $p->brand,
                    'halal_status' => $p->halal_status ?? 'unknown',
                    'image_url' => $p->image ?? null,
                    'source' => 'products',
                ];
            });

        // Search in halal_products table
        $halalProducts = HalalProduct::where('product_name', 'LIKE', "%{$query}%")
            ->orWhere('brand', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->product_name,
                    'brand' => $p->brand,
                    'halal_status' => $p->halal_status ?? 'unknown',
                    'image_url' => null,
                    'source' => 'halal_products',
                ];
            });

        $results = $products->merge($halalProducts)->take($perPage);

        return response()->json([
            'success' => true,
            'data' => $results->values(),
            'total' => $results->count(),
            'query' => $query,
        ]);
    }
}
