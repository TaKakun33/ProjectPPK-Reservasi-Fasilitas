<nav x-data="{ open: false }" class="sticky top-0 z-50 shadow-md" style="background: linear-gradient(135deg, #380F17 0%, #4D0E17 30%, #6B101C 65%, #8F0B13 100%); border-bottom: 1px solid rgba(239, 223, 197, 0.15);">
    {{-- Primary Navigation Menu --}}
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="mp-nav-bar flex justify-between h-16">
            <div class="flex">
                {{-- Logo --}}
                <div class="shrink-0 flex items-center gap-3">
                    <a href="{{ route('facilities.index') }}" class="flex items-center gap-3 group">
                        <x-application-logo class="block h-9 w-auto drop-shadow-sm transition-transform duration-150 group-hover:scale-105" />
                        <span class="mp-m-inline text-base font-extrabold tracking-tight text-[#EFDFC5]" style="font-size:1.05rem;">SyncSpace</span>
                        <span class="hidden md:block leading-tight">
                            <span class="block text-sm font-extrabold tracking-tight text-[#EFDFC5] group-hover:text-white transition-colors">SyncSpace</span>
                            <span class="block text-[11px] font-semibold tracking-widest uppercase text-[#EFDFC5]/70">Reservasi Fasilitas Kampus</span>
                        </span>
                    </a>
                </div>

                {{-- Navigation Links --}}
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @auth
                        @if(auth()->user()->role === \App\Enums\UserRole::Petugas)
                            <x-nav-link :href="route('petugas.dashboard')" :active="request()->routeIs('petugas.dashboard') || request()->routeIs('dashboard')">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                            <x-nav-link :href="route('petugas.reservations.index')" :active="request()->routeIs('petugas.reservations.*')">
                                {{ __('Antrian Reservasi') }}
                            </x-nav-link>
                            <x-nav-link :href="route('petugas.reports.index')" :active="request()->routeIs('petugas.reports.*')">
                                {{ __('Laporan Kerusakan') }}
                            </x-nav-link>
                        @endif
                    @endauth
                    {{-- Menu Admin --}}
                    @auth
                        @if(auth()->user()->role === \App\Enums\UserRole::Admin)
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                                {{ __('Dashboard Admin') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.fasilitas.index')" :active="request()->routeIs('admin.fasilitas.*')">
                                {{ __('Fasilitas Kampus') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                {{ __('Data Pengguna') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.rekap.index')" :active="request()->routeIs('admin.rekap.*')">
                                {{ __('Rekapitulasi Data') }}
                            </x-nav-link>
                        @endif
                    @endauth

                    {{-- Menu Khusus Pengguna Login Milik Anda --}}
                    @auth
                        @if(auth()->user()->role === \App\Enums\UserRole::Pengguna)
                            <x-nav-link :href="route('facilities.index')" :active="request()->routeIs('facilities.*')">
                                {{ __('Daftar Fasilitas') }}
                            </x-nav-link>
                            <x-nav-link :href="route('reservations.index')" :active="request()->routeIs('reservations.*')">
                                {{ __('Reservasi Saya') }}
                            </x-nav-link>
                            <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                                {{ __('Laporan Kerusakan') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            {{-- Settings Dropdown (Khusus Login) / Tombol Login (Untuk Tamu Publik) --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-2 px-3 py-1.5 border text-xs leading-4 font-bold rounded-lg transition ease-in-out duration-150 shadow-xs bg-cream/10 border border-cream/20 text-cream hover:bg-maroon-700 hover:border-maroon-700">
                                <div class="w-6 h-6 rounded-md flex items-center justify-center font-black text-xs shrink-0 shadow-xs"
                                     style="background:#EFDFC5; color:#380F17;">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <div class="max-w-[120px] truncate">{{ Auth::user()->name }}</div>

                                <div class="ms-0.5">
                                    <svg class="fill-current h-3.5 w-3.5 opacity-80" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profil Akun') }}
                            </x-dropdown-link>

                            {{-- Authentication --}}
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Keluar') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('login') }}" class="text-sm font-semibold transition text-cream hover:text-white">Masuk</a>
                        <a href="{{ route('register') }}" class="text-sm font-bold px-4 py-2 rounded-lg transition shadow-xs bg-maroon-700 text-cream border border-cream/25 hover:bg-maroon-900">Daftar</a>
                    </div>
                @endauth
            </div>

            {{-- Hamburger Responsive (mobile): tamu langsung melihat tombol Masuk / Daftar, pengguna login memakai menu --}}
            <div class="-me-2 flex items-center gap-2 sm:hidden">
                @guest
                    <div class="mp-nav-auth flex items-center gap-2 me-2">
                        <a href="{{ route('login') }}" class="mp-nav-login">Masuk</a>
                        <a href="{{ route('register') }}" class="mp-nav-daftar">Daftar</a>
                    </div>
                @endguest
                @auth
                    <button @click="open = ! open" :aria-expanded="open" aria-label="Menu navigasi" class="mp-hamburger inline-flex items-center justify-center p-2 rounded-md text-cream-200 hover:text-white hover:bg-white/10 focus:outline-none focus:bg-white/10 focus:text-white transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endauth
            </div>
        </div>
    </div>

    {{-- Responsive Navigation Menu --}}
    <div :class="{'block': open, 'hidden': ! open}" class="mp-drawer hidden sm:hidden bg-maroon-900 border-t border-cream-300/10">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                @if(auth()->user()->role === \App\Enums\UserRole::Petugas)
                    <x-responsive-nav-link :href="route('petugas.dashboard')" :active="request()->routeIs('petugas.dashboard') || request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('petugas.reservations.index')" :active="request()->routeIs('petugas.reservations.*')">
                        {{ __('Antrian Reservasi') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('petugas.reports.index')" :active="request()->routeIs('petugas.reports.*')">
                        {{ __('Laporan Kerusakan') }}
                    </x-responsive-nav-link>
                @endif
                @if(auth()->user()->role === \App\Enums\UserRole::Admin)
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        {{ __('Dashboard Admin') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.fasilitas.index')" :active="request()->routeIs('admin.fasilitas.*')">
                        {{ __('Fasilitas Kampus') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                        {{ __('Data Pengguna') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.rekap.index')" :active="request()->routeIs('admin.rekap.*')">
                        {{ __('Rekapitulasi Data') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
            <x-responsive-nav-link :href="route('facilities.index')" :active="request()->routeIs('facilities.*')">
                {{ __('Daftar Fasilitas') }}
            </x-responsive-nav-link>
            @auth
                @if(auth()->user()->role === \App\Enums\UserRole::Pengguna)
                    <x-responsive-nav-link :href="route('reservations.index')" :active="request()->routeIs('reservations.*')">
                        {{ __('Reservasi Saya') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                        {{ __('Laporan Kerusakan') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        {{-- Responsive Settings Options --}}
        <div class="pt-4 pb-1 border-t border-cream-300/10">
            @auth
                <div class="px-4">
                    <div class="font-medium text-base text-cream-100">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-cream-300">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profil Akun') }}
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Keluar') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="px-4 py-2 space-y-2">
                    <a href="{{ route('login') }}" class="block text-sm font-medium text-cream-200">Masuk</a>
                    <a href="{{ route('register') }}" class="inline-block text-sm font-bold text-cream-100 bg-maroon-700 px-4 py-2 rounded-md">Daftar</a>
                </div>
            @endauth
        </div>
    </div>
</nav>
