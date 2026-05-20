# 🏗️ HALALYTICS SECURITY ARCHITECTURE DIAGRAM

## System Architecture Overview

```
┌─────────────────────────────────────────────────────────────────────────┐
│                          CLIENT APPLICATIONS                             │
│  ┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐      │
│  │  Android (Compose)   │  Web Browser       │  Mobile Safari     │      │
│  │                      │                    │                    │      │
│  └──────────────────┘  └──────────────────┘  └──────────────────┘      │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    │ HTTPS Only
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                         API GATEWAY LAYER                                │
│  ┌─────────────────────────────────────────────────────────────────┐   │
│  │ Request Validation & Security Headers                            │   │
│  │  - X-Content-Type-Options: nosniff                              │   │
│  │  - X-Frame-Options: DENY                                        │   │
│  │  - Strict-Transport-Security                                    │   │
│  │  - Content-Security-Policy                                      │   │
│  └─────────────────────────────────────────────────────────────────┘   │
│                            │                                             │
│                            ▼                                             │
│  ┌─────────────────────────────────────────────────────────────────┐   │
│  │        EnterpriseRateLimit Middleware                            │   │
│  │  ┌──────────────────┐  ┌──────────────────┐  ┌──────────────┐ │   │
│  │  │  Auth Limit      │  │  Sensitive Limit │  │  General API  │ │   │
│  │  │  5/60 sec        │  │  10/5 min        │  │  100/1 min   │ │   │
│  │  └──────────────────┘  └──────────────────┘  └──────────────┘ │   │
│  │                                                                  │   │
│  │  IP Whitelist Check ──► Allow or Block                         │   │
│  └─────────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                     AUTHENTICATION & ROUTING                             │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │ Route: POST /api/login                                           │  │
│  │  ├─► SecurityValidationService::validateEmail()                │  │
│  │  ├─► Verify credentials (Hash::check)                          │  │
│  │  ├─► TokenRefreshService::issueAuthToken()                     │  │
│  │  ├─► ActivityAuditService::logLogin()                          │  │
│  │  └─► Return: { token, user, role, expires_in }                │  │
│  └──────────────────────────────────────────────────────────────────┘  │
│                                                                          │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │ Route: POST /api/auth/forgot-password                            │  │
│  │  ├─► Validate email                                             │  │
│  │  ├─► Check rate limits (5/day, 60 sec throttle)                │  │
│  │  ├─► SecurePasswordResetService::sendResetLink()               │  │
│  │  ├─► Generate secure token + hash                              │  │
│  │  ├─► Store in password_reset_tokens (30 min expiry)            │  │
│  │  └─► Send email with reset link                                │  │
│  └──────────────────────────────────────────────────────────────────┘  │
│                                                                          │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │ Route: POST /api/auth/reset-password                             │  │
│  │  ├─► Validate token (hash_equals)                              │  │
│  │  ├─► Check expiry & failed attempts                            │  │
│  │  ├─► Validate new password strength                            │  │
│  │  ├─► Update user.password (Hash::make)                         │  │
│  │  ├─► Revoke all tokens (force re-login)                        │  │
│  │  └─► Log password reset                                        │  │
│  └──────────────────────────────────────────────────────────────────┘  │
│                                                                          │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │ Route: POST /api/auth/refresh-token (Protected)                 │  │
│  │  ├─► Validate existing token                                   │  │
│  │  ├─► Check if should refresh (5 min before expiry)             │  │
│  │  ├─► Revoke old token                                          │  │
│  │  ├─► Issue new token + refresh token                           │  │
│  │  ├─► Increment rotation counter                                │  │
│  │  └─► Return new token                                          │  │
│  └──────────────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                    TOKEN STORAGE & VALIDATION                            │
│                                                                          │
│  ┌──────────────────────────────────────┐                              │
│  │    personal_access_tokens (DB)       │                              │
│  │  ┌────────────────────────────────┐ │                              │
│  │  │ - id                           │ │                              │
│  │  │ - user_id                      │ │                              │
│  │  │ - token (hashed)               │ │                              │
│  │  │ - expires_at    ◄─ NEW         │ │                              │
│  │  │ - last_used_at                 │ │                              │
│  │  │ - created_at                   │ │                              │
│  │  └────────────────────────────────┘ │                              │
│  └──────────────────────────────────────┘                              │
│                                                                          │
│  ┌──────────────────────────────────────┐                              │
│  │    refresh_tokens (DB)               │      ◄─ NEW TABLE            │
│  │  ┌────────────────────────────────┐ │                              │
│  │  │ - id                           │ │                              │
│  │  │ - user_id                      │ │                              │
│  │  │ - token (hashed)               │ │                              │
│  │  │ - rotated_count                │ │                              │
│  │  │ - expires_at (30 days)         │ │                              │
│  │  │ - ip_address                   │ │                              │
│  │  │ - user_agent                   │ │                              │
│  │  └────────────────────────────────┘ │                              │
│  └──────────────────────────────────────┘                              │
│                                                                          │
│  ┌──────────────────────────────────────┐                              │
│  │  password_reset_tokens (DB)          │      ◄─ ENHANCED             │
│  │  ┌────────────────────────────────┐ │                              │
│  │  │ - id                           │ │                              │
│  │  │ - email                        │ │                              │
│  │  │ - token (hashed, 64 chars)     │ │                              │
│  │  │ - attempts (max 5)             │ │                              │
│  │  │ - expires_at (30 minutes)      │ │                              │
│  │  │ - ip_address                   │ │                              │
│  │  │ - user_agent                   │ │                              │
│  │  └────────────────────────────────┘ │                              │
│  └──────────────────────────────────────┘                              │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                    ┌───────────────┴───────────────┐
                    ▼                               ▼
┌──────────────────────────────────────┐  ┌──────────────────────────────┐
│   INPUT VALIDATION SERVICE            │  │   AUDIT LOGGING SERVICE       │
│  (SecurityValidationService)          │  │  (ActivityAuditService)       │
│                                       │  │                              │
│  ┌──────────────────────────────────┐ │  │  ┌──────────────────────────┐ │
│  │ validateEmail()                  │ │  │  │ Log Types:              │ │
│  │ validateUsername()               │ │  │  │  - LOGIN                 │ │
│  │ validatePasswordStrength()       │ │  │  │  - LOGIN_FAILED          │ │
│  │ validatePhoneNumber()            │ │  │  │  - PASSWORD_CHANGED      │ │
│  │ validateUrl()                    │ │  │  │  - PROFILE_UPDATED       │ │
│  │ sanitizeHtml()                   │ │  │  │  - SUSPICIOUS_ACTIVITY   │ │
│  │ escapeHtml()                     │ │  │  │  - ADMIN_ACTION          │ │
│  │ validateBarcode()                │ │  │  │  - And more...           │ │
│  │ ... 10+ validation methods       │ │  │  │                          │ │
│  └──────────────────────────────────┘ │  │  └──────────────────────────┘ │
│                                       │  │                              │
│  Returns:                             │  │  Captured Data:             │
│  - Valid data (sanitized)            │  │  - User ID                   │
│  - Validation errors                 │  │  - Activity Type             │
│  - Strength score                    │  │  - Description               │
│  - Feedback for users                │  │  - IP Address                │
│  └──────────────────────────────────┘ │  │  - User Agent                │
└──────────────────────────────────────┘  │  - HTTP Method               │
                                           │  - Request Path              │
                                           │  - Metadata (JSON)           │
                                           │  - Severity Level            │
                                           │  - Timestamp                 │
                                           │                              │
                                           │  Database Table:             │
                                           │  activity_logs               │
                                           │  (with indices for query)    │
                                           └──────────────────────────────┘
```

