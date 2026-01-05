<?php

namespace App\Http\Controllers;

use App\Models\ScanModel;
use App\Models\User;
use App\Models\ProductModel;
use Illuminate\Http\Request;

class AdminScanController extends Controller
{
    // 📌 List data scan
    public function index()
    {
        $scans = ScanModel::with(['user','product'])
            ->orderBy('tanggal_scan', 'desc')
            ->paginate(10);

        return view('admin.scan', compact('scans'));
    }

    // 📌 Detail scan
    public function show($id)
    {
        $scan = ScanModel::with(['user','product'])->findOrFail($id);
        return view('admin.scan_show', compact('scan'));
    }


    // 📌 Simpan scan
    public function store(Request $request)
    {
        $request->validate([
            'user_id'          => 'required|exists:users,id_user',
            'product_id'       => 'required|exists:products,id_product',
            'status_halal'     => 'required|in:halal,tidak halal,diragukan',
            'status_kesehatan' => 'required|in:sehat,tidak sehat',
            'tanggal_expired'  => 'nullable|date',
        ]);

        $data = $request->only([
            'user_id',
            'product_id',
            'nama_produk',
            'barcode',
            'kategori',
            'status_halal',
            'status_kesehatan',
            'tanggal_expired',
        ]);

        $data['status_halal'] = strtolower($data['status_halal']);
        $data['status_kesehatan'] = strtolower($data['status_kesehatan']);
        $data['tanggal_scan'] = \Carbon\Carbon::now();

        ScanModel::create($data);

        return redirect()->route('scan.index')->with('success', 'Data scan berhasil ditambahkan!');
    }

    // 📌 Form edit
    public function edit($id)
    {
        $scan = ScanModel::findOrFail($id);
        $users = User::all();
        $products = ProductModel::all();
        return view('admin.scan_edit', compact('scan','users','products'));
    }

    // 📌 Update
    public function update(Request $request, $id)
    {
        $request->validate([
            'status_halal'     => 'required|in:halal,tidak halal,diragukan',
            'status_kesehatan' => 'required|in:sehat,tidak sehat',
            'tanggal_expired'  => 'nullable|date',
        ]);

        $scan = ScanModel::findOrFail($id);
        $data = $request->only(['status_halal','status_kesehatan','tanggal_expired']);
        $data['status_halal'] = strtolower($data['status_halal']);
        $data['status_kesehatan'] = strtolower($data['status_kesehatan']);
        $scan->update($data);

        return redirect()->route('scan.index')->with('success', 'Data scan berhasil diperbarui!');
    }

    // 📌 Hapus
    public function destroy($id)
    {
        $scan = ScanModel::findOrFail($id);
        $scan->delete();

        return redirect()->route('scan.index')->with('success', 'Data scan berhasil dihapus!');
    }
}
