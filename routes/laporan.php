<?php

// ================= MODUL LAPORAN KERUSAKAN (Akbar) =================
// Bikin controller-nya sendiri:
//   app/Http/Controllers/ReportController.php
// Jangan lupa `php artisan storage:link` biar foto laporan bisa diakses
// publik lewat /storage/... (disk 'public' udah ada di config/filesystems.php).

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// Wajib login — story #6 & #7.
// Catatan: route /laporan ini khusus buat role "pengguna". Pembatasan
// per-role (petugas dilempar ke /petugas/laporan, admin ditolak) dicek
// langsung di ReportController@ensurePengguna(), bukan lewat middleware
// closure di route — Laravel 13 nge-cast middleware jadi string pas
// registrasi route sehingga Closure tidak bisa dipakai di sini.
Route::middleware('auth')->prefix('laporan')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index'); // riwayat + status laporan sendiri
    Route::get('/create', [ReportController::class, 'create'])->name('create'); // form: kategori, deskripsi, foto
    Route::post('/', [ReportController::class, 'store'])->name('store');
    Route::get('/{laporan:id_laporan}', [ReportController::class, 'show'])->name('show'); // detail status
});