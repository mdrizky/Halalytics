<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UmkmProduct;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class UmkmProductController extends Controller
{
    public function index()
    {
        $products = UmkmProduct::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.umkm.index', compact('products'));
    }

    public function create()
    {
        return view('admin.umkm.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'umkm_name' => 'required|string|max:255',
            'umkm_owner' => 'required|string|max:255',
            'umkm_phone' => 'nullable|string',
            'umkm_address' => 'nullable|string',
            'product_name' => 'required|string|max:255',
            'product_description' => 'nullable|string',
            'product_category' => 'required|string',
            'halal_status' => 'required|in:halal_mui,self_declared,in_process',
            'halal_cert_number' => 'nullable|string',
            'halal_cert_expiry' => 'nullable|date',
            'halal_cert_image' => 'nullable|image|max:2048',
        ]);

        // Handle cert image upload
        if ($request->hasFile('halal_cert_image')) {
            $path = $request->file('halal_cert_image')->store('halal_certs', 'public');
            $validated['halal_cert_image'] = '/storage/' . $path;
        }

        $product = UmkmProduct::create($validated);

        // Generate QR Code as SVG (doesn't require GD)
        $this->generateQRCode($product);

        return redirect()->route('admin.umkm.index')
            ->with('success', 'Produk UMKM berhasil ditambahkan!');
    }

    public function downloadQR($id)
    {
        $product = UmkmProduct::findOrFail($id);
        
        if (!$product->qr_code_image_path) {
            $this->generateQRCode($product);
        }

        $qrPath = storage_path('app/public/' . str_replace('/storage/', '', $product->qr_code_image_path));
        
        return response()->download($qrPath, "QR-{$product->product_name}.svg");
    }

    protected function generateQRCode($product)
    {
        // QR Code contains unique ID
        $qrContent = $product->qr_code_unique_id;
        
        // Generate QR image as SVG
        $qrImage = QrCode::format('svg')
            ->size(500)
            ->errorCorrection('H')
            ->generate($qrContent);

        // Save to storage
        $filename = "qr_codes/umkm_{$product->id}.svg";
        Storage::disk('public')->put($filename, $qrImage);

        // Update product
        $product->update([
            'qr_code_image_path' => '/storage/' . $filename
        ]);
    }

    public function destroy(UmkmProduct $umkm)
    {
        if ($umkm->qr_code_image_path) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $umkm->qr_code_image_path));
        }
        $umkm->delete();
        return back()->with('success', 'Produk UMKM dihapus');
    }
}
