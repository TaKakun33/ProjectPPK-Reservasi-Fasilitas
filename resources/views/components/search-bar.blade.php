@props([
    'action',
    'placeholder' => 'Cari...',
    'label' => 'Pencarian',
    'hidden' => [],
])

{{-- Kartu pencarian seragam (gaya sama dengan filter di panel admin).
     $hidden: parameter lain yang ikut terkirim (mis. status tab) agar tidak hilang saat mencari. --}}
<div class="bg-white p-4 rounded-xl shadow-xs border border-cream-border">
    <form method="GET" action="{{ $action }}" class="flex flex-wrap gap-3 items-end">
        @foreach($hidden as $nama => $nilai)
            @if(filled($nilai))
                <input type="hidden" name="{{ $nama }}" value="{{ $nilai }}">
            @endif
        @endforeach

        <div class="flex-1 min-w-[200px]">
            <label for="search" class="block text-sm font-semibold text-charcoal-dark mb-1">{{ $label }}</label>
            <div style="position:relative;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#4C4F54; pointer-events:none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input id="search" type="text" name="search" value="{{ request('search') }}" maxlength="100"
                       placeholder="{{ $placeholder }}"
                       class="w-full rounded-xl border-cream-border shadow-sm focus:border-maroon-700 focus:ring-maroon-700 text-sm"
                       style="padding-left:36px;">
            </div>
        </div>

        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-maroon-700 text-cream-100 text-sm font-semibold rounded-xl hover:brightness-110 shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <span>Cari</span>
        </button>

        <a href="{{ $action }}{{ collect($hidden)->filter(fn ($v) => filled($v))->isNotEmpty() ? '?' . http_build_query(array_filter($hidden, fn ($v) => filled($v))) : '' }}"
           title="Atur Ulang" aria-label="Atur Ulang"
           class="inline-flex items-center justify-center p-2 bg-cream-50 border border-cream-border text-charcoal-medium rounded-xl hover:bg-cream-100 hover:text-charcoal-dark transition h-[38px] w-[38px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
        </a>
    </form>
</div>
