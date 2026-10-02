<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Antrian Laporan Kerusakan') }}
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

        {{-- Filter Tab Status --}}
        <div class="mb-6 flex space-x-2 border-b border-gray-200 pb-2 overflow-x-auto">
            @php
                $reportTabs = [
                    'all'      => 'Semua',
                    'baru'     => 'Baru (Belum Diproses)',
                    'diproses' => 'Sedang Diproses',
                    'selesai'  => 'Selesai',
                    'ditolak'  => 'Ditolak',
                ];
            @endphp
            @foreach($reportTabs as $key => $label)
                <a href="{{ $key === 'all' ? route('petugas.reports.index') : route('petugas.reports.index', ['status' => $key]) }}"
                   class="px-4 py-2 text-sm font-medium rounded-lg transition whitespace-nowrap {{ ($selectedStatus ?? 'all') === $key ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
            @if($reports->isEmpty())
                <div class="p-12 text-center text-gray-500">
                    Belum ada laporan kerusakan yang sesuai dengan filter.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 font-semibold border-b">
                                <th class="p-4">Fasilitas</th>
                                <th class="p-4">Pelapor</th>
                                <th class="p-4">Kategori</th>
                                <th class="p-4">Deskripsi</th>
                                <th class="p-4">Status</th>
                                <th class="p-4">Tanggal</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($reports as $report)
                                <tr class="hover:bg-gray-50">
                                    <td class="p-4 font-medium text-gray-900">{{ $report->facility->facility_name ?? '-' }}</td>
                                    <td class="p-4 text-gray-600">{{ $report->user->name ?? '-' }}</td>
                                    <td class="p-4 text-gray-600">{{ $report->category->category_name ?? '-' }}</td>
                                    <td class="p-4 text-gray-600">{{ Str::limit($report->description, 50) }}</td>
                                    <td class="p-4">
                                        @php
                                            $badges = [
                                                'baru'     => 'bg-blue-100 text-blue-800',
                                                'diproses' => 'bg-yellow-100 text-yellow-800',
                                                'selesai'  => 'bg-green-100 text-green-800',
                                                'ditolak'  => 'bg-red-100 text-red-800',
                                            ];
                                        @endphp
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $badges[$report->report_status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($report->report_status) }}
                                        </span>
                                    </td>
                                    <td class="p-4">{{ $report->created_at->format('d M Y H:i') }}</td>
                                    <td class="p-4 text-center">
                                        <a href="{{ route('petugas.reports.show', $report->id_laporan) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                                            Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
