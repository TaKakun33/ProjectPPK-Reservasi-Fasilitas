<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Riwayat Reservasi Saya') }}
            </h2>
            <a href="{{ route('reservations.create') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700">
                + Ajukan Reservasi Baru
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
            @if($reservations->isEmpty())
                <div class="p-12 text-center text-gray-500">
                    Anda belum memiliki riwayat reservasi fasilitas.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 font-semibold border-b">
                                <th class="p-4">Fasilitas</th>
                                <th class="p-4">Tanggal</th>
                                <th class="p-4">Waktu</th>
                                <th class="p-4">Tujuan</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($reservations as $res)
                                <tr class="hover:bg-gray-50">
                                    <td class="p-4 font-medium text-gray-900">{{ $res->facility->facility_name ?? '-' }}</td>
                                    <td class="p-4">{{ $res->date->format('d M Y') }}</td>
                                    <td class="p-4">{{ substr($res->start_time, 0, 5) }} - {{ substr($res->end_time, 0, 5) }}</td>
                                    <td class="p-4 text-gray-600">{{ $res->purpose }}</td>
                                    <td class="p-4">
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
                                    </td>
                                    <td class="p-4 text-center">
                                        <a href="{{ route('reservations.show', $res->id_reservasi) }}"
                                           class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                                            Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t">
                    {{ $reservations->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
