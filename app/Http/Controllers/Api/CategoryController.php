<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriModel;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = KategoriModel::with('products')->get();
        
        $mapped = $categories->map(function($cat) {
            $icon = match(strtolower($cat->nama_kategori)) {
                'bumbu dapur' => 'kitchen',
                'jamu tradisional' => 'herb',
                'obat bebas' => 'medication',
                'skincare' => 'spa',
                'makanan ringan' => 'cookie',
                'minuman kemasan' => 'local_drink',
                'produk susu' => 'egg',
                'daging olahan' => 'restaurant',
                'kosmetik wajah' => 'face',
                'perawatan rambut' => 'content_cut',
                'kebutuhan bayi' => 'child_care',
                'bahan roti & kue' => 'bakery_dining',
                'suplemen & vitamin' => 'pill',
                'kopi & teh' => 'coffee',
                'produk vegan' => 'eco',
                default => 'category'
            };
            
            return [
                'id' => $cat->id_kategori,
                'name' => $cat->nama_kategori,
                'icon' => $icon,
                'image_url' => $cat->thumbnail_url,
                'slug' => str_replace(' ', '-', strtolower($cat->nama_kategori))
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $mapped
        ]);
    }
}
