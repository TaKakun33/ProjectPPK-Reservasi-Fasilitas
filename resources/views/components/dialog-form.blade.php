{{--
    Pop-up (dialog) khusus formulir: panel bisa digulir sendiri bila isinya panjang.
    Dibuka lewat event:  $dispatch('open-modal', 'nama')
    Ditutup lewat event: $dispatch('close-modal', 'nama'), tombol silang, klik latar, atau tombol Esc.
    Isi formulir tidak hilang saat pop-up ditutup (hanya disembunyikan), jadi aman bila tertutup tidak sengaja.
--}}
@props([
    'name',
    'title',
    'subtitle' => null,
    'show' => false,
    'maxWidth' => '2xl',
])

@php
    $lebar = [
        'lg'  => 'sm:max-w-lg',
        'xl'  => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        '3xl' => 'sm:max-w-3xl',
    ][$maxWidth] ?? 'sm:max-w-2xl';
@endphp

<div x-data="{ show: @js($show) }"
     x-init="
        $watch('show', terbuka => {
            document.body.classList.toggle('overflow-y-hidden', terbuka);
            // Fokus ke isian pertama saat pop-up terbuka
            if (terbuka) $nextTick(() => setTimeout(() => $refs.panel.querySelector('select, input:not([type=hidden]), textarea')?.focus(), 150));
        });
        if (show) document.body.classList.add('overflow-y-hidden');
     "
     x-on:open-modal.window="$event.detail === @js($name) ? show = true : null"
     x-on:close-modal.window="$event.detail === @js($name) ? show = false : null"
     x-on:keydown.escape.window="if (!document.body.dataset.lightbox) show = false"
     x-show="show"
     x-cloak
     class="fixed inset-0 z-[60] overflow-y-auto"
     style="display: {{ $show ? 'block' : 'none' }};"
     role="dialog" aria-modal="true" aria-labelledby="judul-{{ $name }}">

    {{-- Latar gelap: klik untuk menutup --}}
    <div x-show="show"
         x-on:click="show = false"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-[#252B2B]/60 backdrop-blur-[2px]"></div>

    {{-- Panel --}}
    <div x-ref="panel"
         x-show="show"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
         class="mp-dialog-panel relative mx-auto my-4 sm:my-10 w-[calc(100%-2rem)] {{ $lebar }} bg-white rounded-2xl shadow-2xl border border-cream-border border-t-4 border-t-maroon-800">

        <div class="mp-dialog-head flex items-start justify-between gap-4 px-5 sm:px-7 pt-5 pb-2">
            <div class="min-w-0">
                <h2 id="judul-{{ $name }}" class="font-extrabold text-lg sm:text-xl text-maroon-800 leading-tight">{{ $title }}</h2>
                @if($subtitle)
                    <p class="mt-0.5 text-xs text-[#4C4F54]">{{ $subtitle }}</p>
                @endif
            </div>
            <button type="button" x-on:click="show = false" aria-label="Tutup"
                    class="mp-dialog-close shrink-0 -mr-2 -mt-1 w-8 h-8 rounded-full flex items-center justify-center text-[#4C4F54] hover:bg-cream-50 hover:text-maroon-800 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-maroon-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="mp-dialog-body px-5 sm:px-7 pb-6 pt-0">
            {{ $slot }}
        </div>
    </div>
</div>
