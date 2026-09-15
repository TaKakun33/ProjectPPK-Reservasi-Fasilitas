<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Report;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class RekapController extends Controller
{
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
                'is_active'          => $f->is_active,
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

    public function export(Request $request)
    {
        $format = $request->query('format', 'csv');

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
                'Status Aktif'        => $f->is_active ? 'Ya' : 'Tidak',
                'Total Reservasi'     => $f->total_reservasi,
                'Reservasi Approved'  => $f->reservasi_approved,
                'Total Laporan'       => $f->total_laporan,
                'Laporan Selesai'     => $f->laporan_selesai,
            ];
        });

        if ($format === 'csv') {
            $headers = [
                'Content-Type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename="rekap_fasilitas.csv"',
            ];

            $callback = function () use ($data) {
                $file = fopen('php://output', 'w');
                if ($data->isNotEmpty()) {
                    fputcsv($file, array_keys($data->first()));
                }
                foreach ($data as $row) {
                    fputcsv($file, $row);
                }
                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->route('admin.rekap.index')
            ->with('error', 'Format export belum didukung. Gunakan ?format=csv.');
    }
}
