{{--
    Galeri foto kecil dengan lightbox layar penuh (panah kiri/kanan, Esc untuk menutup).
    Ditulis dengan Alpine (bukan <script>) agar tetap berfungsi saat dimuat lewat AJAX ke dalam pop-up.
--}}
@props([
    'urls',
    'gridClass' => 'grid grid-cols-2 sm:grid-cols-3 gap-3',
    'btnClass'  => 'group focus:outline-none',
    'btnStyle'  => '',
    'imgClass'  => 'w-full h-32 object-cover rounded-lg border border-gray-200 group-hover:opacity-80 transition cursor-zoom-in',
])

<div x-data="{
        foto: @js($urls),
        aktif: null,
        buka(i) { this.aktif = i; document.body.dataset.lightbox = '1'; },
        tutup() { this.aktif = null; setTimeout(() => delete document.body.dataset.lightbox, 0); },
        ganti(arah) { this.aktif = (this.aktif + arah + this.foto.length) % this.foto.length; }
     }"
     x-on:keydown.window="
        if (aktif === null) return;
        if ($event.key === 'Escape') tutup();
        else if ($event.key === 'ArrowLeft') ganti(-1);
        else if ($event.key === 'ArrowRight') ganti(1);
     ">
    <div class="{{ $gridClass }}">
        <template x-for="(u, i) in foto" :key="i">
            <button type="button" x-on:click="buka(i)" class="{{ $btnClass }}" style="{{ $btnStyle }}" :aria-label="'Perbesar foto ' + (i + 1)">
                <img :src="u" alt="Foto kerusakan" class="{{ $imgClass }}">
            </button>
        </template>
    </div>

    <template x-teleport="body">
        <div x-show="aktif !== null" x-cloak x-on:click.self="tutup()"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[80] flex items-center justify-center bg-black/85 backdrop-blur-[2px]">
            <div x-show="aktif !== null"
                 x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                 class="relative max-w-4xl w-full mx-4 flex flex-col items-center" x-on:click.self="tutup()">
                <button type="button" x-on:click="tutup()" title="Tutup (Esc)"
                        class="absolute -top-10 right-0 text-white hover:text-red-400 transition text-4xl font-bold leading-none">&times;</button>
                <img :src="aktif !== null ? foto[aktif] : ''" alt="Foto kerusakan" class="max-h-[80vh] max-w-full rounded-xl shadow-2xl object-contain">
                <div class="flex items-center gap-6 mt-4">
                    <button type="button" x-on:click="ganti(-1)" :disabled="foto.length <= 1" title="Sebelumnya"
                            class="text-white bg-white/20 hover:bg-white/40 rounded-full p-2 transition disabled:opacity-30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <span class="text-white text-sm font-medium" x-text="aktif !== null ? (aktif + 1) + ' / ' + foto.length : ''"></span>
                    <button type="button" x-on:click="ganti(1)" :disabled="foto.length <= 1" title="Selanjutnya"
                            class="text-white bg-white/20 hover:bg-white/40 rounded-full p-2 transition disabled:opacity-30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
