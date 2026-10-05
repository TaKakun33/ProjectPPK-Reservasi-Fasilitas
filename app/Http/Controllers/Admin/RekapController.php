<?php

namespace App\Http\Controllers\Admin;

use App\Exports\RekapFasilitasExport;
use App\Http\Controllers\Controller;
use App\Services\RekapService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

// Controller rekap fasilitas (okupansi reservasi dan frekuensi kerusakan) dengan filter periode
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

    // Validasi filter periode (?dari=YYYY-MM-DD&sampai=YYYY-MM-DD) lalu hitung rekapnya
    private function ambilRekap(Request $request): array
    {
        $aturanSampai = ['nullable', 'date_format:Y-m-d'];

        if ($request->filled('dari')) {
            $aturanSampai[] = 'after_or_equal:dari';
        }

        $validated = $request->validate([
            'dari'   => ['nullable', 'date_format:Y-m-d'],
            'sampai' => $aturanSampai,
        ], [
            'dari.date_format'      => 'Format tanggal awal harus YYYY-MM-DD.',
            'sampai.date_format'    => 'Format tanggal akhir harus YYYY-MM-DD.',
            'sampai.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal awal.',
        ]);

        $periode = RekapService::periode($validated['dari'] ?? null, $validated['sampai'] ?? null);

        if ($periode['hari'] > RekapService::MAKS_HARI) {
            // Batasi rentang agar halaman & ekspor tetap ringan
            $periode = RekapService::periode(
                Carbon::createFromFormat('Y-m-d', $periode['sampai'])->subDays(RekapService::MAKS_HARI - 1)->toDateString(),
                $periode['sampai']
            );
        }

        return RekapService::hitung($periode['dari'], $periode['sampai']);
    }

    // Menampilkan halaman tabel rekapitulasi okupansi reservasi dan kerusakan fasilitas
    public function index(Request $request)
    {
        $hasil = $this->ambilRekap($request);

        return view('admin.rekap.index', [
            'rekap'     => collect($hasil['baris']),
            'perLokasi' => collect($hasil['per_lokasi']),
            'periode'   => $hasil['periode'],
        ]);
    }

    /**
     * Export rekap fasilitas dalam format CSV, Excel, atau PDF.
     * Gunakan query parameter ?format=csv|excel|pdf (+ dari/sampai untuk periode)
     */
    public function export(Request $request)
    {
        $format = $request->query('format', 'csv');

        if (! in_array($format, ['csv', 'excel', 'pdf'], true)) {
            return redirect()->route('admin.rekap.index')
                ->with('error', 'Format export tidak dikenali. Gunakan csv, excel, atau pdf.');
        }

        $hasil   = $this->ambilRekap($request);
        $periode = $hasil['periode'];

        // Timestamp WIB
        $timestampWib = now('Asia/Jakarta')->format('d-m-Y H:i') . ' WIB';
        $labelPeriode = Carbon::createFromFormat('Y-m-d', $periode['dari'])->format('d-m-Y')
            . ' s/d ' . Carbon::createFromFormat('Y-m-d', $periode['sampai'])->format('d-m-Y');

        // Excel
        if ($format === 'excel') {
            return Excel::download(
                new RekapFasilitasExport($hasil['baris'], $labelPeriode),
                'rekap_fasilitas.xlsx'
            );
        }

        // Data
        $data = collect($hasil['baris'])->map(function ($f) {
            return [
                'Nama Fasilitas'      => $f['nama'],
                'Tipe'                => $f['tipe'],
                'Lokasi'              => $f['lokasi'],
                'Kapasitas'           => $f['kapasitas'],
                'Status'              => ucfirst($f['status']),
                'Total Reservasi'     => $f['total_reservasi'],
                'Reservasi Approved'  => $f['reservasi_approved'],
                'Jam Terpakai'        => $f['jam_terpakai'],
                'Okupansi (%)'        => $f['okupansi'],
                'Total Laporan'       => $f['total_laporan'],
                'Laporan Selesai'     => $f['laporan_selesai'],
            ];
        });

        // CSV
        if ($format === 'csv') {
            // Sanitasi khusus dipakai di sini, BUKAN untuk $data yang dipakai PDF:
            // PDF cuma teks tercetak (bukan file yang dibuka spreadsheet app),
            // jadi tidak berisiko formula injection dan tidak perlu apostrof kosmetik.
            $csvData = $data->map(function ($row) {
                return array_map(fn ($value) => $this->sanitizeForSpreadsheet($value), $row);
            });

            $headers = [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="rekap_fasilitas.csv"',
            ];

            $callback = function () use ($csvData, $timestampWib, $labelPeriode) {
                $file = fopen('php://output', 'w');
                // BOM UTF-8 supaya Excel membaca file sebagai UTF-8 (huruf non-ASCII tidak rusak)
                fwrite($file, "\xEF\xBB\xBF");
                fputcsv($file, ['Rekap Okupansi & Kerusakan Fasilitas']);
                fputcsv($file, ['Periode: ' . $labelPeriode]);
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
        $pdf = Pdf::loadView('admin.rekap.pdf', [
            'data'      => $data,
            'perLokasi' => collect($hasil['per_lokasi']),
            'tanggal'   => $timestampWib,
            'periode'   => $labelPeriode,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('rekap_fasilitas.pdf');
    }
}
