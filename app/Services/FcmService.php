<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    /**
     * Send notification to a specific token or topic.
     * 
     * @param string $to Token or /topics/name
     * @param string $title
     * @param string $body
     * @param array $data Additional data payload
     * @return bool
     */
    public static function sendNotification($to, $title, $body, $data = [])
    {
        $serverKey = config('services.fcm.server_key');
        
        if (!$serverKey) {
            Log::warning('FCM Server Key not found in config. Notification not sent.');
            return false;
        }

        $payload = [
            'to' => $to,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
            ],
            'data' => array_merge([
                'title' => $title,
                'body' => $body,
            ], $data),
            'priority' => 'high'
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);

            if ($response->successful()) {
                Log::info("FCM Sent to $to: " . $response->body());
                return true;
            }

            Log::error("FCM Error for $to: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("FCM Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Broadcast to multiple users.
     */
    public static function broadcastToUsers($users, $title, $body, $data = [])
    {
        $count = 0;
        foreach ($users as $user) {
            if ($user->fcm_token) {
                if (self::sendNotification($user->fcm_token, $title, $body, $data)) {
                    $count++;
                }
            }
        }
        return $count;
    }
}
