<?php

// ================= MODUL PETUGAS (Ilham) =================
// Bikin controller-nya di app/Http/Controllers/Petugas/:
// DashboardController.php, ReservationController.php, ReportController.php
// Tiap kali ganti status reservasi/laporan, insert 1 baris log ke
// App\Models\LogStatusReservasi / LogStatusLaporan (kolom status_before,
// status_after, changed_by, notes) — belum ada modul lain yang nyentuh ini.

use App\Http\Controllers\Petugas\DashboardController;
use App\Http\Controllers\Petugas\ReportController;
use App\Http\Controllers\Petugas\ReservationController;
use Illuminate\Support\Facades\Route;

Route::prefix('petugas')->middleware(['auth', 'role:petugas'])->name('petugas.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard'); // story #8: antrian reservasi + laporan

    Route::prefix('reservasi')->name('reservations.')->group(function () {
        Route::get('/', [ReservationController::class, 'index'])->name('index');
        Route::get('/{reservasi}', [ReservationController::class, 'show'])->whereUuid('reservasi')->name('show'); // detail lengkap (pop-up AJAX / halaman penuh)
        Route::patch('/{reservasi}/approve', [ReservationController::class, 'approve'])->whereUuid('reservasi')->name('approve'); // cek bentrok jadwal
        Route::patch('/{reservasi}/reject', [ReservationController::class, 'reject'])->whereUuid('reservasi')->name('reject');
        Route::patch('/{reservasi}/cancel', [ReservationController::class, 'cancel'])->whereUuid('reservasi')->name('cancel'); // wajib isi alasan
    });

    Route::prefix('laporan')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/{laporan}', [ReportController::class, 'show'])->whereUuid('laporan')->name('show');
        Route::patch('/{laporan}/status', [ReportController::class, 'updateStatus'])->whereUuid('laporan')->name('update-status'); // + catatan resolusi, toggle facility_status
    });
});
