<x-petugas-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-extrabold text-lg leading-tight" style="color:#252B2B;">Dashboard Petugas</h2>
                <p class="text-xs mt-0.5" style="color:#4C4F54;">Pusat Pengelolaan Antrian Reservasi & Penanganan Laporan Kerusakan</p>
            </div>
            <div class="hidden sm:flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold shadow-xs"
                      style="background:#D1FAE5; color:#065F46; border:1px solid #A7F3D0;">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Petugas Standby
                </span>
            </div>
        </div>
    </x-slot>

    {{-- Container Konten Dashboard Petugas --}}
    <div class="px-4 sm:px-6 py-5 space-y-5" style="background:#FAF6F0;">

        {{-- Welcome Banner --}}
        <div class="relative overflow-hidden rounded-xl px-5 sm:px-6 py-4 flex items-center justify-between gap-4 shrink-0 shadow-xs"
             style="background: linear-gradient(135deg, #380F17 0%, #8F0B13 100%);">
            {{-- Aksen lingkaran dekoratif subtle --}}
            <div class="absolute -right-6 -bottom-10 w-40 h-40 rounded-full blur-xl pointer-events-none" style="background:rgba(239, 223, 197, 0.1);"></div>
            <div class="absolute right-24 -top-10 w-28 h-28 rounded-full blur-lg pointer-events-none" style="background:rgba(143, 11, 19, 0.4);"></div>

            <div class="relative z-10">
                <span class="inline-block text-[10px] font-bold tracking-widest uppercase px-2.5 py-1 rounded-md mb-2"
                      style="background:rgba(239, 223, 197, 0.15); color:#EFDFC5; border:1px solid rgba(239, 223, 197, 0.25);">
                    Portal Petugas Fasilitas
                </span>
                <h1 class="text-lg sm:text-xl font-extrabold text-white tracking-tight leading-snug">
                    Selamat Bertugas, {{ Auth::user()->name }}
                </h1>
                <p class="text-xs flex items-center gap-1.5 mt-1.5" style="color:#EFDFC5; opacity:0.9;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ now()->translatedFormat('l, d F Y') }}
                </p>
            </div>

            <div class="hidden sm:flex items-center justify-center w-11 h-11 rounded-xl backdrop-blur-sm shrink-0"
                 style="background:rgba(239, 223, 197, 0.12); color:#EFDFC5; border:1px solid rgba(239, 223, 197, 0.2);">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
        </div>

        {{-- Stat Cards (4 Cards Grid) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- 1: Reservasi Pending --}}
            <a href="{{ route('petugas.reservations.index', ['status' => 'pending']) }}"
               class="bg-white rounded-2xl p-5 shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-200 block group cursor-pointer"
               style="border:1px solid #EAE0D3; border-top:4px solid #8F0B13;">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider" style="color:#7C7F84;">Reservasi Pending</span>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-transform duration-200 group-hover:scale-110 shadow-xs"
                         style="background:#FEF3C7; color:#B45309; border:1px solid #FDE68A;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="text-3xl font-black transition-colors" style="color:#252B2B;">
                    {{ $stats['reservasi_pending'] }}
                </div>
                <p class="text-xs mt-2 font-medium" style="color:#7C7F84;">
                    Menunggu verifikasi persetujuan
                </p>
            </a>

            {{-- 2: Laporan Baru --}}
            <a href="{{ route('petugas.reports.index', ['status' => 'baru']) }}"
               class="bg-white rounded-2xl p-5 shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-200 block group cursor-pointer"
               style="border:1px solid #EAE0D3; border-top:4px solid #8F0B13;">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider" style="color:#7C7F84;">Laporan Baru</span>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-transform duration-200 group-hover:scale-110 shadow-xs"
                         style="background:#FEE2E2; color:#8F0B13; border:1px solid #FECACA;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                </div>
                <div class="text-3xl font-black transition-colors" style="color:#8F0B13;">
                    {{ $stats['laporan_baru'] }}
                </div>
                <p class="text-xs mt-2 font-medium" style="color:#7C7F84;">
                    Laporan belum diproses
                </p>
            </a>

            {{-- 3: Laporan Diproses --}}
            <a href="{{ route('petugas.reports.index', ['status' => 'diproses']) }}"
               class="bg-white rounded-2xl p-5 shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-200 block group cursor-pointer"
               style="border:1px solid #EAE0D3; border-top:4px solid #380F17;">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider" style="color:#7C7F84;">Laporan Diproses</span>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-transform duration-200 group-hover:scale-110 shadow-xs"
                         style="background:#FAF6F0; color:#380F17; border:1px solid #EAE0D3;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                </div>
                <div class="text-3xl font-black transition-colors" style="color:#252B2B;">
                    {{ $stats['laporan_diproses'] }}
                </div>
                <p class="text-xs mt-2 font-medium" style="color:#7C7F84;">
                    {{ $stats['fasilitas_perbaikan'] }} fasilitas dalam perbaikan
                </p>
            </a>

            {{-- 4: Reservasi Disetujui --}}
            <a href="{{ route('petugas.reservations.index', ['status' => 'approved']) }}"
               class="bg-white rounded-2xl p-5 shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-200 block group cursor-pointer"
               style="border:1px solid #EAE0D3; border-top:4px solid #059669;">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider" style="color:#7C7F84;">Reservasi Disetujui</span>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-transform duration-200 group-hover:scale-110 shadow-xs"
                         style="background:#D1FAE5; color:#065F46; border:1px solid #A7F3D0;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="text-3xl font-black transition-colors" style="color:#252B2B;">
                    {{ $stats['reservasi_approved'] }}
                </div>
                <p class="text-xs mt-2 font-medium" style="color:#7C7F84;">
                    {{ $stats['fasilitas_aktif'] }} fasilitas siap pakai
                </p>
            </a>

        </div>

        {{-- Dua Panel Antrian (Reservasi Menunggu & Laporan Baru) --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            {{-- Panel 1: Antrian Reservasi Menunggu --}}
            <div class="bg-white rounded-xl shadow-xs overflow-hidden" style="border:1px solid #EAE0D3;">
                <div class="p-4 sm:p-5" style="border-bottom:1px solid #EAE0D3; background:#FAF6F0/50;">
                    <h3 class="font-bold text-sm sm:text-base leading-tight" style="color:#252B2B;">Antrian Reservasi Menunggu</h3>
                    <p class="text-xs mt-0.5" style="color:#4C4F54;">Diurutkan dari pemohon yang paling lama menunggu</p>
                </div>

                @if($recentReservations->isEmpty())
                    <div class="py-12 px-6 text-center">
                        <div class="w-12 h-12 rounded-full mx-auto flex items-center justify-center mb-3"
                             style="background:#D1FAE5; color:#065F46;">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold" style="color:#252B2B;">Tidak ada reservasi menunggu</p>
                        <p class="text-xs mt-1" style="color:#4C4F54;">Semua antrian reservasi telah selesai diproses.</p>
                    </div>
                @else
                    <div class="divide-y" style="border-color:#EAE0D3;">
                        @foreach($recentReservations as $res)
                            <div class="p-4 sm:px-5 sm:py-3.5 flex justify-between items-center hover:bg-[#FAF6F0]/70 transition">
                                <div class="pr-3 min-w-0">
                                    <p class="text-sm font-bold truncate" style="color:#252B2B;">
                                        {{ $res->facility->facility_name ?? '-' }}
                                    </p>
                                    <p class="text-xs mt-0.5 flex flex-wrap items-center gap-1.5" style="color:#4C4F54;">
                                        <span class="font-medium" style="color:#252B2B;">{{ $res->user->name ?? '-' }}</span>
                                        <span>&bull;</span>
                                        <span>{{ $res->date->format('d M Y') }}</span>
                                        <span class="px-1.5 py-0.2 rounded text-[11px] font-mono" style="background:#FAF6F0; border:1px solid #EAE0D3;">
                                            {{ substr($res->start_time, 0, 5) }} - {{ substr($res->end_time, 0, 5) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="px-2 py-0.5 text-[11px] font-bold rounded-full"
                                          style="background:#FEF3C7; color:#92400E; border:1px solid #FDE68A;">
                                        Pending
                                    </span>
                                    <a href="{{ route('petugas.reservations.index') }}"
                                       class="px-2.5 py-1 text-xs font-bold rounded-lg text-white transition shadow-xs hover:brightness-110"
                                       style="background:#8F0B13;">
                                        Proses &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Panel 2: Antrian Laporan Baru --}}
            <div class="bg-white rounded-xl shadow-xs overflow-hidden" style="border:1px solid #EAE0D3;">
                <div class="p-4 sm:p-5" style="border-bottom:1px solid #EAE0D3; background:#FAF6F0/50;">
                    <h3 class="font-bold text-sm sm:text-base leading-tight" style="color:#252B2B;">Antrian Laporan Baru</h3>
                    <p class="text-xs mt-0.5" style="color:#4C4F54;">Laporan kerusakan yang belum ditangani</p>
                </div>

                @if($recentReports->isEmpty())
                    <div class="py-12 px-6 text-center">
                        <div class="w-12 h-12 rounded-full mx-auto flex items-center justify-center mb-3"
                             style="background:#D1FAE5; color:#065F46;">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold" style="color:#252B2B;">Tidak ada laporan baru</p>
                        <p class="text-xs mt-1" style="color:#4C4F54;">Fasilitas kampus dalam kondisi optimal tanpa laporan tertunda.</p>
                    </div>
                @else
                    <div class="divide-y" style="border-color:#EAE0D3;">
                        @foreach($recentReports as $report)
                            <div class="p-4 sm:px-5 sm:py-3.5 flex justify-between items-center hover:bg-[#FAF6F0]/70 transition">
                                <div class="pr-3 min-w-0">
                                    <p class="text-sm font-bold truncate" style="color:#252B2B;">
                                        {{ $report->facility->facility_name ?? '-' }}
                                    </p>
                                    <p class="text-xs mt-0.5 flex flex-wrap items-center gap-1.5" style="color:#4C4F54;">
                                        <span class="font-medium" style="color:#252B2B;">{{ $report->user->name ?? '-' }}</span>
                                        <span>&bull;</span>
                                        <span class="px-1.5 py-0.2 rounded text-[11px]" style="background:#FAF6F0; border:1px solid #EAE0D3; color:#4C4F54;">
                                            {{ $report->category->category_name ?? '-' }}
                                        </span>
                                        <span>&bull;</span>
                                        <span>{{ $report->created_at->format('d M Y') }}</span>
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="px-2 py-0.5 text-[11px] font-bold rounded-full"
                                          style="background:#FEE2E2; color:#991B1B; border:1px solid #FECACA;">
                                        Baru
                                    </span>
                                    <a href="{{ route('petugas.reports.show', $report->id_laporan) }}"
                                       class="px-2.5 py-1 text-xs font-bold rounded-lg transition shadow-xs hover:brightness-95"
                                       style="background:#FAF6F0; color:#380F17; border:1px solid #EAE0D3;">
                                        Detail &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

    </div>
</x-petugas-layout>