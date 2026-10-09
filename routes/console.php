<?php

use Illuminate\Support\Facades\Schedule;

// Tolak otomatis reservasi pending yang sudah lewat jadwalnya (jalankan: php artisan schedule:work).
// Cadangan: pembersihan yang sama juga dipanggil otomatis dari halaman petugas & form reservasi.
Schedule::command('reservasi:kedaluwarsakan')->everyFiveMinutes();
