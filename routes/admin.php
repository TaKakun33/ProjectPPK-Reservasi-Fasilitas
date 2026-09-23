<?php

// ================= MODUL ADMIN (Abhista) =================
// Bikin controller-nya di app/Http/Controllers/Admin/:
//   DashboardController.php, FacilityController.php, UserController.php, RekapController.php
//
// Penting soal field di tabel facilities (koordinasi sama Ilham):
// - facility_status (string) -> SATU kolom untuk semua state fasilitas:
//   'aktif' / 'dalam perbaikan' (dari modul petugas) / 'nonaktif' (admin
//   nonaktifkan lewat CRUD di sini). is_active sudah dihapus karena dulu
//   dua kolom ini saling tumpang tindih (harus dicek bareng di
//   ReservationController) — sekarang tinggal satu sumber kebenaran.

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RekapController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Story #16: kelola data fasilitas (tambah/edit/nonaktifkan).
    // NOTE: parameter dipaksa "fasilitas" (bukan hasil auto-singular "fasilita")
    // supaya cocok dengan nama variabel $fasilitas di FacilityController,
    // karena implicit route model binding di Laravel mencocokkan berdasarkan NAMA,
    // bukan cuma tipe. Tanpa ini, binding gagal diam-diam dan controller menerima
    // instance Facility kosong, bukan hasil query dari DB.
    Route::resource('fasilitas', FacilityController::class)
        ->except(['show'])
        ->parameters(['fasilitas' => 'fasilitas']);

    // Aktifkan kembali fasilitas yang sudah dinonaktifkan (kebalikan dari destroy).
    Route::patch('/fasilitas/{fasilitas}/activate', [FacilityController::class, 'activate'])
        ->name('fasilitas.activate');

    // Story #13, #14, #15: daftarkan akun petugas/pengguna langsung + verifikasi registrasi mandiri.
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}/verify', [UserController::class, 'verify'])->name('users.verify');
    Route::patch('/users/{user}/reject', [UserController::class, 'reject'])->name('users.reject');
    Route::patch('/users/{user}/suspend', [UserController::class, 'suspend'])->name('users.suspend');
    Route::patch('/users/{user}/reactivate', [UserController::class, 'reactivate'])->name('users.reactivate');

    // Story #17: rekap okupansi & frekuensi kerusakan, export CSV/Excel/PDF.
    Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
    Route::get('/rekap/export', [RekapController::class, 'export'])->name('rekap.export');
});