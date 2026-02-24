<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use App\Models\UserFcmToken;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        $credentialsFile = config('firebase.credentials.file');
        
        // Check if file exists, if not use a fallback or handle error
        if (!file_exists($credentialsFile)) {
            // Log error or notify admin
            \Log::error("Firebase credentials file not found: " . $credentialsFile);
            return;
        }

        $factory = (new Factory)->withServiceAccount($credentialsFile);
        $this->messaging = $factory->createMessaging();
    }

    /**
     * Send notification to specific user
     */
    public function sendToUser($userId, $title, $body, $data = [])
    {
        if (!$this->messaging) return ['success' => false, 'message' => 'Messaging service not initialized'];

        $tokens = UserFcmToken::where('user_id', $userId)->pluck('fcm_token')->toArray();
        
        if (empty($tokens)) {
            return ['success' => false, 'message' => 'No FCM token found for user ID ' . $userId];
        }

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    /**
     * Send notification to multiple tokens
     */
    public function sendToTokens(array $tokens, $title, $body, $data = [])
    {
        if (!$this->messaging) return ['success' => false, 'message' => 'Messaging service not initialized'];

        try {
            $notification = Notification::create($title, $body);
            
            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData($data);

            $report = $this->messaging->sendMulticast($message, $tokens);

            return [
                'success' => true,
                'success_count' => $report->successes()->count(),
                'failure_count' => $report->failures()->count(),
            ];
        } catch (\Exception $e) {
            \Log::error("Firebase send error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Send to all users
     */
    public function sendToAll($title, $body, $data = [])
    {
        $tokens = UserFcmToken::pluck('fcm_token')->toArray();
        if (empty($tokens)) return ['success' => false, 'message' => 'No FCM tokens registered'];
        
        return $this->sendToTokens($tokens, $title, $body, $data);
    }
}
