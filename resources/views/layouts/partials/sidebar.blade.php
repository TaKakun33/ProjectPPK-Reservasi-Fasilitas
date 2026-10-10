{{--
    Sidebar bersama untuk portal Admin dan Petugas (tampilan identik).
    Variabel yang dibutuhkan:
      $portal    : teks kecil di bawah logo, mis. 'Portal Petugas'
      $homeRoute : nama route tujuan saat logo diklik
      $nav       : daftar menu; tiap item punya route, match, icon, label dan (opsional) badge
    Harus dipanggil di dalam elemen yang memiliki x-data="{ sidebarOpen: false }".
--}}

{{-- Overlay gelap untuk mobile --}}
<div
    x-show="sidebarOpen"
    x-cloak
    x-transition:enter="transition-opacity ease-linear duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="sidebarOpen = false"
    class="fixed inset-0 z-20 bg-black/50 lg:hidden"
></div>

{{-- Di layar kecil sidebar melayang (fixed) dan digeser keluar layar; di layar lg menempel (sticky) di kiri --}}
<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-30 w-64 shrink-0 flex flex-col transition-transform duration-200 ease-in-out lg:translate-x-0 lg:sticky lg:top-0 lg:h-screen lg:inset-auto lg:z-auto"
    style="background: linear-gradient(180deg, #380F17 0%, #4D0E17 40%, #6B101C 80%, #8F0B13 100%); border-right: 1px solid rgba(239, 223, 197, 0.15);"
>
    {{-- Logo / Brand Header --}}
    <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
        <a href="{{ route($homeRoute) }}" class="flex items-center gap-3 group">
            <x-application-logo class="h-9 w-auto drop-shadow-sm transition-transform duration-150 group-hover:scale-105" />
            <span class="leading-tight">
                <span class="block text-sm font-black text-[#EFDFC5] tracking-wide">SyncSpace</span>
                <span class="block text-[11px] font-semibold text-[#EFDFC5]/70 tracking-widest uppercase">{{ $portal }}</span>
            </span>
        </a>
    </div>

    {{-- Menu Navigasi --}}
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        @foreach($nav as $item)
            @php $active = request()->routeIs($item['match']); @endphp
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition duration-150
                      {{ $active
                         ? 'bg-white/20 text-white shadow-xs'
                         : 'text-[#EFDFC5]/85 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                </svg>
                <span class="flex-1">{{ $item['label'] }}</span>
                @if(!empty($item['badge']))
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-cream text-maroon-900">{{ $item['badge'] }}</span>
                @endif
            </a>
        @endforeach
    </nav>

    {{-- Info Pengguna + Keluar --}}
    <div class="px-4 py-4 border-t border-white/10">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center font-black text-sm shrink-0 shadow-sm"
                 style="background:#EFDFC5; color:#380F17; border:1px solid rgba(239, 223, 197, 0.35);">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <p class="text-sm font-extrabold text-[#EFDFC5] truncate leading-tight">{{ Auth::user()->name }}</p>
                <p class="text-xs font-semibold text-[#EFDFC5]/70 truncate mt-0.5 leading-tight">{{ Auth::user()->email }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('profile.edit') }}"
               class="flex-1 inline-flex items-center justify-center text-center text-xs font-medium text-[#EFDFC5]/85 hover:text-white hover:bg-white/10 px-3 py-1.5 rounded-lg transition whitespace-nowrap">
                Profil Akun
            </a>
            <form method="POST" action="{{ route('logout') }}" class="flex-1 flex">
                @csrf
                <button type="submit"
                        class="w-full inline-flex items-center justify-center text-xs font-medium text-[#EFDFC5]/85 hover:text-white hover:bg-white/10 px-3 py-1.5 rounded-lg transition whitespace-nowrap">
                    Keluar
                </button>
            </form>
        </div>
    </div>
</aside>
