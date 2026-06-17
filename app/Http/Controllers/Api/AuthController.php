<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Google_Client;
use GuzzleHttp\Client as GuzzleClient;

class AuthController extends Controller
{
    // REGISTER USER
    public function register(Request $request)
    {
        $payload = $this->normalizeRegisterPayload($request);

        $validator = Validator::make($payload, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'username' => 'required|string|max:255|unique:users,username',
            'phone' => 'nullable|string|max:20',
            'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-,A,B,AB,O',
            'allergy' => 'nullable|string|max:1000',
            'medical_history' => 'nullable|string|max:2000',
            'weight_kg' => 'nullable|integer|min:1',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'username.unique' => 'Username sudah digunakan.',
            'blood_type.in' => 'Golongan darah tidak valid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Data registrasi belum valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = User::create([
                'full_name' => $payload['name'],
                'username' => $payload['username'],
                'email' => $payload['email'],
                'password' => Hash::make($payload['password']),
                'role' => 'user',
                'phone' => $payload['phone'],
                'allergy' => $payload['allergy'],
                'medical_history' => $payload['medical_history'],
                'weight_kg' => $payload['weight_kg'],
                'blood_type' => $payload['blood_type'],
                'active' => true,
            ]);

            if ($request->filled('fcm_token')) {
                $user->fcm_token = $request->string('fcm_token')->toString();
                $user->save();
            }

