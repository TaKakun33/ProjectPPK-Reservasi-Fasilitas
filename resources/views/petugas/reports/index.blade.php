<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Antrian Laporan') }}
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
            @if($reports->isEmpty())
                <div class="p-12 text-center text-gray-500">
                    Tidak ada laporan yang menunggu diproses.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 font-semibold border-b">
                                <th class="p-4">Pemohon</th>
                                <th class="p-4">Fasilitas</th>
                                <th class="p-4">Kategori</th>
                                <th class="p-4">Deskripsi</th>
                                <th class="p-4">Foto</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($reports as $report)
                                <tr class="hover:bg-gray-50 align-top">
                                    {{-- Pemohon --}}
                                    <td class="p-4 font-medium text-gray-900">{{ $report->user->name ?? '-' }}</td>

                                    {{-- Fasilitas --}}
                                    <td class="p-4">{{ $report->facility->facility_name ?? '-' }}</td>

                                    {{-- Kategori --}}
                                    <td class="p-4">{{ $report->category->category_name ?? '-' }}</td>

                                    {{-- Deskripsi + tanggal lapor --}}
                                    <td class="p-4 text-gray-600 max-w-xs">
                                        <div>{{ Str::limit($report->description, 80) }}</div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            {{ $report->created_at->format('d M Y H:i') }}
                                        </div>
                                    </td>

                                    {{-- Foto (thumbnail, klik untuk perbesar) --}}
                                    <td class="p-4">
                                        @if($report->photo)
                                            <a href="{{ asset('storage/' . $report->photo) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $report->photo) }}"
                                                     alt="Foto laporan"
                                                     class="w-12 h-12 object-cover rounded border">
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>

                                    {{-- Badge status --}}
                                    <td class="p-4">
                                        @php
                                            $badges = [
                                                'baru'     => 'bg-yellow-100 text-yellow-800',
                                                'diproses' => 'bg-blue-100 text-blue-800',
                                                'selesai'  => 'bg-green-100 text-green-800',
                                                'ditolak'  => 'bg-red-100 text-red-800',
                                            ];
                                        @endphp
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $badges[$report->report_status] ?? 'bg-gray-100' }}">
                                            {{ ucfirst($report->report_status) }}
                                        </span>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="p-4">
                                        @if($report->report_status === 'baru')
                                            <form method="POST"
                                                  action="{{ route('petugas.reports.update-status', $report->id_laporan) }}"
                                                  class="space-y-2">
                                                @csrf
                                                @method('PATCH')

                                                <input type="text"
                                                       name="resolution_notes"
                                                       placeholder="Catatan resolusi (opsional)"
                                                       class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">

                                                <div class="flex justify-center gap-2">
                                                    <button type="submit"
                                                            name="report_status"
                                                            value="diproses"
                                                            onclick="return confirm('Proses laporan ini? Fasilitas akan ditandai dalam perbaikan.')"
                                                            class="px-3 py-1 text-xs font-semibold text-white bg-blue-600 rounded hover:bg-blue-700">
                                                        Proses
                                                    </button>

                                                    <button type="submit"
                                                            name="report_status"
                                                            value="ditolak"
                                                            onclick="return confirm('Tolak laporan ini?')"
                                                            class="px-3 py-1 text-xs font-semibold text-white bg-red-600 rounded hover:bg-red-700">
                                                        Tolak
                                                    </button>
                                                </div>
                                            </form>
                                        @elseif($report->report_status === 'diproses')
                                            <form method="POST"
                                                  action="{{ route('petugas.reports.update-status', $report->id_laporan) }}"
                                                  class="space-y-2">
                                                @csrf
                                                @method('PATCH')

                                                <input type="text"
                                                       name="resolution_notes"
                                                       placeholder="Catatan resolusi"
                                                       class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">

                                                <div class="flex justify-center gap-2">
                                                    <button type="submit"
                                                            name="report_status"
                                                            value="selesai"
                                                            onclick="return confirm('Tandai laporan selesai? Fasilitas akan kembali aktif.')"
                                                            class="px-3 py-1 text-xs font-semibold text-white bg-green-600 rounded hover:bg-green-700">
                                                        Selesai
                                                    </button>
                                                </div>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
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