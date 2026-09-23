<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Report;
use App\Models\Reservation;
use Illuminate\Http\Request;

// Dashboard untuk petugas
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'reservasi_pending'   => Reservation::where('reservation_status', 'pending')->count(),
            'reservasi_approved'  => Reservation::where('reservation_status', 'approved')->count(),
            'laporan_baru'        => Report::where('report_status', 'baru')->count(),
            'laporan_diproses'    => Report::where('report_status', 'diproses')->count(),
            'fasilitas_perbaikan' => Facility::where('facility_status', 'dalam perbaikan')->count(),
            'fasilitas_aktif'     => Facility::where('facility_status', 'aktif')->count(),
        ];

        // Reservasi terbaru yang relevan untuk dipantau petugas
        $recentReservations = Reservation::with(['user', 'facility'])
            ->latest('created_at')
            ->limit(5)
            ->get();

        // Laporan kerusakan terbaru yang relevan untuk ditangani petugas
        $recentReports = Report::with(['user', 'facility', 'category'])
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('petugas.dashboard', compact('stats', 'recentReservations', 'recentReports'));
    }
}