            $token = $this->issueAuthToken($user);

            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => 'Akun berhasil dibuat! Silakan login.',
                'user' => $user->fresh(),
                'role' => 'user',
                'token' => $token,
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Register error', [
                'email' => $payload['email'] ?? null,
                'username' => $payload['username'] ?? null,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Terjadi kesalahan server saat membuat akun. Silakan coba lagi.',
            ], 500);
        }
    }

    // LOGIN
    public function login(Request $request)
    {
        $payload = [
            'login' => trim((string) ($request->input('login')
                ?? $request->input('username')
                ?? $request->input('email')
                ?? '')),
            'password' => (string) $request->input('password', ''),
        ];

        $validator = Validator::make($payload, [
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Email atau username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Data login belum lengkap.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $throttleKey = 'login_attempts|' . $request->ip() . '|' . strtolower($payload['login']);
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
            ], 429);
        }

        try {
            $user = User::query()
                ->where('email', $payload['login'])
                ->orWhere('username', $payload['login'])
                ->first();

            if (!$user || !Hash::check($payload['password'], $user->password)) {
                RateLimiter::hit($throttleKey, 300); // Lock for 5 minutes
                return response()->json([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Email atau password salah.',
                    'errors' => [
                        'login' => ['Email atau password salah.'],
                    ],
                ], 401);
            }
            
            RateLimiter::clear($throttleKey);

            if (!(bool) ($user->active ?? $user->is_active ?? true)) {
                return response()->json([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Akun Anda sedang dinonaktifkan.',
                ], 403);
            }

            // Only allow 'user' and 'ahli_gizi' roles on API (mobile/Android)
            // Admin must use website directly
            $userRole = strtolower($user->role ?? $user->getRoleNames()->first() ?? '');
            if (!in_array($userRole, ['user', 'ahli_gizi'], true)) {
                return response()->json([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Role ini tidak dapat login melalui API. Gunakan website untuk admin.',
                ], 403);
            }

            $token = $this->issueAuthToken($user);

            $this->updateLoginStreak($user);
            $user->last_login = now();

            if ($request->filled('fcm_token')) {
                $user->fcm_token = $request->string('fcm_token')->toString();
            }

            $user->save();

            $role = $user->role;
            try {
                $role = $user->getRoleNames()->first() ?? $role;
            } catch (\Throwable $e) {
                Log::warning('getRoleNames failed in login', ['message' => $e->getMessage()]);
            }

            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => 'Login berhasil.',
                'user' => $user->fresh(),
                'role' => $role,
                'token' => $token,
                'streak' => [
                    'current' => (int) ($user->current_streak ?? 0),
                    'longest' => (int) ($user->longest_streak ?? 0),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Login error', [
                'login' => $payload['login'],
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Terjadi kesalahan server',
            ], 500);
        }
    }

    // GOOGLE LOGIN (Mobile)
    public function googleLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'ID Token is required'], 422);
        }

        try {
            $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
            $payload = $client->verifyIdToken($request->id_token);

            if (!$payload) {
                return response()->json(['success' => false, 'message' => 'Invalid Google Token'], 401);
            }

            $email = $payload['email'];
            $name = $payload['name'] ?? $payload['given_name'] . ' ' . ($payload['family_name'] ?? '');
            $googleId = $payload['sub'];
            $avatarUrl = $payload['picture'] ?? null;

            $user = User::where('email', $email)->orWhere('google_id', $googleId)->first();

            if (!$user) {
                $user = User::create([
                    'full_name' => $name,
                    'username' => $this->generateAvailableUsername(null, $name, $email),
                    'email' => $email,
                    'google_id' => $googleId,
                    'social_provider' => 'google',
                    'avatar_url' => $avatarUrl,
                    'role' => 'user',
                    'active' => true,
                    'password' => Hash::make(Str::random(24)), // Random password for social login
                ]);
            } else {
                // Update social info if already exists but was regular user
                if (!$user->google_id) {
                    $user->google_id = $googleId;
                    $user->social_provider = 'google';
                    $user->save();
                }
            }

            $userRole = strtolower($user->role ?? '');
            try {
                $spatieRole = $user->getRoleNames()->first();
                if ($spatieRole) {
                    $userRole = strtolower($spatieRole);
                }
            } catch (\Throwable $e) {
                Log::warning('getRoleNames failed in googleLogin', ['message' => $e->getMessage()]);
            }

            if (!in_array($userRole, ['user', 'ahli_gizi'], true)) {
                return response()->json([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Role ini tidak dapat login melalui API. Gunakan website untuk admin.',
                ], 403);
            }

            $token = $this->issueAuthToken($user);
            $this->updateLoginStreak($user);
            $user->last_login = now();
            $user->save();

            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => 'Google Login successful.',
                'user' => $user->fresh(),
                'token' => $token,
            ]);
        } catch (\Throwable $e) {
            Log::error('Google Login error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Authentication failed'], 500);
        }
    }

    // FACEBOOK LOGIN (Mobile)
    public function facebookLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'access_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Access Token is required'], 422);
        }

        try {
            $fbToken = $request->access_token;
            $client = new GuzzleClient();
            
            // Verify token with Facebook Graph API
            $response = $client->get("https://graph.facebook.com/me", [
                'query' => [
                    'fields' => 'id,name,email,picture',
                    'access_token' => $fbToken
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (!isset($data['id'])) {
                return response()->json(['success' => false, 'message' => 'Invalid Facebook Token'], 401);
            }

            $email = $data['email'] ?? ($data['id'] . '@facebook.com');
            $name = $data['name'];
            $fbId = $data['id'];
            $avatarUrl = $data['picture']['data']['url'] ?? null;

            $user = User::where('email', $email)->orWhere('facebook_id', $fbId)->first();

            if (!$user) {
                $user = User::create([
                    'full_name' => $name,
                    'username' => $this->generateAvailableUsername(null, $name, $email),
                    'email' => $email,
                    'facebook_id' => $fbId,
                    'social_provider' => 'facebook',
                    'avatar_url' => $avatarUrl,
                    'role' => 'user',
                    'active' => true,
                    'password' => Hash::make(Str::random(24)),
                ]);
            } else {
                if (!$user->facebook_id) {
                    $user->facebook_id = $fbId;
                    $user->social_provider = 'facebook';
                    $user->save();
                }
            }

            $userRole = strtolower($user->role ?? '');
            try {
                $spatieRole = $user->getRoleNames()->first();
                if ($spatieRole) {
                    $userRole = strtolower($spatieRole);
                }
            } catch (\Throwable $e) {
                Log::warning('getRoleNames failed in facebookLogin', ['message' => $e->getMessage()]);
            }

            if (!in_array($userRole, ['user', 'ahli_gizi'], true)) {
                return response()->json([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Role ini tidak dapat login melalui API. Gunakan website untuk admin.',
                ], 403);
            }

            $token = $this->issueAuthToken($user);
            $this->updateLoginStreak($user);
            $user->last_login = now();
            $user->save();

            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => 'Facebook Login successful.',
                'user' => $user->fresh(),
                'token' => $token,
            ]);
        } catch (\Throwable $e) {
            Log::error('Facebook Login error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Authentication failed'], 500);
        }
    }

    // LOGOUT
    public function logout(Request $request)
    {
        $currentToken = $request->user()?->currentAccessToken();

        if ($currentToken) {
            $currentToken->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => true,
                'message' => 'Jika email terdaftar, kode OTP akan dikirimkan.'
            ]);
        }

        PasswordResetOtp::where('email', $request->email)->delete();

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        PasswordResetOtp::create([
            'email' => $request->email,
            'otp' => bcrypt($otp),
            'expires_at' => now()->addMinutes(10),
        ]);

        try {
            Mail::to($request->email)->send(new OtpMail($otp, $user->full_name ?? $user->username));
        } catch (\Throwable $e) {
            Log::error('Failed to send OTP email: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP telah dikirim ke email Anda.'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $record = PasswordResetOtp::where('email', $request->email)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$record || !Hash::check($request->otp, $record->otp)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP tidak valid atau sudah kadaluarsa.'
            ], 422);
        }

        $resetToken = Str::uuid()->toString();

        $record->update([
            'reset_token' => $resetToken,
            'expires_at' => now()->addMinutes(15),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP valid.',
            'reset_token' => $resetToken,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'reset_token' => 'required|string',
            'password' => 'required|min:8|confirmed',
        ]);

        $record = PasswordResetOtp::where('reset_token', $request->reset_token)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid atau sudah kadaluarsa.'
            ], 422);
        }

        $user = User::where('email', $record->email)->first();
        $user->update(['password' => Hash::make($request->password)]);

        $record->update(['is_used' => true]);

        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil direset. Silakan login.'
        ]);
    }
    // SYNC USER (FROM FIREBASE)
    public function syncUser(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'firebase_uid' => 'required|string',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Update user properties
        if ($request->has('fcm_token') && !empty($request->fcm_token)) {
            $user->fcm_token = $request->fcm_token;
        }
        
        // Save the firebase UID if we decide to store it
        // $user->firebase_uid = $request->firebase_uid; 
        
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User synced successfully',
            'user' => $user
        ]);
    }

    private function normalizeRegisterPayload(Request $request): array
    {
        $name = trim((string) ($request->input('name') ?? $request->input('full_name') ?? ''));
        $email = trim((string) $request->input('email', ''));
        $requestedUsername = trim((string) $request->input('username', ''));
        $phone = trim((string) ($request->input('phone') ?? $request->input('phone_number') ?? ''));

        return [
            'name' => $name,
            'email' => $email,
            'username' => $this->generateAvailableUsername($requestedUsername, $name, $email),
            'password' => (string) $request->input('password', ''),
            'password_confirmation' => (string) ($request->input('password_confirmation')
                ?? $request->input('confirm_password')
                ?? ''),
            'phone' => $phone,
            'blood_type' => $this->normalizeBloodType($request->input('blood_type')),
            'allergy' => $this->normalizeTextField($request->input('allergy', $request->input('allergies'))),
            'medical_history' => $this->normalizeTextField($request->input('medical_history')),
            'weight_kg' => $request->input('weight_kg'),
        ];
    }

    private function normalizeBloodType(mixed $bloodType): ?string
    {
        $value = trim((string) ($bloodType ?? ''));
        if ($value === '' || Str::lower($value) === 'tidak tahu' || Str::lower($value) === 'unknown') {
            return null;
        }

        return strtoupper($value);
    }

    private function normalizeTextField(mixed $value): ?string
    {
        if (is_array($value)) {
            $value = implode(', ', array_filter(array_map('trim', $value)));
        }

        $text = trim((string) ($value ?? ''));

        return $text !== '' ? $text : null;
    }

    private function generateAvailableUsername(?string $username, string $name, string $email): string
    {
        $candidate = Str::of($username ?: Str::before($email, '@') ?: $name)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9_]+/', '_')
            ->trim('_')
            ->value();

        $candidate = $candidate !== '' ? $candidate : 'user';
        $baseCandidate = Str::limit($candidate, 40, '');
        $suffix = 0;

        while (User::where('username', $candidate)->exists()) {
            $suffix++;
            $candidate = Str::limit($baseCandidate, max(1, 40 - strlen((string) $suffix) - 1), '') . '_' . $suffix;
        }

        return $candidate;
    }

    private function issueAuthToken(User $user): string
    {
        if ($this->tokenTableSupportsExpiry()) {
            return $user->createToken('auth_token')->plainTextToken;
        }

        Log::warning('Sanctum token table is missing expires_at column. Falling back to legacy token insert.', [
            'user_id' => $user->getKey(),
            'email' => $user->email,
        ]);

        $plainTextToken = $user->generateTokenString();
        $token = $user->tokens()->make([
            'name' => 'auth_token',
            'token' => hash('sha256', $plainTextToken),
            'abilities' => ['*'],
        ]);
        $token->save();

        return $token->getKey() . '|' . $plainTextToken;
    }

    private function tokenTableSupportsExpiry(): bool
    {
        return Schema::hasTable('personal_access_tokens')
            && Schema::hasColumn('personal_access_tokens', 'expires_at');
    }

    private function updateLoginStreak(User $user): void
    {
        $today = Carbon::today();
        $lastActive = $user->last_active_date
            ? Carbon::parse($user->last_active_date)->startOfDay()
            : null;

        if (!$lastActive) {
            $user->current_streak = 1;
            $user->longest_streak = max(1, (int) ($user->longest_streak ?? 0));
        } elseif ($lastActive->diffInDays($today) === 1) {
            $user->current_streak = (int) ($user->current_streak ?? 0) + 1;
            $user->longest_streak = max((int) ($user->longest_streak ?? 0), (int) $user->current_streak);
        } elseif ($lastActive->diffInDays($today) > 1) {
            $user->current_streak = 1;
        }

        $user->last_active_date = $today->toDateString();
    }
}
