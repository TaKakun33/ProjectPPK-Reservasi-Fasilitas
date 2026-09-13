<?php

use App\Enums\UserRole;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Alias biar /home juga nunjuk ke halaman yang sama kayak /.
Route::get('/home', function () {
    return view('welcome');
})->name('home');

// /dashboard tetap jadi satu pintu masuk yang sama buat semua role
// (link navbar & redirect lama masih nunjuk ke sini), tapi sekarang
// dia cuma "penerus": admin/petugas langsung dilempar ke dashboard
// masing-masing, pengguna biasa tetap lihat dashboard seperti biasa.
Route::get('/dashboard', function () {
    return match (auth()->user()->role) {
        UserRole::Admin => redirect()->route('admin.dashboard'),
        UserRole::Petugas => redirect()->route('petugas.dashboard'),
        default => view('dashboard'),
    };
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Routes per modul dipisah ke file sendiri-sendiri biar tiap orang cuma
// nambah/edit file miliknya sendiri, bukan blok bareng di sini.
// -> Zhafran: routes/reservasi.php
// -> Akbar:   routes/laporan.php
// -> Ilham:   routes/petugas.php
// -> Abhista: routes/admin.php
require __DIR__.'/reservasi.php';
require __DIR__.'/laporan.php';
require __DIR__.'/petugas.php';
require __DIR__.'/admin.php';

require __DIR__.'/auth.php';
