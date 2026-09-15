<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Report;
use App\Models\Reservation;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_fasilitas' => Facility::count(),
            'fasilitas_aktif' => Facility::where('is_active', true)->count(),
            'reservasi_pending' => Reservation::where('reservation_status', 'pending')->count(),
            'laporan_baru' => Report::where('report_status', 'baru')->count(),
            'user_pending' => User::where('account_status', 'pending')->count(),
            'total_user' => User::count(),
        ];

        $recentReservations = Reservation::with(['user', 'facility'])
            ->latest('created_at')
            ->limit(5)
            ->get();

        $recentReports = Report::with(['user', 'facility', 'category'])
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentReservations', 'recentReports'));
    }
}
