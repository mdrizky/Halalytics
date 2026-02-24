<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BpomData;
use Illuminate\Http\Request;

class BpomAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = BpomData::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_produk', 'LIKE', "%{$search}%")
                  ->orWhere('nomor_reg', 'LIKE', "%{$search}%")
                  ->orWhere('merk', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status')) {
            $query->where('status_keamanan', $request->status);
        }

        $bpom_data = $query->latest()->paginate(20)->withQueryString();
        
        $stats = [
            'total' => BpomData::count(),
            'verified' => BpomData::where('verification_status', 'verified')->count(),
            'ai_generated' => BpomData::where('sumber_data', 'ai')->count(),
            'dangerous' => BpomData::where('status_keamanan', 'bahaya')->count(),
        ];

        return view('admin.bpom.index', compact('bpom_data', 'stats'));
    }

    public function show($id)
    {
        $product = BpomData::findOrFail($id);
        return view('admin.bpom.show', compact('product'));
    }

    public function verify($id)
    {
        $product = BpomData::findOrFail($id);
        $product->update([
            'verification_status' => 'verified',
            'verified_at' => now()
        ]);

        return back()->with('success', 'Produk berhasil diverifikasi.');
    }

    public function destroy($id)
    {
        $product = BpomData::findOrFail($id);
        $product->delete();

        return back()->with('success', 'Data BPOM berhasil dihapus.');
    }
}
