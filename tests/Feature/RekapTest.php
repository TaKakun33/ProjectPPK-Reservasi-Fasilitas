<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Services\RekapService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MembuatData;
use Tests\TestCase;

class RekapTest extends TestCase
{
    use RefreshDatabase, MembuatData;

    public function test_okupansi_dihitung_dari_jam_terpakai_dibanding_jam_operasional(): void
    {
        $fasilitas = $this->buatFasilitas();
        // Kemarin (sudah selesai), 3 jam disetujui: 180 menit dari 780 menit operasional = 23,1%.
        // Kemarin bisa jatuh di akhir pekan, tetapi hari itu tetap dihitung karena fasilitas dipakai.
        $this->buatReservasi($this->buatUser(), $fasilitas, -1, '09:00', '12:00', 'approved');

        $hari  = now()->subDay()->toDateString();
        $hasil = RekapService::hitung($hari, $hari);

        $baris = collect($hasil['baris'])->firstWhere('id', $fasilitas->id_fasilitas);

        $this->assertSame(3.0, $baris['jam_terpakai']);
        $this->assertSame(23.1, $baris['okupansi']);
    }

    public function test_reservasi_approved_yang_belum_terjadi_tidak_dihitung(): void
    {
        $fasilitas = $this->buatFasilitas();
        $this->buatReservasi($this->buatUser(), $fasilitas, 2, '09:00', '12:00', 'approved');

        $dari  = now()->toDateString();
        $sampai = now()->addDays(3)->toDateString();
        $baris = collect(RekapService::hitung($dari, $sampai)['baris'])->firstWhere('id', $fasilitas->id_fasilitas);

        $this->assertSame(0, $baris['menit_terpakai']);
        $this->assertSame(0.0, $baris['okupansi']);
    }

    public function test_penyebut_hanya_hari_kerja_dan_fasilitas_nonaktif_tidak_dihitung(): void
    {
        $aktif    = $this->buatFasilitas();
        $nonaktif = $this->buatFasilitas(['facility_status' => 'nonaktif']);

        // Senin 05-10-2026 sampai Minggu 11-10-2026 sudah lewat bila tes dijalankan setelahnya;
        // pakai pekan lampau yang tetap: Senin 2026-01-05 s/d Minggu 2026-01-11 (5 hari kerja)
        $hasil = RekapService::hitung('2026-01-05', '2026-01-11');

        $a = collect($hasil['baris'])->firstWhere('id', $aktif->id_fasilitas);
        $n = collect($hasil['baris'])->firstWhere('id', $nonaktif->id_fasilitas);

        $this->assertSame(5 * 780, $a['menit_tersedia']);
        $this->assertSame(0, $n['menit_tersedia']);
        $this->assertSame(0.0, $n['okupansi']);
    }

    public function test_ekspor_csv_memakai_bom_utf8_dan_charset(): void
    {
        $admin = $this->buatUser(UserRole::Admin);
        $this->buatFasilitas(['facility_name' => 'Aula Serbaguna é']);

        $respons = $this->actingAs($admin)->get(route('admin.rekap.export', ['format' => 'csv']));

        $respons->assertOk();
        $this->assertStringContainsString('charset=UTF-8', $respons->headers->get('Content-Type'));
        $this->assertStringStartsWith("\xEF\xBB\xBF", $respons->streamedContent());
    }

    public function test_halaman_rekap_dan_ekspor_csv_dapat_diakses_admin(): void
    {
        $admin = $this->buatUser(UserRole::Admin);
        $this->buatFasilitas();

        $this->actingAs($admin)->get(route('admin.rekap.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.rekap.export', ['format' => 'csv']))->assertOk();
    }

    public function test_rentang_tanggal_terbalik_ditolak(): void
    {
        $admin = $this->buatUser(UserRole::Admin);

        $this->actingAs($admin)
            ->from(route('admin.rekap.index'))
            ->get(route('admin.rekap.index', ['dari' => '2026-10-10', 'sampai' => '2026-10-01']))
            ->assertSessionHasErrors('sampai');
    }
}
