# 🚀 IMPLEMENTATION GUIDE - SECURITY HARDENING

## Quick Start: Integrating Security Services

### Step 1: Run Database Migrations
```bash
php artisan migrate --path=database/migrations/2026_05_20_000001_create_enterprise_security_tables.php
```

This creates:
- `refresh_tokens` table
- Enhanced `password_reset_tokens` table
- `expires_at` column on `personal_access_tokens`

### Step 2: Register Middleware

Edit `app/Http/Kernel.php`:
```php
protected $routeMiddleware = [
    // ... existing middleware
    'rate.limit.enterprise' => \App\Http\Middleware\EnterpriseRateLimit::class,
];
```

### Step 3: Update API Routes

In `routes/api.php`, add these routes:

```php
<?php
use App\Http\Controllers\Api\AuthControllerV2;

// Public auth endpoints
Route::post('/auth/refresh-token', [AuthControllerV2::class, 'refreshToken']);
Route::post('/auth/forgot-password', [AuthControllerV2::class, 'forgotPassword']);
Route::post('/auth/validate-reset-token', [AuthControllerV2::class, 'validateResetToken']);
Route::post('/auth/reset-password', [AuthControllerV2::class, 'resetPassword']);

// Protected endpoints (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/change-password', [AuthControllerV2::class, 'changePassword']);
    Route::post('/auth/logout', [AuthControllerV2::class, 'logout']);
    Route::post('/auth/logout-all-devices', [AuthControllerV2::class, 'logoutFromAllDevices']);
    
    Route::get('/auth/sessions', [AuthControllerV2::class, 'getActiveSessions']);
    Route::delete('/auth/sessions/{tokenId}', [AuthControllerV2::class, 'revokeSession']);
});
```

### Step 4: Apply Rate Limiting to Routes

```php
// Apply to all API routes
Route::middleware('api', 'rate.limit.enterprise')->group(function () {
    // All API routes here
});

// Or apply to specific routes
Route::post('/login', [...])
    ->middleware('rate.limit.enterprise');
```

### Step 5: Configure Environment Variables

In `.env`:
```env
# Token Configuration
SANCTUM_EXPIRATION=60  # minutes

# Rate Limiting Whitelist (comma-separated)
RATE_LIMIT_WHITELIST=127.0.0.1,::1

# Email Configuration (for password reset emails)
MAIL_FROM_ADDRESS=noreply@halalytics.com
MAIL_FROM_NAME="Halalytics"
```

### Step 6: Create Password Reset Email

Create `app/Mail/PasswordResetMail.php`:
```php
<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $token,
        public int $expiryMinutes
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Your Halalytics Password',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset',
            with: [
                'user' => $this->user,
                'token' => $this->token,
                'expiryMinutes' => $this->expiryMinutes,
                'resetUrl' => url("/reset-password/{$this->token}/{$this->user->email}"),
            ],
        );
    }
}
```

### Step 7: Create Password Reset Email Template

Create `resources/views/emails/password-reset.blade.php`:
```blade
<h1>Reset Your Password</h1>

<p>Hi {{ $user->full_name }},</p>

<p>We received a request to reset your password. Click the link below to proceed:</p>

<p>
    <a href="{{ $resetUrl }}" style="display: inline-block; padding: 10px 20px; background-color: #004D40; color: white; text-decoration: none; border-radius: 5px;">
        Reset Password
    </a>
</p>

<p>This link expires in {{ $expiryMinutes }} minutes.</p>

<p>If you didn't request this, ignore this email.</p>

<p>— Halalytics Team</p>
```

### Step 8: Update User Model (Optional)

Ensure `app/Models/User.php` has:
```php
<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;

class User extends Model
{
    use HasApiTokens;
    
    // ... rest of model
}
```

---

## Testing the Implementation

### Test Token Refresh
```bash
# Get token
TOKEN=$(curl -X POST http://localhost/api/login \
  -H "Content-Type: application/json" \
  -d '{"login":"user@example.com","password":"password"}' \
  | jq -r '.token')

# Refresh token
curl -X POST http://localhost/api/auth/refresh-token \
  -H "Authorization: Bearer $TOKEN"
```

### Test Rate Limiting
```bash
# Try to login 6 times rapidly
for i in {1..6}; do
  curl -X POST http://localhost/api/login \
    -H "Content-Type: application/json" \
    -d '{"login":"user@example.com","password":"wrong"}' \
    -w "\nStatus: %{http_code}\n"
  sleep 0.5
done

# 6th request should return 429
```

