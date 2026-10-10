{{-- Pengganti foto fasilitas bila admin belum mengunggah foto. Class tambahan (ukuran, rounded) lewat atribut class. --}}
<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center gap-1 bg-cream-50 text-charcoal-light']) }} role="img" aria-label="Belum ada foto fasilitas">
    <svg class="w-8 h-8 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>
    @isset($label)
        <span class="text-[11px] font-semibold">{{ $label }}</span>
    @endisset
</div>
