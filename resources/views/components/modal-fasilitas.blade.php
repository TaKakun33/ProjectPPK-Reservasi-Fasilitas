{{--
    Pop-up pendaftaran fasilitas baru (admin). Opsi isian ($daftarKategori, $daftarLokasi, $daftarSarana)
    disuplai view composer di AppServiceProvider.
    Buka dengan: $dispatch('open-modal', 'fasilitas')
--}}
<x-dialog-form name="fasilitas" title="Pendaftaran Fasilitas Kampus Baru"
               subtitle="Lengkapi data fasilitas yang akan dibuka untuk reservasi"
               max-width="3xl"
               :show="$errors->any() && old('_modal') === 'fasilitas'">
    @include('admin.facilities.partials.form-tambah', ['modal' => true])
</x-dialog-form>
