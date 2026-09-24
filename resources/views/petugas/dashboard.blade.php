<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Petugas') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{-- Reservasi Pending --}}
            <a href="{{ route('petugas.reservations.index', ['status' => 'pending']) }}"
               class="block bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md hover:border-yellow-300 transition group cursor-pointer">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-50 text-yellow-600 group-hover:bg-yellow-100 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 group-hover:text-yellow-700 transition">Reservasi Pending</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['reservasi_pending'] }}</p>
                    </div>
                </div>
                <div class="mt-3 flex justify-between items-center text-xs text-gray-500">
                    <span>Menunggu persetujuan</span>
                    <span class="font-semibold text-yellow-700 group-hover:underline">Buka antrian &rarr;</span>
                </div>
            </a>

            {{-- Laporan Baru --}}
            <a href="{{ route('petugas.reports.index', ['status' => 'baru']) }}"
               class="block bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md hover:border-red-300 transition group cursor-pointer">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-50 text-red-600 group-hover:bg-red-100 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 group-hover:text-red-700 transition">Laporan Baru</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['laporan_baru'] }}</p>
                    </div>
                </div>
                <div class="mt-3 flex justify-between items-center text-xs text-gray-500">
                    <span>Belum diproses</span>
                    <span class="font-semibold text-red-700 group-hover:underline">Tindak lanjuti &rarr;</span>
                </div>
            </a>

            {{-- Laporan Sedang Diproses --}}
            <a href="{{ route('petugas.reports.index', ['status' => 'diproses']) }}"
               class="block bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md hover:border-blue-300 transition group cursor-pointer">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-50 text-blue-600 group-hover:bg-blue-100 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 group-hover:text-blue-700 transition">Laporan Diproses</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['laporan_diproses'] }}</p>
                    </div>
                </div>
                <div class="mt-3 flex justify-between items-center text-xs text-gray-500">
                    <span>{{ $stats['fasilitas_perbaikan'] }} fasilitas perbaikan</span>
                    <span class="font-semibold text-blue-700 group-hover:underline">Lihat progress &rarr;</span>
                </div>
            </a>

            {{-- Reservasi Approved --}}
            <a href="{{ route('petugas.reservations.index', ['status' => 'approved']) }}"
               class="block bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md hover:border-green-300 transition group cursor-pointer">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-50 text-green-600 group-hover:bg-green-100 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 group-hover:text-green-700 transition">Reservasi Disetujui</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['reservasi_approved'] }}</p>
                    </div>
                </div>
                <div class="mt-3 flex justify-between items-center text-xs text-gray-500">
                    <span>{{ $stats['fasilitas_aktif'] }} fasilitas siap pakai</span>
                    <span class="font-semibold text-green-700 group-hover:underline">Lihat detail &rarr;</span>
                </div>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Antrian & Reservasi Terbaru --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="font-semibold text-gray-900">Antrian & Reservasi Terbaru</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Pantau status permohonan reservasi</p>
                    </div>
                    <a href="{{ route('petugas.reservations.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        Buka Antrian &rarr;
                    </a>
                </div>
                @if($recentReservations->isEmpty())
                    <div class="p-6 text-center text-gray-500 text-sm">Belum ada reservasi.</div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($recentReservations as $res)
                            <div class="px-6 py-4 flex justify-between items-center hover:bg-gray-50/60 transition">
                                <div class="pr-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $res->facility->facility_name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ $res->user->name ?? '-' }} &bull; {{ $res->date->format('d M Y') }}
                                        <span class="text-gray-400">({{ substr($res->start_time, 0, 5) }} - {{ substr($res->end_time, 0, 5) }})</span>
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2 shrink-0">
                                    @php
                                        $badges = [
                                            'pending'   => 'bg-yellow-100 text-yellow-800',
                                            'approved'  => 'bg-green-100 text-green-800',
                                            'rejected'  => 'bg-red-100 text-red-800',
                                            'cancelled' => 'bg-gray-100 text-gray-800',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $badges[$res->reservation_status] ?? 'bg-gray-100' }}">
                                        {{ ucfirst($res->reservation_status) }}
                                    </span>
                                    @if($res->reservation_status === 'pending')
                                        <a href="{{ route('petugas.reservations.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                            Proses &rarr;
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Laporan Kerusakan Terbaru --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="font-semibold text-gray-900">Laporan Kerusakan Terbaru</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Pantau laporan fasilitas yang rusak</p>
                    </div>
                    <a href="{{ route('petugas.reports.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        Buka Antrian &rarr;
                    </a>
                </div>
                @if($recentReports->isEmpty())
                    <div class="p-6 text-center text-gray-500 text-sm">Belum ada laporan kerusakan.</div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($recentReports as $report)
                            <div class="px-6 py-4 flex justify-between items-center hover:bg-gray-50/60 transition">
                                <div class="pr-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $report->facility->facility_name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ $report->user->name ?? '-' }} &bull; {{ $report->category->category_name ?? '-' }} &bull; {{ $report->created_at->format('d M Y') }}
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2 shrink-0">
                                    @php
                                        $reportBadges = [
                                            'baru'            => 'bg-blue-100 text-blue-800',
                                            'diproses'        => 'bg-yellow-100 text-yellow-800',
                                            'selesai'         => 'bg-green-100 text-green-800',
                                            'ditolak'         => 'bg-red-100 text-red-800',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $reportBadges[$report->report_status] ?? 'bg-gray-100' }}">
                                        {{ ucfirst($report->report_status) }}
                                    </span>
                                    <a href="{{ route('petugas.reports.show', $report->id_laporan) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
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
</x-app-layout>