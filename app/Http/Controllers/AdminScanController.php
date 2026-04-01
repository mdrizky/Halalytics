<?php

namespace App\Http\Controllers;

use App\Models\ScanModel;
use App\Models\User;
use App\Models\ProductModel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;

class AdminScanController extends Controller
{
    // List data scan dengan filter dan statistik
    public function index(Request $request)
    {
        // Statistics
        $todayScans = ScanModel::whereDate('tanggal_scan', Carbon::today())->count();
        $yesterdayScans = ScanModel::whereDate('tanggal_scan', Carbon::yesterday())->count();
        $scanTrend = $yesterdayScans > 0 ? round((($todayScans - $yesterdayScans) / $yesterdayScans) * 100, 1) : 0;
        
        $haramFlags = ScanModel::whereIn('status_halal', ['tidak halal', 'haram'])->whereDate('tanggal_scan', Carbon::today())->count();
        
        $activeUsers = ScanModel::where('tanggal_scan', '>=', Carbon::now()->subDays(7))
            ->distinct('user_id')
            ->count('user_id');
        $prevActiveUsers = ScanModel::whereBetween('tanggal_scan', [Carbon::now()->subDays(14), Carbon::now()->subDays(7)])
            ->distinct('user_id')
            ->count('user_id');
        $userTrend = $prevActiveUsers > 0 ? round((($activeUsers - $prevActiveUsers) / $prevActiveUsers) * 100, 1) : 0;
        
        $stats = [
            'today_scans' => $todayScans,
            'scan_trend' => $scanTrend,
            'haram_flags' => $haramFlags,
            'active_users' => $activeUsers,
            'user_trend' => $userTrend
        ];
        
        // Query with filters
        $query = ScanModel::with(['user', 'product']);
        
        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('username', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by status
        if ($request->has('status') && $request->status && $request->status !== 'all') {
            $status = $request->status;
            if ($status === 'halal') {
                $query->where('status_halal', 'halal');
            } elseif ($status === 'syubhat') {
                $query->whereIn('status_halal', ['diragukan', 'syubhat']);
            } elseif ($status === 'haram') {
                $query->whereIn('status_halal', ['tidak halal', 'haram']);
            }
        }
        
        // Date range filter
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('tanggal_scan', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('tanggal_scan', '<=', $request->date_to);
        }
        
        $scans = $query->orderBy('tanggal_scan', 'desc')->paginate(10)->withQueryString();

        return view('admin.scan-new', compact('scans', 'stats'));
    }

    // Create scan form
    public function create()
    {
        $users = User::all();
        $products = ProductModel::all();
        return view('admin.scan_create', compact('users', 'products'));
    }

    // Detail scan
    public function show($id)
    {
        $scan = ScanModel::with(['user','product'])->findOrFail($id);
        return view('admin.scan_show', compact('scan'));
    }


    // Simpan scan
    public function store(Request $request)
    {
        // Handle JSON request from scanner
        if ($request->expectsJson()) {
            $validated = $request->validate([
                'barcode' => 'required|string',
                'user_id' => 'required|integer',
            ]);

            $user = User::query()
                ->where('id_user', $validated['user_id'])
                ->orWhere('id', $validated['user_id'])
                ->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 422);
            }

            // Find product by barcode
            $product = ProductModel::where('barcode', $validated['barcode'])->first();
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk dengan barcode tersebut tidak ditemukan'
                ], 404);
            }

            // Create scan record
            $scan = ScanModel::create([
                'user_id' => $user->id_user,
                'product_id' => $product->id_product,
                'nama_produk' => $product->nama_produk,
                'barcode' => $validated['barcode'],
                'kategori' => optional($product->kategori)->nama_kategori ?? null,
                'status_halal' => strtolower((string) ($product->status ?? 'syubhat')),
                'status_kesehatan' => 'sehat',
                'tanggal_expired' => null,
                'tanggal_scan' => Carbon::now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Scan berhasil disimpan',
                'scan' => $scan
            ]);
        }

        // Handle regular form submission
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
        $data['tanggal_scan'] = Carbon::now();

        ScanModel::create($data);

        return redirect()->route('admin.scan.index')->with('success', 'Data scan berhasil ditambahkan!');
    }

    // Form edit
    public function edit($id)
    {
        $scan = ScanModel::findOrFail($id);
        $users = User::all();
        $products = ProductModel::all();
        return view('admin.scan_edit', compact('scan','users','products'));
    }

    // Update
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

        return redirect()->route('admin.scan.index')->with('success', 'Data scan berhasil diperbarui!');
    }

    // Hapus
    public function destroy($id)
    {
        $scan = ScanModel::findOrFail($id);
        $scan->delete();

        return redirect()->route('admin.scan.index')->with('success', 'Data scan berhasil dihapus!');
    }
    
    // Export CSV
    public function export(Request $request)
    {
        $scans = ScanModel::with(['user', 'product'])->orderBy('tanggal_scan', 'desc')->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="scan_history_' . date('Y-m-d') . '.csv"',
        ];
        
        $callback = function() use ($scans) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['User', 'Product', 'Barcode', 'Status Halal', 'Time']);
            
            foreach ($scans as $scan) {
                fputcsv($file, [
                    $scan->user->username ?? 'Unknown',
                    $scan->nama_produk,
                    $scan->barcode,
                    $scan->status_halal,
                    $scan->tanggal_scan
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    // Export PDF
    public function exportPdf(Request $request)
    {
        $scans = ScanModel::with(['user', 'product'])->orderBy('tanggal_scan', 'desc')->get();
        
        $data = [
            'title' => 'Scan History Report',
            'date' => date('m/d/Y'),
            'scans' => $scans
        ];
          
        $pdf = PDF::loadView('admin.reports.scan_pdf', $data);
    
        return $pdf->download('scan_history_' . date('Y-m-d') . '.pdf');
    }
}
