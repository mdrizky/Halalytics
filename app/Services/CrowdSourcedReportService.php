<?php

namespace App\Services;

use App\Models\ReportModel;
use App\Models\ProductModel;

class CrowdSourcedReportService
{
    /**
     * Check if product should be flagged based on reports
     */
    public function checkProductStatus($productId)
    {
        $reportCount = ReportModel::where('product_id', $productId)
            ->where('status', 'approved')
            ->where('reason', 'like', '%palsu%')
            ->count();

        if ($reportCount >= 5) {
            // Update product status to 'waspada'
            ProductModel::where('id_product', $productId)->update([
                'status' => 'waspada',
                'verification_status' => 'crowd_flagged'
            ]);

            return [
                'flagged' => true,
                'reason' => 'Banyak laporan pemalsuan',
                'report_count' => $reportCount
            ];
        }

        return ['flagged' => false];
    }

    /**
     * Get crowd status for product
     */
    public function getCrowdStatus($productId)
    {
        $reports = ReportModel::where('product_id', $productId)
            ->where('status', 'approved')
            ->get();

        $fakeReports = $reports->filter(function($report) {
            return stripos($report->reason, 'palsu') !== false ||
                   stripos($report->reason, 'fake') !== false;
        });

        return [
            'total_reports' => $reports->count(),
            'fake_reports' => $fakeReports->count(),
            'status' => $fakeReports->count() >= 5 ? 'waspada' : 'normal'
        ];
    }
}