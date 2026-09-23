<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Kelola Fasilitas') }}
            </h2>
            <a href="{{ route('admin.fasilitas.create') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700">
                + Tambah Fasilitas
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        {{-- Filter --}}
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
            <form method="GET" action="{{ route('admin.fasilitas.index') }}" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, tipe, atau lokasi..."
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Semua</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="dalam perbaikan" {{ request('status') === 'dalam perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700">Filter</button>
                <a href="{{ route('admin.fasilitas.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm rounded-md hover:bg-gray-200">Reset</a>
            </form>
        </div>

        {{-- Tabel --}}
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
            @if($facilities->isEmpty())
                <div class="p-12 text-center text-gray-500">Tidak ada fasilitas ditemukan.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 font-semibold border-b">
                                <th class="p-4">Nama</th>
                                <th class="p-4">Tipe</th>
                                <th class="p-4">Lokasi</th>
                                <th class="p-4 text-center">Kapasitas</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($facilities as $f)
                                <tr class="hover:bg-gray-50 {{ $f->facility_status === 'nonaktif' ? 'bg-red-50' : '' }}">
                                    <td class="p-4 font-medium text-gray-900">{{ $f->facility_name }}</td>
                                    <td class="p-4">
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-indigo-50 text-indigo-700">{{ $f->type }}</span>
                                    </td>
                                    <td class="p-4 text-gray-600">{{ $f->location }}</td>
                                    <td class="p-4 text-center">{{ $f->capacity }}</td>
                                    <td class="p-4 text-center">
                                        @php
                                            $statusBadge = match($f->facility_status) {
                                                'aktif' => 'bg-green-100 text-green-700',
                                                'dalam perbaikan' => 'bg-yellow-100 text-yellow-800',
                                                default => 'bg-red-100 text-red-700',
                                            };
                                        @endphp
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded {{ $statusBadge }}">{{ ucfirst($f->facility_status) }}</span>
                                    </td>
                                    <td class="p-4 text-center space-x-2">
                                        <a href="{{ route('admin.fasilitas.edit', $f->id_fasilitas) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">Edit</a>
                                        @if($f->facility_status !== 'nonaktif')
                                            <form method="POST" action="{{ route('admin.fasilitas.destroy', $f->id_fasilitas) }}" class="inline" onsubmit="return confirm('Nonaktifkan fasilitas ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800 hover:underline">Nonaktifkan</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.fasilitas.activate', $f->id_fasilitas) }}" class="inline" onsubmit="return confirm('Aktifkan kembali fasilitas ini?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-xs font-semibold text-green-600 hover:text-green-800 hover:underline">Aktifkan</button>
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
</x-app-layout>