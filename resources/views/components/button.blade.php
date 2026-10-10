@props(['variant' => 'primary', 'href' => null])

@php
    // Kelas dasar dipakai semua varian, termasuk state fokus untuk pengguna keyboard
    $dasar = 'inline-flex items-center justify-center gap-1.5 rounded-xl px-4 py-2.5 text-sm font-bold '
           . 'transition duration-150 focus:outline-none focus-visible:ring-2 '
           . 'focus-visible:ring-maroon-700 focus-visible:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed';

    $varian = [
        'primary'   => 'bg-maroon-700 text-cream border border-maroon-800 hover:bg-maroon-900',
        'secondary' => 'bg-white text-maroon-800 border border-cream-border hover:bg-cream-50',
        'danger'    => 'bg-red-600 text-white border border-transparent hover:bg-red-700',
    ];

    $kelas = $dasar . ' ' . ($varian[$variant] ?? $varian['primary']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $kelas]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => $kelas]) }}>{{ $slot }}</button>
@endif
