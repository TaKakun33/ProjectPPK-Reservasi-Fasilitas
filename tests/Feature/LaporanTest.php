<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Report;
use App\Models\ReportCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MembuatData;
use Tests\TestCase;

class LaporanTest extends TestCase
{
    use RefreshDatabase, MembuatData;

    private function buatKategori(): ReportCategory
    {
        return ReportCategory::create(['category_name' => 'Kerusakan Listrik', 'is_active' => true]);
    }

    public function test_laporan_ganda_dari_pelapor_yang_sama_ditolak(): void
    {
        $pelapor   = $this->buatUser();
        $fasilitas = $this->buatFasilitas();
        $kategori  = $this->buatKategori();

        $data = [
            'id_fasilitas' => $fasilitas->id_fasilitas,
            'id_kategori'  => $kategori->id_kategori,
            'description'  => 'Lampu di ruangan ini mati total sejak kemarin.',
        ];

        $this->actingAs($pelapor)->post(route('reports.store'), $data)->assertSessionHasNoErrors();
        $this->actingAs($pelapor)->post(route('reports.store'), $data)->assertSessionHasErrors('id_fasilitas');

        $this->assertSame(1, Report::count());
    }

    public function test_catatan_wajib_saat_laporan_diselesaikan(): void
    {
        $petugas   = $this->buatUser(UserRole::Petugas);
        $fasilitas = $this->buatFasilitas(['facility_status' => 'dalam perbaikan']);
        $laporan   = Report::create([
            'id_user'       => $this->buatUser()->id_user,
            'id_fasilitas'  => $fasilitas->id_fasilitas,
            'id_kategori'   => $this->buatKategori()->id_kategori,
            'description'   => 'Lampu mati total di ruangan ini.',
            'report_status' => 'diproses',
        ]);

        $url = route('petugas.reports.update-status', $laporan->id_laporan);

        $this->actingAs($petugas)
            ->patch($url, ['report_status' => 'selesai'])
            ->assertSessionHasErrors('resolution_notes');

        $this->assertSame('diproses', $laporan->fresh()->report_status);

        $this->actingAs($petugas)
            ->patch($url, ['report_status' => 'selesai', 'resolution_notes' => 'Lampu sudah diganti.'])
            ->assertSessionHasNoErrors();

        $this->assertSame('selesai', $laporan->fresh()->report_status);
        $this->assertSame('aktif', $fasilitas->fresh()->facility_status);
    }

    private function buatLaporanBaru($fasilitas): Report
    {
        return Report::create([
            'id_user'       => $this->buatUser()->id_user,
            'id_fasilitas'  => $fasilitas->id_fasilitas,
            'id_kategori'   => $this->buatKategori()->id_kategori,
            'description'   => 'Lampu mati total di ruangan ini.',
            'report_status' => 'baru',
        ]);
    }

    public function test_memproses_laporan_tanpa_menutup_fasilitas_tidak_mengubah_apa_pun(): void
    {
        $petugas   = $this->buatUser(UserRole::Petugas);
        $fasilitas = $this->buatFasilitas();
        $laporan   = $this->buatLaporanBaru($fasilitas);
        $approved  = $this->buatReservasi($this->buatUser(), $fasilitas, 3, '09:00', '10:00', 'approved');

        $this->actingAs($petugas)
            ->patch(route('petugas.reports.update-status', $laporan->id_laporan), ['report_status' => 'diproses'])
            ->assertSessionHasNoErrors();

        $this->assertSame('diproses', $laporan->fresh()->report_status);
        $this->assertSame('aktif', $fasilitas->fresh()->facility_status);
        $this->assertSame('approved', $approved->fresh()->reservation_status);
    }

    public function test_menutup_fasilitas_membatalkan_approved_dan_menolak_pending_mendatang(): void
    {
        $petugas   = $this->buatUser(UserRole::Petugas);
        $fasilitas = $this->buatFasilitas();
        $laporan   = $this->buatLaporanBaru($fasilitas);
        $approved  = $this->buatReservasi($this->buatUser(), $fasilitas, 3, '09:00', '10:00', 'approved');
        $pending   = $this->buatReservasi($this->buatUser(), $fasilitas, 4, '09:00', '10:00', 'pending');

        $this->actingAs($petugas)
            ->patch(route('petugas.reports.update-status', $laporan->id_laporan), [
                'report_status'     => 'diproses',
                'menutup_fasilitas' => '1',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame('dalam perbaikan', $fasilitas->fresh()->facility_status);
        $this->assertSame('cancelled', $approved->fresh()->reservation_status);
        $this->assertNotEmpty($approved->fresh()->cancellation_reason);
        $this->assertSame('rejected', $pending->fresh()->reservation_status);
        $this->assertNotEmpty($pending->fresh()->alasan_ditolak);

        // Setelah laporan selesai, fasilitas kembali aktif
        $this->actingAs($petugas)
            ->patch(route('petugas.reports.update-status', $laporan->id_laporan), [
                'report_status'    => 'selesai',
                'resolution_notes' => 'Sudah diperbaiki.',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame('aktif', $fasilitas->fresh()->facility_status);
    }
}
