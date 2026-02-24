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

    public function verify(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
            'product_name' => 'required|string',
            'brand' => 'required|string'
        ]);

        $result = $this->halalService->verifyAndStore(
            $request->barcode,
            $request->product_name,
            $request->brand
        );

        return response()->json($result);
    }

    public function manualUpdate(Request $request, $id)
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
