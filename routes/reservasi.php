<?php

// ================= MODUL RESERVASI (Zhafran) =================
// Punya kamu semua ada di sini. Bikin controller-nya sendiri:
//   app/Http/Controllers/FacilityController.php
//   app/Http/Controllers/ReservationController.php

use App\Http\Controllers\FacilityController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

// Publik, TANPA login — story #1 & #2: liat daftar fasilitas + status
// ketersediaan per slot (tersedia/tidak), TANPA detail pemohon/tujuan.
// Sengaja dipisah dari "/" (welcome page) biar konsisten sama pola
// resource lain (admin.fasilitas.* punya Abhista) dan gampang dipakai
// ulang. Kalau user login, controller ini juga yang dipakai —
// bedanya cuma boleh-tidaknya lanjut ke tombol "Ajukan Reservasi".
Route::get('/home', [FacilityController::class, 'index'])->name('facilities.index');
Route::redirect('/fasilitas', '/home');
Route::get('/fasilitas/{fasilitas}', [FacilityController::class, 'show'])->name('facilities.show');

// Wajib login — story #3, #4, #5.
Route::middleware('auth')->prefix('reservasi')->name('reservations.')->group(function () {
    Route::get('/', [ReservationController::class, 'index'])->name('index'); // riwayat + status
    Route::get('/create', [ReservationController::class, 'create'])->name('create'); // form ajukan
    Route::post('/', [ReservationController::class, 'store'])->name('store'); // validasi server: jam operasional, slot 30 menit, bentrok
    Route::delete('/{reservasi}', [ReservationController::class, 'destroy'])->name('destroy'); // batalkan punya sendiri
});
