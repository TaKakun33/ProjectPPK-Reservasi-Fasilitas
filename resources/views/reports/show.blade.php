<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Laporan Kerusakan') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
            <div class="p-6 space-y-5">
                <div class="flex justify-between items-start gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Fasilitas</p>
                        <p class="font-semibold text-gray-900">{{ $laporan->facility->facility_name ?? '-' }}</p>
                        <p class="text-sm text-gray-500">{{ $laporan->facility->type ?? '' }} - {{ $laporan->facility->location ?? '' }}</p>
                    </div>
                    @php
                        $badges = [
                            'baru'     => 'bg-blue-100 text-blue-800',
                            'diproses' => 'bg-yellow-100 text-yellow-800',
                            'selesai'  => 'bg-green-100 text-green-800',
                            'ditolak'  => 'bg-red-100 text-red-800',
                        ];
                    @endphp
                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $badges[$laporan->report_status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($laporan->report_status) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Kategori</p>
                        <p class="font-medium text-gray-800">{{ $laporan->category->category_name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tanggal Laporan</p>
                        <p class="font-medium text-gray-800">{{ $laporan->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Deskripsi Kerusakan</p>
                    <p class="text-gray-800">{{ $laporan->description }}</p>
                </div>

                @if($laporan->photos->isNotEmpty())
                    <div>
                        <p class="text-sm text-gray-500 mb-2">Foto Kerusakan ({{ $laporan->photos->count() }})</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($laporan->photos as $foto)
                                <a href="{{ route('reports.photo', $foto->id_foto) }}" target="_blank">
                                    <img src="{{ route('reports.photo', $foto->id_foto) }}" alt="Foto kerusakan"
                                        class="w-full h-32 object-cover rounded-lg border border-gray-200 hover:opacity-80 transition">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                @if($laporan->resolution_notes)
                    <div class="p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
                        <p class="text-sm font-semibold text-blue-800">Catatan Resolusi Petugas</p>
                        <p class="mt-1 text-sm text-blue-900">{{ $laporan->resolution_notes }}</p>
                    </div>
                @endif

                <div class="flex justify-end pt-2 border-t">
                    <a href="{{ route('reports.index') }}" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50">
                        Kembali ke Riwayat
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>