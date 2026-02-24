<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserFcmToken;
use Illuminate\Http\Request;

class FcmController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'fcm_token' => 'required|string',
            'device_type' => 'required|in:android,ios',
            'device_id' => 'required|string'
        ]);

        UserFcmToken::updateOrCreate(
            [
                'user_id' => $request->user()->id_user,
                'fcm_token' => $validated['fcm_token']
            ],
            [
                'device_type' => $validated['device_type'],
                'device_id' => $validated['device_id'],
                'last_used_at' => now()
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'FCM token registered successfully'
        ]);
    }
}
