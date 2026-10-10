<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Admin – {{ config('app.name', 'SyncSpace') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

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
    <body class="font-sans antialiased bg-gray-100">
        <div class="flex min-h-screen" x-data="{ sidebarOpen: false }">

            {{-- ===================== SIDEBAR ===================== --}}
            {{-- Overlay mobile --}}
            <div
                x-show="sidebarOpen"
                x-transition:enter="transition-opacity ease-linear duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="sidebarOpen = false"
                class="fixed inset-0 z-20 bg-black/40 lg:hidden"
            ></div>

            <aside
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                class="fixed inset-y-0 left-0 z-30 w-64 flex flex-col bg-maroon-800 transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:inset-auto lg:z-auto"
            >
                {{-- Logo --}}
                <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                        <x-application-logo class="h-9 w-auto" />
                        <span class="leading-tight">
                            <span class="block text-sm font-bold text-cream-100 tracking-wide">SyncSpace</span>
                            <span class="block text-[10px] font-medium text-cream-300 tracking-widest uppercase">Portal Administrasi</span>
                        </span>
                    </a>
                </div>

                {{-- Nav Items --}}
                <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                    @php
                        $adminNav = [
                            ['route' => 'admin.dashboard',        'match' => 'admin.dashboard',      'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'label' => 'Dashboard Admin'],
                            ['route' => 'admin.fasilitas.index',   'match' => 'admin.fasilitas.*',    'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'label' => 'Fasilitas Kampus'],
                            ['route' => 'admin.users.index',       'match' => 'admin.users.*',        'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Data Pengguna'],
                            ['route' => 'admin.rekap.index',       'match' => 'admin.rekap.*',        'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'label' => 'Rekapitulasi Data'],
                        ];
                    @endphp

                    @foreach($adminNav as $item)
                        @php $active = request()->routeIs($item['match']); @endphp
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition
                                  {{ $active
                                     ? 'bg-white/20 text-white'
                                     : 'text-cream-200 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                            </svg>
                            {{ $item['label'] }}
                            @if($item['route'] === 'admin.users.index' && isset($adminPendingCount) && $adminPendingCount > 0)
                                <span class="ml-auto bg-orange-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $adminPendingCount }}</span>
                            @endif
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
                            <p class="text-sm font-extrabold text-cream-100 truncate leading-tight">{{ Auth::user()->name }}</p>
                            <p class="text-xs font-semibold text-cream-200 truncate mt-0.5 leading-tight">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('profile.edit') }}"
                           class="flex-1 inline-flex items-center justify-center text-center text-xs font-medium text-cream-200 hover:text-white hover:bg-white/10 px-3 py-1.5 rounded-lg transition whitespace-nowrap">
                            Profil Akun
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="flex-1 flex">
                            @csrf
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center text-xs font-medium text-cream-200 hover:text-white hover:bg-white/10 px-3 py-1.5 rounded-lg transition whitespace-nowrap">
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            {{-- ===================== MAIN CONTENT ===================== --}}
            <div class="flex-1 flex flex-col min-w-0">
                {{-- Top bar (mobile hamburger + page title) --}}
                <header class="bg-white shadow-sm border-b-4 border-maroon-800 sticky top-0 z-10">
                    <div class="flex items-center gap-4 px-4 sm:px-6 py-4">
                        {{-- Hamburger (mobile only) --}}
                        <button @click="sidebarOpen = true"
                                class="lg:hidden p-1.5 rounded-md text-maroon-800 hover:bg-maroon-50 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        {{-- Page heading slot --}}
                        <div class="flex-1 text-maroon-800">
                            @isset($header)
                                {{ $header }}
                            @endisset
                        </div>
                    </div>
                </header>

                {{-- Page Content --}}
                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <x-popup :validasi="true" />
    </body>
</html>
