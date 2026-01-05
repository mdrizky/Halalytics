<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportModel;

class AdminReportController extends Controller
{
    function admin_report()
    {
        $reports = ReportModel::with(['user', 'product'])->latest()->get();
        return view('admin.report', compact('reports'));
    }

    // Update status report
    function update_status($id, Request $request)
    {
        $report = ReportModel::findOrFail($id);
        $report->status = $request->status;
        $report->save();

        return redirect()->route('admin_report')->with('success', 'Status report berhasil diperbarui.');
    }

    // Hapus report
    function destroy($id)
    {
        ReportModel::findOrFail($id)->delete();
        return redirect()->route('admin_report')->with('success', 'Report berhasil dihapus.');
    }
}
