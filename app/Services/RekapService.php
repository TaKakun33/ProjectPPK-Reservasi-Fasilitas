<?php

namespace App\Services;

use App\Models\Facility;
use App\Models\Report;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Satu sumber data rekap (halaman web, CSV, Excel, PDF) supaya angkanya selalu konsisten.
// Okupansi = jam reservasi DISETUJUI yang sudah SELESAI dibanding jam operasional (07:00-20:00)
// pada hari yang tersedia: hanya hari kerja (Senin-Jumat) yang sudah/sedang berjalan, tanpa fasilitas
// nonaktif. Hari akhir pekan tetap dihitung bila fasilitas dipakai pada hari itu (reservasi approved),
// sehingga okupansi tidak pernah melebihi 100%.
class RekapService
{
    // Batas rentang periode (hari) agar query tetap ringan
    public const MAKS_HARI = 366;

    // "HH:MM" atau "HH:MM:SS" menjadi menit sejak 00:00
    private static function menit(string $jam): int
    {
        $bagian = explode(':', $jam);

        return ((int) ($bagian[0] ?? 0)) * 60 + (int) ($bagian[1] ?? 0);
    }

    // Menit operasional per hari (07:00 - 20:00 = 780 menit)
    public static function menitOperasionalPerHari(): int
    {
        return self::menit(ReservationAvailability::OPERATIONAL_END) - self::menit(ReservationAvailability::OPERATIONAL_START);
    }

    // Periode default: awal bulan berjalan sampai hari ini (WIB)
    public static function periode(?string $dari, ?string $sampai): array
    {
        $zona    = config('app.timezone', 'Asia/Jakarta');
        $hariIni = Carbon::now($zona)->startOfDay();

        $awal  = $dari ? Carbon::createFromFormat('Y-m-d', $dari, $zona)->startOfDay() : $hariIni->copy()->startOfMonth();
        $akhir = $sampai ? Carbon::createFromFormat('Y-m-d', $sampai, $zona)->startOfDay() : $hariIni->copy();

        if ($akhir->lt($awal)) {
            $akhir = $awal->copy();
        }

        $hari = (int) round(($akhir->timestamp - $awal->timestamp) / 86400) + 1;

        return [
            'dari'   => $awal->toDateString(),
            'sampai' => $akhir->toDateString(),
            'hari'   => $hari,
        ];
    }

    // Menit operasional yang sudah berjalan pada satu tanggal: hari lampau penuh, hari ini sebagian, masa depan nol
    private static function menitBerjalan(string $tanggal, string $hariIni, int $menitSekarang): int
    {
        $penuh = self::menitOperasionalPerHari();

        if ($tanggal < $hariIni) {
            return $penuh;
        }

        if ($tanggal > $hariIni) {
            return 0;
        }

        $mulai = self::menit(ReservationAvailability::OPERATIONAL_START);

        return max(0, min($penuh, $menitSekarang - $mulai));
    }

