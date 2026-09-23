<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Reservasi') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
            <div class="p-6 space-y-5">
                <div class="flex justify-between items-start gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Fasilitas</p>
                        <p class="font-semibold text-gray-900">{{ $reservation->facility->facility_name ?? '-' }}</p>
                        <p class="text-sm text-gray-500">{{ $reservation->facility->type ?? '' }} - {{ $reservation->facility->location ?? '' }}</p>
                    </div>
                    @php
                        $badges = [
                            'pending'   => 'bg-yellow-100 text-yellow-800',
                            'approved'  => 'bg-green-100 text-green-800',
                            'rejected'  => 'bg-red-100 text-red-800',
                            'cancelled' => 'bg-gray-100 text-gray-800',
                        ];
                    @endphp
                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $badges[$reservation->reservation_status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($reservation->reservation_status) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Tanggal</p>
                        <p class="font-medium text-gray-800">{{ $reservation->date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Waktu</p>
                        <p class="font-medium text-gray-800">{{ substr($reservation->start_time, 0, 5) }} - {{ substr($reservation->end_time, 0, 5) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Diajukan pada</p>
                        <p class="font-medium text-gray-800">{{ $reservation->created_at->format('d M Y H:i') }}</p>
                    </div>
                    @if($reservation->processedBy)
                        <div>
                            <p class="text-sm text-gray-500">Diproses oleh</p>
                            <p class="font-medium text-gray-800">{{ $reservation->processedBy->name }}</p>
                        </div>
                    @endif
                </div>

                <div>
                    <p class="text-sm text-gray-500">Tujuan Penggunaan</p>
                    <p class="text-gray-800">{{ $reservation->purpose }}</p>
                </div>

                {{-- US #9: alasan penolakan petugas, biar user tahu kenapa ditolak --}}
                @if($reservation->reservation_status === 'rejected' && $reservation->alasan_ditolak)
                    <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded">
                        <p class="text-sm font-semibold text-red-800">Alasan Ditolak</p>
                        <p class="mt-1 text-sm text-red-900">{{ $reservation->alasan_ditolak }}</p>
                    </div>
                @endif

                {{-- Alasan pembatalan, baik dibatalkan sendiri maupun oleh petugas --}}
                @if($reservation->reservation_status === 'cancelled' && $reservation->cancellation_reason)
                    <div class="p-4 bg-gray-50 border-l-4 border-gray-400 rounded">
                        <p class="text-sm font-semibold text-gray-700">Alasan Pembatalan</p>
                        <p class="mt-1 text-sm text-gray-700">{{ $reservation->cancellation_reason }}</p>
                    </div>
                @endif

                @if($reservation->logs->isNotEmpty())
                    <div>
                        <p class="text-sm text-gray-500 mb-2">Riwayat Status</p>
                        <ol class="space-y-3 border-l-2 border-gray-200 pl-4">
                            @foreach($reservation->logs as $log)
                                <li class="relative">
                                    <span class="absolute -left-[21px] top-1 w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                                    <p class="text-sm font-medium text-gray-800">
                                        {{ $log->status_before ? ucfirst($log->status_before) . ' → ' : '' }}{{ ucfirst($log->status_after) }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y H:i') }}
                                        @if($log->changedBy) &middot; oleh {{ $log->changedBy->name }} @endif
                                    </p>
                                    @if($log->notes)
                                        <p class="text-xs text-gray-600 mt-0.5">{{ $log->notes }}</p>
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                    </div>
                @endif

                <div class="flex justify-between items-center pt-2 border-t">
                    <a href="{{ route('reservations.index') }}" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50">
                        Kembali ke Riwayat
                    </a>

                    @if(in_array($reservation->reservation_status, ['pending', 'approved']))
                        <form method="POST" action="{{ route('reservations.destroy', $reservation->id_reservasi) }}"
                              onsubmit="return confirm('Apakah Anda yakin ingin membatalkan reservasi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 text-sm font-semibold text-red-600 border border-red-200 rounded-md hover:bg-red-50">
                                Batalkan Reservasi
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
