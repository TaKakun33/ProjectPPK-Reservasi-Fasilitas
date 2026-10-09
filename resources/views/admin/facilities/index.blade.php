<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-xl text-maroon-800 leading-tight">
                {{ __('Manajemen Fasilitas Kampus') }}
            </h2>
            <a href="{{ route('admin.fasilitas.create') }}" class="px-4 py-2 bg-maroon-800 text-cream-100 text-sm font-bold rounded-md hover:bg-maroon-900 shadow-sm">
                + Daftarkan Fasilitas Baru
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

        {{-- Filter --}}
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
            <form method="GET" action="{{ route('admin.fasilitas.index') }}" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama fasilitas, kategori, atau lokasi gedung..."
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-maroon-700 focus:ring-maroon-700 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Operasional</label>
                    <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-maroon-700 focus:ring-maroon-700 text-sm">
                        <option value="">Semua Status Operasional</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Berstatus Aktif</option>
                        <option value="dalam perbaikan" {{ request('status') === 'dalam perbaikan' ? 'selected' : '' }}>Dalam Pemeliharaan</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-maroon-800 text-cream-100 text-sm font-semibold rounded-md hover:bg-maroon-900 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Cari</span>
                </button>
                <a href="{{ route('admin.fasilitas.index') }}" title="Atur Ulang" aria-label="Atur Ulang"
                   class="inline-flex items-center justify-center p-2 bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 hover:text-gray-900 transition h-[38px] w-[38px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </a>
            </form>
        </div>

        {{-- Tabel --}}
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
            @if($facilities->isEmpty())
                <div class="p-12 text-center text-gray-500">Tidak ditemukan data fasilitas yang sesuai dengan kriteria.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-maroon-800 text-white font-semibold border-b border-maroon-900">
                                <th class="p-4">Dokumentasi Sarana</th>
                                <th class="p-4">Nama Fasilitas</th>
                                <th class="p-4">Kategori</th>
                                <th class="p-4">Lokasi Gedung</th>
                                <th class="p-4 text-center">Kapasitas (Orang)</th>
                                <th class="p-4 text-center">Status Operasional</th>
                                <th class="p-4 text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($facilities as $f)
                                <tr class="hover:bg-cream-100 {{ $f->facility_status === 'nonaktif' ? 'bg-red-50' : '' }}">
                                    <td class="p-4"><img src="{{ $f->photo_url }}" alt="" loading="lazy" class="w-20 h-14 rounded-lg object-cover bg-slate-100 border border-slate-200" onerror="this.src='https://picsum.photos/seed/{{ $f->id_fasilitas }}/200/140'"></td>
                                    <td class="p-4 font-medium text-gray-900">{{ $f->facility_name }}</td>
                                    <td class="p-4">
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-maroon-50 text-maroon-700">{{ $f->type }}</span>
                                    </td>
                                    <td class="p-4 text-gray-600">{{ $f->location }}</td>
                                    <td class="p-4 text-center">{{ $f->capacity }}</td>
                                    <td class="p-4 text-center">
                                        @php
                                            $statusBadge = match($f->facility_status) {
                                                'aktif' => ['class' => 'bg-green-100 text-green-700', 'label' => 'Aktif'],
                                                'dalam perbaikan' => ['class' => 'bg-yellow-100 text-yellow-800', 'label' => 'Dalam Pemeliharaan'],
                                                default => ['class' => 'bg-red-100 text-red-700', 'label' => 'Nonaktif'],
                                            };
                                        @endphp
                                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $statusBadge['class'] }}">{{ $statusBadge['label'] }}</span>
                                    </td>
                                    <td class="p-4 text-center space-x-2">
                                        <a href="{{ route('admin.fasilitas.edit', $f->id_fasilitas) }}" class="text-xs font-semibold text-maroon-700 hover:text-maroon-900 hover:underline">Sunting Data</a>
                                        @if($f->facility_status !== 'nonaktif')
                                            <form method="POST" action="{{ route('admin.fasilitas.destroy', $f->id_fasilitas) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan fasilitas ini? Permohonan reservasi pending yang akan datang akan dibatalkan secara otomatis.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800 hover:underline">Nonaktifkan</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.fasilitas.activate', $f->id_fasilitas) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin mengaktifkan kembali fasilitas kampus ini?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-xs font-semibold text-green-600 hover:text-green-800 hover:underline">Aktifkan Kembali</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t">
                    {{ $facilities->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
