<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Services\TokenRefreshService;
use App\Services\SecurePasswordResetService;
use Illuminate\Support\Facades\Log;

/**
 * 🔐 Enhanced Auth Controller with Enterprise Security
 * Handles authentication, token management, and password reset with security best practices
 */
class AuthControllerV2 extends \App\Http\Controllers\Controller
{
    /**
     * Refresh the authentication token
     * Called when token is about to expire
     */
    public function refreshToken(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }

            // Check if token needs refresh
            if (!TokenRefreshService::shouldRefreshToken($user)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Token is still valid',
                    'token' => $request->bearerToken(),
                    'expires_in' => 3600,
                ]);
            }

            // Issue new token
            $newToken = TokenRefreshService::refreshAuthToken($user);

            Log::info('Token refreshed', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'token' => $newToken,
                'expires_in' => 3600, // 1 hour
            ]);
        } catch (\Exception $e) {
            Log::error('Token refresh error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh token',
            ], 500);
        }
    }

    /**
     * Request password reset link
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Invalid email format',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = SecurePasswordResetService::sendResetLink($request->email);

        return response()->json($result, $result['success'] ? 200 : 429);
    }

    /**
     * Validate password reset token
     */
    public function validateResetToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = SecurePasswordResetService::validateResetToken(
            $request->email,
            $request->token
        );

        $statusCode = $result['valid'] ? 200 : 400;
        return response()->json($result, $statusCode);
    }

    /**
     * Reset password with token
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Invalid email format',
            'token.required' => 'Reset token is required',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = SecurePasswordResetService::resetPassword(
            $request->email,
            $request->token,
            $request->password
        );

        $statusCode = $result['success'] ? 200 : 400;
        return response()->json($result, $statusCode);
    }

    /**
     * Change password (authenticated user)
     */
    public function changePassword(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            Log::warning('Failed password change attempt - wrong current password', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
                'error_code' => 'INVALID_CURRENT_PASSWORD',
            ], 401);
        }

        // Check new password not same as current
        if (Hash::check($request->new_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'New password must be different from current password',
                'error_code' => 'SAME_PASSWORD',
            ], 422);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Revoke all tokens (force re-login on all devices)
        TokenRefreshService::revokeAllTokens($user);

        Log::info('Password changed successfully', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully. Please login again on all devices.',
        ]);
    }

    /**
     * Logout (revoke current token)
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => true,
                'message' => 'Already logged out',
            ]);
        }

        // Revoke current token
        $currentToken = $user->currentAccessToken();
        if ($currentToken) {
            $currentToken->delete();
        }

        Log::info('User logged out', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Logout from all devices (revoke all tokens)
     */
    public function logoutFromAllDevices(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        TokenRefreshService::revokeAllTokens($user);

        Log::info('User logged out from all devices', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Logged out from all devices successfully',
        ]);
    }

    /**
     * Get active sessions (authenticated user)
     */
    public function getActiveSessions(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $tokens = $user->tokens()
            ->where('expires_at', '>', now())
            ->select('id', 'name', 'last_used_at', 'expires_at', 'created_at')
            ->get();

        return response()->json([
            'success' => true,
            'sessions' => $tokens,
            'count' => $tokens->count(),
        ]);
    }

    /**
     * Revoke specific token (logout from specific device)
     */
    public function revokeSession(Request $request, $tokenId): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $token = $user->tokens()->find($tokenId);

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token not found',
            ], 404);
        }

        $token->delete();

        Log::info('Session revoked', [
            'user_id' => $user->id,
            'token_id' => $tokenId,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Session revoked successfully',
        ]);
    }
}
