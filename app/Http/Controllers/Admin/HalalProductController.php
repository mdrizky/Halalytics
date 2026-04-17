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
        if (HalalProduct::count() === 0) {
            $this->seedFallbackHalalProducts();
        }

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

    private function seedFallbackHalalProducts(): void
    {
        $items = [
            ['product_name' => 'Indomie Goreng Original', 'brand' => 'Indomie', 'product_barcode' => '089686010384', 'halal_status' => 'halal', 'certification_body' => 'MUI'],
            ['product_name' => 'Pocari Sweat 500ml', 'brand' => 'Pocari', 'product_barcode' => '4987035131411', 'halal_status' => 'halal', 'certification_body' => 'MUI'],
            ['product_name' => 'Teh Botol Sosro', 'brand' => 'Sosro', 'product_barcode' => '8991003000003', 'halal_status' => 'halal', 'certification_body' => 'MUI'],
            ['product_name' => 'Bango Kecap Manis', 'brand' => 'Bango', 'product_barcode' => '8991004000004', 'halal_status' => 'halal', 'certification_body' => 'MUI'],
            ['product_name' => 'Samyang Buldak Hot Chicken', 'brand' => 'Samyang', 'product_barcode' => '8801073311534', 'halal_status' => 'syubhat', 'certification_body' => 'Import'],
        ];

        foreach ($items as $item) {
            HalalProduct::firstOrCreate(
                ['product_barcode' => $item['product_barcode']],
                [
                    'product_name' => $item['product_name'],
                    'brand' => $item['brand'],
                    'halal_status' => $item['halal_status'],
                    'certification_body' => $item['certification_body'],
                    'certificate_valid_until' => now()->addYear(),
                ]
            );
        }
    }
}
