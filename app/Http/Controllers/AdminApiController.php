<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductModel;
use App\Models\KategoriModel;
use App\Models\ReportModel;

class AdminApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    private function ensureAdmin()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            abort(response()->json([
                'response_code' => 403,
                'message' => 'Akses ditolak: khusus admin'
            ], 403));
        }
    }

    // PRODUCTS (ADMIN)
    public function indexProducts()
    {
        $this->ensureAdmin();
        $products = ProductModel::orderBy('id_product', 'desc')->get();
        return response()->json([
            'response_code' => 200,
            'message' => 'Daftar produk (admin)',
            'content' => $products
        ]);
    }

    public function storeProduct(Request $request)
    {
        $this->ensureAdmin();
        $request->validate([
            'nama_product' => 'required|string|max:255',
            'barcode' => 'required|string|unique:products,barcode',
            'komposisi' => 'nullable|string',
            'status' => 'required|string|in:halal,haram,syubhat',
            'info_gizi' => 'nullable|string',
            'kategori_id' => 'nullable|exists:kategori,id_kategori',
        ]);

        $product = ProductModel::create($request->all());
        return response()->json([
            'response_code' => 201,
            'message' => 'Produk dibuat',
            'content' => $product
        ], 201);
    }

    public function updateProduct(Request $request, $id)
    {
        $this->ensureAdmin();
        $product = ProductModel::find($id);
        if (!$product) {
            return response()->json(['response_code' => 404, 'message' => 'Produk tidak ditemukan'], 404);
        }

        $request->validate([
            'nama_product' => 'sometimes|string|max:255',
            'barcode' => 'sometimes|string|unique:products,barcode,' . $id . ',id_product',
            'komposisi' => 'nullable|string',
            'status' => 'sometimes|string|in:halal,haram,syubhat',
            'info_gizi' => 'nullable|string',
            'kategori_id' => 'nullable|exists:kategori,id_kategori',
        ]);

        $product->update($request->all());
        return response()->json([
            'response_code' => 200,
            'message' => 'Produk diperbarui',
            'content' => $product
        ]);
    }

    public function destroyProduct($id)
    {
        $this->ensureAdmin();
        $product = ProductModel::find($id);
        if (!$product) {
            return response()->json(['response_code' => 404, 'message' => 'Produk tidak ditemukan'], 404);
        }
        $product->delete();
        return response()->json([
            'response_code' => 200,
            'message' => 'Produk dihapus'
        ]);
    }

    // KATEGORI (ADMIN)
    public function indexKategori()
    {
        $this->ensureAdmin();
        $kategori = KategoriModel::orderBy('id_kategori', 'desc')->get();
        return response()->json([
            'response_code' => 200,
            'message' => 'Daftar kategori (admin)',
            'content' => $kategori
        ]);
    }

    public function storeKategori(Request $request)
    {
        $this->ensureAdmin();
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);
        $kategori = KategoriModel::create($request->all());
        return response()->json([
            'response_code' => 201,
            'message' => 'Kategori dibuat',
            'content' => $kategori
        ], 201);
    }

    public function updateKategori(Request $request, $id)
    {
        $this->ensureAdmin();
        $kategori = KategoriModel::find($id);
        if (!$kategori) {
            return response()->json(['response_code' => 404, 'message' => 'Kategori tidak ditemukan'], 404);
        }
        $request->validate([
            'nama_kategori' => 'sometimes|string|max:255',
        ]);
        $kategori->update($request->all());
        return response()->json([
            'response_code' => 200,
            'message' => 'Kategori diperbarui',
            'content' => $kategori
        ]);
    }

    public function destroyKategori($id)
    {
        $this->ensureAdmin();
        $kategori = KategoriModel::find($id);
        if (!$kategori) {
            return response()->json(['response_code' => 404, 'message' => 'Kategori tidak ditemukan'], 404);
        }
        $kategori->delete();
        return response()->json([
            'response_code' => 200,
            'message' => 'Kategori dihapus'
        ]);
    }

    // REPORTS REVIEW (ADMIN)
    public function indexReports()
    {
        $this->ensureAdmin();
        $reports = ReportModel::with('product')->orderBy('created_at', 'desc')->get();
        return response()->json([
            'response_code' => 200,
            'message' => 'Daftar laporan (admin)',
            'content' => $reports
        ]);
    }

    public function updateReportStatus(Request $request, $id)
    {
        $this->ensureAdmin();
        $request->validate([
            'status' => 'required|in:pending,accepted,rejected,reviewed',
        ]);
        $report = ReportModel::find($id);
        if (!$report) {
            return response()->json(['response_code' => 404, 'message' => 'Laporan tidak ditemukan'], 404);
        }
        $report->status = $request->status;
        $report->save();
        return response()->json([
            'response_code' => 200,
            'message' => 'Status laporan diperbarui',
            'content' => $report
        ]);
    }
}


