<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScanModel;
use App\Models\ReportModel;
use App\Models\ProductModel;
use App\Models\KategoriModel;
use Illuminate\Support\Facades\Auth;

class UserPortalController extends Controller
{
    /**
     * Display user scan history
     */
    public function myScans()
    {
        $scans = ScanModel::where('user_id', Auth::id())
            ->orderByDesc('tanggal_scan')
            ->paginate(10);
            
        return view('user.my_scans', compact('scans'));
    }

    /**
     * Display product catalog for users
     */
    public function products(Request $request)
    {
        $query = ProductModel::with('kategori');
        
        if ($request->has('search')) {
            $query->where('nama_product', 'like', '%' . $request->search . '%')
                  ->orWhere('barcode', 'like', '%' . $request->search . '%');
        }
        
        if ($request->has('category')) {
            $query->where('id_kategori', $request->category);
        }
        
        $products = $query->paginate(12);
        $categories = KategoriModel::all();
        
        return view('user.products', compact('products', 'categories'));
    }

    /**
     * Display user reports
     */
    public function reports()
    {
        $reports = ReportModel::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(10);
            
        return view('user.reports', compact('reports'));
    }

    /**
     * Submit a new report
     */
    public function storeReport(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'laporan' => 'required|string',
            'product_id' => 'nullable|exists:products,id_product'
        ]);
        
        ReportModel::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'product_name' => $request->product_name,
            'laporan' => $request->laporan,
            'status' => 'pending'
        ]);
        
        return redirect()->back()->with('success', 'Laporan Anda telah berhasil dikirim dan akan segera ditinjau oleh tim kami.');
    }

    /**
     * Placeholder for Web Scanner
     */
    public function scanner()
    {
        return view('user.scanner');
    }
}
