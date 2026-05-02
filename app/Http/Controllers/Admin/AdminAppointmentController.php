<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonorAppointment;
use App\Models\BloodEvent;
use Illuminate\Http\Request;

class AdminAppointmentController extends Controller
{
    public function index()
    {
        $events = BloodEvent::withCount(['appointments as pending_count' => function ($query) {
            $query->where('status', 'pending');
        }])->orderBy('event_date', 'desc')->get();

        return view('admin.donor.participants.index', compact('events'));
    }

    public function scanner()
    {
        return view('admin.donor.participants.scanner');
    }

    public function scanQr(Request $request)
    {
        $request->validate(['qr_code' => 'required|string']);

        $appointment = DonorAppointment::with(['user', 'event'])
            ->where('qr_code', $request->qr_code)
            ->first();

        if (!$appointment) {
            return response()->json(['success' => false, 'message' => 'QR Code tidak valid atau tidak ditemukan.']);
        }

        return response()->json([
            'success' => true,
            'data' => $appointment
        ]);
    }

    public function verify(Request $request, $id)
    {
        $appointment = DonorAppointment::findOrFail($id);

        $request->validate([
            'status' => 'required|in:approved,rejected,no_show',
            'weight_kg' => 'nullable|numeric',
            'hemoglobin' => 'nullable|numeric',
            'blood_pressure' => 'nullable|string',
            'admin_notes' => 'nullable|string',
            'blood_volume_ml' => 'nullable|integer|required_if:status,approved'
        ]);

        $appointment->status = $request->status;
        $appointment->weight_kg = $request->weight_kg;
        $appointment->hemoglobin = $request->hemoglobin;
        $appointment->blood_pressure = $request->blood_pressure;
        $appointment->admin_notes = $request->admin_notes;
        
        if ($request->status == 'approved') {
            $appointment->approved_at = now();
            
            // Add points to user
            $user = $appointment->user;
            $user->increment('total_donor_count');
            $user->increment('total_donor_points', 50); // Give 50 points per donation
            $user->last_donor_date = now();
            $user->next_eligible_date = now()->addDays(90);
            $user->save();

            // Create Blood Stock
            \App\Models\BloodStock::create([
                'blood_type' => $user->blood_type ?? 'O+', // default if not set
                'volume_ml' => $request->blood_volume_ml ?? 350,
                'bags_count' => 1,
                'source_appointment_id' => $appointment->id,
                'event_id' => $appointment->event_id,
                'collected_date' => now(),
                'expiry_date' => now()->addDays(35),
                'location' => 'PMI / Event: ' . $appointment->event->title,
                'status' => 'available'
            ]);

            // Add Reward
            \App\Models\DonorReward::create([
                'user_id' => $user->id_user,
                'appointment_id' => $appointment->id,
                'points_earned' => 50,
                'badge_awarded' => $user->donor_badge,
            ]);
        }

        $appointment->save();

        return response()->json(['success' => true, 'message' => 'Status berhasil diupdate']);
    }
}
