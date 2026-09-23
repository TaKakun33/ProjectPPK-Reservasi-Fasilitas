<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
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

    public function verify(User $user)
    {
        $user->update(['account_status' => 'verified']);

        return redirect()->route('admin.users.index')
            ->with('success', "Akun {$user->name} berhasil diverifikasi.");
    }

    public function reject(User $user)
    {
        $user->update(['account_status' => 'rejected']);

        return redirect()->route('admin.users.index')
            ->with('success', "Akun {$user->name} telah ditolak.");
    }

    /**
     * Bekukan akun yang sebelumnya sudah verified — dulu ini yang
     * seharusnya jadi fungsi is_active=false, tapi is_active tidak pernah
     * dicek di alur login (AuthenticatedSessionController hanya mengecek
     * account_status). Sekarang 'suspended' otomatis ikut tercek di sana,
     * jadi akun yang di-suspend memang benar-benar tidak bisa login.
     */
    public function suspend(User $user)
    {
        $user->update(['account_status' => 'suspended']);

        return redirect()->route('admin.users.index')
            ->with('success', "Akun {$user->name} berhasil dibekukan.");
    }

    public function reactivate(User $user)
    {
        $user->update(['account_status' => 'verified']);

        return redirect()->route('admin.users.index')
            ->with('success', "Akun {$user->name} berhasil diaktifkan kembali.");
    }
}