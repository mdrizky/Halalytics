<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DonorAppointment;
use App\Models\BloodEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DonorAppointmentController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:blood_events,id',
            'age' => 'required|integer|min:17|max:65',
            'weight_kg' => 'required|numeric|min:45',
            'screening_answers' => 'required|array', // expected array of booleans/strings from self-screening
        ], [
            'age.min' => 'Usia minimal untuk donor darah adalah 17 tahun.',
            'age.max' => 'Usia maksimal untuk donor darah adalah 65 tahun.',
            'weight_kg.min' => 'Berat badan minimal untuk donor darah adalah 45 kg.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first(), 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $event = BloodEvent::findOrFail($request->event_id);

        if ($event->is_full) {
            return response()->json(['status' => 'error', 'message' => 'Event quota is full'], 400);
        }

        // Check if user already registered for this event
        if (DonorAppointment::where('user_id', $user->id_user)->where('event_id', $event->id)->exists()) {
            return response()->json(['status' => 'error', 'message' => 'You are already registered for this event'], 400);
        }

        // Check 90 days eligibility
        if ($user->next_eligible_date && Carbon::parse($user->next_eligible_date)->isFuture()) {
            return response()->json([
                'status' => 'error', 
                'message' => 'You are not eligible yet. Next eligible date is ' . Carbon::parse($user->next_eligible_date)->format('d M Y')
            ], 400);
        }

        // Evaluate screening (basic logic: if any critical question is 'yes', they might fail)
        $passed = true;
        $notes = '';
        foreach ($request->screening_answers as $question => $answer) {
            if (in_array($question, ['recent_surgery', 'feel_sick', 'pregnant']) && $answer == true) {
                $passed = false;
                $notes .= "Failed on: $question. ";
            }
        }

        $appointment = DonorAppointment::create([
            'user_id' => $user->id_user,
            'event_id' => $event->id,
            'qr_code' => 'DONOR-' . strtoupper(uniqid()),
            'queue_number' => DonorAppointment::where('event_id', $event->id)->count() + 1,
            'screening_passed' => $passed,
            'screening_notes' => $notes ?: 'All good',
            'weight_kg' => $request->weight_kg,
            'status' => $passed ? 'pending' : 'rejected'
        ]);

        if ($passed) {
            $event->increment('registered_count');
        }

        return response()->json([
            'status' => 'success',
            'data' => $appointment,
            'message' => $passed ? 'Registration successful. Check your QR Code.' : 'Registration failed based on self-screening.'
        ]);
    }

    public function myHistory(Request $request)
    {
        $appointments = DonorAppointment::with('event')
            ->where('user_id', $request->user()->id_user)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['status' => 'success', 'data' => $appointments]);
    }

    public function getQr(Request $request, $id)
    {
        $appointment = DonorAppointment::with('event')->where('user_id', $request->user()->id_user)->findOrFail($id);
        
        return response()->json([
            'status' => 'success',
            'qr_code' => $appointment->qr_code,
            'queue_number' => $appointment->queue_number,
            'event' => $appointment->event
        ]);
    }

    public function cancel(Request $request, $id)
    {
        $appointment = DonorAppointment::where('user_id', $request->user()->id_user)
            ->where('status', 'pending')
            ->findOrFail($id);

        $appointment->event->decrement('registered_count');
        $appointment->delete();

        return response()->json(['status' => 'success', 'message' => 'Appointment cancelled']);
    }

    public function donorCard(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'status' => 'success',
            'data' => [
                'full_name' => $user->full_name,
                'blood_type' => $user->blood_type ?? 'Unknown',
                'total_donations' => $user->total_donor_count,
                'last_donation_date' => $user->last_donor_date,
                'next_eligible_date' => $user->next_eligible_date,
                'badge' => $user->donor_badge,
                'points' => $user->total_donor_points,
            ]
        ]);
    }

    public function voluntaryStatus(Request $request)
    {
        $validated = $request->validate([
            'is_voluntary_donor' => 'required|boolean',
        ]);

        $user = $request->user();
        $user->is_voluntary_donor = $validated['is_voluntary_donor'];
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Status donor sukarela berhasil diperbarui.',
        ]);
    }
}
