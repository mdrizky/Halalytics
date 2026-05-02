<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BloodStock;
use Illuminate\Http\Request;

class BloodStockController extends Controller
{
    public function summary()
    {
        $stocks = BloodStock::available()
            ->selectRaw('blood_type, SUM(bags_count) as total_bags, SUM(volume_ml) as total_volume')
            ->groupBy('blood_type')
            ->get();

        $formatted = [];
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        
        foreach ($bloodTypes as $type) {
            $stock = $stocks->firstWhere('blood_type', $type);
            $count = $stock ? (int)$stock->total_bags : 0;
            
            // Determine status based on thresholds (e.g. < 5 critical, < 15 warning, else safe)
            $status = 'safe';
            if ($count < 5) $status = 'critical';
            elseif ($count < 15) $status = 'warning';

            $formatted[] = [
                'blood_type' => $type,
                'total_bags' => $count,
                'status' => $status
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $formatted
        ]);
    }
}
