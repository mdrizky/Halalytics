<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    // Tampilkan form register
    public function showRegisterForm()
    {
        return view('register'); // pastikan ada file register.blade.php
    }

    // Proses register
    public function registeraction(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        // Simpan user baru
        User::create([
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password), // ✅ aman
            'role'     => 'user',
            'active'   => 1,
        ]);

        // Redirect ke login
        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }
}
