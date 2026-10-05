<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Report;
use App\Models\Reservation;
use App\Services\ReservationExpiry;
use Illuminate\Http\Request;

// Dashboard untuk petugas
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Pastikan antrian dan angka statistik tidak memuat reservasi pending yang sudah kedaluwarsa
        ReservationExpiry::kedaluwarsakanDiamDiam();

        $stats = [
            'reservasi_pending'   => Reservation::where('reservation_status', 'pending')->count(),
            'reservasi_approved'  => Reservation::where('reservation_status', 'approved')->count(),
            'laporan_baru'        => Report::where('report_status', 'baru')->count(),
            'laporan_diproses'    => Report::where('report_status', 'diproses')->count(),
            'fasilitas_perbaikan' => Facility::where('facility_status', 'dalam perbaikan')->count(),
            'fasilitas_aktif'     => Facility::where('facility_status', 'aktif')->count(),
        ];

        // PERBAIKAN (US #8): panel antrian berisi reservasi yang MASIH MENUNGGU, diurutkan dari
        // yang paling lama menunggu, supaya tidak ada yang terlewat. Sebelumnya panel ini hanya
        // menampilkan 5 reservasi terbaru dari semua status.
        $recentReservations = Reservation::with(['user', 'facility'])
            ->where('reservation_status', 'pending')
            ->oldest('created_at')
            ->limit(5)
            ->get();

        // Idem untuk laporan: tampilkan yang berstatus 'baru' (belum disentuh), terlama dulu
        $recentReports = Report::with(['user', 'facility', 'category'])
            ->where('report_status', 'baru')
            ->oldest('created_at')
            ->limit(5)
            ->get();

        return view('petugas.dashboard', compact('stats', 'recentReservations', 'recentReports'));
    }
}
