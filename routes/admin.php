<?php

// ================= MODUL ADMIN (Abhista) =================
// Bikin controller-nya di app/Http/Controllers/Admin/:
//   DashboardController.php, FacilityController.php, UserController.php, RekapController.php
//
// Penting soal field di tabel facilities (koordinasi sama Ilham):
// - is_active (boolean)      -> punya kamu, buat nonaktifkan/aktifkan fasilitas dari CRUD.
// - facility_status (string) -> punya Ilham, 'aktif'/'dalam perbaikan' dari modul petugas.
// Dua field beda tujuan, jangan saling timpa nilainya.

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RekapController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Story #16: kelola data fasilitas (tambah/edit/nonaktifkan).
    Route::resource('fasilitas', FacilityController::class)->except(['show']);

    // Story #13, #14, #15: daftarkan akun petugas/pengguna langsung + verifikasi registrasi mandiri.
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}/verify', [UserController::class, 'verify'])->name('users.verify');
    Route::patch('/users/{user}/reject', [UserController::class, 'reject'])->name('users.reject');

    // Story #17: rekap okupansi & frekuensi kerusakan, export CSV/Excel/PDF.
    Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
    Route::get('/rekap/export', [RekapController::class, 'export'])->name('rekap.export');
});
