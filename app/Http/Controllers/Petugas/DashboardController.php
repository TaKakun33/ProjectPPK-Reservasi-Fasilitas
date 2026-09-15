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
       // Jumlah reservasi yang statusnya masih 'pending' (menunggu diproses).
        $pendingReservations = Reservation::where('reservation_status', 'pending')->count();

        // Jumlah laporan yang statusnya masih 'baru' (belum diproses).
        $newReports = Report::where('report_status', 'baru')->count();

        return view('petugas.dashboard', compact('pendingReservations', 'newReports'));
    }
}
