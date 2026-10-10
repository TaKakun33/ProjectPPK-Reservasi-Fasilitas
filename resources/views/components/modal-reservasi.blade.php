{{--
    Pop-up ajukan reservasi. Daftar fasilitas ($facilities) disuplai view composer di AppServiceProvider.
    Buka dengan: $dispatch('isi-reservasi', { facility_id, date, start_time }) lalu $dispatch('open-modal', 'reservasi')
--}}
@props(['facilityId' => null, 'date' => null])

@php
    $selectedFacilityId = old('id_fasilitas', $facilityId);
    $selectedDate = old('date', $date ?? now()->toDateString());
@endphp

<x-dialog-form name="reservasi" title="Ajukan Reservasi Fasilitas"
               subtitle="Pilih jadwal dan isi tujuan penggunaan fasilitas"
               :show="$errors->any() && old('_modal') === 'reservasi'">
    @include('reservations.partials.form', ['modal' => true])
</x-dialog-form>
