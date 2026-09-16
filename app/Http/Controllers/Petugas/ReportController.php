<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\LogStatusLaporan;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// TODO(Ilham): ubah status laporan + catatan resolusi, dan toggle
// Facility::facility_status ('aktif'/'dalam perbaikan') di sini. Tiap ganti
// status, insert baris ke App\Models\LogStatusLaporan.
class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Tampilkan laporan yang masih dalam proses penanganan: 'baru' dan 'diproses'.
        // Laporan 'diproses' tetap muncul supaya petugas bisa menandainya 'selesai'.
        $reports = Report::with(['user', 'facility', 'category'])
            ->whereIn('report_status', ['baru', 'diproses'])
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        return view('petugas.reports.index', compact('reports'));
    }

    public function updateStatus(Request $request, string $laporan)
    {
        $report = Report::findOrFail($laporan);

        // Validasi input: status baru + catatan resolusi.
        // Status laporan: 'diproses', 'selesai', 'ditolak'.
        $validated = $request->validate([
            'report_status' => ['required', 'in:diproses,selesai,ditolak'],
            'resolution_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($report, $request, $validated) {
            $statusBefore = $report->report_status;

            // Update laporan: status, catatan resolusi, dan siapa yang menangani.
            $report->update([
                'report_status' => $validated['report_status'],
                'resolution_notes' => $validated['resolution_notes'] ?? $report->resolution_notes,
                'handled_by' => $request->user()->id_user,
            ]);

            // Toggle status fasilitas sesuai status laporan:
            // - laporan 'diproses' -> fasilitas jadi 'dalam perbaikan'
            // - laporan 'selesai'  -> fasilitas kembali 'aktif'
            // - laporan 'ditolak'  -> fasilitas tidak diubah
            if ($validated['report_status'] === 'diproses') {
                Facility::where('id_fasilitas', $report->id_fasilitas)
                    ->update(['facility_status' => 'dalam perbaikan']);
            } elseif ($validated['report_status'] === 'selesai') {
                Facility::where('id_fasilitas', $report->id_fasilitas)
                    ->update(['facility_status' => 'aktif']);
            }

            // Catat log perubahan status ke LogStatusLaporan.
            LogStatusLaporan::create([
                'id_laporan' => $report->id_laporan,
                'status_before' => $statusBefore,
                'status_after' => $validated['report_status'],
                'changed_by' => $request->user()->id_user,
                'notes' => $validated['resolution_notes'] ?? null,
            ]);
        });

        return back()->with('success', 'Status laporan berhasil diperbarui.');
    }
}