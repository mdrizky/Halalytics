<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BloodEvent;
use Illuminate\Http\Request;

class BloodEventController extends Controller
{
    public function index(Request $request)
    {
        $query = BloodEvent::active()->orderBy('event_date', 'asc');

        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        $events = $query->paginate(10);
        
        $events->getCollection()->transform(function ($event) {
            $event->is_full = $event->is_full;
            $event->quota_remaining = $event->quota_remaining;
            return $event;
        });

        return response()->json([
            'status' => 'success',
            'data' => $events
        ]);
    }

    public function show($id)
    {
        $event = BloodEvent::findOrFail($id);
        
        $event->is_full = $event->is_full;
        $event->quota_remaining = $event->quota_remaining;

        return response()->json([
            'status' => 'success',
            'data' => $event
        ]);
    }
}
