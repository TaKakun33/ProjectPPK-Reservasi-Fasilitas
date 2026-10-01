<nav x-data="{ open: false }" class="bg-navy-700 border-b-4 border-gold-400 shadow-md">
    {{-- Primary Navigation Menu --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                {{-- Logo --}}
                <div class="shrink-0 flex items-center gap-3">
                    <a href="{{ route('facilities.index') }}" class="flex items-center gap-3">
                        <x-application-logo class="block h-9 w-auto" />
                        <span class="hidden md:block leading-tight">
                            <span class="block text-sm font-bold text-white tracking-wide">RESERVASI FASILITAS</span>
                            <span class="block text-[11px] font-medium text-gold-300 tracking-widest uppercase">Portal Akademik Kampus</span>
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
                                {{ __('Dashboard') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.fasilitas.index')" :active="request()->routeIs('admin.fasilitas.*')">
                                {{ __('Fasilitas') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                {{ __('User') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.rekap.index')" :active="request()->routeIs('admin.rekap.*')">
                                {{ __('Rekap') }}
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
                            <button class="inline-flex items-center px-3 py-2 border border-white/20 text-sm leading-4 font-medium rounded-md text-white bg-white/10 hover:bg-white/20 hover:text-white focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            {{-- Authentication --}}
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('login') }}" class="text-sm font-medium text-blue-100 hover:text-white">Log in</a>
                        <a href="{{ route('register') }}" class="text-sm font-bold text-navy-900 bg-gold-400 hover:bg-gold-500 px-4 py-2 rounded-md transition">Register</a>
                    </div>
                @endauth
            </div>

            {{-- Hamburger Responsive --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-blue-100 hover:text-white hover:bg-white/10 focus:outline-none focus:bg-white/10 focus:text-white transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Responsive Navigation Menu --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-navy-800 border-t border-white/10">
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
                        {{ __('Fasilitas') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                        {{ __('User') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.rekap.index')" :active="request()->routeIs('admin.rekap.*')">
                        {{ __('Rekap') }}
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
        <div class="pt-4 pb-1 border-t border-white/10">
            @auth
                <div class="px-4">
                    <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-blue-200">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="px-4 py-2 space-y-2">
                    <a href="{{ route('login') }}" class="block text-sm font-medium text-blue-100">Log in</a>
                    <a href="{{ route('register') }}" class="inline-block text-sm font-bold text-navy-900 bg-gold-400 px-4 py-2 rounded-md">Register</a>
                </div>
            @endauth
        </div>
    </div>
</nav>