<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportModel;
use App\Services\GeminiService;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminReportController extends Controller
{
    protected $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }    function admin_report(Request $request)
    {
        // Statistics
        $totalReports = ReportModel::count();
        $pendingReports = ReportModel::where('status', 'pending')->count();
        
        // Resolved Today (approved or rejected today)
        $resolvedToday = ReportModel::whereIn('status', ['approved', 'rejected'])
            ->whereDate('updated_at', Carbon::today())
            ->count();
            
        // Rejection Rate
        $rejectedCount = ReportModel::where('status', 'rejected')->count();
        $rejectionRate = $totalReports > 0 ? round(($rejectedCount / $totalReports) * 100, 1) : 0;
        
        $stats = [
            'total_reports' => $totalReports,
            'pending_reports' => $pendingReports,
            'resolved_today' => $resolvedToday,
            'rejection_rate' => $rejectionRate
        ];
        
        // Query with filters
        $query = ReportModel::with(['user', 'product']);
        
        // Filter by status
        if ($request->has('status') && $request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter by reason
        if ($request->has('reason') && $request->reason && $request->reason !== 'all') {
            // Mapping mockup reasons to DB or search logic
            if ($request->reason === 'incorrect_status') {
                $query->where(function($q) {
                    $q->where('reason', 'like', '%wrong status%')
                      ->orWhere('laporan', 'like', '%status%');
                });
            } elseif ($request->reason === 'expired_cert') {
                $query->where(function($q) {
                    $q->where('reason', 'like', '%expired%')
                      ->orWhere('laporan', 'like', '%expired%');
                });
            } elseif ($request->reason === 'fake_forgery') {
                $query->where(function($q) {
                    $q->where('reason', 'like', '%palsu%')
                      ->orWhere('reason', 'like', '%fake%')
                      ->orWhere('laporan', 'like', '%palsu%');
                });
            } else {
                $query->where('reason', $request->reason);
            }
        }
        
        $reports = $query->latest()->paginate(10)->withQueryString();
        
        return view('admin.report-new', compact('reports', 'stats'));
    }

    // Update status report
    function update_status($id, Request $request)
    {
        $report = ReportModel::findOrFail($id);
        $report->status = $request->status;
        $report->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status report berhasil diperbarui.'
            ]);
        }

        return redirect()->route('admin.report.index')->with('success', 'Status report berhasil diperbarui.');
    }

    // Hapus report
    function destroy($id)
    {
        ReportModel::findOrFail($id)->delete();
        return redirect()->route('admin.report.index')->with('success', 'Report berhasil dihapus.');
    }
    
    // Export PDF
    public function exportPdf(Request $request)
    {
        $reports = ReportModel::with(['user', 'product'])->latest()->get();
        
        $data = [
            'title' => 'Product Reports Summary',
            'date' => date('m/d/Y'),
            'reports' => $reports
        ];
          
        $pdf = Pdf::loadView('admin.reports.report_pdf', $data);
    
        return $pdf->download('product_reports_' . date('Y-m-d') . '.pdf');
    }
    
    // Batch verification with AI
    public function batchVerify(Request $request)
    {
        $reports = ReportModel::where('status', 'pending')->get();
        $processedCount = 0;
        
        foreach ($reports as $report) {
            // Intelligent analysis using Gemini
            $analysis = $this->gemini->analyzeIngredients($report->laporan);
            
            if ($analysis['status'] === 'halal') {
                $report->status = 'approved';
            } elseif ($analysis['status'] === 'haram') {
                $report->status = 'rejected';
            }
            
            // Store AI analysis in notes if possible (assuming there's a notes field or similar)
            // For now, we just update status
            $report->save();
            $processedCount++;
        }
        
        return response()->json([
            'success' => true,
            'message' => $processedCount . ' reports have been analyzed and processed by the Smart AI Assistant.'
        ]);
    }

    // Resolve Forgery Report
    public function resolveForgery($id, Request $request)
    {
        $report = ReportModel::findOrFail($id);
        $action = $request->action; // 'confirm_fake' or 'dismiss'
        
        if ($action === 'confirm_fake') {
            $report->status = 'approved';
            $report->admin_notes = "Dikonfirmasi sebagai pemalsuan oleh admin pada " . now();
            
            // Update product status
            $product = \App\Models\ProductModel::find($report->product_id);
            if ($product) {
                $product->update([
                    'verification_status' => 'forgery_confirmed',
                    'active' => false // Disable product from general search
                ]);
            }
        } else {
            $report->status = 'rejected';
            $report->admin_notes = "Laporan ditolak oleh admin pada " . now();
        }
        
        $report->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Laporan pemalsuan berhasil diproses.'
            ]);
        }

        return redirect()->route('admin.report.index')->with('success', 'Laporan pemalsuan berhasil diproses.');
    }
}
