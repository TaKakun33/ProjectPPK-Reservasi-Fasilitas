<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// TODO(Ilham): isi resources/views/petugas/dashboard.blade.php dengan antrian
// reservasi & laporan yang menunggu diproses. View-nya udah pakai
// <x-app-layout> yang sama dengan dashboard pengguna biasa (navbar + logout
// otomatis ikut).
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return view('petugas.dashboard');
    }
}
