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
            'fcm_token' => 'nullable|string|max:500|required_without:token',
            'token' => 'nullable|string|max:500|required_without:fcm_token',
            'device_type' => 'nullable|in:android,ios',
            'device_id' => 'nullable|string|max:255',
        ]);

        $token = $validated['fcm_token'] ?? $validated['token'];
        $userId = $request->user()->id_user;

        UserFcmToken::where('fcm_token', $token)
            ->where('user_id', '!=', $userId)
            ->delete();

        UserFcmToken::updateOrCreate(
            [
                'user_id' => $userId,
                'fcm_token' => $token,
            ],
            [
                'device_type' => $validated['device_type'] ?? 'android',
                'device_id' => $validated['device_id'] ?? null,
                'last_used_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'FCM token registered successfully',
            'token' => $token,
        ]);
    }

    public function store(Request $request)
    {
        return $this->register($request);
    }

    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'fcm_token' => 'nullable|string|max:500|required_without:token,device_id',
            'token' => 'nullable|string|max:500|required_without:fcm_token,device_id',
            'device_id' => 'nullable|string|max:255|required_without:fcm_token,token',
        ]);

        $query = UserFcmToken::where('user_id', $request->user()->id_user);

        if (!empty($validated['fcm_token']) || !empty($validated['token'])) {
            $query->where('fcm_token', $validated['fcm_token'] ?? $validated['token']);
        } elseif (!empty($validated['device_id'])) {
            $query->where('device_id', $validated['device_id']);
        }

        $deleted = $query->delete();

        return response()->json([
            'success' => true,
            'message' => $deleted > 0 ? 'FCM token deleted successfully' : 'No matching FCM token found',
        ]);
    }
}
