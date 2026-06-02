<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HealthEvent;
use App\Models\EventTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index()
    {
        $events = HealthEvent::orderBy('event_date', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $events->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'type' => $event->type,
                    'date' => $event->event_date?->toDateString(),
                    'description' => $event->description,
                    'image' => $event->image,
                    'location' => $event->location,
                    'max_participants' => $event->max_participants,
                    'zoom_link' => $event->zoom_link,
                    'whatsapp_group' => $event->whatsapp_group,
                    'created_at' => $event->created_at,
                ];
            }),
        ]);
    }

    public function register(Request $request, $id)
    {
        $event = HealthEvent::findOrFail($id);
        $user = Auth::user();

        $existing = EventTicket::where('health_event_id', $id)
            ->where('user_id', $user->id_user)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah terdaftar di event ini',
            ], 409);
        }

        $ticketCode = 'TKT-' . strtoupper(Str::random(8));
        $qrData = 'halalytics:ticket:' . $ticketCode;

        $ticket = EventTicket::create([
            'health_event_id' => $id,
            'user_id' => $user->id_user,
            'ticket_code' => $ticketCode,
            'qr_data' => $qrData,
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendaftar event',
            'ticket_id' => $ticketCode,
            'qr_code' => $qrData,
        ], 201);
    }

    public function myTickets()
    {
        $user = Auth::user();
        $tickets = EventTicket::with('event')
            ->where('user_id', $user->id_user)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tickets->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'event_id' => $ticket->health_event_id,
                    'event_title' => $ticket->event?->title,
                    'event_date' => $ticket->event?->event_date?->toDateString(),
                    'ticket_code' => $ticket->ticket_code,
                    'qr_data' => $ticket->qr_data,
                    'status' => $ticket->status,
                    'created_at' => $ticket->created_at,
                ];
            }),
        ]);
    }
}