---

## Data Flow: User Login with Token Management

```
CLIENT                          API SERVER                      DATABASE
  │                                 │                               │
  ├─ POST /api/login ──────────────>│                               │
  │  { email, password }            │                               │
  │                                 ├─ Rate Limit Check ────────────>│
  │                                 │                               │
  │                                 │<─ Within limits ───────────────┤
  │                                 │                               │
  │                                 ├─ Input Validation             │
  │                                 │  (email, password format)      │
  │                                 │                               │
  │                                 ├─ Find User ──────────────────>│
  │                                 │                               │
  │                                 │<─ User Record ────────────────┤
  │                                 │                               │
  │                                 ├─ Hash::check(password)        │
  │                                 │                               │
  │                                 ├─ TokenRefreshService         │
  │                                 │  ::issueAuthToken() ───────┐ │
  │                                 │                         ┌──┴─>│
  │                                 │  Create Token Record    │  │  │
  │                                 │  expires_at = +60 min   │  │  │
  │                                 │<────────────────────────┘  │  │
  │                                 │                            │  │
  │                                 ├─ ActivityAuditService     │  │
  │                                 │  ::logLogin() ─────────────>│
  │                                 │  (Create audit record)      │  │
  │                                 │                            │  │
  │<─ { token, user, expires_in }──┤                            │  │
  │                                 │                            │  │
```

---

## Token Refresh Flow (Auto-Refresh)

