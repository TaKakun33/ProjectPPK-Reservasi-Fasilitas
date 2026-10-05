<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Export data rekap ke excel. Data dihitung oleh App\Services\RekapService (sama dengan halaman web, CSV, dan PDF).
class RekapFasilitasExport implements FromArray, WithStyles, WithColumnWidths
{
    // Baris header kolom berada di baris ke-5 (judul, periode, timestamp, baris kosong, lalu header)
    private const BARIS_HEADER = 5;

    /**
     * @param array  $baris        baris rekap dari RekapService::hitung()['baris']
     * @param string $labelPeriode teks periode, mis. "01-10-2026 s/d 04-10-2026"
     */
    public function __construct(private array $baris, private string $labelPeriode)
    {
    }

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

        $rows = [];

        // Baris judul, periode & timestamp
        $rows[] = ['Rekap Okupansi & Kerusakan Fasilitas'];
        $rows[] = ['Periode: ' . $this->labelPeriode];
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
            'Jam Terpakai',
            'Okupansi (%)',
            'Total Laporan',
            'Laporan Selesai',
        ];

        // Data
        foreach ($this->baris as $f) {
            $rows[] = [
                $this->sanitizeForSpreadsheet($f['nama']),
                $this->sanitizeForSpreadsheet($f['tipe']),
                $this->sanitizeForSpreadsheet($f['lokasi']),
                $f['kapasitas'],
                $this->sanitizeForSpreadsheet(ucfirst($f['status'])),
                $f['total_reservasi'],
                $f['reservasi_approved'],
                $f['jam_terpakai'],
                $f['okupansi'],
                $f['total_laporan'],
                $f['laporan_selesai'],
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        $header  = self::BARIS_HEADER;
        $lastRow = max($sheet->getHighestRow(), $header);

        // Set font Times New Roman untuk seluruh sheet
        $sheet->getStyle("A1:K{$lastRow}")->getFont()->setName('Times New Roman');
        $sheet->getStyle('A' . ($header + 1) . ":K{$lastRow}")->getFont()->setBold(false);

        return [
            // Baris judul
            1 => ['font' => ['bold' => true, 'size' => 14, 'name' => 'Times New Roman']],
            // Baris periode & timestamp
            2 => ['font' => ['italic' => true, 'size' => 10, 'name' => 'Times New Roman']],
            3 => ['font' => ['italic' => true, 'size' => 10, 'name' => 'Times New Roman']],
            // Baris header kolom
            "A{$header}:K{$header}" => [
                'font' => ['bold' => true, 'name' => 'Times New Roman'],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE0E0E0']],
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
            'H' => 14,
            'I' => 14,
            'J' => 16,
            'K' => 16,
        ];
    }
}
