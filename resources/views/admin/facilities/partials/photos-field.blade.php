{{--
    Field foto fasilitas (maksimal 5 foto), dipakai form tambah dan ubah.
    Variabel: $fasilitas = model Facility (dengan relasi photos) saat edit, null saat tambah.
--}}
@php
    $idFoto = ($idPrefiks ?? '') . 'photos';
    $fotoAda = $fasilitas ? $fasilitas->photos : collect();
    $idUtamaDefault = $fotoAda->first()?->id_foto;
@endphp

<div x-data="{
        maks: 5,
        ada: {{ $fotoAda->count() }},
        hapus: @js(array_values((array) old('remove_photos', []))),
        previews: [],
        error: '',
        sisa() { return this.maks - this.ada + this.hapus.length; },
        pilih(e) {
            const files = [...e.target.files];
            this.previews = [];
            this.error = '';
            if (files.some(f => f.size > 2 * 1024 * 1024)) {
                this.error = 'Ukuran tiap foto maksimal 2 MB.';
                e.target.value = '';
                return;
            }
            if (files.length > this.sisa()) {
                this.error = 'Total foto per fasilitas maksimal ' + this.maks + '. Anda hanya bisa menambah ' + this.sisa() + ' foto lagi.';
                e.target.value = '';
                return;
            }
            this.previews = files.map(f => URL.createObjectURL(f));
        }
     }">
    <div class="flex items-center justify-between mb-1">
        <label for="{{ $idFoto }}" class="block text-sm font-semibold text-charcoal-dark">Foto Fasilitas</label>
        <span class="text-xs font-semibold text-charcoal-medium" x-text="(ada - hapus.length) + ' dari ' + maks + ' foto'"></span>
    </div>

    {{-- Foto yang sudah tersimpan (saat edit) --}}
    @if($fotoAda->isNotEmpty())
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-4">
            @foreach($fotoAda as $i => $foto)
                <div class="rounded-xl border border-cream-border overflow-hidden bg-white transition"
                     :class="hapus.includes('{{ $foto->id_foto }}') ? 'opacity-40' : ''">
                    <div class="h-28 bg-cream-50">
                        <img src="{{ $foto->url }}" alt="Foto {{ $i + 1 }} {{ $fasilitas->facility_name }}" class="w-full h-full object-cover">
                    </div>
                    <div class="p-2 space-y-1.5 text-xs">
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-charcoal-dark">
                            <input type="radio" name="cover_photo" value="{{ $foto->id_foto }}"
                                   @checked(old('cover_photo', $idUtamaDefault) === $foto->id_foto)
                                   class="text-maroon-700 focus:ring-maroon-700">
                            Foto utama
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-red-700">
                            <input type="checkbox" name="remove_photos[]" value="{{ $foto->id_foto }}" x-model="hapus"
                                   class="rounded border-cream-border text-red-600 focus:ring-red-500">
                            Hapus
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if($fasilitas && $fotoAda->isEmpty())
        <p class="mb-3 text-xs font-semibold px-3 py-2 rounded-xl bg-cream-50 border border-cream-border text-charcoal-dark">
            Saat ini fasilitas memakai foto bawaan. Foto bawaan akan diganti setelah Anda mengunggah foto sendiri.
        </p>
    @endif

    {{-- Unggah foto baru --}}
    <div x-show="sisa() > 0">
        <input id="{{ $idFoto }}" type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/webp"
               @change="pilih($event)"
               class="block w-full text-sm text-charcoal-medium file:mr-3 file:px-4 file:py-2 file:rounded-xl file:border-0 file:bg-maroon-700 file:text-cream-100 file:font-semibold file:cursor-pointer hover:file:brightness-110 border border-cream-border rounded-xl">
    </div>
    <p x-show="sisa() <= 0" x-cloak class="text-xs font-semibold text-charcoal-medium">
        Batas 5 foto tercapai. Hapus salah satu foto untuk menambah yang baru.
    </p>
    <p x-show="error" x-cloak x-text="error" class="text-xs mt-1.5 font-semibold text-red-600" role="alert"></p>
    @error('photos')
        <p class="text-xs mt-1.5 font-semibold text-red-600" role="alert">{{ $message }}</p>
    @enderror

    {{-- Pratinjau foto yang baru dipilih --}}
    <div x-show="previews.length" x-cloak class="grid grid-cols-3 sm:grid-cols-5 gap-2 mt-3">
        <template x-for="(p, i) in previews" :key="i">
            <div class="h-20 rounded-xl overflow-hidden border border-cream-border">
                <img :src="p" alt="Pratinjau foto baru" class="w-full h-full object-cover">
            </div>
        </template>
    </div>

    <p class="text-xs mt-2 text-charcoal-medium">
        Format JPG, PNG, atau WebP, maksimal 2 MB per foto dan 5 foto per fasilitas. Pilih beberapa foto sekaligus bila perlu.
        @if($fasilitas)
            Foto bertanda "Foto utama" tampil sebagai sampul di daftar fasilitas.
        @else
            Bila dikosongkan, sistem memakai foto bawaan sesuai jenis fasilitas. Foto pertama menjadi foto utama; foto utama bisa diubah lewat menu Ubah Fasilitas.
        @endif
    </p>
</div>
