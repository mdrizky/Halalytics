<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OCRProduct;
use App\Models\ScanHistory;
use App\Models\User;
use App\Models\ProductModel;
use App\Models\FavoriteProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('admin');
    }

    /**
     * Get dashboard statistics
     */
    /**
     * Get dashboard statistics
     */
    public function getDashboardStats()
    {
        // Count products (ProductModel)
        $totalProducts = ProductModel::where('approval_status', 'approved')->count();
        $pendingProducts = ProductModel::where('approval_status', 'pending')->count();
        
        $stats = [
            'total_products' => $totalProducts,
            'pending_approval' => $pendingProducts,
            'total_users' => User::where('role', 'user')->count(),
            'total_scans_today' => ScanHistory::whereDate('created_at', today())->count(),
            
            // Legacy/OCR stats
            'total_ocr_products' => OCRProduct::count(),
            'pending_review' => OCRProduct::where('status', 'pending_admin_review')->count(),
            
            'recent_scans' => ScanHistory::with(['user'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get Pending Products (ProductModel)
     */
    public function getPendingProducts()
    {
        $products = ProductModel::where('approval_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Approve Product
     */
    public function approveProduct(Request $request, $id)
    {
        $product = ProductModel::where('id_product', $id)->orWhere('id', $id)->first();
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->update([
            'approval_status' => 'approved',
            'approved_by' => Auth::id(), // Use user id
            'approved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product approved successfully',
        ]);
    }

    /**
     * Reject Product
     */
    public function rejectProduct(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string']);
        
        $product = ProductModel::where('id_product', $id)->orWhere('id', $id)->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->update([
            'approval_status' => 'rejected',
            'rejection_reason' => $request->reason,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product rejected',
        ]);
    }

    /**
     * Get all OCR products with filters
     */
    public function getOCRProducts(Request $request)
    {
        $query = OCRProduct::with(['user', 'ingredients', 'verifier']);

        // Filters
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->halal_status) {
            $query->where('halal_status', $request->halal_status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('product_name', 'like', '%' . $request->search . '%')
                  ->orWhere('brand', 'like', '%' . $request->search . '%');
            });
        }

        // Sorting
        $sortBy = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Get OCR product details for admin review
     */
    public function getOCRProductDetail($id)
    {
        $product = OCRProduct::with([
            'user',
            'ingredients',
            'scanHistories',
            'favorites',
            'verifier'
        ])->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * Verify OCR product
     */
    public function verifyOCRProduct(Request $request, $id)
    {
        $request->validate([
            'product_name' => 'required|string',
            'brand' => 'nullable|string',
            'country' => 'nullable|string',
            'halal_status' => 'required|in:halal,haram,syubhat,unknown',
            'status' => 'required|in:verified,rejected',
            'admin_notes' => 'nullable|string',
            'ingredients' => 'nullable|array',
            'ingredients.*.id' => 'required|integer',
            'ingredients.*.status' => 'required|in:halal,haram,syubhat,unknown',
            'ingredients.*.risk_level' => 'required|in:low,medium,high'
        ]);

        $product = OCRProduct::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        // Update product
        $product->update([
            'product_name' => $request->product_name,
            'brand' => $request->brand,
            'country' => $request->country,
            'halal_status' => $request->halal_status,
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'verified_by' => Auth::id(),
            'verified_at' => now()
        ]);

        // Update ingredients if provided
        if ($request->ingredients) {
            foreach ($request->ingredients as $ingredient) {
                $product->ingredients()->updateExistingPivot($ingredient['id'], [
                    'status' => $ingredient['status'],
                    'risk_level' => $ingredient['risk_level']
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $product->load(['ingredients', 'verifier']),
            'message' => 'Product verified successfully'
        ]);
    }

    /**
     * Reject OCR product
     */
    public function rejectOCRProduct(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string'
        ]);

        $product = OCRProduct::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $product->update([
            'status' => 'rejected',
            'admin_notes' => $request->rejection_reason,
            'verified_by' => Auth::id(),
            'verified_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product rejected successfully'
        ]);
    }

    /**
     * Get scan history
     */
    public function getScanHistory(Request $request)
    {
        $query = ScanHistory::with(['user']);

        // Filters
        if ($request->product_type) {
            $query->where('product_type', $request->product_type);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->date_from) {
            $query->whereDate('scanned_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('scanned_at', '<=', $request->date_to);
        }

        $scans = $query->orderBy('scanned_at', 'desc')
            ->paginate($request->per_page ?? 50);

        return response()->json([
            'success' => true,
            'data' => $scans
        ]);
    }

    /**
     * Get users list
     */
    public function getUsers(Request $request)
    {
        $query = User::withCount(['scanHistories', 'favorites']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Get user details
     */
    public function getUserDetail($id)
    {
        $user = User::with([
            'scanHistories' => function ($query) {
                $query->orderBy('scanned_at', 'desc')->take(20);
            },
            'favorites' => function ($query) {
                $query->with(['ocrProduct', 'product'])->orderBy('created_at', 'desc');
            }
        ])->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Get system statistics
     */
    public function getSystemStats()
    {
        $stats = [
            'products_by_status' => OCRProduct::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
            
            'products_by_halal_status' => OCRProduct::selectRaw('halal_status, COUNT(*) as count')
                ->groupBy('halal_status')
                ->pluck('count', 'halal_status'),
            
            'scans_by_month' => ScanHistory::selectRaw('DATE_FORMAT(scanned_at, "%Y-%m") as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->take(12)
                ->pluck('count', 'month'),
            
            'top_scanners' => User::withCount(['scanHistories'])
                ->orderBy('scan_histories_count', 'desc')
                ->take(10)
                ->get(['id', 'name', 'email', 'scan_histories_count']),
            
            'recent_activity' => ScanHistory::with(['user'])
                ->orderBy('scanned_at', 'desc')
                ->take(20)
                ->get()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Export data
     */
    public function exportData(Request $request)
    {
        $request->validate([
            'type' => 'required|in:ocr_products,scan_history,users',
            'format' => 'required|in:csv,xlsx',
            'filters' => 'nullable|array'
        ]);

        // TODO: Implement export functionality
        return response()->json([
            'success' => false,
            'message' => 'Export feature coming soon'
        ]);
    }
}
