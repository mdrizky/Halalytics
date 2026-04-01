<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductRequest;
use App\Models\BpomData;
use App\Models\Notification;
use App\Models\ProductModel;
use App\Models\User;

class AdminRequestController extends Controller
{
    public function index()
    {
        if (ProductRequest::where('status', 'pending')->count() === 0) {
            $this->seedFallbackRequests();
        }

        $requests = ProductRequest::where('status', 'pending')
                    ->with('user')
                    ->latest()
                    ->paginate(10);
        
        return view('admin.requests.index', compact('requests'));
    }

    public function approve($id)
    {
        $request = ProductRequest::findOrFail($id);

        if ($request->status !== 'pending') {
            return redirect()->back()->with('error', 'Request already processed');
        }

        // Create BPOM Data entry
        BpomData::create([
            'nama_produk' => $request->product_name,
            'kategori' => 'umum', // Default, admin can edit later
            'ingredients_text' => $request->ocr_text,
            'image_url' => $request->image_front, // Use front image as main image
            'barcode' => $request->barcode,
            'submitted_by' => $request->user_id,
            'verification_status' => 'verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'sumber_data' => 'user_contribution'
        ]);

        $request->update(['status' => 'approved']);

        Notification::create([
            'user_id' => $request->user_id,
            'title' => 'Permintaan Produk Disetujui',
            'message' => "Produk '{$request->product_name}' telah disetujui dan ditambahkan ke database.",
            'type' => 'request',
            'action_type' => 'open_search',
            'action_value' => $request->product_name,
            'extra_data' => [
                'request_id' => $request->id,
                'status' => 'approved',
            ],
        ]);

        return redirect()->back()->with('success', 'Product approved and added to database!');
    }

    public function reject(Request $request, $id)
    {
        $productRequest = ProductRequest::findOrFail($id);
        $productRequest->update([
            'status' => 'rejected',
            'admin_notes' => $request->input('reason', 'Data tidak valid')
        ]);

        Notification::create([
            'user_id' => $productRequest->user_id,
            'title' => 'Permintaan Produk Ditolak',
            'message' => "Produk '{$productRequest->product_name}' ditolak. Catatan admin: " . ($productRequest->admin_notes ?: 'Data tidak valid'),
            'type' => 'request',
            'action_type' => 'open_search',
            'action_value' => $productRequest->product_name,
            'extra_data' => [
                'request_id' => $productRequest->id,
                'status' => 'rejected',
            ],
        ]);

        return redirect()->back()->with('success', 'Request rejected.');
    }

    private function seedFallbackRequests(): void
    {
        $user = User::where('role', 'user')->first() ?? User::first();
        if (!$user) {
            return;
        }

        $products = ProductModel::query()->latest('id_product')->limit(4)->get();
        $placeholder = 'images/placeholders/product-placeholder.svg';

        foreach ($products as $product) {
            ProductRequest::firstOrCreate(
                [
                    'user_id' => $user->id_user,
                    'barcode' => $product->barcode,
                    'product_name' => $product->nama_product,
                ],
                [
                    'image_front' => $placeholder,
                    'image_back' => $placeholder,
                    'ocr_text' => 'Komposisi: ' . ($product->komposisi ?: 'Air, gula, perisa, bahan tambahan pangan.'),
                    'status' => 'pending',
                    'admin_notes' => null,
                ]
            );
        }
    }
}
