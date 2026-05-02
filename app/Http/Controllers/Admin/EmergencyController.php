<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodEmergencyRequest;
use App\Models\User;
use Illuminate\Http\Request;

class EmergencyController extends Controller
{
    public function index()
    {
        $requests = BloodEmergencyRequest::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.donor.emergency.index', compact('requests'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hospital_name' => 'required|string|max:255',
            'blood_type_needed' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-,A,B,AB,O',
            'bags_needed' => 'required|integer|min:1',
            'urgency_level' => 'required|in:critical,high,medium',
            'contact_person' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->user()->id_user ?? null;

        $emergency = BloodEmergencyRequest::create($validated);

        // Auto trigger FCM
        $this->sendNotification($emergency->id);

        return redirect()->route('admin.blood-emergency.index')->with('success', 'Emergency broadcast sent successfully.');
    }

    public function sendNotification($id)
    {
        $emergency = BloodEmergencyRequest::findOrFail($id);
        
        // Find users matching blood type
        $users = User::whereNotNull('fcm_token')
            ->where('blood_type', $emergency->blood_type_needed)
            ->get();

        $successCount = \App\Services\FcmService::broadcastToUsers(
            $users,
            "URGENT: Blood Needed ({$emergency->blood_type_needed})",
            "A patient at {$emergency->hospital_name} needs {$emergency->bags_needed} bags of your blood type.",
            [
                'type' => 'blood_emergency',
                'navigate_to' => 'donor_home',
                'emergency_id' => (string)$emergency->id
            ]
        );

        return redirect()->back()->with('success', "Notification broadcasted to $successCount matching donors.");
    }
}