### Test Password Reset
```bash
# Request reset
curl -X POST http://localhost/api/auth/forgot-password \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com"}'

# Check email for token
# Validate token
curl -X POST http://localhost/api/auth/validate-reset-token \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","token":"RESET_TOKEN_HERE"}'

# Reset password
curl -X POST http://localhost/api/auth/reset-password \
  -H "Content-Type: application/json" \
  -d '{
    "email":"user@example.com",
    "token":"RESET_TOKEN_HERE",
    "password":"NewPassword123!",
    "password_confirmation":"NewPassword123!"
  }'
```

### Test Audit Logging
```bash
# Login and check activity logs
$activities = ActivityAuditService::getUserHistory($userId);
echo json_encode($activities);

# Get suspicious activities
$suspicious = ActivityAuditService::getSuspiciousActivities();
echo json_encode($suspicious);
```

---

## Monitoring & Maintenance

### Daily Tasks
```bash
# Check failed login attempts
SELECT COUNT(*) as failed_attempts 
FROM activity_logs 
WHERE type = 'user_login_failed' 
AND created_at > NOW() - INTERVAL 24 HOUR;

# Monitor rate limit hits
SELECT ip_address, COUNT(*) as attempts
FROM activity_logs
WHERE severity >= 1
AND created_at > NOW() - INTERVAL 1 HOUR
GROUP BY ip_address;
```

### Weekly Tasks
```bash
# Clean up old tokens
php artisan tinker
>> TokenRefreshService::cleanupExpiredTokens()
>> SecurePasswordResetService::cleanupExpiredTokens()
>> ActivityAuditService::cleanupOldLogs(90)
```

### Monthly Tasks
```bash
# Review suspicious activities
$suspicious = ActivityAuditService::getSuspiciousActivities();

# Analyze patterns
SELECT type, COUNT(*) as count
FROM activity_logs
WHERE created_at > NOW() - INTERVAL 30 DAYS
GROUP BY type
ORDER BY count DESC;
```

---

## Troubleshooting

### Issue: "Too many auth attempts"
**Solution:** Wait 60 seconds or clear rate limit:
```php
RateLimiter::clear('auth:user@example.com');
```

### Issue: Password reset token expired
**Solution:** Request a new reset link (30 minute expiry)
```
POST /api/auth/forgot-password
```

### Issue: Token refresh not working
**Solution:** Check token expiry:
```php
$user = auth()->user();
$shouldRefresh = TokenRefreshService::shouldRefreshToken($user);
echo $shouldRefresh ? "Should refresh" : "Token still valid";
```

### Issue: Activity logs not appearing
**Solution:** Ensure middleware is registered and routes are protected:
```php
// In routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    // These routes will have activity logging
});
```

---

## Performance Considerations

### Database Optimization
```sql
-- Add indices for better query performance
CREATE INDEX idx_refresh_tokens_expires ON refresh_tokens(expires_at);
CREATE INDEX idx_activity_logs_user ON activity_logs(user_id, created_at);
CREATE INDEX idx_password_reset_email ON password_reset_tokens(email, expires_at);
```

### Rate Limiting Cache
```php
// Use Redis for better performance
CACHE_DRIVER=redis  # in .env

// Automatic rate limit cleanup
php artisan schedule:run  # runs daily scheduled tasks
```

---

## Security Best Practices

### 1. Always Use HTTPS
```php
// In app/Http/Middleware/ForceHttps.php
if (app()->environment('production')) {
    URL::forceScheme('https');
}
```

### 2. Set Secure Headers
```php
// In app/Http/Middleware/SecurityHeaders.php
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
```

### 3. Monitor Suspicious Activity
```php
// Weekly review script
$suspicious = ActivityAuditService::getSuspiciousActivities();
foreach ($suspicious as $activity) {
    // Alert admin
    Log::critical("Suspicious: {$activity->description}");
}
```

### 4. Regular Token Cleanup
```php
// In app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        TokenRefreshService::cleanupExpiredTokens();
        SecurePasswordResetService::cleanupExpiredTokens();
        ActivityAuditService::cleanupOldLogs(90);
    })->daily();
}
```

---

## Next Steps

1. ✅ Run migrations
2. ✅ Register middleware
3. ✅ Update routes
4. ✅ Configure environment
5. ✅ Create email template
6. ✅ Run tests
7. ✅ Deploy to staging
8. ✅ Monitor for 1 week
9. ✅ Deploy to production
10. ✅ Document incidents

---

## Support

For issues or questions:
- Review `SECURITY_IMPLEMENTATION.md` for detailed docs
- Check `PHASE_1_2_COMPLETION_REPORT.md` for architecture overview
- View `SECURITY_ROUTES.md` for API endpoint examples

**Questions?** Contact the security team at security@halalytics.com

