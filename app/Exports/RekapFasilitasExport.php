<?php

namespace App\Exports;

use App\Models\Facility;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Export data ke excel
class RekapFasilitasExport implements FromArray, WithStyles, WithColumnWidths
{
    /**
     * Cegah Excel formula injection: kalau nilai string diawali karakter
     * yang ditafsirkan Excel/Sheets/LibreOffice sebagai awal formula
     * ('=', '+', '-', '@', atau tab/CR buat menyamarkan awalan itu),
     * tambahkan apostrof di depan supaya dibaca sebagai teks biasa.
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

    public function array(): array
    {
        $timestampWib = now('Asia/Jakarta')->format('d-m-Y H:i') . ' WIB';

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

        $rows = [];

        // Baris judul & timestamp
        $rows[] = ['Rekap Okupansi & Kerusakan Fasilitas'];
        $rows[] = ['Didownload pada: ' . $timestampWib];
        $rows[] = ['']; 

        // Header kolom 
        $rows[] = [
            'Nama Fasilitas',
            'Tipe',
            'Lokasi',
            'Kapasitas',
            'Status',
            'Total Reservasi',
            'Reservasi Approved',
            'Total Laporan',
            'Laporan Selesai',
        ];

        // Data 
        foreach ($facilities as $f) {
            $rows[] = [
                $this->sanitizeForSpreadsheet($f->facility_name),
                $this->sanitizeForSpreadsheet($f->type),
                $this->sanitizeForSpreadsheet($f->location),
                $f->capacity,
                $this->sanitizeForSpreadsheet(ucfirst($f->facility_status)),
                $f->total_reservasi,
                $f->reservasi_approved,
                $f->total_laporan,
                $f->laporan_selesai,
            ];
        }
        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        // Hitung jumlah baris total
        $lastRow = max($sheet->getHighestRow(), 5);

        // Set font Times New Roman untuk seluruh sheet 
        $sheet->getStyle("A1:I{$lastRow}")->getFont()->setName('Times New Roman');
        $sheet->getStyle("A5:I{$lastRow}")->getFont()->setBold(false);

        return [
            // Baris judul 
            1 => ['font' => ['bold' => true, 'size' => 14, 'name' => 'Times New Roman']],
            // Baris timestamp 
            2 => ['font' => ['italic' => true, 'size' => 10, 'name' => 'Times New Roman']],
            // Baris header kolom 
            'A4:I4' => ['font' => ['bold' => true, 'name' => 'Times New Roman'],
                        'fill' => ['fillType'   => Fill::FILL_SOLID,'startColor' => ['argb' => 'FFE0E0E0']],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 18,
            'C' => 28,
            'D' => 12,
            'E' => 15,
            'F' => 16,
            'G' => 20,
            'H' => 16,
            'I' => 16,
        ];
    }
}