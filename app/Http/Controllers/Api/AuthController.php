<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Carbon\Carbon;

class AuthController extends Controller
{
    // REGISTER USER
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'username' => 'required|string|unique:users', // Existing Logic support
            'weight_kg' => 'nullable|integer',
            'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-,A,B,AB,O',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'full_name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password), // SetPasswordAttribute handles this too but explicit is fine
            'role' => 'user', // Default role
            'phone' => $request->phone,
            'allergy' => $request->allergy,
            'medical_history' => $request->medical_history,
            'weight_kg' => $request->weight_kg,
            'blood_type' => $request->blood_type,
            'active' => true,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    // LOGIN
    public function login(Request $request)
    {
        // Support login by username or email
        $validator = Validator::make($request->all(), [
            'username' => 'required', // Can be email or username
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if input is email or username
        $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        $user = User::where($fieldType, $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if (!$user->active && !$user->is_active) { // Check both fields for backwards compat
            return response()->json(['message' => 'Account is deactivated'], 403);
        }

        // Delete old tokens if needed or just creating new one
        // $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        // --- STREAK LOGIC ---
        $today = Carbon::today();
        $lastActive = $user->last_active_date ? Carbon::parse($user->last_active_date)->startOfDay() : null;

        if (!$lastActive) {
            // First time active
            $user->current_streak = 1;
            $user->longest_streak = 1;
        } elseif ($lastActive->diffInDays($today) == 1) {
            // Logged in yesterday
            $user->current_streak += 1;
            if ($user->current_streak > $user->longest_streak) {
                $user->longest_streak = $user->current_streak;
            }
        } elseif ($lastActive->diffInDays($today) > 1) {
            // Streak broken
            $user->current_streak = 1;
        }
        
        $user->last_active_date = $today->toDateString();
        $user->save();

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'role' => $user->role, // Critical for Role-Based Routing
            'token' => $token,
            'streak' => [
                'current' => $user->current_streak,
                'longest' => $user->longest_streak
            ]
        ]);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    // FORGOT PASSWORD
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak terdaftar atau format tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        $status = Password::sendResetLink([
            'email' => (string) $request->email,
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => 'Instruksi reset password telah dikirim ke email Anda.'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => __($status),
        ], 500);
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
}
