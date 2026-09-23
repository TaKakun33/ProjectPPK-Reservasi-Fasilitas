<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Rekap Okupansi & Kerusakan Fasilitas') }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.rekap.export', ['format' => 'csv']) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                    CSV
                </a>
                <a href="{{ route('admin.rekap.export', ['format' => 'excel']) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-700 text-white text-sm font-semibold rounded-md hover:bg-emerald-800 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Excel
                </a>
                <a href="{{ route('admin.rekap.export', ['format' => 'pdf']) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-md hover:bg-red-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    PDF
                </a>
            </div>
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
