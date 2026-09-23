<?php

// ================= MODUL LAPORAN KERUSAKAN (Akbar) =================
// Bikin controller-nya sendiri:
// app/Http/Controllers/ReportController.php
// Foto laporan disimpan di disk 'local' (private, storage/app/private) —
// BUKAN disk 'public'. Jangan pakai storage:link / asset('storage/...')
// buat foto laporan; foto hanya boleh diakses lewat route
// reports.photo di bawah (ada pengecekan otorisasi di ReportController@photo).

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
    Route::get('/foto/{foto:id_foto}', [ReportController::class, 'photo'])->name('photo'); // serve foto privat, dicek otorisasi
});