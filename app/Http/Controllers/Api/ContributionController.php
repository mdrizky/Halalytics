<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ContributionController extends Controller
{
    /**
     * Submit a new product contribution
     */
    public function submit(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string',
            'barcode' => 'nullable|string',
            'complaint' => 'nullable|string',
            'image' => 'nullable|image|max:5120', // Max 5MB
        ]);

        $user = Auth::user();

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('contributions', 'public');
            $imageUrl = asset('storage/' . $path);
        }

        $contribution = Contribution::create([
            'user_id' => $user->id_user,
            'product_name' => $request->product_name,
            'barcode' => $request->barcode,
            'complaint' => $request->complaint,
            'image_url' => $imageUrl,
            'status' => 'pending',
        ]);

        // Create Admin Notification
        try {
            \App\Http\Controllers\Admin\AdminNotificationController::createNotification(
                'product',
                'Kontribusi Produk Baru',
                "User " . $user->username . " menambahkan produk: " . $request->product_name,
                ['contribution_id' => $contribution->id, 'product_name' => $request->product_name]
            );
        } catch (\Exception $e) {
            \Log::error('Gagal membuat notifikasi admin (Contribution): ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Kontribusi Anda telah dikirim dan akan segera diverifikasi oleh tim kami. Terima kasih!',
            'data' => $contribution
        ]);
    }

    /**
     * Get user's own contributions
     */
    public function myContributions()
    {
        $user = Auth::user();
        $contributions = Contribution::where('user_id', $user->id_user)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $contributions
        ]);
    }

    /**
     * (Admin) List all contributions
     */
    public function indexAll()
    {
        $contributions = Contribution::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $contributions
        ]);
    }

    /**
     * (Admin) List all pending contributions
     */
    public function pending()
    {
        $contributions = Contribution::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $contributions
        ]);
    }
}