```
5 MINUTES BEFORE EXPIRY:

CLIENT                          API SERVER                      DATABASE
  │                                 │                               │
  ├─ POST /api/auth/refresh-token ─>│                               │
  │  { (implicit from auth header) }│                               │
  │                                 ├─ Get Current Token ──────────>│
  │                                 │                               │
  │                                 │<─ Token Record ───────────────┤
  │                                 │                               │
  │                                 ├─ Check shouldRefresh()        │
  │                                 │  if (expires_at - now) <= 5min│
  │                                 │                               │
  │                                 ├─ Revoke Old Token ───────────>│
  │                                 │  DELETE old_token            │  │
  │                                 │<──────────────────────────────┤
  │                                 │                               │
  │                                 ├─ Issue New Token ───────────>│
  │                                 │  INSERT new token              │
  │                                 │  expires_at = +60 min          │
  │                                 │  rotated_count += 1            │
  │                                 │<──────────────────────────────┤
  │                                 │                               │
  │<─ { token: new_token } ────────┤                               │
  │                                 │                               │
```

---

## Password Reset Flow

```
FORGOT PASSWORD FLOW:

CLIENT                  API SERVER                         DATABASE / EMAIL
  │                         │                                    │
  ├─ POST /forgot-password ─>│                                    │
  │  { email }              │                                    │
  │                         ├─ Rate Limit Check ───────────────>│
  │                         │ (5 per day, 60 sec throttle)      │
  │                         │<───────────────────────────────────┤
  │                         │                                    │
  │                         ├─ Find User ──────────────────────>│
  │                         │                                    │
  │                         ├─ Generate Secure Token           │
  │                         │ (64 chars, SHA256 hash)            │
  │                         │                                    │
  │                         ├─ Store Token ────────────────────>│
  │                         │ INSERT password_reset_tokens       │
  │                         │ expires_at = +30 min               │
  │                         │ attempts = 0                       │
  │                         │<───────────────────────────────────┤
  │                         │                                    │
  │                         ├─ Send Email ────────────────────>│
  │                         │ PasswordResetMail                  │
  │                         │ with reset link & token            │
  │                         │<───────────────────────────────────┤
  │                         │                                    │
  │<─ { success: true } ───┤                                    │
  │                         │                                    │


RESET PASSWORD WITH TOKEN:

CLIENT              API SERVER                          DATABASE
  │                     │                                   │
  ├─ POST /reset ──────>│                                   │
  │ { token, password }  │                                   │
  │                      ├─ Validate Token ───────────────>│
  │                      │ hash_equals check                 │
  │                      │<─ Token Valid ──────────────────┤
  │                      │                                 │
  │                      ├─ Check Expiry ─────────────────>│
  │                      │ expires_at > now?                │
  │                      │<─ Not Expired ──────────────────┤
  │                      │                                 │
  │                      ├─ Check Attempts ───────────────>│
  │                      │ attempts < 5?                    │
  │                      │<─ Valid ────────────────────────┤
  │                      │                                 │
  │                      ├─ Update Password ──────────────>│
  │                      │ UPDATE users                     │
  │                      │ password = Hash::make(new_pwd)   │
  │                      │<─ Updated ──────────────────────┤
  │                      │                                 │
  │                      ├─ Revoke Tokens ────────────────>│
  │                      │ DELETE from personal_access...   │
  │                      │ (force re-login)                 │
  │                      │<─ Revoked ──────────────────────┤
  │                      │                                 │
  │                      ├─ Delete Reset Token ───────────>│
  │                      │ DELETE password_reset_tokens      │
  │                      │<─ Deleted ──────────────────────┤
  │                      │                                 │
  │<─ { success: true }──┤                                 │
  │                      │                                 │
```

---

## Security Layers Summary

```
┌────────────────────────────────────────────────────────────────┐
│ LAYER 1: REQUEST VALIDATION & RATE LIMITING                    │
│  - IP Whitelist Check                                          │
│  - Endpoint-specific rate limits                               │
│  - IP blocking on excessive attempts                           │
└────────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌────────────────────────────────────────────────────────────────┐
│ LAYER 2: INPUT VALIDATION & SANITIZATION                       │
│  - Email validation                                            │
│  - Password strength check                                     │
│  - XSS prevention (HTML escape)                                │
│  - SQL injection prevention (parameterized queries)            │
└────────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌────────────────────────────────────────────────────────────────┐
│ LAYER 3: AUTHENTICATION & AUTHORIZATION                        │
│  - Credentials verification (Hash::check)                      │
│  - Token validation                                            │
│  - Role-based access control                                   │
│  - Multi-session management                                    │
└────────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌────────────────────────────────────────────────────────────────┐
│ LAYER 4: CRYPTOGRAPHIC SECURITY                                │
│  - Password hashing (bcrypt)                                   │
│  - Token hashing (SHA-256)                                     │
│  - Secure random generation                                    │
│  - Database encryption (at rest)                               │
└────────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌────────────────────────────────────────────────────────────────┐
│ LAYER 5: MONITORING & LOGGING                                  │
│  - Activity audit trails                                       │
│  - Failed attempt tracking                                     │
│  - Suspicious activity detection                               │
│  - Admin action logging                                        │
└────────────────────────────────────────────────────────────────┘
```

---

This architecture provides defense-in-depth security across all layers of the application, from network boundary to database records.

