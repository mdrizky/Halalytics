<?php

namespace App\Services;

use App\Models\UserFcmToken;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        $credentialsFile = config('firebase.credentials.file', config('firebase.credentials_path'));

        if (!$credentialsFile || !is_readable($credentialsFile)) {
            Log::warning('Firebase credentials file not found or unreadable.', [
                'path' => $credentialsFile,
            ]);
            return;
        }

        try {
            $factory = (new Factory)->withServiceAccount($credentialsFile);
            $this->messaging = $factory->createMessaging();
        } catch (\Throwable $e) {
            Log::error('Firebase initialization failed: ' . $e->getMessage());
        }
    }

    /**
     * Send notification to specific user
     */
    public function sendToUser($userId, $title, $body, $data = [], $imageUrl = null)
    {
        if (!$this->messaging) {
            return ['success' => false, 'message' => 'Messaging service not initialized'];
        }

        $tokens = UserFcmToken::where('user_id', $userId)
            ->orderByDesc('last_used_at')
            ->pluck('fcm_token')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($tokens)) {
            return ['success' => false, 'message' => 'No FCM token found for user ID ' . $userId];
        }

        return $this->sendToTokens($tokens, $title, $body, $data, $imageUrl);
    }

    /**
     * Send notification to multiple tokens
     */
    public function sendToTokens(array $tokens, $title, $body, $data = [], $imageUrl = null)
    {
        if (!$this->messaging) {
            return ['success' => false, 'message' => 'Messaging service not initialized'];
        }

        $tokens = collect($tokens)
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($tokens)) {
            return ['success' => false, 'message' => 'No FCM tokens supplied'];
        }

        try {
            $successCount = 0;
            $failureCount = 0;
            $invalidTokens = [];

            foreach (array_chunk($tokens, 500) as $tokenChunk) {
                $message = CloudMessage::new()
                    ->withNotification(Notification::create($title, $body, $imageUrl))
                    ->withData($this->stringifyData($data));

                $report = $this->messaging->sendMulticast($message, $tokenChunk);
                $successCount += $report->successes()->count();
                $failureCount += $report->failures()->count();
                $invalidTokens = array_merge(
                    $invalidTokens,
                    $report->unknownTokens(),
                    $report->invalidTokens()
                );
            }

            $invalidTokens = array_values(array_unique($invalidTokens));

            if (!empty($invalidTokens)) {
                UserFcmToken::whereIn('fcm_token', $invalidTokens)->delete();
            }

            return [
                'success' => $successCount > 0,
                'success_count' => $successCount,
                'failure_count' => $failureCount,
                'invalid_tokens' => $invalidTokens,
            ];
        } catch (\Throwable $e) {
            Log::error('Firebase send error: ' . $e->getMessage(), [
                'title' => $title,
                'token_count' => count($tokens),
            ]);

            return [
                'success' => false,
                'success_count' => 0,
                'failure_count' => count($tokens),
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send to all users
     */
    public function sendToAll($title, $body, $data = [], $imageUrl = null)
    {
        $tokens = UserFcmToken::orderByDesc('last_used_at')
            ->pluck('fcm_token')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($tokens)) {
            return ['success' => false, 'message' => 'No FCM tokens registered'];
        }

        return $this->sendToTokens($tokens, $title, $body, $data, $imageUrl);
    }

    public function isConfigured(): bool
    {
        return $this->messaging !== null;
    }

    private function stringifyData(array $data): array
    {
        return collect($data)
            ->filter(function ($value) {
                return !is_null($value);
            })
            ->map(function ($value) {
                if (is_scalar($value)) {
                    return (string) $value;
                }

                return json_encode($value);
            })
            ->all();
    }
}
