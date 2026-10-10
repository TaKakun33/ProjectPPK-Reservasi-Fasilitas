{{-- Form laporan kerusakan. Dipakai halaman penuh (reports.create) dan pop-up (x-modal-laporan). --}}
@php $modal = $modal ?? false; @endphp
<form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" class="space-y-6"
      x-data="{ mengirim: false }" @submit="mengirim = true">
    @csrf
    @if($modal)
        {{-- Penanda: bila validasi gagal, halaman dimuat ulang dan pop-up dibuka kembali --}}
        <input type="hidden" name="_modal" value="laporan">
    @endif

    {{-- Pilihan Fasilitas --}}
    <div>
        <x-input-label for="id_fasilitas" value="Pilih Fasilitas" />
        <x-select id="id_fasilitas" name="id_fasilitas" required class="mt-1 block w-full">
            <option value="">-- Pilih Fasilitas --</option>
            @foreach($facilities as $facility)
                <option value="{{ $facility->id_fasilitas }}" @selected(old('id_fasilitas') == $facility->id_fasilitas)>
                    {{ $facility->facility_name }} ({{ $facility->type }} - {{ $facility->location }})
                </option>
            @endforeach
        </x-select>
        <x-input-error :messages="$errors->get('id_fasilitas')" class="mt-2" />
    </div>

    {{-- Kategori Kerusakan --}}
    <div>
        <x-input-label for="id_kategori" value="Kategori Kerusakan" />
        <x-select id="id_kategori" name="id_kategori" required class="mt-1 block w-full">
            <option value="">-- Pilih Kategori --</option>
            @foreach($categories as $category)
                <option value="{{ $category->id_kategori }}" @selected(old('id_kategori') == $category->id_kategori)>
                    {{ $category->category_name }}
                </option>
            @endforeach
        </x-select>
        <x-input-error :messages="$errors->get('id_kategori')" class="mt-2" />
    </div>

    {{-- Deskripsi Kerusakan --}}
    <div>
        <x-input-label for="description" value="Deskripsi Kerusakan" />
        <textarea id="description" name="description" rows="4" required placeholder="Jelaskan kerusakan yang terjadi pada fasilitas..."
                  class="mt-1 block w-full rounded-xl border-cream-border shadow-sm focus:border-maroon-700 focus:ring-maroon-700 text-sm text-charcoal-dark">{{ old('description') }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    {{-- Foto Kerusakan --}}
    <div>
        <x-input-label for="photos" value="Foto Kerusakan (opsional, bisa lebih dari 1)" />
        <input id="photos" type="file" name="photos[]" accept="image/*" multiple aria-describedby="bantuan_foto"
               class="mt-1 block w-full text-sm text-charcoal-medium rounded-xl border border-cream-border bg-white
                      file:mr-4 file:rounded-l-xl file:border-0 file:bg-cream-50 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-maroon-800
                      hover:file:bg-cream-100 focus:outline-none focus:ring-2 focus:ring-maroon-700">
        <p id="bantuan_foto" class="mt-1 text-xs text-charcoal-light">Format: JPG/PNG/GIF/WebP, maksimal 2 MB per foto, maksimal 5 foto.</p>
        <x-input-error :messages="$errors->get('photos')" class="mt-2" />
        <x-input-error :messages="$errors->get('photos.*')" class="mt-2" />
    </div>

    <div class="mp-actions flex justify-end gap-3 pt-4 border-t border-dashed border-cream-border">
        @if($modal)
            <x-button type="button" variant="secondary" x-on:click="$dispatch('close-modal', 'laporan')">Batal</x-button>
        @else
            <x-button variant="secondary" :href="route('reports.index')">Batal</x-button>
        @endif
        <x-button x-bind:disabled="mengirim">
            <span x-text="mengirim ? 'Mengirim...' : 'Kirim Laporan'">Kirim Laporan</span>
        </x-button>
    </div>
</form>
