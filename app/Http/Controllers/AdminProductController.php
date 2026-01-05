<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductModel;
use App\Models\ScanModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class AdminProductController extends Controller
{
    // tampil semua produk
    public function admin_product()
    {
        $products = ProductModel::all();
        return view('admin.product', compact('products'));
    }

    // form tambah produk
    public function create()
    {
        return view('admin.product_tambah');
    }

    // simpan produk baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_product' => 'required|string|max:255',
            'barcode' => 'required|string|unique:products,barcode',
            'komposisi' => 'nullable|string',
            'status' => 'required|in:halal,tidak halal,diragukan',
            'info_gizi' => 'nullable|string',
            'kategori_id' => 'nullable|integer',
        ]);

        ProductModel::create($request->only([
            'nama_product', 'barcode', 'komposisi', 'status', 'info_gizi', 'kategori_id'
        ]));

        return redirect()->route('admin_product')->with('success', 'Produk berhasil ditambahkan!');
    }

    // form edit produk
    public function edit($id)
    {
        $product = ProductModel::findOrFail($id);
        return view('admin.product_edit', compact('product'));
    }

    // update produk
    public function update(Request $request, $id)
    {
        $product = ProductModel::findOrFail($id);

        $request->validate([
            'nama_product' => 'required|string|max:255',
            'barcode' => 'required|string|unique:products,barcode,'.$id.',id_product',
            'komposisi' => 'nullable|string',
            'status' => 'required|in:halal,tidak halal,diragukan',
            'info_gizi' => 'nullable|string',
            'kategori_id' => 'nullable|integer',
        ]);

        $product->update($request->only([
            'nama_product', 'barcode', 'komposisi', 'status', 'info_gizi', 'kategori_id'
        ]));

        return redirect()->route('admin_product')->with('success', 'Produk berhasil diupdate!');
    }

    // hapus produk
    public function destroy($id)
    {
        $product = ProductModel::findOrFail($id);
        $product->delete();

        return redirect()->route('admin_product')->with('success', 'Produk berhasil dihapus!');
    }

    // 🔎 Cari produk by barcode (lokal + internasional)
    public function searchByBarcode($barcode)
    {
        // Cek dulu di database lokal
        $product = ProductModel::where('barcode', $barcode)->first();

        if ($product) {
            // Simpan scan otomatis
            ScanModel::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id_product,
                'nama_produk' => $product->nama_product,
                'barcode' => $product->barcode,
                'kategori' => $product->kategori_id ? $product->kategori_id : 'Tidak Ada',
                'status_halal' => $product->status,
                'status_kesehatan' => 'diragukan',
                'tanggal_scan' => now(),
            ]);

            return response()->json([
                'status' => 'local',
                'data' => $product
            ]);
        }

        // Kalau tidak ada → ambil dari API OpenFoodFacts
        $url = "https://world.openfoodfacts.org/api/v0/product/{$barcode}.json";
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['status']) && $data['status'] == 1) {
                $productData = $data['product'];

                $newProduct = ProductModel::create([
                    'nama_product' => $productData['product_name'] ?? 'Produk Tidak Dikenal',
                    'barcode'      => $barcode,
                    'komposisi'    => $productData['ingredients_text'] ?? null,
                    'status'       => 'diragukan',
                    'info_gizi'    => isset($productData['nutriments']) ? json_encode($productData['nutriments']) : null,
                    'kategori_id'  => null,
                ]);

                // Simpan scan internasional
                ScanModel::create([
                    'user_id' => Auth::id(),
                    'product_id' => $newProduct->id_product,
                    'nama_produk' => $newProduct->nama_product,
                    'barcode' => $newProduct->barcode,
                    'kategori' => 'Internasional',
                    'status_halal' => 'diragukan',
                    'status_kesehatan' => 'diragukan',
                    'tanggal_scan' => now(),
                ]);

                return response()->json([
                    'status' => 'international',
                    'data' => $newProduct
                ]);
            }
        }

        return response()->json([
            'status' => 'not_found',
            'message' => 'Produk tidak ditemukan di database lokal maupun internasional'
        ]);
    }
}
