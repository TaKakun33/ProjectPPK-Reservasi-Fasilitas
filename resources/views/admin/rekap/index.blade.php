<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Rekap Okupansi & Kerusakan Fasilitas') }}
            </h2>
            <a href="{{ route('admin.rekap.export', ['format' => 'csv']) }}" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700">
                Export CSV
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
            @if($rekap->isEmpty())
                <div class="p-12 text-center text-gray-500">Belum ada data fasilitas.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 font-semibold border-b">
                                <th class="p-4">Fasilitas</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4 text-center">Reservasi Total</th>
                                <th class="p-4 text-center">Reservasi Approved</th>
                                <th class="p-4 text-center">Reservasi Pending</th>
                                <th class="p-4 text-center">Laporan Total</th>
                                <th class="p-4 text-center">Laporan Baru</th>
                                <th class="p-4 text-center">Laporan Selesai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($rekap as $r)
                                <tr class="hover:bg-gray-50 {{ $r['status'] === 'nonaktif' ? 'bg-red-50' : '' }}">
                                    <td class="p-4">
                                        <p class="font-medium text-gray-900">{{ $r['nama'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $r['tipe'] }} &bull; {{ $r['lokasi'] }}</p>
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($r['status'] === 'aktif')
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-700">Aktif</span>
                                        @elseif($r['status'] === 'dalam perbaikan')
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Dalam Perbaikan</span>
                                        @else
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center font-semibold text-gray-900">{{ $r['total_reservasi'] }}</td>
                                    <td class="p-4 text-center text-green-700">{{ $r['reservasi_approved'] }}</td>
                                    <td class="p-4 text-center text-yellow-700">{{ $r['reservasi_pending'] }}</td>
                                    <td class="p-4 text-center font-semibold text-gray-900">{{ $r['total_laporan'] }}</td>
                                    <td class="p-4 text-center text-blue-700">{{ $r['laporan_baru'] }}</td>
                                    <td class="p-4 text-center text-green-700">{{ $r['laporan_selesai'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
