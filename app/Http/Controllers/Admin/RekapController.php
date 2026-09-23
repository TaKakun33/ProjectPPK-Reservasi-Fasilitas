<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\RekapFasilitasExport;
use App\Models\Facility;
use App\Models\Report;
use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

// Controller rekap fasilitas (statistik okupansi dan frekuensi kerusakan)
class RekapController extends Controller
{
    /**
     * Cegah CSV/Excel formula injection: kalau nilai string diawali
     * karakter yang ditafsirkan Excel/Sheets/LibreOffice sebagai awal
     * formula ('=', '+', '-', '@', atau tab/CR yang bisa dipakai buat
     * "menyamarkan" awalan itu), tambahkan apostrof di depan supaya
     * dibaca sebagai teks biasa, bukan dieksekusi sebagai formula.
     * Aman dipanggil untuk nilai non-string (int, null, dst) — dikembalikan apa adanya.
     */
    private function sanitizeForSpreadsheet($value)
    {
        if (! is_string($value) || $value === '') {
            return $value;
        }

        if (preg_match('/^[=+\-@\t\r]/', $value)) {
            return "'" . $value;
        }

        return $value;
    }

    // Menampilkan halaman tabel rekapitulasi okupansi reservasi dan kerusakan fasilitas
    public function index(Request $request)
    {
        $facilities = Facility::withCount([
            'reservations as total_reservasi',
            'reservations as reservasi_approved' => function ($q) {
                $q->where('reservation_status', 'approved');
            },
            'reservations as reservasi_pending' => function ($q) {
                $q->where('reservation_status', 'pending');
            },
            'reports as total_laporan',
            'reports as laporan_baru' => function ($q) {
                $q->where('report_status', 'baru');
            },
            'reports as laporan_selesai' => function ($q) {
                $q->where('report_status', 'selesai');
            },
        ])->get();

        $rekap = $facilities->map(function ($f) {
            return [
                'id'                 => $f->id_fasilitas,
                'nama'               => $f->facility_name,
                'tipe'               => $f->type,
                'lokasi'             => $f->location,
                'kapasitas'          => $f->capacity,
                'status'             => $f->facility_status,
                'total_reservasi'    => $f->total_reservasi,
                'reservasi_approved' => $f->reservasi_approved,
                'reservasi_pending'  => $f->reservasi_pending,
                'total_laporan'      => $f->total_laporan,
                'laporan_baru'       => $f->laporan_baru,
                'laporan_selesai'    => $f->laporan_selesai,
            ];
        });

        return view('admin.rekap.index', compact('rekap'));
    }

    /**
     * Export rekap fasilitas dalam format CSV, Excel, atau PDF.
     * Gunakan query parameter ?format=csv|excel|pdf
     */
    public function export(Request $request)
    {
        $format = $request->query('format', 'csv');

        // Excel
        if ($format === 'excel') {
            return Excel::download(new RekapFasilitasExport, 'rekap_fasilitas.xlsx');
        }

        // Data
        $facilities = Facility::withCount([
            'reservations as total_reservasi',
            'reservations as reservasi_approved' => function ($q) {
                $q->where('reservation_status', 'approved');
            },
            'reports as total_laporan',
            'reports as laporan_selesai' => function ($q) {
                $q->where('report_status', 'selesai');
            },
        ])->get();

        $data = $facilities->map(function ($f) {
            return [
                'Nama Fasilitas'      => $f->facility_name,
                'Tipe'                => $f->type,
                'Lokasi'              => $f->location,
                'Kapasitas'           => $f->capacity,
                'Status'              => ucfirst($f->facility_status),
                'Total Reservasi'     => $f->total_reservasi,
                'Reservasi Approved'  => $f->reservasi_approved,
                'Total Laporan'       => $f->total_laporan,
                'Laporan Selesai'     => $f->laporan_selesai,
            ];
        });

        // Timestamp WIB
        $timestampWib = now('Asia/Jakarta')->format('d-m-Y H:i') . ' WIB';

        // CSV
        if ($format === 'csv') {
            // Sanitasi khusus dipakai di sini, BUKAN untuk $data yang dipakai PDF:
            // PDF cuma teks tercetak (bukan file yang dibuka spreadsheet app),
            // jadi tidak berisiko formula injection dan tidak perlu apostrof kosmetik.
            $csvData = $data->map(function ($row) {
                return array_map(fn ($value) => $this->sanitizeForSpreadsheet($value), $row);
            });

            $headers = [
                'Content-Type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename="rekap_fasilitas.csv"',
            ];

            $callback = function () use ($csvData, $timestampWib) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Rekap Okupansi & Kerusakan Fasilitas']);
                fputcsv($file, ['Didownload pada: ' . $timestampWib]);
                fputcsv($file, []); // baris kosong pemisah
                if ($csvData->isNotEmpty()) {
                    fputcsv($file, array_keys($csvData->first()));
                }
                foreach ($csvData as $row) {
                    fputcsv($file, $row);
                }
                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        // PDF
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.rekap.pdf', [
                'data'    => $data,
                'tanggal' => $timestampWib,
            ])->setPaper('a4', 'landscape');

            return $pdf->download('rekap_fasilitas.pdf');
        }

        return redirect()->route('admin.rekap.index')
            ->with('error', 'Format export tidak dikenali. Gunakan csv, excel, atau pdf.');
    }
}