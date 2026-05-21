<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 🔍 Activity Audit Log Service
 * Tracks user and system activities for security and compliance
 */
class ActivityAuditService
{
    const ACTIVITY_TYPES = [
        'LOGIN' => 'user_login',
        'LOGIN_FAILED' => 'user_login_failed',
        'LOGOUT' => 'user_logout',
        'REGISTER' => 'user_registered',
        'PASSWORD_CHANGED' => 'password_changed',
        'PASSWORD_RESET' => 'password_reset',
        'EMAIL_VERIFIED' => 'email_verified',
        'PROFILE_UPDATED' => 'profile_updated',
        'ROLE_CHANGED' => 'role_changed',
        'ACCOUNT_DISABLED' => 'account_disabled',
        'ACCOUNT_ENABLED' => 'account_enabled',
        'TOKEN_ISSUED' => 'token_issued',
        'TOKEN_REVOKED' => 'token_revoked',
        'SUSPICIOUS_ACTIVITY' => 'suspicious_activity',
        'ADMIN_ACTION' => 'admin_action',
    ];

    /**
     * Log an activity
     */
    public static function log(
        string $type,
        ?int $userId = null,
        string $description = '',
        array $metadata = [],
        int $severity = 0 // 0=info, 1=warning, 2=critical
    ): void {
        try {
            $request = request();
            
            DB::table('activity_logs')->insert([
                'user_id' => $userId ?? $request->user()?->id,
                'type' => $type,
                'description' => $description,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'method' => $request->method(),
                'path' => $request->path(),
                'metadata' => json_encode($metadata),
                'severity' => $severity,
                'created_at' => now(),
            ]);

            // Log to Laravel log if critical
            if ($severity >= 2) {
                Log::critical("Activity: {$type}", [
                    'user_id' => $userId,
                    'description' => $description,
                    'ip' => $request->ip(),
                    'metadata' => $metadata,
                ]);
            } elseif ($severity >= 1) {
                Log::warning("Activity: {$type}", [
                    'user_id' => $userId,
                    'description' => $description,
                    'metadata' => $metadata,
                ]);
            } else {
                Log::info("Activity: {$type}", [
                    'user_id' => $userId,
                    'metadata' => $metadata,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to log activity', [
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Log successful login
     */
    public static function logLogin(int $userId): void
    {
        self::log(
            self::ACTIVITY_TYPES['LOGIN'],
            $userId,
            'User logged in successfully',
            ['event' => 'successful_login']
        );
    }

    /**
     * Log failed login
     */
    public static function logFailedLogin(string $identifier): void
    {
        self::log(
            self::ACTIVITY_TYPES['LOGIN_FAILED'],
            null,
            "Failed login attempt for: {$identifier}",
            ['identifier' => $identifier],
            1 // Warning
        );
    }

    /**
     * Log logout
     */
    public static function logLogout(int $userId): void
    {
        self::log(
            self::ACTIVITY_TYPES['LOGOUT'],
            $userId,
            'User logged out'
        );
    }

    /**
     * Log user registration
     */
    public static function logRegistration(int $userId, string $email): void
    {
        self::log(
            self::ACTIVITY_TYPES['REGISTER'],
            $userId,
            "New user registered: {$email}",
            ['email' => $email]
        );
    }

    /**
     * Log password change
     */
    public static function logPasswordChange(int $userId): void
    {
        self::log(
            self::ACTIVITY_TYPES['PASSWORD_CHANGED'],
            $userId,
            'User changed password',
            ['event' => 'password_changed'],
            1 // Warning
        );
    }

    /**
     * Log password reset
     */
    public static function logPasswordReset(int $userId): void
    {
        self::log(
            self::ACTIVITY_TYPES['PASSWORD_RESET'],
            $userId,
            'User reset password',
            ['event' => 'password_reset'],
            1 // Warning
        );
    }

    /**
     * Log email verification
     */
    public static function logEmailVerified(int $userId, string $email): void
    {
        self::log(
            self::ACTIVITY_TYPES['EMAIL_VERIFIED'],
            $userId,
            "Email verified: {$email}",
            ['email' => $email]
        );
    }

    /**
     * Log profile update
     */
    public static function logProfileUpdate(int $userId, array $changedFields): void
    {
        self::log(
            self::ACTIVITY_TYPES['PROFILE_UPDATED'],
            $userId,
            'User profile updated',
            ['changed_fields' => $changedFields]
        );
    }

    /**
     * Log role change
     */
    public static function logRoleChange(int $userId, string $oldRole, string $newRole, int $adminId): void
    {
        self::log(
            self::ACTIVITY_TYPES['ROLE_CHANGED'],
            $userId,
            "User role changed from {$oldRole} to {$newRole}",
            [
                'old_role' => $oldRole,
                'new_role' => $newRole,
                'admin_id' => $adminId,
            ],
            1 // Warning
        );
    }

    /**
     * Log account disabled
     */
    public static function logAccountDisabled(int $userId, string $reason = ''): void
    {
        self::log(
            self::ACTIVITY_TYPES['ACCOUNT_DISABLED'],
            $userId,
            "Account disabled: {$reason}",
            ['reason' => $reason],
            1 // Warning
        );
    }

    /**
     * Log account enabled
     */
    public static function logAccountEnabled(int $userId): void
    {
        self::log(
            self::ACTIVITY_TYPES['ACCOUNT_ENABLED'],
            $userId,
            'Account enabled'
        );
    }

    /**
     * Log suspicious activity
     */
    public static function logSuspiciousActivity(
        ?int $userId,
        string $description,
        array $metadata = []
    ): void {
        self::log(
            self::ACTIVITY_TYPES['SUSPICIOUS_ACTIVITY'],
            $userId,
            $description,
            $metadata,
            2 // Critical
        );
    }

    /**
     * Log admin action
     */
    public static function logAdminAction(
        int $adminId,
        string $action,
        array $metadata = []
    ): void {
        self::log(
            self::ACTIVITY_TYPES['ADMIN_ACTION'],
            $adminId,
            "Admin action: {$action}",
            $metadata,
            1 // Warning
        );
    }

    /**
     * Get user activity history
     */
    public static function getUserHistory(int $userId, int $limit = 50, int $daysBack = 90): array
    {
        return DB::table('activity_logs')
            ->where('user_id', $userId)
            ->where('created_at', '>', now()->subDays($daysBack))
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'type' => $activity->type,
                    'description' => $activity->description,
                    'ip_address' => $activity->ip_address,
                    'metadata' => json_decode($activity->metadata, true),
                    'created_at' => $activity->created_at,
                ];
            })
            ->toArray();
    }

    /**
     * Get suspicious activities
     */
    public static function getSuspiciousActivities(int $limit = 100): array
    {
        return DB::table('activity_logs')
            ->where('type', self::ACTIVITY_TYPES['SUSPICIOUS_ACTIVITY'])
            ->where('created_at', '>', now()->subDay())
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get failed logins for IP
     */
    public static function getFailedLoginsForIp(string $ipAddress, int $withinMinutes = 60): int
    {
        return DB::table('activity_logs')
            ->where('type', self::ACTIVITY_TYPES['LOGIN_FAILED'])
            ->where('ip_address', $ipAddress)
            ->where('created_at', '>', now()->subMinutes($withinMinutes))
            ->count();
    }

    /**
     * Clean up old activity logs
     */
    public static function cleanupOldLogs(int $daysToKeep = 90): int
    {
        return DB::table('activity_logs')
            ->where('created_at', '<', now()->subDays($daysToKeep))
            ->delete();
    }
}
