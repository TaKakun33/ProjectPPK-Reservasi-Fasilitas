<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-indigo-50 text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Fasilitas</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_fasilitas'] }}</p>
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-500">{{ $stats['fasilitas_aktif'] }} aktif</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-50 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Reservasi Pending</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['reservasi_pending'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-50 text-red-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Laporan Baru</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['laporan_baru'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-50 text-orange-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">User Pending</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['user_pending'] }}</p>
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-500">{{ $stats['total_user'] }} total user</p>
                @if($stats['user_pending'] > 0)
                    <a href="{{ route('admin.users.index', ['status' => 'pending']) }}" class="mt-2 inline-block text-xs font-semibold text-orange-600 hover:text-orange-800">Review sekarang &rarr;</a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Reservasi Terbaru --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-900">Reservasi Terbaru</h3>
                    <a href="{{ route('admin.rekap.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Lihat Rekap &rarr;</a>
                </div>
                @if($recentReservations->isEmpty())
                    <div class="p-6 text-center text-gray-500 text-sm">Belum ada reservasi.</div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($recentReservations as $res)
                            <div class="px-6 py-4 flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $res->facility->facility_name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">{{ $res->user->name ?? '-' }} &bull; {{ $res->date->format('d M Y') }}</p>
                                </div>
                                @php
                                    $badges = [
                                        'pending'   => 'bg-yellow-100 text-yellow-800',
                                        'approved'  => 'bg-green-100 text-green-800',
                                        'rejected'  => 'bg-red-100 text-red-800',
                                        'cancelled' => 'bg-gray-100 text-gray-800',
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $badges[$res->reservation_status] ?? 'bg-gray-100' }}">
                                    {{ ucfirst($res->reservation_status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Laporan Terbaru --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-900">Laporan Kerusakan Terbaru</h3>
                    <a href="{{ route('admin.rekap.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Lihat Rekap &rarr;</a>
                </div>
                @if($recentReports->isEmpty())
                    <div class="p-6 text-center text-gray-500 text-sm">Belum ada laporan.</div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($recentReports as $report)
                            <div class="px-6 py-4 flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $report->facility->facility_name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">{{ $report->user->name ?? '-' }} &bull; {{ $report->category->category_name ?? '-' }}</p>
                                </div>
                                @php
                                    $reportBadges = [
                                        'baru'            => 'bg-blue-100 text-blue-800',
                                        'dalam perbaikan' => 'bg-yellow-100 text-yellow-800',
                                        'selesai'         => 'bg-green-100 text-green-800',
                                        'ditolak'         => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $reportBadges[$report->report_status] ?? 'bg-gray-100' }}">
                                    {{ ucfirst($report->report_status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
