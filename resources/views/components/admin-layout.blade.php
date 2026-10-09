<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Admin – {{ config('app.name', 'SyncSpace') }}</title>

        {{-- Font: Plus Jakarta Sans --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

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
    <body class="antialiased overflow-hidden text-[#252B2B]" style="background:#FAF6F0; font-family:'Plus Jakarta Sans', sans-serif;">
        <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

            {{-- ===================== SIDEBAR ===================== --}}
            {{-- Mobile Backdrop --}}
            <div
                x-show="sidebarOpen"
                x-transition:enter="transition-opacity ease-linear duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="sidebarOpen = false"
                class="fixed inset-0 z-20 bg-black/50 backdrop-blur-sm lg:hidden"
            ></div>

            {{-- Sidebar dengan Gradien Maroon (#380F17 ke #8F0B13 sesuai gambar referensi) --}}
            <aside
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                class="fixed inset-y-0 left-0 z-30 w-64 flex flex-col transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:inset-auto lg:z-auto relative overflow-hidden"
                style="background: linear-gradient(170deg, #380F17 0%, #4D0E17 25%, #6B101C 55%, #820D17 80%, #8F0B13 100%); border-right:1px solid rgba(239, 223, 197, 0.15); box-shadow: 2px 0 16px rgba(56, 15, 23, 0.35);"
            >
                {{-- Efek Glow Halus di Sudut Bawah Sidebar --}}
                <div class="absolute -right-12 -bottom-12 w-44 h-44 rounded-full blur-2xl pointer-events-none" style="background:rgba(143, 11, 19, 0.45);"></div>
                <div class="absolute -left-10 top-20 w-32 h-32 rounded-full blur-xl pointer-events-none" style="background:rgba(239, 223, 197, 0.05);"></div>
                {{-- Logo SyncSpace (Tinggi h-16 agar garis bawahnya sejajar presisi dengan top header) --}}
                <div class="h-16 flex items-center gap-3 px-5 shrink-0" style="border-bottom:1px solid rgba(239, 223, 197, 0.12);">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group">
                        <x-application-logo class="h-9 w-auto drop-shadow-sm transition-transform duration-150 group-hover:scale-105" />
                        <span class="leading-tight">
                            <span class="block text-base font-extrabold tracking-tight text-[#EFDFC5] group-hover:text-white transition-colors">SyncSpace</span>
                            <span class="block text-[10px] font-semibold tracking-widest uppercase text-[#EFDFC5]/70">Portal Administrasi</span>
                        </span>
                    </a>
                </div>

                {{-- Navigation Items --}}
                <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto relative z-10">
                    @php
                        $adminNav = [
                            ['route' => 'admin.dashboard',        'match' => 'admin.dashboard',      'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'label' => 'Dashboard'],
                            ['route' => 'admin.fasilitas.index',   'match' => 'admin.fasilitas.*',    'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'label' => 'Fasilitas'],
                            ['route' => 'admin.users.index',       'match' => 'admin.users.*',        'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Data Pengguna'],
                            ['route' => 'admin.rekap.index',       'match' => 'admin.rekap.*',        'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'label' => 'Rekapitulasi Data'],
                        ];
                    @endphp

                    @foreach($adminNav as $item)
                        @php $active = request()->routeIs($item['match']); @endphp
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-150"
                           style="{{ $active
                                    ? 'background:rgba(239, 223, 197, 0.16); border:1px solid rgba(239, 223, 197, 0.28); color:#FFFFFF; box-shadow:0 2px 8px rgba(0,0,0,0.25);'
                                    : 'border:1px solid transparent; color:rgba(239, 223, 197, 0.78);' }}"
                           onmouseover="if(!{{ $active ? 'true' : 'false' }}) { this.style.background='rgba(239, 223, 197, 0.08)'; this.style.borderColor='rgba(239, 223, 197, 0.14)'; this.style.color='#EFDFC5'; }"
                           onmouseout="if(!{{ $active ? 'true' : 'false' }}) { this.style.background=''; this.style.borderColor='transparent'; this.style.color='rgba(239, 223, 197, 0.78)'; }"
                        >
                            <span class="p-1.5 rounded-lg shrink-0 transition-colors"
                                  style="{{ $active ? 'background:rgba(239, 223, 197, 0.22); color:#EFDFC5; border:1px solid rgba(239, 223, 197, 0.3);' : 'background:rgba(239, 223, 197, 0.08); color:rgba(239, 223, 197, 0.85); border:1px solid rgba(239, 223, 197, 0.12);' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                                </svg>
                            </span>
                            <span class="flex-1">{{ $item['label'] }}</span>
                            @if($active)
                                <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background:#EFDFC5; box-shadow:0 0 6px #EFDFC5;"></span>
                            @endif
                            @if($item['route'] === 'admin.users.index' && isset($adminPendingCount) && $adminPendingCount > 0)
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full shadow-xs"
                                      style="background:#EFDFC5; color:#380F17;">{{ $adminPendingCount }}</span>
                            @endif
                        </a>
                    @endforeach
                </nav>

                {{-- User Info + Logout Footer --}}
                <div class="px-4 py-3.5 relative z-10" style="border-top:1px solid rgba(239, 223, 197, 0.12); background:rgba(0, 0, 0, 0.15);">
                    <div class="flex items-center gap-3 mb-2.5">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center font-black text-sm shrink-0 shadow-sm"
                             style="background:#EFDFC5; color:#380F17; border:1px solid rgba(239, 223, 197, 0.35);">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-extrabold truncate text-[#EFDFC5] leading-tight">{{ Auth::user()->name }}</p>
                            <p class="text-xs font-semibold truncate text-[#EFDFC5] opacity-90 mt-0.5 leading-tight">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('profile.edit') }}"
                           class="flex-1 inline-flex items-center justify-center text-center text-xs font-medium px-2.5 py-1.5 rounded-lg transition whitespace-nowrap"
                           style="background:rgba(239, 223, 197, 0.1); border:1px solid rgba(239, 223, 197, 0.18); color:#EFDFC5;"
                           onmouseover="this.style.background='#8F0B13'; this.style.borderColor='#8F0B13';"
                           onmouseout="this.style.background='rgba(239, 223, 197, 0.1)'; this.style.borderColor='rgba(239, 223, 197, 0.18)';">
                            Profil Akun
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="flex-1 flex">
                            @csrf
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center text-center text-xs font-medium px-2.5 py-1.5 rounded-lg transition whitespace-nowrap"
                                    style="background:rgba(239, 223, 197, 0.1); border:1px solid rgba(239, 223, 197, 0.18); color:#EFDFC5;"
                                    onmouseover="this.style.background='#8F0B13'; this.style.borderColor='#8F0B13';"
                                    onmouseout="this.style.background='rgba(239, 223, 197, 0.1)'; this.style.borderColor='rgba(239, 223, 197, 0.18)';">
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            {{-- ===================== MAIN CONTENT ===================== --}}
            <div class="flex-1 flex flex-col min-w-0">
                {{-- Top bar (#FFFFFF dengan border #EAE0D3, tinggi h-16 agar garisnya sejajar dengan garis logo SyncSpace) --}}
                <header class="h-16 sticky top-0 z-10 bg-white flex items-center shrink-0" style="border-bottom:1px solid #EAE0D3; box-shadow:0 1px 2px rgba(37, 43, 43, 0.03);">
                    <div class="flex items-center gap-4 px-4 sm:px-6 w-full">
                        {{-- Hamburger (mobile only) --}}
                        <button @click="sidebarOpen = true"
                                class="lg:hidden p-1.5 rounded-lg text-[#380F17] hover:bg-[#FAF6F0] transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        {{-- Page heading slot --}}
                        <div class="flex-1">
                            @isset($header)
                                {{ $header }}
                            @endisset
                        </div>
                    </div>
                </header>

                <main class="flex-1 overflow-y-auto" style="background:#FAF6F0;">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
