<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// TODO(Ilham): ubah status laporan + catatan resolusi, dan toggle
// Facility::facility_status ('aktif'/'dalam perbaikan') di sini. Tiap ganti
// status, insert baris ke App\Models\LogStatusLaporan.
class ReportController extends Controller
{
    public function index(Request $request)
    {
        return response('TODO(Ilham): antrian laporan yang belum diproses.');
    }

    public function updateStatus(Request $request, string $laporan)
    {
        return response("TODO(Ilham): update status laporan {$laporan} + catatan resolusi.");
    }
}
