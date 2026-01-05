<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ProductModel;
use App\Models\ScanModel;
use App\Models\ReportModel;
use App\Models\ActivityModel;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | Statistik Utama
        |--------------------------------------------------------------------------
        */
        $totalUsers   = User::count();
        $totalProduk  = ProductModel::count();
        $totalScan    = ScanModel::count();
        $scanToday    = ScanModel::whereDate('tanggal_scan', Carbon::today())->count();
        $laporanMasuk = ReportModel::where('status', 'pending')->count();

        /*
        |--------------------------------------------------------------------------
        | Statistik Scan per Bulan (Jan - Des)
        |--------------------------------------------------------------------------
        */
        $scanPerBulan = ScanModel::selectRaw('MONTH(tanggal_scan) as bulan, COUNT(*) as total')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $scanChartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $scanChartData[] = $scanPerBulan[$i] ?? 0;
        }

        /*
        |--------------------------------------------------------------------------
        | Statistik Produk (Halal, Diragukan, Tidak Halal)
        |--------------------------------------------------------------------------
        */
        $produkHalal     = ProductModel::where('status', 'halal')->count();
        $produkDiragukan = ProductModel::where('status', 'diragukan')->count();
        $produkHaram     = ProductModel::where('status', 'tidak halal')->count();

        /*
        |--------------------------------------------------------------------------
        | Trend Scan 30 Hari Terakhir
        |--------------------------------------------------------------------------
        */
        $scan30Hari = ScanModel::selectRaw('DATE(tanggal_scan) as tgl, COUNT(*) as total')
            ->where('tanggal_scan', '>=', Carbon::today()->subDays(29))
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->pluck('total', 'tgl')
            ->toArray();

        $labels30Hari = [];
        $data30Hari   = [];
        for ($i = 0; $i < 30; $i++) {
            $tanggal = Carbon::today()->subDays(29 - $i)->toDateString();
            $labels30Hari[] = Carbon::parse($tanggal)->format('d M');
            $data30Hari[]   = $scan30Hari[$tanggal] ?? 0;
        }

        /*
        |--------------------------------------------------------------------------
        | Data Tambahan
        |--------------------------------------------------------------------------
        */
        $activities = ActivityModel::with('user')->latest('created_at')->take(10)->get();
        $users      = User::latest('created_at')->take(10)->get();

        /*
        |--------------------------------------------------------------------------
        | Kirim ke view
        |--------------------------------------------------------------------------
        */
        return view('admin.home', compact(
            'totalUsers',
            'totalProduk',
            'totalScan',
            'scanToday',
            'laporanMasuk',
            'scanChartData',
            'produkHalal',
            'produkDiragukan',
            'produkHaram',
            'labels30Hari',
            'data30Hari',
            'activities',
            'users'
        ));
    }
}
