<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // pastikan model User dipanggil

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login-new'); 
    }

    /**
     * Redirect users after login based on their role.
     */
    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            // User portal has been decommissioned — redirect to app download
            Auth::logout();
            return redirect()->route('download')->with('error', 'Portal user hanya tersedia di aplikasi Android.');
        }
        return redirect()->route('login');
    }

    // ✅ Proses login
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // ✅ Cek status akun
            if (!$user->active) {
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'username' => 'Akun anda tidak aktif. Silakan hubungi admin.',
                ]);
            }

            // ✅ Update last_login
            $user->last_login = now();
            $user->save();

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            // User portal decommissioned — block web login for non-admin
            Auth::logout();
            return redirect()->route('download')->with('error', 'Silakan gunakan aplikasi Android untuk mengakses fitur pengguna.');
        }

        // ✅ Kalau gagal login
        return redirect()->route('login')->with('error', 'Username atau password salah.');
    }

    // ✅ Logout
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Berhasil logout.');
    }

    // ✅ Toggle suspend/aktif user (admin tidak bisa disuspend)
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        // Jangan biarkan admin disuspend
        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Akun admin tidak boleh disuspend.');
        }

        // Jangan biarkan user menyuspend dirinya sendiri
        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'Anda tidak bisa menyuspend akun Anda sendiri.');
        }

        // Toggle active
        $user->active = !$user->active;
        $user->save();

        $msg = $user->active ? 'Akun berhasil diaktifkan.' : 'Akun berhasil disuspend.';
        return redirect()->back()->with('success', $msg);
    }
}
