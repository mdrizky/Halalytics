<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductRequest;
use App\Models\BpomData;
use Illuminate\Support\Facades\Storage;

class AdminRequestController extends Controller
{
    public function index()
    {
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

        // TODO: Send Notification to User

        return redirect()->back()->with('success', 'Product approved and added to database!');
    }

    public function reject(Request $request, $id)
    {
        $productRequest = ProductRequest::findOrFail($id);
        $productRequest->update([
            'status' => 'rejected',
            'admin_notes' => $request->input('reason', 'Data tidak valid')
        ]);

        return redirect()->back()->with('success', 'Request rejected.');
    }
}
