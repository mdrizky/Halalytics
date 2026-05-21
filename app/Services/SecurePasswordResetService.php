<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * 🔐 Secure Password Reset Service
 * Implements enterprise-grade password reset with token expiry and verification
 */
class SecurePasswordResetService
{
    const TOKEN_EXPIRY_MINUTES = 30; // 30 minute expiry
    const MAX_RESET_ATTEMPTS = 5; // Max reset attempts per day
    const RESET_THROTTLE_MINUTES = 60; // Throttle between attempts

    /**
     * Send password reset link
     */
    public static function sendResetLink(string $email): array
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            // Return success anyway to prevent email enumeration
            Log::warning('Password reset requested for non-existent email', ['email' => $email]);
            return [
                'success' => true,
                'message' => 'If an account with that email exists, a reset link has been sent.',
            ];
        }

        // Check rate limiting
        $resetCount = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('created_at', '>', now()->subDay())
            ->count();

        if ($resetCount >= self::MAX_RESET_ATTEMPTS) {
            Log::warning('Password reset limit exceeded', ['email' => $email]);
            return [
                'success' => false,
                'message' => 'Too many reset attempts. Please try again tomorrow.',
                'error_code' => 'RESET_LIMIT_EXCEEDED',
            ];
        }

        // Check throttling
        $lastReset = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->latest('created_at')
            ->first();

        if ($lastReset && $lastReset->created_at->addMinutes(self::RESET_THROTTLE_MINUTES) > now()) {
            $waitMinutes = ceil($lastReset->created_at->diffInSeconds(now()->addMinutes(self::RESET_THROTTLE_MINUTES)) / 60);
            return [
                'success' => false,
                'message' => "Please wait {$waitMinutes} minutes before requesting another reset.",
                'retry_after' => $waitMinutes * 60,
                'error_code' => 'RESET_THROTTLED',
            ];
        }

        // Generate secure token
        $token = self::generateSecureToken();
        $hashedToken = hash('sha256', $token);

        // Store in database
        DB::table('password_reset_tokens')->updateOrCreate(
            ['email' => $email],
            [
                'token' => $hashedToken,
                'attempts' => 0,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'expires_at' => now()->addMinutes(self::TOKEN_EXPIRY_MINUTES),
                'created_at' => now(),
            ]
        );

        // Send email with reset link
        \Mail::to($user->email)->send(new \App\Mail\PasswordResetMail(
            $user,
            $token,
            self::TOKEN_EXPIRY_MINUTES
        ));

        Log::info('Password reset link sent', [
            'user_id' => $user->id,
            'email' => $email,
            'ip' => request()->ip(),
        ]);

        return [
            'success' => true,
            'message' => 'Password reset link has been sent to your email.',
        ];
    }

    /**
     * Validate reset token
     */
    public static function validateResetToken(string $email, string $token): array
    {
        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetToken) {
            return [
                'valid' => false,
                'message' => 'Invalid or expired reset token.',
                'error_code' => 'TOKEN_NOT_FOUND',
            ];
        }

        // Check expiry
        if ($resetToken->expires_at < now()) {
            DB::table('password_reset_tokens')->delete($resetToken->id);
            return [
                'valid' => false,
                'message' => 'Reset token has expired. Please request a new one.',
                'error_code' => 'TOKEN_EXPIRED',
            ];
        }

        // Verify token
        if (!hash_equals($resetToken->token, hash('sha256', $token))) {
            // Increment attempts
            DB::table('password_reset_tokens')
                ->where('id', $resetToken->id)
                ->increment('attempts');

            // Lock after 5 failed attempts
            if ($resetToken->attempts >= 5) {
                DB::table('password_reset_tokens')->delete($resetToken->id);
                return [
                    'valid' => false,
                    'message' => 'Too many failed attempts. Please request a new reset link.',
                    'error_code' => 'MAX_ATTEMPTS_EXCEEDED',
                ];
            }

            Log::warning('Invalid password reset token', [
                'email' => $email,
                'attempts' => $resetToken->attempts + 1,
                'ip' => request()->ip(),
            ]);

            return [
                'valid' => false,
                'message' => 'Invalid reset token.',
                'error_code' => 'INVALID_TOKEN',
            ];
        }

        return [
            'valid' => true,
            'message' => 'Token is valid.',
            'expires_in' => ceil($resetToken->expires_at->diffInSeconds(now()) / 60),
        ];
    }

    /**
     * Reset password with token
     */
    public static function resetPassword(string $email, string $token, string $newPassword): array
    {
        // Validate token first
        $validation = self::validateResetToken($email, $token);
        if (!$validation['valid']) {
            return $validation;
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found.',
                'error_code' => 'USER_NOT_FOUND',
            ];
        }

        // Validate new password
        if (strlen($newPassword) < 8) {
            return [
                'success' => false,
                'message' => 'Password must be at least 8 characters.',
                'error_code' => 'WEAK_PASSWORD',
            ];
        }

        // Check password not same as current
        if (Hash::check($newPassword, $user->password)) {
            return [
                'success' => false,
                'message' => 'New password must be different from current password.',
                'error_code' => 'SAME_PASSWORD',
            ];
        }

        // Reset password
        $user->password = Hash::make($newPassword);
        $user->save();

        // Delete reset token
        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->delete();

        // Revoke all tokens (force re-login)
        $user->tokens()->delete();

        Log::info('Password reset successful', [
            'user_id' => $user->id,
            'email' => $email,
            'ip' => request()->ip(),
        ]);

        return [
            'success' => true,
            'message' => 'Password has been reset successfully. Please login with your new password.',
        ];
    }

    /**
     * Generate cryptographically secure token
     */
    private static function generateSecureToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Clean up expired tokens (run periodically)
     */
    public static function cleanupExpiredTokens(): void
    {
        DB::table('password_reset_tokens')
            ->where('expires_at', '<', now())
            ->delete();

        Log::info('Cleaned up expired password reset tokens');
    }
}
