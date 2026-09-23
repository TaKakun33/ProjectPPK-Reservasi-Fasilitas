<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

// Controller untuk manajemen pengguna dan verifikasi akun oleh Admin
class UserController extends Controller
{
    // Menampilkan daftar pengguna dengan filter status akun, role, dan pencarian
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('status')) {
            $query->where('account_status', $request->status);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        $pendingCount = User::where('account_status', 'pending')->count();

        return view('admin.users.index', compact('users', 'pendingCount'));
    }

    // Mendaftarkan akun petugas atau pengguna baru secara langsung oleh admin
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role'     => 'required|in:petugas,pengguna',
        ]);

        $admin = $request->user();

        User::create([
            'name'           => $validated['name'],
            'email'          => $validated['email'],
            'password'       => Hash::make($validated['password']),
            'role'           => $validated['role'],
            'account_status' => 'verified',
            'registered_by'  => $admin->id_user,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Akun {$validated['role']} berhasil dibuat dan langsung verified.");
    }

    // Menyetujui/verifikasi akun pengguna yang mendaftar mandiri
    public function verify(User $user)
    {
        $user->update(['account_status' => 'verified']);

        return redirect()->route('admin.users.index')
            ->with('success', "Akun {$user->name} berhasil diverifikasi.");
    }

    // Menolak pendaftaran akun pengguna
    public function reject(User $user)
    {
        $user->update(['account_status' => 'rejected']);

        return redirect()->route('admin.users.index')
            ->with('success', "Akun {$user->name} telah ditolak.");
    }

    // Membekukan akun yang sebelumnya sudah verified agar tidak bisa login
    public function suspend(User $user)
    {
        $user->update(['account_status' => 'suspended']);

        return redirect()->route('admin.users.index')
            ->with('success', "Akun {$user->name} berhasil dibekukan.");
    }

    // Mengaktifkan kembali akun pengguna yang dibekukan
    public function reactivate(User $user)
    {
        $user->update(['account_status' => 'verified']);

        return redirect()->route('admin.users.index')
            ->with('success', "Akun {$user->name} berhasil diaktifkan kembali.");
    }
}