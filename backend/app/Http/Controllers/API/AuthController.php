<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'login' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],
            'username' => ['nullable', 'string'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $loginValue = $payload['login'] ?? $payload['email'] ?? $payload['username'] ?? null;
        if (!$loginValue) {
            return response()->json([
                'success' => false,
                'message' => 'Gunakan field login/email/username.',
            ], 422);
        }

        $field = filter_var($loginValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [$field => $loginValue, 'password' => $payload['password']];

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Username/email atau password salah.',
            ], 401);
        }

        $user = $request->user();
        $token = $user->createToken('halalytics-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'avatar' => $user->avatar,
            ],
        ]);
    }
}
