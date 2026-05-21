<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 🔐 Enterprise Token Management Service
 * Handles JWT token refresh, rotation, and expiry management
 */
class TokenRefreshService
{
    const TOKEN_EXPIRY_MINUTES = 60; // 1 hour
    const REFRESH_TOKEN_EXPIRY_DAYS = 30; // 30 days
    const REFRESH_BEFORE_MINUTES = 5; // Refresh 5 minutes before expiry

    /**
     * Issue a new auth token with proper expiry
     */
    public static function issueAuthToken(User $user): string
    {
        if (self::tokenTableSupportsExpiry()) {
            $token = $user->createToken('auth_token', ['*'], now()->addMinutes(self::TOKEN_EXPIRY_MINUTES));
            return $token->plainTextToken;
        }

        return $user->createToken('auth_token')->plainTextToken;
    }

    /**
     * Issue a refresh token that never expires but can be rotated
     */
    public static function issueRefreshToken(User $user): string
    {
        // Store in database with rotation tracking
        $refreshToken = DB::table('refresh_tokens')->create([
            'user_id' => $user->id,
            'token' => hash('sha256', $newToken = bin2hex(random_bytes(40))),
            'rotated_count' => 0,
            'last_rotated_at' => now(),
            'expires_at' => now()->addDays(self::REFRESH_TOKEN_EXPIRY_DAYS),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return $newToken;
    }

    /**
     * Check if token needs refresh
     */
    public static function shouldRefreshToken(User $user): bool
    {
        $token = $user->currentAccessToken();
        
        if (!$token || !self::tokenTableSupportsExpiry()) {
            return false;
        }

        if (!$token->expires_at) {
            return false;
        }

        return now()->diffInMinutes($token->expires_at) <= self::REFRESH_BEFORE_MINUTES;
    }

    /**
     * Refresh the auth token and optionally rotate it
     */
    public static function refreshAuthToken(User $user): string
    {
        // Revoke old token
        $oldToken = $user->currentAccessToken();
        if ($oldToken) {
            $oldToken->delete();
        }

        // Issue new token
        return self::issueAuthToken($user);
    }

    /**
     * Rotate refresh token (security measure)
     */
    public static function rotateRefreshToken(User $user, string $currentRefreshToken): ?string
    {
        $hashedToken = hash('sha256', $currentRefreshToken);

        $refreshTokenRecord = DB::table('refresh_tokens')
            ->where('user_id', $user->id)
            ->where('token', $hashedToken)
            ->first();

        if (!$refreshTokenRecord || $refreshTokenRecord->expires_at < now()) {
            Log::warning('Invalid or expired refresh token', [
                'user_id' => $user->id,
                'ip' => request()->ip(),
            ]);
            return null;
        }

        // Check rotation limit
        if ($refreshTokenRecord->rotated_count >= 10) {
            Log::warning('Refresh token rotation limit exceeded', [
                'user_id' => $user->id,
            ]);
            return null;
        }

        // Delete old token
        DB::table('refresh_tokens')->delete($refreshTokenRecord->id);

        // Issue new refresh token
        $newToken = self::issueRefreshToken($user);

        // Update rotation tracking
        DB::table('refresh_tokens')
            ->where('user_id', $user->id)
            ->increment('rotated_count');

        return $newToken;
    }

    /**
     * Revoke all tokens for a user (logout from all devices)
     */
    public static function revokeAllTokens(User $user): void
    {
        $user->tokens()->delete();
        DB::table('refresh_tokens')
            ->where('user_id', $user->id)
            ->delete();

        Log::info('All tokens revoked', ['user_id' => $user->id]);
    }

    /**
     * Revoke token from specific device
     */
    public static function revokeTokenByDevice(User $user, string $userAgent): void
    {
        // Revoke access token (find by user agent comparison)
        $tokens = $user->tokens()->get();
        foreach ($tokens as $token) {
            if ($token->last_used_at && strpos($userAgent, request()->userAgent()) !== false) {
                $token->delete();
            }
        }

        // Revoke refresh token
        DB::table('refresh_tokens')
            ->where('user_id', $user->id)
            ->where('user_agent', $userAgent)
            ->delete();
    }

    /**
     * Check table supports expiry column
     */
    private static function tokenTableSupportsExpiry(): bool
    {
        static $supports = null;

        if ($supports === null) {
            $supports = \Illuminate\Support\Facades\Schema::hasTable('personal_access_tokens')
                && \Illuminate\Support\Facades\Schema::hasColumn('personal_access_tokens', 'expires_at');
        }

        return $supports;
    }

    /**
     * Clean up expired tokens
     */
    public static function cleanupExpiredTokens(): void
    {
        DB::table('personal_access_tokens')
            ->where('expires_at', '<', now())
            ->delete();

        DB::table('refresh_tokens')
            ->where('expires_at', '<', now())
            ->delete();

        Log::info('Cleaned up expired tokens');
    }
}
