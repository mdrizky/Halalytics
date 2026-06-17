<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use App\Models\ScanModel;
use App\Models\ReportModel;
use App\Models\OCRProduct;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * 📢 Send real-time notification
     */
    public function sendNotification(User $user, string $type, string $title, string $message, array $data = []): Notification
    {
        $notification = Notification::create([
            'user_id' => $user->id_user,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'extra_data' => $data ?: null,
            'is_read' => false,
        ]);

        // 1. Broadcast WebSockets
        try {
            broadcast(new \App\Events\NotificationSent($notification))->toOthers();
        } catch (\Exception $e) {
            Log::error('Failed to broadcast notification', ['error' => $e->getMessage()]);
        }

        // 2. Send Push Notification via FCM
        try {
            $this->firebaseService->sendToUser(
                $user->id_user,
                $title,
                $message,
                array_merge(['type' => $type, 'id' => $notification->id], $data)
            );
        } catch (\Exception $e) {
            Log::error('Failed to send FCM notification', ['error' => $e->getMessage()]);
        }

        // Update user notification count in cache
        $this->updateNotificationCount($user->id_user);

        return $notification;
    }

    /**
     * 💬 Send chat notification
     */
    public function sendChatNotification(User $recipient, string $senderName, string $message, int $consultationId): void
    {
        $this->sendNotification(
            $recipient,
            'new_chat',
            "Pesan baru dari {$senderName}",
            $message,
            [
                'consultation_id' => $consultationId,
                'sender_name' => $senderName,
            ]
        );
    }

    /**
     * 🎯 Send scan notification
     */
    public function sendScanNotification(User $user, ScanModel $scan): void
    {
        $halalStatus = $this->getHalalStatusEmoji($scan->status_halal);
        
        $this->sendNotification(
            $user,
            'scan_completed',
            "Scan Completed {$halalStatus}",
            "Successfully scanned {$scan->nama_produk}",
            [
                'scan_id' => $scan->id_scan,
                'product_name' => $scan->nama_produk,
                'barcode' => $scan->barcode,
                'halal_status' => $scan->status_halal,
            ]
        );
    }

    /**
     * 📝 Send report notification
     */
    public function sendReportNotification(User $user, ReportModel $report): void
    {
        $this->sendNotification(
            $user,
            'report_submitted',
            'Report Submitted 📝',
            "Your report for {$report->product->nama_product} has been submitted",
            [
                'report_id' => $report->id_report,
                'product_id' => $report->product_id,
                'status' => $report->status,
            ]
        );
    }

    /**
     * 📷 Send OCR notification
     */
    public function sendOCRNotification(User $user, OCRProduct $ocrProduct): void
    {
        $statusEmoji = $this->getHalalStatusEmoji($ocrProduct->halal_status);
        
        $this->sendNotification(
            $user,
            'ocr_completed',
            "OCR Analysis Complete {$statusEmoji}",
            "Analysis of {$ocrProduct->product_name} is complete",
            [
                'ocr_id' => $ocrProduct->id,
                'product_name' => $ocrProduct->product_name,
                'halal_status' => $ocrProduct->halal_status,
                'confidence' => $ocrProduct->confidence_level,
            ]
        );
    }

    /**
     * 🏆 Send achievement notification
     */
    public function sendAchievementNotification(User $user, string $achievement, int $points): void
    {
        $this->sendNotification(
            $user,
            'achievement_unlocked',
            'Achievement Unlocked 🏆',
            "You've earned {$points} points: {$achievement}",
            [
                'achievement' => $achievement,
                'points' => $points,
                'total_points' => $user->onboarding_points ?? 0,
            ]
        );
    }

    /**
     * 🎯 Send onboarding notification
     */
    public function sendOnboardingNotification(User $user, string $step, int $points): void
    {
        $this->sendNotification(
            $user,
            'onboarding_progress',
            'Step Completed ✅',
            "Great job! You completed: {$step}",
            [
                'step' => $step,
                'points' => $points,
                'progress_percentage' => $this->calculateOnboardingProgress($user),
            ]
        );
    }

    /**
     * 🔔 Send system notification (admin)
     */
    public function sendSystemNotification(string $title, string $message, array $data = []): void
    {
        $adminUsers = User::where('role', 'admin')->where('active', true)->get();
        
        foreach ($adminUsers as $admin) {
            $this->sendNotification(
                $admin,
                'system',
                $title,
                $message,
                array_merge($data, ['priority' => 'high'])
            );
        }
    }

    /**
     * 📊 Get user notifications with pagination
     */
    public function getUserNotifications(User $user, int $limit = 20): array
    {
        $cacheKey = "user_notifications:{$user->id_user}";
        
        return Cache::remember($cacheKey, 300, function () use ($user, $limit) {
            return Notification::where('user_id', $user->id_user)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'data' => $notification->extra_data ?? [],
                        'read' => (bool) $notification->is_read,
                        'created_at' => $notification->created_at->toISOString(),
                        'time_ago' => $notification->created_at->diffForHumans(),
                    ];
                })
                ->toArray();
        });
    }

    /**
     * 🔢 Get unread notification count
     */
    public function getUnreadCount(User $user): int
    {
        $cacheKey = "unread_count:{$user->id_user}";
        
        return Cache::remember($cacheKey, 300, function () use ($user) {
            return Notification::where('user_id', $user->id_user)
                ->where('is_read', false)
                ->count();
        });
    }

    /**
     * ✅ Mark notification as read
     */
    public function markAsRead(int $notificationId, User $user): bool
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $user->id_user)
            ->first();

        if (!$notification) {
            return false;
        }

        $notification->update(['is_read' => true]);
        
        // Update cached count
        $this->updateNotificationCount($user->id_user);
        
        return true;
    }

    /**
     * ✅ Mark all notifications as read
     */
    public function markAllAsRead(User $user): int
    {
        $count = Notification::where('user_id', $user->id_user)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Update cached count
        $this->updateNotificationCount($user->id_user);
        
        return $count;
    }

    /**
     * 🗑️ Delete notification
     */
    public function deleteNotification(int $notificationId, User $user): bool
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $user->id_user)
            ->first();

        if (!$notification) {
            return false;
        }

        $notification->delete();
        
        // Update cached count
        $this->updateNotificationCount($user->id_user);
        
        return true;
    }

    /**
     * 🔄 Update notification count cache
     */
    private function updateNotificationCount(int $userId): void
    {
        $cacheKey = "unread_count:{$userId}";
        Cache::forget($cacheKey);
        
        // Pre-warm the cache
        $this->getUnreadCount(User::find($userId));
    }

    /**
     * 📊 Calculate onboarding progress
     */
    private function calculateOnboardingProgress(User $user): int
    {
        $onboardingData = $user->onboarding_progress ?? [];
        $totalSteps = 8; // Total onboarding steps
        $completedSteps = count(array_filter($onboardingData, fn($step) => ($step['completed'] ?? false)));
        
        return round(($completedSteps / $totalSteps) * 100);
    }

    /**
     * 😊 Get halal status emoji
     */
    private function getHalalStatusEmoji(string $status): string
    {
        return match ($status) {
            'halal' => '✅',
            'haram' => '❌',
            'diragukan' => '⚠️',
            default => '❓',
        };
    }

    /**
     * 🧹 Clean old notifications
     */
    public function cleanOldNotifications(int $daysOld = 30): int
    {
        $deleted = Notification::where('created_at', '<', now()->subDays($daysOld))
            ->delete();

        // Clear all notification caches
        Cache::flush(); // Simplified for now
        
        return $deleted;
    }

    /**
     * 📈 Get notification statistics
     */
    public function getNotificationStats(): array
    {
        return [
            'total_notifications' => Notification::count(),
            'unread_notifications' => Notification::where('is_read', false)->count(),
            'notifications_today' => Notification::whereDate('created_at', now()->toDate())->count(),
            'notifications_this_week' => Notification::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'by_type' => Notification::select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->orderByDesc('count')
                ->get()
                ->toArray(),
        ];
    }
}
