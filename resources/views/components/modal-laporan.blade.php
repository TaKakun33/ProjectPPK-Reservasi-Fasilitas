{{--
    Pop-up laporkan kerusakan. $facilities dan $categories disuplai view composer di AppServiceProvider.
    Buka dengan: $dispatch('open-modal', 'laporan')
--}}
<x-dialog-form name="laporan" title="Laporkan Kerusakan Fasilitas"
               subtitle="Jelaskan kerusakan yang ditemukan agar petugas dapat menindaklanjuti"
               :show="$errors->any() && old('_modal') === 'laporan'">
    @include('reports.partials.form', ['modal' => true])
</x-dialog-form>
