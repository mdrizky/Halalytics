<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    // Menampilkan semua user
    public function admin_user()
    {
        // ✅ ambil semua user + hitung jumlah scan
        $users = User::withCount('scans')->get();

        return view('admin.user', compact('users'));
    }

    // Edit user form
    public function edit($id_user)
    {
        $user = User::findOrFail($id_user);
        return view('admin.user_edit', compact('user'));
    }

    // Update user
    public function update(Request $request, $id_user)
    {
        $user = User::findOrFail($id_user);

        $user->update($request->only(['nama_lengkap','username', 'email', 'role', 'active','last_login']));

        return redirect()->route('admin.user')->with('success', 'User berhasil diperbarui');
    }

    // Hapus user
    public function hapus($id_user)
    {
        $user = User::findOrFail($id_user);
        $user->delete();

        return redirect()->route('admin.user')->with('success', 'User berhasil dihapus');
    }

    // ✅ Toggle status active/non-active
    public function toggleStatus($id_user)
    {
        $user = User::findOrFail($id_user);

        $user->active = $user->active == 1 ? 0 : 1;
        $user->save();

        return redirect()->route('admin.user')->with('success', 'Status akun berhasil diubah!');
    }
}
