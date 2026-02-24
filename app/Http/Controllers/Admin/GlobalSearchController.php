<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ProductModel;
use App\Models\ScanModel;
use App\Models\ReportModel;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    /**
     * Global search across all entities
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Query must be at least 2 characters'
            ]);
        }
        
        $results = [
            'products' => $this->searchProducts($query),
            'users' => $this->searchUsers($query),
            'scans' => $this->searchScans($query),
            'reports' => $this->searchReports($query),
        ];
        
        $totalCount = collect($results)->flatten(1)->count();
        
        return response()->json([
            'success' => true,
            'query' => $query,
            'total_count' => $totalCount,
            'results' => $results
        ]);
    }
    
    private function searchProducts($query)
    {
        return ProductModel::where('nama_product', 'LIKE', "%{$query}%")
            ->orWhere('barcode', 'LIKE', "%{$query}%")
            ->select('id', 'nama_product', 'barcode', 'status', 'image')
            ->limit(5)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'title' => $product->nama_product,
                    'subtitle' => $product->barcode,
                    'status' => $product->status,
                    'image' => $product->image,
                    'type' => 'product',
                    'url' => route('admin.product.edit', $product->id)
                ];
            });
    }
    
    private function searchUsers($query)
    {
        return User::where('username', 'LIKE', "%{$query}%")
            ->orWhere('full_name', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->select('id_user', 'username', 'full_name', 'email', 'role')
            ->limit(5)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id_user,
                    'title' => $user->full_name ?? $user->username,
                    'subtitle' => $user->email,
                    'status' => $user->role,
                    'type' => 'user',
                    'url' => route('admin.user.edit', $user->id_user)
                ];
            });
    }
    
    private function searchScans($query)
    {
        return ScanModel::where('nama_produk', 'LIKE', "%{$query}%")
            ->orWhere('barcode', 'LIKE', "%{$query}%")
            ->select('id', 'nama_produk', 'barcode', 'status_halal', 'tanggal_scan')
            ->orderByDesc('tanggal_scan')
            ->limit(5)
            ->get()
            ->map(function ($scan) {
                return [
                    'id' => $scan->id,
                    'title' => $scan->nama_produk,
                    'subtitle' => $scan->barcode,
                    'status' => $scan->status_halal,
                    'type' => 'scan',
                    'url' => route('admin.scan.show', $scan->id)
                ];
            });
    }
    
    private function searchReports($query)
    {
        return ReportModel::where('product_name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->select('id', 'product_name', 'status', 'created_at')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($report) {
                return [
                    'id' => $report->id,
                    'title' => $report->product_name,
                    'subtitle' => 'Report #' . $report->id,
                    'status' => $report->status,
                    'type' => 'report',
                    'url' => route('admin.report.index')
                ];
            });
    }
}
