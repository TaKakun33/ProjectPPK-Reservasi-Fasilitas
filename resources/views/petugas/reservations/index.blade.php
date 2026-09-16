<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Antrian Reservasi') }}
        </h2>
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
                    Tidak ada reservasi yang menunggu diproses.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 font-semibold border-b">
                                <th class="p-4">Pemohon</th>
                                <th class="p-4">Fasilitas</th>
                                <th class="p-4">Tanggal</th>
                                <th class="p-4">Waktu</th>
                                <th class="p-4">Tujuan</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($reservations as $res)
                                <tr class="hover:bg-gray-50">
                                    {{-- Pemohon --}}
                                    <td class="p-4 font-medium text-gray-900">{{ $res->user->name ?? '-' }}</td>

                                    {{-- Fasilitas --}}
                                    <td class="p-4">{{ $res->facility->facility_name ?? '-' }}</td>

                                    {{-- Tanggal --}}
                                    <td class="p-4">{{ $res->date->format('d M Y') }}</td>

                                    {{-- Waktu --}}
                                    <td class="p-4">{{ substr($res->start_time, 0, 5) }} - {{ substr($res->end_time, 0, 5) }}</td>

                                    {{-- Tujuan --}}
                                    <td class="p-4 text-gray-600">{{ $res->purpose }}</td>

                                    {{-- Aksi --}}
                                    <td class="p-4">
                                        <div class="flex justify-center gap-2">
                                            <form method="POST"
                                                  action="{{ route('petugas.reservations.approve', $res->id_reservasi) }}"
                                                  onsubmit="return confirm('Setujui reservasi ini?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="px-3 py-1 text-xs font-semibold text-white bg-green-600 rounded hover:bg-green-700">
                                                    Setujui
                                                </button>
                                            </form>

                                            <form method="POST"
                                                  action="{{ route('petugas.reservations.reject', $res->id_reservasi) }}"
                                                  onsubmit="return confirm('Tolak reservasi ini?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="px-3 py-1 text-xs font-semibold text-white bg-red-600 rounded hover:bg-red-700">
                                                    Tolak
                                                </button>
                                            </form>
                                        </div>
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