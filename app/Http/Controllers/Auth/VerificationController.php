<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\EmailVerificationService;
use App\Services\PasswordResetService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VerificationController extends Controller
{
    private $emailVerificationService;
    private $passwordResetService;

    public function __construct(
        EmailVerificationService $emailVerificationService,
        PasswordResetService $passwordResetService
    ) {
        $this->emailVerificationService = $emailVerificationService;
        $this->passwordResetService = $passwordResetService;
    }

    /**
     * 📧 Send email verification
     */
    public function sendVerification(Request $request): JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.',
            ], 401);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Email is already verified.',
            ]);
        }

        $result = $this->emailVerificationService->sendVerification($user);

        return response()->json($result);
    }

    /**
     * ✅ Verify email with token
     */
    public function verifyEmail(Request $request, $token): JsonResponse
    {
        $result = $this->emailVerificationService->verifyEmail($token);

        if ($result['success']) {
            // Mark onboarding step as completed
            $user = $result['user'];
            $onboardingData = $user->onboarding_progress ?? [];
            $onboardingData['email_verified'] = [
                'completed' => true,
                'completed_at' => now()->toISOString(),
            ];
            $user->onboarding_progress = $onboardingData;
            $user->increment('onboarding_points', 10);
            $user->save();
        }

        return response()->json($result);
    }

    /**
     * 🔄 Resend verification email
     */
    public function resendVerification(Request $request): JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.',
            ], 401);
        }

        $result = $this->emailVerificationService->resendVerification($user);

        return response()->json($result);
    }

    /**
     * 🔐 Send password reset link
     */
    public function sendPasswordReset(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $result = $this->passwordResetService->sendResetLink($request->email);

        // Always return success to prevent email enumeration attacks
        return response()->json([
            'success' => true,
            'message' => 'If an account with that email exists, a password reset link has been sent.',
        ]);
    }

    /**
     * 🔄 Reset password
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $result = $this->passwordResetService->resetPassword(
            $request->token,
            $request->email,
            $request->password
        );

        return response()->json($result);
    }

    /**
     * ✅ Validate reset token
     */
    public function validateResetToken(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
        ]);

        $result = $this->passwordResetService->validateResetToken(
            $request->token,
            $request->email
        );

        return response()->json($result);
    }

    /**
     * 📱 Show verification page (web)
     */
    public function showVerificationPage($token)
    {
        $result = $this->emailVerificationService->verifyEmail($token);

        if ($result['success']) {
            return view('auth.verified', [
                'user' => $result['user'],
                'message' => $result['message']
            ]);
        }

        return view('auth.verification-failed', [
            'message' => $result['message']
        ]);
    }

    /**
     * 📱 Show password reset page (web)
     */
    public function showPasswordResetPage($token, $email)
    {
        $result = $this->passwordResetService->validateResetToken($token, $email);

        if ($result['success']) {
            return view('auth.reset-password', [
                'token' => $token,
                'email' => $email,
                'expires_at' => $result['expires_at']
            ]);
        }

        return view('auth.reset-failed', [
            'message' => $result['message']
        ]);
    }
}
