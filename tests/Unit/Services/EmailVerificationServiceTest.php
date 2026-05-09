<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\EmailVerificationService;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class EmailVerificationServiceTest extends TestCase
{
    use RefreshDatabase;

    private EmailVerificationService $emailVerificationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->emailVerificationService = app(EmailVerificationService::class);
        Mail::fake();
    }

    /**
     * 📧 Test sendVerification creates token and sends email
     */
    public function test_send_verification_creates_token_and_sends_email(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $result = $this->emailVerificationService->sendVerification($user);

        $this->assertTrue($result);

        $user->refresh();
        $this->assertNotNull($user->email_verification_token);
        $this->assertNotNull($user->email_verification_expires_at);
        $this->assertNull($user->email_verified_at);

        // Verify email was sent
        Mail::assertSent(\App\Mail\EmailVerification::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email) && $mail->user->id_user === $user->id_user;
        });
    }

    /**
     * 📧 Test sendVerification with already verified user
     */
    public function test_send_verification_with_verified_user(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $result = $this->emailVerificationService->sendVerification($user);

        $this->assertTrue($result);

        // Verify no email was sent since user is already verified
        Mail::assertNotSent(\App\Mail\EmailVerification::class);
    }

    /**
     * ✅ Test verifyEmail with valid token
     */
    public function test_verify_email_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email_verification_token' => 'test-token',
            'email_verification_expires_at' => now()->addHours(24),
            'email_verified_at' => null
        ]);

        $result = $this->emailVerificationService->verifyEmail('test-token');

        $this->assertTrue($result['success']);
        $this->assertEquals('Email verified successfully!', $result['message']);
        $this->assertArrayHasKey('user', $result);

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->email_verification_token);
        $this->assertNull($user->email_verification_expires_at);
    }

    /**
     * ✅ Test verifyEmail with invalid token
     */
    public function test_verify_email_with_invalid_token(): void
    {
        User::factory()->create([
            'email_verification_token' => 'valid-token',
            'email_verification_expires_at' => now()->addHours(24),
            'email_verified_at' => null
        ]);

        $result = $this->emailVerificationService->verifyEmail('invalid-token');

        $this->assertFalse($result['success']);
        $this->assertEquals('Invalid or expired verification token.', $result['message']);
    }

    /**
     * ✅ Test verifyEmail with expired token
     */
    public function test_verify_email_with_expired_token(): void
    {
        $user = User::factory()->create([
            'email_verification_token' => 'expired-token',
            'email_verification_expires_at' => now()->subHours(1), // Expired
            'email_verified_at' => null
        ]);

        $result = $this->emailVerificationService->verifyEmail('expired-token');

        $this->assertFalse($result['success']);
        $this->assertEquals('Invalid or expired verification token.', $result['message']);

        $user->refresh();
        $this->assertNull($user->email_verified_at);
    }

    /**
     * 🔄 Test resendVerification with unverified user
     */
    public function test_resend_verification_with_unverified_user(): void
    {
        $user = User::factory()->create([
            'email_verification_token' => 'old-token',
            'email_verification_expires_at' => now()->subHours(2), // Expired
            'email_verified_at' => null
        ]);

        $result = $this->emailVerificationService->resendVerification($user);

        $this->assertTrue($result['success']);
        $this->assertStringContains('sent successfully', $result['message']);

        $user->refresh();
        $this->assertNotEquals('old-token', $user->email_verification_token);
        $this->assertGreaterThan(now(), $user->email_verification_expires_at);

        // Verify email was sent
        Mail::assertSent(\App\Mail\EmailVerification::class);
    }

    /**
     * 🔄 Test resendVerification with already verified user
     */
    public function test_resend_verification_with_verified_user(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $result = $this->emailVerificationService->resendVerification($user);

        $this->assertFalse($result['success']);
        $this->assertEquals('Email is already verified.', $result['message']);

        // Verify no email was sent
        Mail::assertNotSent(\App\Mail\EmailVerification::class);
    }

    /**
     * 🔄 Test resendVerification rate limiting
     */
    public function test_resend_verification_rate_limiting(): void
    {
        $user = User::factory()->create([
            'email_verification_token' => 'recent-token',
            'email_verification_expires_at' => now()->addMinutes(50), // Not expired yet
            'email_verified_at' => null
        ]);

        $result = $this->emailVerificationService->resendVerification($user);

        $this->assertFalse($result['success']);
        $this->assertStringContains('wait before requesting', $result['message']);

        // Verify no email was sent
        Mail::assertNotSent(\App\Mail\EmailVerification::class);
    }

    /**
     * 📧 Test token generation uniqueness
     */
    public function test_token_generation_uniqueness(): void
    {
        $user1 = User::factory()->create(['email_verified_at' => null]);
        $user2 = User::factory()->create(['email_verified_at' => null]);

        $this->emailVerificationService->sendVerification($user1);
        $this->emailVerificationService->sendVerification($user2);

        $user1->refresh();
        $user2->refresh();

        $this->assertNotEquals($user1->email_verification_token, $user2->email_verification_token);
    }

    /**
     * ⏰ Test token expiration time
     */
    public function test_token_expiration_time(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $this->emailVerificationService->sendVerification($user);

        $user->refresh();
        $expectedExpiration = now()->addHours(24);
        $actualExpiration = $user->email_verification_expires_at;

        // Allow for 1 minute difference
        $this->assertLessThan(61, abs($expectedExpiration->diffInSeconds($actualExpiration)));
    }

    /**
     * 🔄 Test verification token cleanup after verification
     */
    public function test_verification_token_cleanup_after_verification(): void
    {
        $user = User::factory()->create([
            'email_verification_token' => 'test-token',
            'email_verification_expires_at' => now()->addHours(24),
            'email_verified_at' => null
        ]);

        $this->emailVerificationService->verifyEmail('test-token');

        $user->refresh();
        $this->assertNull($user->email_verification_token);
        $this->assertNull($user->email_verification_expires_at);
        $this->assertNotNull($user->email_verified_at);
    }

    /**
     * 📧 Test email verification with multiple users
     */
    public function test_email_verification_with_multiple_users(): void
    {
        $users = User::factory()->count(3)->create(['email_verified_at' => null]);

        foreach ($users as $user) {
            $this->emailVerificationService->sendVerification($user);
        }

        // Verify all tokens are different
        $tokens = $users->map(fn($user) => $user->fresh()->email_verification_token);
        $uniqueTokens = $tokens->unique();
        $this->assertEquals($tokens->count(), $uniqueTokens->count());

        // Verify all emails were sent
        Mail::assertSent(\App\Mail\EmailVerification::class, 3);
    }

    /**
     * 📧 Test verification with case-insensitive token
     */
    public function test_verification_case_insensitive(): void
    {
        $user = User::factory()->create([
            'email_verification_token' => 'Test-Token',
            'email_verification_expires_at' => now()->addHours(24),
            'email_verified_at' => null
        ]);

        // Test with different case
        $result = $this->emailVerificationService->verifyEmail('test-token');

        $this->assertTrue($result['success']);
    }

    /**
     * 📧 Test verification with token containing special characters
     */
    public function test_verification_with_special_characters(): void
    {
        $token = 'token-with_special.chars_123';
        $user = User::factory()->create([
            'email_verification_token' => $token,
            'email_verification_expires_at' => now()->addHours(24),
            'email_verified_at' => null
        ]);

        $result = $this->emailVerificationService->verifyEmail($token);

        $this->assertTrue($result['success']);
    }
}