    // Ekspresi SQL durasi (detik) end_time - start_time, agar penjumlahan dilakukan database, bukan PHP
    private static function ekspresiDurasiDetik(): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "(CAST(strftime('%s', end_time) AS INTEGER) - CAST(strftime('%s', start_time) AS INTEGER))",
            'pgsql'  => 'EXTRACT(EPOCH FROM (end_time - start_time))',
            'sqlsrv' => 'DATEDIFF(SECOND, start_time, end_time)',
            default  => '(TIME_TO_SEC(end_time) - TIME_TO_SEC(start_time))',
        };
    }

    // Hitung rekap per fasilitas + agregasi per lokasi untuk periode tertentu
    public static function hitung(?string $dari = null, ?string $sampai = null): array
    {
        $periode = self::periode($dari, $sampai);
        $zona    = config('app.timezone', 'Asia/Jakarta');
        $now     = Carbon::now($zona);
        $hariIni = $now->toDateString();
        $menitSekarang = $now->hour * 60 + $now->minute;

        // Hari kerja (Senin-Jumat) yang sudah berjalan dalam periode beserta menit operasionalnya.
        // Hari akan datang tidak ikut: reservasinya pun belum terjadi.
        $menitHariKerja = [];
        $tanggal        = Carbon::createFromFormat('Y-m-d', $periode['dari'], $zona)->startOfDay();
        $batas          = Carbon::createFromFormat('Y-m-d', $periode['sampai'], $zona)->startOfDay();

        while ($tanggal->lte($batas)) {
            $iso = $tanggal->toDateString();

            if (! $tanggal->isWeekend()) {
                $menit = self::menitBerjalan($iso, $hariIni, $menitSekarang);

                if ($menit > 0) {
                    $menitHariKerja[$iso] = $menit;
                }
            }

            $tanggal->addDay();
        }

        $totalMenitHariKerja = array_sum($menitHariKerja);

        // Jumlah reservasi per fasilitas dan status (berdasarkan tanggal kegiatan)
        $hitungReservasi = [];
        $barisReservasi  = Reservation::selectRaw('id_fasilitas, reservation_status, COUNT(*) as jumlah')
            ->whereBetween('date', [$periode['dari'], $periode['sampai']])
            ->groupBy('id_fasilitas', 'reservation_status')
            ->get();

        foreach ($barisReservasi as $baris) {
            $hitungReservasi[$baris->id_fasilitas][$baris->reservation_status] = (int) $baris->jumlah;
        }

        // Menit terpakai = durasi reservasi disetujui yang SUDAH SELESAI, dijumlahkan di database per
        // fasilitas dan tanggal (jumlah baris dibatasi hari x fasilitas, bukan jumlah reservasi).
        $menitTerpakai = [];
        $hariDipakai   = []; // fasilitas => [tanggal => true], hari tempat fasilitas benar-benar dipakai
        $disetujui     = Reservation::query()
            ->select(['id_fasilitas', 'date'])
            ->selectRaw('SUM(' . self::ekspresiDurasiDetik() . ') as detik')
            ->where('reservation_status', 'approved')
            ->selesai()
            ->whereBetween('date', [$periode['dari'], $periode['sampai']])
            ->groupBy('id_fasilitas', 'date')
            ->toBase()
            ->get();

        foreach ($disetujui as $res) {
            $iso   = substr((string) $res->date, 0, 10);
            $menit = max(0, (int) round(((float) $res->detik) / 60));

            $menitTerpakai[$res->id_fasilitas] = ($menitTerpakai[$res->id_fasilitas] ?? 0) + $menit;
            $hariDipakai[$res->id_fasilitas][$iso] = true;
        }

        // Jumlah laporan per fasilitas dan status (berdasarkan tanggal laporan dibuat)
        $hitungLaporan = [];
        // Rentang created_at (>= awal hari pertama, < awal hari setelah terakhir) agar index created_at terpakai;
        // whereDate() membungkus kolom dengan fungsi DATE() sehingga index tidak bisa dipakai.
        $awalLaporan  = Carbon::createFromFormat('Y-m-d', $periode['dari'], $zona)->startOfDay()->format('Y-m-d H:i:s');
        $akhirLaporan = Carbon::createFromFormat('Y-m-d', $periode['sampai'], $zona)->addDay()->startOfDay()->format('Y-m-d H:i:s');

        $barisLaporan = Report::selectRaw('id_fasilitas, report_status, COUNT(*) as jumlah')
            ->where('created_at', '>=', $awalLaporan)
            ->where('created_at', '<', $akhirLaporan)
            ->groupBy('id_fasilitas', 'report_status')
            ->get();

        foreach ($barisLaporan as $baris) {
            $hitungLaporan[$baris->id_fasilitas][$baris->report_status] = (int) $baris->jumlah;
        }

        $baris = [];

        foreach (Facility::orderBy('location')->orderBy('facility_name')->get() as $f) {
            $id    = $f->id_fasilitas;
            $menit = $menitTerpakai[$id] ?? 0;
            $r     = $hitungReservasi[$id] ?? [];
            $l     = $hitungLaporan[$id] ?? [];

            // Menit tersedia: hari kerja berjalan (kecuali fasilitas nonaktif) + hari non-kerja/hari fasilitas
            // nonaktif yang ternyata dipakai (ada reservasi approved selesai), agar penyebut tidak lebih kecil dari pemakaian.
            $tersedia = $f->facility_status === 'nonaktif' ? 0 : $totalMenitHariKerja;

            foreach (array_keys($hariDipakai[$id] ?? []) as $iso) {
                if ($f->facility_status === 'nonaktif' || ! isset($menitHariKerja[$iso])) {
                    $tersedia += self::menitBerjalan($iso, $hariIni, $menitSekarang);
                }
            }

            $baris[] = [
                'id'                 => $id,
                'nama'               => $f->facility_name,
                'tipe'               => $f->type,
                'lokasi'             => $f->location,
                'kapasitas'          => $f->capacity,
                'status'             => $f->facility_status,
                'total_reservasi'    => array_sum($r),
                'reservasi_approved' => $r['approved'] ?? 0,
                'reservasi_pending'  => $r['pending'] ?? 0,
                'menit_terpakai'     => $menit,
                'menit_tersedia'     => $tersedia,
                'jam_terpakai'       => round($menit / 60, 1),
                'okupansi'           => $tersedia > 0 ? min(100.0, round($menit / $tersedia * 100, 1)) : 0.0,
                'total_laporan'      => array_sum($l),
                'laporan_baru'       => $l['baru'] ?? 0,
                'laporan_selesai'    => $l['selesai'] ?? 0,
            ];
        }

        // Agregasi per lokasi (frekuensi kerusakan & okupansi per lokasi)
        $perLokasi = [];

        foreach ($baris as $b) {
            $kunci = $b['lokasi'];

            $perLokasi[$kunci] ??= [
                'lokasi'             => $kunci,
                'jumlah_fasilitas'   => 0,
                'reservasi_approved' => 0,
                'menit_terpakai'     => 0,
                'menit_tersedia'     => 0,
                'total_laporan'      => 0,
            ];

            $perLokasi[$kunci]['jumlah_fasilitas']   += 1;
            $perLokasi[$kunci]['reservasi_approved'] += $b['reservasi_approved'];
            $perLokasi[$kunci]['menit_terpakai']     += $b['menit_terpakai'];
            $perLokasi[$kunci]['menit_tersedia']     += $b['menit_tersedia'];
            $perLokasi[$kunci]['total_laporan']      += $b['total_laporan'];
        }

        $perLokasi = array_values(array_map(function ($lokasi) {
            $tersedia = $lokasi['menit_tersedia'];

            $lokasi['jam_terpakai'] = round($lokasi['menit_terpakai'] / 60, 1);
            $lokasi['okupansi']     = $tersedia > 0 ? min(100.0, round($lokasi['menit_terpakai'] / $tersedia * 100, 1)) : 0.0;

            return $lokasi;
        }, $perLokasi));

        return [
            'periode'    => $periode,
            'baris'      => $baris,
            'per_lokasi' => $perLokasi,
        ];
    }
}
