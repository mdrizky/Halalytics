<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EmailVerificationService
{
    /**
     * 📧 Send email verification
     */
    public function sendVerification(User $user): bool
    {
        try {
            // Generate verification token
            $token = Str::random(60);
            $expiresAt = Carbon::now()->addHours(24);

            // Store token in user record
            $user->update([
                'email_verification_token' => $token,
                'email_verification_expires_at' => $expiresAt,
            ]);

            // Send verification email
            Mail::to($user->email)->send(new \App\Mail\EmailVerification($user, $token));

            Log::info('Email verification sent', ['user_id' => $user->id_user, 'email' => $user->email]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email verification', [
                'user_id' => $user->id_user,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * ✅ Verify email token
     */
    public function verifyEmail(string $token): array
    {
        try {
            $user = User::where('email_verification_token', $token)
                ->where('email_verification_expires_at', '>', Carbon::now())
                ->first();

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Invalid or expired verification token.',
                ];
            }

            // Mark email as verified
            $user->update([
                'email_verified_at' => Carbon::now(),
                'email_verification_token' => null,
                'email_verification_expires_at' => null,
            ]);

            Log::info('Email verified successfully', ['user_id' => $user->id_user]);

            return [
                'success' => true,
                'message' => 'Email verified successfully!',
                'user' => $user,
            ];
        } catch (\Exception $e) {
            Log::error('Email verification failed', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'Verification failed. Please try again.',
            ];
        }
    }

    /**
     * 🔄 Resend verification email
     */
    public function resendVerification(User $user): array
    {
        if ($user->email_verified_at) {
            return [
                'success' => false,
                'message' => 'Email is already verified.',
            ];
        }

        // Rate limiting: only allow resend every 5 minutes
        if ($user->email_verification_expires_at && 
            $user->email_verification_expires_at->diffInMinutes(Carbon::now()) < 55) {
            
            return [
                'success' => false,
                'message' => 'Please wait before requesting another verification email.',
            ];
        }

        $sent = $this->sendVerification($user);

        return [
            'success' => $sent,
            'message' => $sent ? 
                'Verification email sent successfully!' : 
                'Failed to send verification email. Please try again.',
        ];
    }
}
