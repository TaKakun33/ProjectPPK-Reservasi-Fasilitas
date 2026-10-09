<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Petugas – {{ config('app.name', 'SyncSpace') }}</title>

        {{-- Fonts: Plus Jakarta Sans --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @auth
            <script>
                (function() {
                    @if(session('just_logged_in'))
                        sessionStorage.setItem('tab_session_active', '1');
                    @endif

                    if (!sessionStorage.getItem('tab_session_active')) {
                        fetch("{{ route('logout') }}", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Content-Type": "application/json",
                                "Accept": "application/json"
                            }
                        }).finally(function() {
                            window.location.replace("{{ route('welcome') }}");
                        });
                    } else {
                        sessionStorage.setItem('tab_session_active', '1');
                    }
                })();
            </script>
        @endauth
    </head>
    <body class="antialiased" style="font-family:'Plus Jakarta Sans', sans-serif; background:#FAF6F0; color:#252B2B;">
        <div class="flex min-h-screen" x-data="{ sidebarOpen: false }">

            {{-- ===================== SIDEBAR / SIDE NAV ===================== --}}
            {{-- Mobile Overlay --}}
            <div
                x-show="sidebarOpen"
                x-transition:enter="transition-opacity ease-linear duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="sidebarOpen = false"
                class="fixed inset-0 z-20 bg-black/50 lg:hidden"
            ></div>

            <aside
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                class="fixed inset-y-0 left-0 z-30 w-64 flex flex-col transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:inset-auto lg:z-auto"
                style="background: linear-gradient(180deg, #380F17 0%, #4D0E17 40%, #6B101C 80%, #8F0B13 100%); border-right: 1px solid rgba(239, 223, 197, 0.15);"
            >
                {{-- Logo / Brand Header --}}
                <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
                    <a href="{{ route('petugas.dashboard') }}" class="flex items-center gap-3 group">
                        <x-application-logo class="h-9 w-auto drop-shadow-xs transition-transform duration-150 group-hover:scale-105" />
                        <span class="leading-tight">
                            <span class="block text-sm font-black text-[#EFDFC5] tracking-wide">SyncSpace</span>
                            <span class="block text-[10px] font-semibold text-[#EFDFC5]/70 tracking-widest uppercase">Portal Petugas</span>
                        </span>
                    </a>
                </div>

                {{-- Nav Items --}}
                <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                    @php
                        $petugasNav = [
                            [
                                'route' => 'petugas.dashboard',
                                'match' => 'petugas.dashboard',
                                'icon'  => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                                'label' => 'Dashboard Petugas',
                            ],
                            [
                                'route' => 'petugas.reservations.index',
                                'match' => 'petugas.reservations.*',
                                'icon'  => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                                'label' => 'Antrian Reservasi',
                            ],
                            [
                                'route' => 'petugas.reports.index',
                                'match' => 'petugas.reports.*',
                                'icon'  => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z',
                                'label' => 'Laporan Kerusakan',
                            ],
                        ];
                    @endphp

                    @foreach($petugasNav as $item)
                        @php $active = request()->routeIs($item['match']); @endphp
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition duration-150
                                  {{ $active
                                     ? 'bg-white/20 text-white shadow-xs'
                                     : 'text-[#EFDFC5]/85 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                            </svg>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                {{-- User Info + Logout --}}
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

            {{-- ===================== MAIN CONTENT AREA ===================== --}}
            <div class="flex-1 flex flex-col min-w-0">
                {{-- Sticky Top Bar --}}
                <header class="bg-white shadow-xs sticky top-0 z-10" style="border-bottom: 4px solid #8F0B13;">
                    <div class="flex items-center gap-4 px-4 sm:px-6 py-4">
                        {{-- Hamburger Button (Mobile Only) --}}
                        <button @click="sidebarOpen = true"
                                class="lg:hidden p-1.5 rounded-lg text-maroon-800 hover:bg-[#FAF6F0] transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        {{-- Page Heading Slot --}}
                        <div class="flex-1">
                            @isset($header)
                                {{ $header }}
                            @endisset
                        </div>
                    </div>
                </header>

                {{-- Page Body --}}
                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>

        </div>
    </body>
</html>
