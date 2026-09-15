<?php

// ================= MODUL LAPORAN KERUSAKAN (Akbar) =================
// Bikin controller-nya sendiri:
//   app/Http/Controllers/ReportController.php
// Jangan lupa `php artisan storage:link` biar foto laporan bisa diakses
// publik lewat /storage/... (disk 'public' udah ada di config/filesystems.php).

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// Wajib login — story #6 & #7.
Route::middleware('auth')->prefix('laporan')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index'); // riwayat + status laporan sendiri
    Route::get('/create', [ReportController::class, 'create'])->name('create'); // form: kategori, deskripsi, foto
    Route::post('/', [ReportController::class, 'store'])->name('store');
    Route::get('/{laporan:id_laporan}', [ReportController::class, 'show'])->name('show'); // detail status
});
