<?php

namespace App\Services;

use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PasswordResetService
{
    /**
     * 🔐 Send password reset link
     */
    public function sendResetLink(string $email): array
    {
        try {
            $user = User::where('email', $email)->first();

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'If an account with that email exists, a password reset link has been sent.',
                ];
            }

            // Delete any existing tokens for this user
            PasswordReset::where('email', $email)->delete();

            // Generate reset token
            $token = Str::random(60);
            $expiresAt = Carbon::now()->addHours(1);

            // Store reset token
            PasswordReset::create([
                'email' => $email,
                'token' => $token,
                'expires_at' => $expiresAt,
            ]);

            // Send reset email
            Mail::to($email)->send(new \App\Mail\PasswordReset($user, $token));

            Log::info('Password reset link sent', ['email' => $email]);

            return [
                'success' => true,
                'message' => 'Password reset link sent to your email.',
            ];
        } catch (\Exception $e) {
            Log::error('Failed to send password reset', ['email' => $email, 'error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'Failed to send reset link. Please try again.',
            ];
        }
    }

    /**
     * 🔄 Reset password with token
     */
    public function resetPassword(string $token, string $email, string $password): array
    {
        try {
            $reset = PasswordReset::where('token', $token)
                ->where('email', $email)
                ->where('expires_at', '>', Carbon::now())
                ->first();

            if (!$reset) {
                return [
                    'success' => false,
                    'message' => 'Invalid or expired reset token.',
                ];
            }

            $user = User::where('email', $email)->first();

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found.',
                ];
            }

            // Update password
            $user->update([
                'password' => Hash::make($password),
                'last_login' => Carbon::now(),
            ]);

            // Delete the reset token
            $reset->delete();

            // Log the password reset
            activity('password_reset')
                ->by($user)
                ->withProperties(['ip' => request()->ip()])
                ->log('Password reset successfully');

            Log::info('Password reset successful', ['user_id' => $user->id_user, 'email' => $email]);

            return [
                'success' => true,
                'message' => 'Password reset successfully!',
                'user' => $user,
            ];
        } catch (\Exception $e) {
            Log::error('Password reset failed', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'Password reset failed. Please try again.',
            ];
        }
    }

    /**
     * ✅ Validate reset token
     */
    public function validateResetToken(string $token, string $email): array
    {
        try {
            $reset = PasswordReset::where('token', $token)
                ->where('email', $email)
                ->where('expires_at', '>', Carbon::now())
                ->first();

            if (!$reset) {
                return [
                    'success' => false,
                    'message' => 'Invalid or expired reset token.',
                ];
            }

            return [
                'success' => true,
                'message' => 'Token is valid.',
                'expires_at' => $reset->expires_at,
            ];
        } catch (\Exception $e) {
            Log::error('Token validation failed', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'Token validation failed.',
            ];
        }
    }

    /**
     * 🧹 Clean expired tokens
     */
    public function cleanExpiredTokens(): int
    {
        return PasswordReset::where('expires_at', '<', Carbon::now())->delete();
    }
}
