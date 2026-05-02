<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodStock;
use Illuminate\Http\Request;

class AdminBloodStockController extends Controller
{
    public function index()
    {
        $stocks = BloodStock::orderBy('expiry_date', 'asc')->paginate(20);
        
        $summary = BloodStock::available()
            ->selectRaw('blood_type, SUM(bags_count) as total_bags')
            ->groupBy('blood_type')
            ->get();

        $events = \App\Models\BloodEvent::where('status', 'active')
            ->where('event_date', '>=', now())
            ->orderBy('event_date', 'asc')
            ->take(5)
            ->get();

        $emergencies = \App\Models\BloodEmergencyRequest::where('is_fulfilled', false)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.blood_donor', compact('stocks', 'summary', 'events', 'emergencies'));
    }
}
