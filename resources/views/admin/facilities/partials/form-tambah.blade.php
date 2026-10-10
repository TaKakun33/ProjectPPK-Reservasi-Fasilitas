{{-- Form tambah fasilitas. Dipakai halaman penuh (admin.fasilitas.create) dan pop-up (x-modal-fasilitas). --}}
@php $modal = $modal ?? false; @endphp
<form method="POST" action="{{ route('admin.fasilitas.store') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if($modal)
        {{-- Penanda: bila validasi gagal, halaman dimuat ulang dan pop-up dibuka kembali --}}
        <input type="hidden" name="_modal" value="fasilitas">
    @endif

    <div>
        <label for="facility_name" class="block text-sm font-semibold text-charcoal-dark mb-1">Nama Fasilitas Kampus</label>
        <input id="facility_name" type="text" name="facility_name" value="{{ old('facility_name') }}" required
               class="w-full rounded-xl border-cream-border shadow-sm focus:border-maroon-700 focus:ring-maroon-700"
               placeholder="Contoh: Laboratorium Komputer Terpadu 1">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="type" class="block text-sm font-semibold text-charcoal-dark mb-1">Kategori Fasilitas</label>
            <x-combo-box name="type" :options="$daftarKategori" :value="old('type')" :required="true"
                         placeholder="Pilih atau ketik kategori, mis. Laboratorium / Ruang Seminar" />
        </div>
        <div>
            <label for="capacity" class="block text-sm font-semibold text-charcoal-dark mb-1">Kapasitas Maksimal (Orang)</label>
            <input id="capacity" type="number" name="capacity" value="{{ old('capacity') }}" required min="1"
                   class="w-full rounded-xl border-cream-border shadow-sm focus:border-maroon-700 focus:ring-maroon-700"
                   placeholder="Contoh: 40">
        </div>
    </div>

    <div>
        <label for="location" class="block text-sm font-semibold text-charcoal-dark mb-1">Lokasi Gedung & Ruang</label>
        <x-combo-box name="location" :options="$daftarLokasi" :value="old('location')" :required="true"
                     placeholder="Pilih atau ketik lokasi, mis. Gedung Kuliah Bersama Lantai 2" />
    </div>

    <div>
        <label for="description" class="block text-sm font-semibold text-charcoal-dark mb-1">Deskripsi & Spesifikasi Fasilitas</label>
        <textarea id="description" name="description" rows="3"
                  class="w-full rounded-xl border-cream-border shadow-sm focus:border-maroon-700 focus:ring-maroon-700"
                  placeholder="Uraikan fungsi fasilitas, kelengkapan sarana, dan ketentuan operasional...">{{ old('description') }}</textarea>
    </div>

    <div>
        <label for="amenities" class="block text-sm font-semibold text-charcoal-dark mb-1">Fasilitas &amp; Sarana Penunjang</label>
        <x-tag-input name="amenities" :values="old('amenities', [])" :suggestions="$daftarSarana"
                     placeholder="Ketik sarana lalu tekan Enter, mis. Wi-Fi Cepat" />
        @error('amenities')
            <p class="text-xs mt-1.5 font-semibold text-red-600" role="alert">{{ $message }}</p>
        @enderror
    </div>

    {{-- Foto fasilitas (maks. 5) --}}
    @include('admin.facilities.partials.photos-field', ['fasilitas' => null])

    <div class="flex justify-end space-x-3 pt-4 border-t">
        @if($modal)
            <button type="button" x-on:click="$dispatch('close-modal', 'fasilitas')" class="px-4 py-2 border border-cream-border rounded-xl text-charcoal-medium hover:bg-cream-50">Batalkan</button>
        @else
            <a href="{{ route('admin.fasilitas.index') }}" class="px-4 py-2 border border-cream-border rounded-xl text-charcoal-medium hover:bg-cream-50">Batalkan</a>
        @endif
        <button type="submit" class="px-5 py-2 bg-maroon-700 text-cream-100 font-semibold rounded-xl hover:brightness-110 transition">
            Simpan Data Fasilitas
        </button>
    </div>
</form>
