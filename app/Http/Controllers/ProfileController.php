<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\LogStatusReservasi;
use App\Models\Report;
use App\Models\Reservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    // Menampilkan formulir profil pengguna
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Ringkasan aktivitas (hanya relevan untuk pengguna kampus)
        $statistik = null;
        if ($user->role === UserRole::Pengguna) {
            $statistik = [
                'reservasi' => Reservation::where('id_user', $user->id_user)->count(),
                'laporan'   => Report::where('id_user', $user->id_user)->count(),
            ];
        }

        return view('profile.edit', [
            'user'      => $user,
            'statistik' => $statistik,
        ]);
    }

    // Update informasi profil pengguna
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Hanya name & email yang boleh diubah lewat form ini (current_password hanya untuk validasi)
        $request->user()->fill($request->safe()->only(['name', 'email']));

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    // Menghapus akun pengguna
    public function destroy(Request $request): RedirectResponse
    {
        // PERBAIKAN E11: admin & petugas tidak boleh menghapus akunnya sendiri lewat profil
        abort_unless($request->user()->role === UserRole::Pengguna, 403, 'Akun admin/petugas hanya dapat dihapus oleh admin.');

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Logout lebih dulu (urutan asli): logout menyimpan token "remember me" sebelum akun di-soft-delete
        Auth::logout();

        DB::transaction(function () use ($user) {
            // Batalkan reservasi aktif milik pemesan agar slotnya tidak terblokir oleh akun yang sudah dihapus
            $aktif = Reservation::where('id_user', $user->id_user)
                ->whereIn('reservation_status', ['pending', 'approved'])
                ->lockForUpdate()
                ->get();

            foreach ($aktif as $reservasi) {
                $statusSebelum = $reservasi->reservation_status;

                $reservasi->update([
                    'reservation_status'  => 'cancelled',
                    'cancellation_reason' => 'Akun pemesan dihapus.',
                ]);

                LogStatusReservasi::create([
                    'id_reservasi'  => $reservasi->id_reservasi,
                    'status_before' => $statusSebelum,
                    'status_after'  => 'cancelled',
                    'changed_by'    => $user->id_user,
                    'notes'         => 'Dibatalkan otomatis karena akun pemesan dihapus.',
                    'created_at'    => now(),
                ]);
            }

            $user->delete();
        });

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
