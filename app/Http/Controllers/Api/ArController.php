<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArController extends Controller
{
    public function nearbyForAr(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $lat    = $request->lat;
        $lng    = $request->lng;
        $radius = $request->radius ?? 2; // km, default 2km untuk AR

        $merchants = Merchant::select('id', 'name', 'type', 'latitude', 'longitude', 'phone', DB::raw("
            (6371 * acos(cos(radians($lat))
            * cos(radians(latitude))
            * cos(radians(longitude) - radians($lng))
            + sin(radians($lat))
            * sin(radians(latitude)))) AS distance
        "))
        ->having('distance', '<=', $radius)
        ->orderBy('distance')
        ->limit(10)
        ->get();

        return response()->json(['success' => true, 'data' => $merchants]);
    }
}
