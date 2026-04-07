<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HalalProduct;
use App\Services\HalalCertificationService;
use Illuminate\Http\Request;

class HalalProductController extends Controller
{
    protected $halalService;

    public function __construct(HalalCertificationService $halalService)
    {
        $this->halalService = $halalService;
    }

    public function index()
    {
        $products = HalalProduct::latest()->paginate(20);
        return view('admin.halal-products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.halal-products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'product_barcode' => 'nullable|string|max:100',
            'halal_status' => 'required|in:halal,haram,syubhat,non_halal,unknown',
            'halal_certificate_number' => 'nullable|string|max:255',
            'certification_body' => 'nullable|string|max:255',
            'certificate_valid_until' => 'nullable|date',
        ]);

        HalalProduct::create($validated);

        return redirect()->route('halal-products.index')
            ->with('success', 'Produk halal berhasil ditambahkan!');
    }

    public function show($id)
    {
        $product = HalalProduct::findOrFail($id);
        return view('admin.halal-products.show', compact('product'));
    }

    public function edit($id)
    {
        $product = HalalProduct::findOrFail($id);
        return view('admin.halal-products.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'product_barcode' => 'nullable|string|max:100',
            'halal_status' => 'required|in:halal,haram,syubhat,non_halal,unknown',
            'halal_certificate_number' => 'nullable|string|max:255',
            'certification_body' => 'nullable|string|max:255',
            'certificate_valid_until' => 'nullable|date',
        ]);

        $product = HalalProduct::findOrFail($id);
        $product->update($validated);

        return redirect()->route('halal-products.index')
            ->with('success', 'Produk halal berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $product = HalalProduct::findOrFail($id);
        $product->delete();

        return redirect()->route('halal-products.index')
            ->with('success', 'Produk halal berhasil dihapus!');
    }

    public function search(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string',
            'brand' => 'nullable|string'
        ]);

        $result = $this->halalService->checkMUIDatabase(
            $request->product_name,
            $request->brand
        );

        return response()->json($result);
    }

    public function verify(Request $request, $id)
    {
        $request->validate([
            'halal_status' => 'required|in:halal,non_halal,unknown',
            'halal_certificate_number' => 'nullable|string',
            'certification_body' => 'nullable|string',
            'certificate_valid_until' => 'nullable|date'
        ]);

        $product = HalalProduct::findOrFail($id);
        $product->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }
}
