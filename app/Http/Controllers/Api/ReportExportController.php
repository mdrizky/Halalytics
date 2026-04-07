<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ScanHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportExportController extends Controller
{
    /**
     * Export scan history as CSV
     */
    public function export(Request $request)
    {
        $user = Auth::user();

        $scans = ScanHistory::where('user_id', $user->id_user)
            ->orderBy('created_at', 'desc')
            ->limit(500)
            ->get();

        $csvData = "Tanggal,Nama Produk,Barcode,Status Halal,Confidence\n";

        foreach ($scans as $scan) {
            $csvData .= implode(',', [
                '"' . ($scan->created_at ? $scan->created_at->format('Y-m-d H:i') : '') . '"',
                '"' . str_replace('"', '""', $scan->product_name ?? 'Unknown') . '"',
                '"' . ($scan->barcode ?? '') . '"',
                '"' . ($scan->status ?? $scan->halal_status ?? 'unknown') . '"',
                '"' . ($scan->confidence_score ?? '-') . '"',
            ]) . "\n";
        }

        return response($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="halalytics_scan_report_' . date('Y-m-d') . '.csv"',
        ]);
    }
}
