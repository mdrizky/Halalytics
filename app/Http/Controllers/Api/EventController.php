<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => [
                [
                    'id' => 1,
                    'title' => 'Webinar Gizi Halal',
                    'type' => 'online',
                    'date' => '2026-06-01',
                    'description' => 'Diskusi bersama pakar gizi halal.',
                    'zoom_link' => 'https://zoom.us/j/123456789'
                ],
                [
                    'id' => 2,
                    'title' => 'Expo Halalytics v4',
                    'type' => 'offline',
                    'date' => '2026-07-15',
                    'description' => 'Kunjungi stand kami di JCC Senayan.',
                    'whatsapp_group' => 'https://chat.whatsapp.com/mock'
                ]
            ]
        ]);
    }

    public function register(Request $request, $id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendaftar event',
            'ticket_id' => 'TKT-' . strtoupper(Str::random(6)),
            'qr_code' => 'mock_qr_data',
        ]);
    }

    public function myTickets()
    {
        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }
}

