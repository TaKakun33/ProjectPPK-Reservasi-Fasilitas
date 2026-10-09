<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BuatAkunRequest;
use App\Models\LogStatusReservasi;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Controller untuk manajemen pengguna dan verifikasi akun oleh Admin
class UserController extends Controller
{
    // Escape karakter wildcard LIKE supaya input pencarian diperlakukan sebagai teks biasa
    private function escapeLike(string $nilai): string
    {
        return addcslashes($nilai, '%_\\');
    }

    // Menampilkan daftar pengguna dengan filter status akun, role, dan pencarian
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('status') && in_array($request->status, ['pending', 'verified', 'rejected', 'suspended'], true)) {
            $query->where('account_status', $request->status);
        }

        if ($request->filled('role') && in_array($request->role, ['pengguna', 'petugas', 'admin'], true)) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $this->escapeLike(mb_substr((string) $request->search, 0, 100));
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
    public function store(BuatAkunRequest $request)
    {
        $validated = $request->validated();

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

    // Menyetujui/verifikasi akun pengguna yang mendaftar mandiri (dari status pending, atau rejected
    // bila admin salah menolak sehingga pendaftar sah tidak buntu)
    public function verify(User $user): RedirectResponse
    {
        if (! in_array($user->account_status, ['pending', 'rejected'], true)) {
            return back()->with('error', 'Hanya akun berstatus pending atau rejected yang dapat diverifikasi.');
        }

        $user->update(['account_status' => 'verified']);

        return redirect()->route('admin.users.index')
            ->with('success', "Akun {$user->name} berhasil diverifikasi.");
    }

    // Menolak pendaftaran akun pengguna (hanya dari status pending, bukan akun admin/sendiri)
    public function reject(User $user): RedirectResponse
    {
        if ($user->is(auth()->user()) || $user->role === UserRole::Admin) {
            return back()->with('error', 'Akun admin tidak dapat ditolak.');
        }

        if ($user->account_status !== 'pending') {
            return back()->with('error', 'Hanya pendaftaran berstatus pending yang dapat ditolak.');
        }

        $user->update(['account_status' => 'rejected']);

        return redirect()->route('admin.users.index')
            ->with('success', "Akun {$user->name} telah ditolak.");
    }

    // Membekukan akun yang sebelumnya verified; sesi aktifnya ikut diputus
    public function suspend(User $user): RedirectResponse
    {
        // PERBAIKAN E11: admin tidak boleh mengunci dirinya sendiri / admin lain
        if ($user->is(auth()->user()) || $user->role === UserRole::Admin) {
            return back()->with('error', 'Akun admin tidak dapat dibekukan.');
        }

        if ($user->account_status !== 'verified') {
            return back()->with('error', 'Hanya akun terverifikasi yang dapat dibekukan.');
        }

        $adminId = auth()->user()->id_user;
        $ditolak = 0;

        DB::transaction(function () use ($user, $adminId, &$ditolak) {
            $user->update(['account_status' => 'suspended']);

            // PERBAIKAN E2: putus semua sesi aktif (SESSION_DRIVER=database)
            DB::table('sessions')->where('user_id', $user->id_user)->delete();

            // Akun beku tidak boleh menahan slot: reservasi pending miliknya yang belum berlangsung
            // ditolak otomatis (tercatat di log). Reservasi approved dibiarkan: pembatalannya
            // keputusan petugas (wajib beralasan, User Story #10).
            $pending = Reservation::where('id_user', $user->id_user)
                ->where('reservation_status', 'pending')
                ->mendatang()
                ->lockForUpdate()
                ->get();

            $alasan = 'Akun pemesan dibekukan oleh admin.';

            foreach ($pending as $reservasi) {
                $reservasi->update([
                    'reservation_status' => 'rejected',
                    'alasan_ditolak'     => $alasan,
                    'processed_by'       => $adminId,
                ]);

                LogStatusReservasi::create([
                    'id_reservasi'  => $reservasi->id_reservasi,
                    'status_before' => 'pending',
                    'status_after'  => 'rejected',
                    'changed_by'    => $adminId,
                    'notes'         => $alasan,
                    'created_at'    => now(),
                ]);

                $ditolak++;
            }
        });

        $pesan = "Akun {$user->name} berhasil dibekukan.";

        if ($ditolak > 0) {
            $pesan .= " {$ditolak} reservasi pending miliknya ditolak otomatis.";
        }

        return redirect()->route('admin.users.index')
            ->with('success', $pesan);
    }

    // Mengaktifkan kembali akun pengguna yang dibekukan
    public function reactivate(User $user): RedirectResponse
    {
        if ($user->account_status !== 'suspended') {
            return back()->with('error', 'Hanya akun yang dibekukan yang dapat diaktifkan kembali.');
        }

        $user->update(['account_status' => 'verified']);

        return redirect()->route('admin.users.index')
            ->with('success', "Akun {$user->name} berhasil diaktifkan kembali.");
    }
}
