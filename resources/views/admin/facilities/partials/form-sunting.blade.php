{{-- Form sunting fasilitas. Dipakai halaman penuh (admin.fasilitas.edit) dan pop-up (AJAX, $modal = true). --}}
@php $modal = $modal ?? false; @endphp
<form method="POST" action="{{ route('admin.fasilitas.update', $fasilitas->id_fasilitas) }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')
    @if($modal)
        {{-- Penanda: bila validasi gagal, pop-up sunting dibuka kembali untuk fasilitas ini --}}
        <input type="hidden" name="_modal" value="sunting">
        <input type="hidden" name="_id" value="{{ $fasilitas->id_fasilitas }}">
    @endif

    <div>
        <label for="sunting_facility_name" class="block text-sm font-semibold text-charcoal-dark mb-1">Nama Fasilitas Kampus</label>
        <input id="sunting_facility_name" type="text" name="facility_name" value="{{ old('facility_name', $fasilitas->facility_name) }}" required
               class="w-full rounded-xl border-cream-border shadow-sm focus:border-maroon-700 focus:ring-maroon-700">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="sunting_type" class="block text-sm font-semibold text-charcoal-dark mb-1">Kategori Fasilitas</label>
            <x-combo-box name="type" id="sunting_type" :options="$daftarKategori" :value="old('type', $fasilitas->type)" :required="true"
                         placeholder="Pilih atau ketik kategori, mis. Laboratorium / Ruang Seminar" />
        </div>
        <div>
            <label for="sunting_capacity" class="block text-sm font-semibold text-charcoal-dark mb-1">Kapasitas Maksimal (Orang)</label>
            <input id="sunting_capacity" type="number" name="capacity" value="{{ old('capacity', $fasilitas->capacity) }}" required min="1"
                   class="w-full rounded-xl border-cream-border shadow-sm focus:border-maroon-700 focus:ring-maroon-700">
        </div>
    </div>

    <div>
        <label for="sunting_location" class="block text-sm font-semibold text-charcoal-dark mb-1">Lokasi Gedung & Ruang</label>
        <x-combo-box name="location" id="sunting_location" :options="$daftarLokasi" :value="old('location', $fasilitas->location)" :required="true"
                     placeholder="Pilih atau ketik lokasi, mis. Gedung Kuliah Bersama Lantai 2" />
    </div>

    <div>
        <label for="sunting_description" class="block text-sm font-semibold text-charcoal-dark mb-1">Deskripsi & Spesifikasi Fasilitas</label>
        <textarea id="sunting_description" name="description" rows="3"
                  class="w-full rounded-xl border-cream-border shadow-sm focus:border-maroon-700 focus:ring-maroon-700">{{ old('description', $fasilitas->description) }}</textarea>
    </div>

    <div>
        <label for="sunting_amenities" class="block text-sm font-semibold text-charcoal-dark mb-1">Fasilitas &amp; Sarana Penunjang</label>
        <x-tag-input name="amenities" id="sunting_amenities" :values="old('amenities', $fasilitas->amenities ?? [])" :suggestions="$daftarSarana"
                     placeholder="Ketik sarana lalu tekan Enter, mis. Wi-Fi Cepat" />
        @error('amenities')
            <p class="text-xs mt-1.5 font-semibold text-red-600" role="alert">{{ $message }}</p>
        @enderror
    </div>

    {{-- Foto fasilitas (maks. 5) --}}
    @include('admin.facilities.partials.photos-field', ['fasilitas' => $fasilitas, 'idPrefiks' => 'sunting_'])

    <div class="flex items-center space-x-4 p-4 bg-cream-50 border border-cream-border rounded-xl">
        <div>
            <span class="text-sm font-medium text-charcoal-dark">Status Operasional Saat Ini:</span>
            @php
                $statusStyle = match($fasilitas->facility_status) {
                    'aktif' => ['class' => 'bg-green-100 text-green-700', 'label' => 'Aktif'],
                    'dalam perbaikan' => ['class' => 'bg-yellow-100 text-yellow-800', 'label' => 'Dalam Pemeliharaan'],
                    default => ['class' => 'bg-red-100 text-red-700', 'label' => 'Nonaktif'],
                };
            @endphp
            <span class="ml-2 px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $statusStyle['class'] }}">
                {{ $statusStyle['label'] }}
            </span>
        </div>
    </div>

    <div class="flex justify-end space-x-3 pt-4 border-t">
        @if($modal)
            <button type="button" x-on:click="$dispatch('close-modal', 'sunting')" class="px-4 py-2 border border-cream-border rounded-xl text-charcoal-medium hover:bg-cream-50">Batalkan</button>
        @else
            <a href="{{ route('admin.fasilitas.index') }}" class="px-4 py-2 border border-cream-border rounded-xl text-charcoal-medium hover:bg-cream-50">Batalkan</a>
        @endif
        <button type="submit" class="px-5 py-2 bg-maroon-700 text-cream-100 font-semibold rounded-xl hover:brightness-110 transition">
            Simpan Perubahan Data
        </button>
    </div>
</form>

{{-- Zona status operasional: form terpisah (bukan di dalam form di atas agar tidak bersarang) --}}
<div class="mt-6 p-4 rounded-xl border {{ $fasilitas->facility_status === 'nonaktif' ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
    @if($fasilitas->facility_status !== 'nonaktif')
        <form method="POST" action="{{ route('admin.fasilitas.destroy', $fasilitas->id_fasilitas) }}"
              class="flex flex-col sm:flex-row sm:items-center justify-between gap-3"
              data-confirm-type="danger" data-confirm-title="Nonaktifkan Fasilitas?" data-confirm-ok="Ya, Nonaktifkan"
              data-confirm="Apakah Anda yakin ingin menonaktifkan fasilitas ini? Permohonan reservasi pending yang akan datang akan dibatalkan secara otomatis.">
            @csrf
            @method('DELETE')
            <div>
                <p class="text-sm font-bold text-red-800">Nonaktifkan Fasilitas</p>
                <p class="text-xs text-red-700 mt-0.5">Fasilitas tidak dapat direservasi. Permohonan pending yang akan datang dibatalkan otomatis.</p>
            </div>
            <button type="submit" class="shrink-0 px-4 py-2 text-xs font-bold text-white rounded-xl transition shadow-xs hover:brightness-110" style="background:#8F0B13;">
                Nonaktifkan
            </button>
        </form>
    @else
        <form method="POST" action="{{ route('admin.fasilitas.activate', $fasilitas->id_fasilitas) }}"
              class="flex flex-col sm:flex-row sm:items-center justify-between gap-3"
              data-confirm-type="info" data-confirm-title="Aktifkan Fasilitas?" data-confirm-ok="Ya, Aktifkan"
              data-confirm="Apakah Anda yakin ingin mengaktifkan kembali fasilitas kampus ini?">
            @csrf
            @method('PATCH')
            <div>
                <p class="text-sm font-bold text-green-800">Aktifkan Kembali Fasilitas</p>
                <p class="text-xs text-green-700 mt-0.5">Fasilitas dapat kembali direservasi oleh pengguna.</p>
            </div>
            <button type="submit" class="shrink-0 px-4 py-2 text-xs font-bold text-white rounded-xl transition shadow-xs hover:brightness-110" style="background:#059669;">
                Aktifkan Kembali
            </button>
        </form>
    @endif
</div>
