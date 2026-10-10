{{--
    Combo box: ketik bebas ATAU pilih dari daftar nilai yang sudah ada (daftar tersaring saat mengetik).
    Nilai yang dikirim tetap berupa teks biasa pada input[name], jadi validasi server tidak berubah.

    Props:
      name        : nama field (juga dipakai sebagai id bila id tidak diisi)
      options     : daftar pilihan (array string)
      value       : nilai awal
      placeholder : teks bantuan
      required    : wajib diisi
    Class tambahan lewat atribut class diterapkan pada input.
--}}
@props(['name', 'id' => null, 'options' => [], 'value' => '', 'placeholder' => '', 'required' => false])

@php $id = $id ?? $name; @endphp

<div class="relative"
     x-data="{
        open: false,
        value: @js((string) $value),
        options: @js(array_values((array) $options)),
        aktif: -1,
        get hasil() {
            const q = this.value.trim().toLowerCase();
            return this.options.filter(o => q === '' || o.toLowerCase().includes(q));
        },
        get baru() {
            const q = this.value.trim().toLowerCase();
            return q !== '' && !this.options.some(o => o.toLowerCase() === q);
        },
        pilih(o) { this.value = o; this.open = false; this.aktif = -1; },
        turun() { this.open = true; this.aktif = Math.min(this.aktif + 1, this.hasil.length - 1); },
        naik() { this.aktif = Math.max(this.aktif - 1, 0); }
     }"
     @click.outside="open = false">

    <input id="{{ $id }}" name="{{ $name }}" type="text" x-ref="input" x-model="value"
           autocomplete="off" role="combobox" aria-autocomplete="list" aria-controls="{{ $id }}-list"
           :aria-expanded="open && hasil.length > 0 ? 'true' : 'false'"
           placeholder="{{ $placeholder }}"
           @if($required) required @endif
           @focus="open = true"
           @input="open = true; aktif = -1"
           @keydown.arrow-down.prevent="turun()"
           @keydown.arrow-up.prevent="naik()"
           @keydown.enter="if (open && aktif >= 0 && hasil[aktif] !== undefined) { $event.preventDefault(); pilih(hasil[aktif]); }"
           @keydown.escape="open = false"
           {{ $attributes->merge(['class' => 'w-full rounded-xl border-cream-border shadow-sm focus:border-maroon-700 focus:ring-maroon-700 pr-10']) }}>

    {{-- Tombol panah untuk membuka seluruh daftar --}}
    <button type="button" tabindex="-1" aria-label="Tampilkan pilihan"
            @click="open = !open; $refs.input.focus()"
            class="absolute top-0 right-0 h-[42px] px-3 flex items-center text-charcoal-medium hover:text-maroon-700 transition">
        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <ul id="{{ $id }}-list" role="listbox" x-show="open && hasil.length > 0" x-cloak
        class="absolute z-20 mt-1 w-full max-h-56 overflow-auto rounded-xl bg-white border border-cream-border shadow-md py-1 text-sm">
        <template x-for="(o, i) in hasil" :key="o">
            <li role="option" :aria-selected="o === value ? 'true' : 'false'"
                @mousedown.prevent="pilih(o)" @mouseenter="aktif = i"
                class="px-3.5 py-2 cursor-pointer"
                :class="aktif === i ? 'bg-cream-100 text-maroon-800 font-semibold' : 'text-charcoal-dark'"
                x-text="o"></li>
        </template>
    </ul>

    <p x-show="baru && !open" x-cloak class="mt-1 text-xs text-charcoal-medium">
        Belum ada di daftar, akan disimpan sebagai pilihan baru.
    </p>
</div>
