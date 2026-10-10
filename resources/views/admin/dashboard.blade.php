<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-extrabold text-lg leading-tight" style="color:#252B2B;">Administrator</h2>
                <p class="text-xs mt-0.5" style="color:#4C4F54;">Pusat Kendali Utama Operasional Fasilitas Kampus</p>
            </div>
            <div class="hidden sm:flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold shadow-xs"
                      style="background:#D1FAE5; color:#065F46; border:1px solid #A7F3D0;">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Sistem Operasional Aktif
                </span>
            </div>
        </div>
    </x-slot>

    {{-- Container Konten Dashboard --}}
    <div class="px-4 sm:px-6 py-5 space-y-5" style="background:#FAF6F0;">

        {{-- Welcome Banner (Gradien #380F17 Deep Maroon ke #8F0B13 Crimson Maroon) --}}
        <div class="relative overflow-hidden rounded-xl px-5 sm:px-6 py-4 flex items-center justify-between gap-4 shrink-0 shadow-xs"
             style="background: linear-gradient(135deg, #380F17 0%, #8F0B13 100%);">
            {{-- Aksen lingkaran dekoratif subtle --}}
            <div class="absolute -right-6 -bottom-10 w-40 h-40 rounded-full blur-xl pointer-events-none" style="background:rgba(239, 223, 197, 0.1);"></div>
            <div class="absolute right-24 -top-10 w-28 h-28 rounded-full blur-lg pointer-events-none" style="background:rgba(143, 11, 19, 0.4);"></div>

            <div class="relative z-10">
                <span class="inline-block text-[11px] font-bold tracking-widest uppercase px-2.5 py-1 rounded-md mb-2"
                      style="background:rgba(239, 223, 197, 0.15); color:#EFDFC5; border:1px solid rgba(239, 223, 197, 0.25);">
                    Selamat Datang di Portal Administrasi
                </span>
                <h1 class="text-lg sm:text-xl font-extrabold text-white tracking-tight leading-snug">
                    {{ Auth::user()->name }}
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        </div>

        {{-- Stat Cards (gaya sama dengan dashboard petugas) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            <a href="{{ route('admin.fasilitas.index') }}"
               class="bg-white rounded-2xl p-5 shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-200 block group cursor-pointer"
               style="border:1px solid #EAE0D3; border-top:4px solid #8F0B13;">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider" style="color:#7C7F84;">Total Fasilitas</span>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-transform duration-200 group-hover:scale-110 shadow-xs"
                         style="background:#FAF2E8; color:#8F0B13; border:1px solid #E2D0B4;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>
                <div class="text-3xl font-black transition-colors" style="color:#8F0B13;">
                    {{ $stats['total_fasilitas'] }}
                </div>
                <p class="text-xs mt-2 font-medium" style="color:#7C7F84;">
                    {{ $stats['fasilitas_aktif'] }} fasilitas berstatus aktif
                </p>
            </a>

            <a href="{{ route('admin.rekap.index', ['status' => 'pending']) }}"
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

            <a href="{{ route('admin.rekap.index') }}"
               class="bg-white rounded-2xl p-5 shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-200 block group cursor-pointer"
               style="border:1px solid #EAE0D3; border-top:4px solid #380F17;">
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
                    Laporan belum ditindaklanjuti
                </p>
            </a>

            <a href="{{ route('admin.users.index', ['status' => 'pending']) }}"
               class="bg-white rounded-2xl p-5 shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-200 block group cursor-pointer"
               style="border:1px solid #EAE0D3; border-top:4px solid #380F17;">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider" style="color:#7C7F84;">Pengguna Menunggu</span>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-transform duration-200 group-hover:scale-110 shadow-xs"
                         style="background:#FAF6F0; color:#380F17; border:1px solid #EAE0D3;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                </div>
                <div class="text-3xl font-black transition-colors" style="color:#252B2B;">
                    {{ $stats['user_pending'] }}
                </div>
                <p class="text-xs mt-2 font-medium" style="color:#7C7F84;">
                    {{ $stats['total_user'] }} total akun terdaftar
                </p>
            </a>

        </div>

        {{-- Tabel Bawah: grid 2 kolom dengan tinggi natural (items-start) agar membungkus data secara presisi tanpa ruang kosong di bawahnya --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            {{-- Kolom 1: Permohonan Reservasi Terkini --}}
            <div class="bg-white rounded-xl shadow-xs overflow-hidden" style="border:1px solid #EAE0D3;">
                <div class="flex items-center justify-between gap-3 p-4 sm:p-5" style="border-bottom:1px solid #EAE0D3;">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full" style="background:#8F0B13;"></span>
                        <h3 class="font-bold text-sm sm:text-base leading-tight" style="color:#252B2B;">Permohonan Reservasi Terkini</h3>
                    </div>
                    <a href="{{ route('admin.rekap.index') }}"
                       class="text-xs font-semibold flex items-center gap-1 transition text-maroon-700 hover:text-maroon-900">
                        <span>Lihat Semua Data</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                @if($recentReservations->isEmpty())
                    <div class="flex flex-col items-center justify-center py-12 px-6 text-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center mb-2" style="background:#FAF2E8; color:#4C4F54;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <p class="text-xs font-medium" style="color:#4C4F54;">Belum ada permohonan reservasi yang tercatat.</p>
                    </div>
                @else
                    <div class="divide-y" style="border-color:#EAE0D3;">
                        @foreach($recentReservations as $res)
                            @php
                                $badgeData = [
                                    'pending'   => ['style' => 'background:#FEF3C7; color:#92400E; border:1px solid #FDE68A;', 'label' => 'Menunggu Persetujuan'],
                                    'approved'  => ['style' => 'background:#D1FAE5; color:#065F46; border:1px solid #A7F3D0;', 'label' => 'Disetujui'],
                                    'rejected'  => ['style' => 'background:#FEE2E2; color:#991B1B; border:1px solid #FECACA;', 'label' => 'Ditolak'],
                                    'cancelled' => ['style' => 'background:#F3F4F6; color:#4C4F54; border:1px solid #E5E7EB;', 'label' => 'Dibatalkan'],
                                ];
                                $bItem = $badgeData[$res->reservation_status] ?? ['style' => 'background:#F3F4F6; color:#4C4F54; border:1px solid #E5E7EB;', 'label' => ucfirst($res->reservation_status)];
                            @endphp
                            <div class="flex justify-between items-center p-4 sm:px-5 sm:py-3.5 hover:bg-[#FAF6F0]/70 transition">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-7 h-7 rounded-full font-bold text-xs flex items-center justify-center shrink-0"
                                         style="background:#EFDFC5; color:#380F17; border:1px solid #E2D0B4;">
                                        {{ strtoupper(substr($res->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold truncate" style="color:#252B2B;">{{ $res->facility->facility_name ?? '-' }}</p>
                                        <p class="text-[11px] truncate" style="color:#4C4F54;">{{ $res->user->name ?? '-' }} &bull; {{ $res->date->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 text-[11px] font-bold rounded-full shrink-0 ml-2" style="{{ $bItem['style'] }}">
                                    {{ $bItem['label'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Kolom 2: Laporan Kerusakan Sarana Terkini --}}
            <div class="bg-white rounded-xl shadow-xs overflow-hidden" style="border:1px solid #EAE0D3;">
                <div class="flex items-center justify-between gap-3 p-4 sm:p-5" style="border-bottom:1px solid #EAE0D3;">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full" style="background:#380F17;"></span>
                        <h3 class="font-bold text-sm sm:text-base leading-tight" style="color:#252B2B;">Laporan Kerusakan Sarana Terkini</h3>
                    </div>
                    <a href="{{ route('admin.rekap.index') }}"
                       class="text-xs font-semibold flex items-center gap-1 transition text-maroon-700 hover:text-maroon-900">
                        <span>Lihat Semua Data</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                @if($recentReports->isEmpty())
                    <div class="flex flex-col items-center justify-center py-12 px-6 text-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center mb-2" style="background:#FAF2E8; color:#4C4F54;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-medium" style="color:#4C4F54;">Tidak ada laporan kerusakan sarana yang memerlukan tindakan.</p>
                    </div>
                @else
                    <div class="divide-y" style="border-color:#EAE0D3;">
                        @foreach($recentReports as $report)
                            @php
                                $reportBadgeData = [
                                    'baru'            => ['style' => 'background:#FAF2E8; color:#380F17; border:1px solid #E2D0B4;', 'label' => 'Laporan Baru'],
                                    'dalam perbaikan' => ['style' => 'background:#FEF3C7; color:#92400E; border:1px solid #FDE68A;', 'label' => 'Dalam Pemeliharaan'],
                                    'diproses'        => ['style' => 'background:#FEF3C7; color:#92400E; border:1px solid #FDE68A;', 'label' => 'Sedang Diproses'],
                                    'selesai'         => ['style' => 'background:#D1FAE5; color:#065F46; border:1px solid #A7F3D0;', 'label' => 'Selesai Ditangani'],
                                    'ditolak'         => ['style' => 'background:#FEE2E2; color:#991B1B; border:1px solid #FECACA;', 'label' => 'Ditolak'],
                                ];
                                $rItem = $reportBadgeData[$report->report_status] ?? ['style' => 'background:#FAF2E8; color:#380F17; border:1px solid #E2D0B4;', 'label' => ucfirst($report->report_status)];
                            @endphp
                            <div class="flex justify-between items-center p-4 sm:px-5 sm:py-3.5 hover:bg-[#FAF6F0]/70 transition">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0"
                                         style="background:#EFDFC5; color:#8F0B13; border:1px solid #E2D0B4;">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold truncate" style="color:#252B2B;">{{ $report->facility->facility_name ?? '-' }}</p>
                                        <p class="text-[11px] truncate" style="color:#4C4F54;">{{ $report->user->name ?? '-' }} &bull; {{ $report->category->category_name ?? '-' }}</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 text-[11px] font-bold rounded-full shrink-0 ml-2" style="{{ $rItem['style'] }}">
                                    {{ $rItem['label'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-admin-layout>
