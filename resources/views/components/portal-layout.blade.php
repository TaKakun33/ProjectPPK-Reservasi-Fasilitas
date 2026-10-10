{{--
    Kerangka halaman bersama untuk portal Admin dan Petugas (tampilan identik).
    Yang berbeda antar portal hanya isi menu, teks di bawah logo, dan judul tab.

    Props:
      judul     : judul tab browser, mis. 'Admin'
      portal    : teks kecil di bawah logo, mis. 'Portal Administrasi'
      homeRoute : nama route tujuan saat logo diklik
      nav       : daftar menu (route, match, icon, label, badge opsional)
--}}
@props(['judul', 'portal', 'homeRoute', 'nav'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('layouts.partials.head', ['judul' => $judul])
        @include('layouts.partials.mobile-pengguna')
        @include('layouts.partials.mobile-portal')
        @include('layouts.partials.session-guard')
    </head>
    <body class="antialiased" style="font-family:'Plus Jakarta Sans', sans-serif; background:#FAF6F0; color:#252B2B;">
        <div class="flex min-h-screen" x-data="{ sidebarOpen: false }">

            {{-- Sidebar --}}
            @include('layouts.partials.sidebar', ['portal' => $portal, 'homeRoute' => $homeRoute, 'nav' => $nav])

            {{-- Konten utama --}}
            <div class="flex-1 flex flex-col min-w-0">
                {{-- Top bar --}}
                <header class="mb-topbar bg-white shadow-xs sticky top-0 z-10" style="border-bottom: 4px solid #8F0B13;">
                    <div class="flex items-center gap-4 px-4 sm:px-6 py-4">
                        {{-- Hamburger (khusus mobile) --}}
                        <button @click="sidebarOpen = true"
                                class="lg:hidden p-1.5 rounded-lg text-maroon-800 hover:bg-[#FAF6F0] transition"
                                aria-label="Buka menu">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        {{-- Slot judul halaman --}}
                        <div class="flex-1 min-w-0">
                            @isset($header)
                                {{ $header }}
                            @endisset
                        </div>
                    </div>
                </header>

                {{-- Isi halaman --}}
                <main class="mb-main flex-1">
                    {{ $slot }}
                </main>
            </div>

        </div>

        {{-- Mobile: beri label kolom pada tiap sel tabel agar bisa tampil sebagai kartu --}}
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('main table').forEach(function (tabel) {
                    var judul = Array.from(tabel.querySelectorAll('thead th')).map(function (th) {
                        return th.textContent.replace(/\s+/g, ' ').trim();
                    });
                    tabel.querySelectorAll('tbody tr').forEach(function (baris) {
                        Array.from(baris.children).forEach(function (sel, i) {
                            if (judul[i] && !sel.dataset.label) sel.dataset.label = judul[i];
                        });
                    });
                });
            });
        </script>

        <x-popup :validasi="true" />
    </body>
</html>
