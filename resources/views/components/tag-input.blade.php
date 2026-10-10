{{--
    Input daftar item (chip): ketik lalu tekan Enter atau koma untuk menambah, klik × untuk menghapus.
    Setiap item dikirim sebagai name[] (array teks biasa), jadi validasi server memakai aturan array biasa.

    Props:
      name        : nama field array, mis. 'amenities'
      values      : daftar item awal
      suggestions : saran item yang bisa diklik untuk menambah
      placeholder : teks bantuan
      max         : jumlah item maksimal
      maxLength   : panjang maksimal tiap item
--}}
@props(['name', 'values' => [], 'suggestions' => [], 'placeholder' => '', 'max' => 12, 'maxLength' => 60, 'id' => null])

@php $id = $id ?? $name; @endphp

<div x-data="{
        items: @js(array_values(array_filter((array) $values, 'is_string'))),
        saran: @js(array_values((array) $suggestions)),
        teks: '',
        maks: {{ (int) $max }},
        panjang: {{ (int) $maxLength }},
        error: '',
        get sisaSaran() {
            return this.saran.filter(s => !this.items.some(i => i.toLowerCase() === s.toLowerCase())).slice(0, 12);
        },
        tambah(nilai) {
            nilai = (nilai === undefined ? this.teks : nilai).trim();
            this.teks = '';
            if (nilai === '') { return; }
            if (nilai.length > this.panjang) { this.error = 'Tiap item maksimal ' + this.panjang + ' karakter.'; return; }
            if (this.items.some(i => i.toLowerCase() === nilai.toLowerCase())) { this.error = ''; return; }
            if (this.items.length >= this.maks) { this.error = 'Maksimal ' + this.maks + ' item.'; return; }
            this.error = '';
            this.items.push(nilai);
        },
        hapus(i) { this.items.splice(i, 1); this.error = ''; }
     }">

    <div class="flex flex-wrap gap-2 p-2 rounded-xl border border-cream-border bg-white shadow-sm focus-within:border-maroon-700 focus-within:ring-1 focus-within:ring-maroon-700"
         @click="$refs.ketik.focus()">
        <template x-for="(it, i) in items" :key="it">
            <span class="inline-flex items-center gap-1 pl-2.5 pr-1 py-1 rounded-lg text-xs font-semibold"
                  style="background:#FAF6F0; color:#380F17; border:1px solid #EAE0D3;">
                <span x-text="it"></span>
                <button type="button" @click.stop="hapus(i)" :aria-label="'Hapus ' + it"
                        class="w-4 h-4 inline-flex items-center justify-center rounded-md text-maroon-700 hover:bg-maroon-100 transition">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <input type="hidden" name="{{ $name }}[]" :value="it">
            </span>
        </template>

        <input type="text" x-ref="ketik" x-model="teks" id="{{ $id }}"
               placeholder="{{ $placeholder }}"
               :disabled="items.length >= maks"
               @keydown.enter.prevent="tambah()"
               @keydown.comma.prevent="tambah()"
               @keydown.backspace="if (teks === '' && items.length) { items.pop(); }"
               @blur="tambah()"
               class="flex-1 min-w-[10rem] border-0 p-1 text-sm bg-transparent focus:ring-0 text-charcoal-dark disabled:cursor-not-allowed">
    </div>

    <p x-show="error" x-cloak x-text="error" class="text-xs mt-1.5 font-semibold text-red-600" role="alert"></p>

    <div x-show="sisaSaran.length > 0 && items.length < maks" x-cloak class="mt-2 flex flex-wrap items-center gap-1.5">
        <span class="text-xs text-charcoal-medium">Saran:</span>
        <template x-for="s in sisaSaran" :key="s">
            <button type="button" @click="tambah(s)"
                    class="px-2 py-0.5 rounded-lg text-xs font-medium border border-cream-border text-charcoal-medium hover:bg-cream-100 hover:text-maroon-800 transition"
                    x-text="'+ ' + s"></button>
        </template>
    </div>

    <p class="mt-1.5 text-xs text-charcoal-medium">
        Tekan Enter atau koma untuk menambah item (maksimal <span x-text="maks"></span>). Contoh: Wi-Fi Cepat, AC, Proyektor.
        <span x-text="'(' + items.length + '/' + maks + ')'" class="font-semibold"></span>
    </p>
</div>
