<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Fasilitas Kampus') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Form Filter & Pencarian -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-8">
            <form method="GET" action="{{ route('facilities.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Fasilitas</label>
                    <select name="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Semua Tipe</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                    <select name="location" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Semua Lokasi</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc }}" {{ request('location') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Min. Kapasitas (Orang)</label>
                    <input type="number" name="capacity" value="{{ request('capacity') }}" placeholder="Contoh: 30"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>

                <div class="flex items-end space-x-2">
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 transition text-sm">
                        Cari
                    </button>
                    <a href="{{ route('facilities.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 transition text-sm text-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Daftar Fasilitas -->
        @if($facilities->isEmpty())
            <div class="bg-white p-12 text-center rounded-xl border border-gray-100 text-gray-500">
                Tidak ada fasilitas yang sesuai dengan pencarian Anda.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($facilities as $facility)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col justify-between hover:shadow-md transition">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700">
                                    {{ $facility->type }}
                                </span>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded {{ $facility->facility_status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ ucfirst($facility->facility_status) }}
                                </span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $facility->facility_name }}</h3>
                            <p class="text-sm text-gray-500 mb-4">
                                📍 {{ $facility->location }} &bull; 👥 {{ $facility->capacity }} orang
                            </p>
                            <p class="text-sm text-gray-600 line-clamp-3">
                                {{ $facility->description ?? 'Tidak ada deskripsi.' }}
                            </p>
                        </div>

                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                            <a href="{{ route('facilities.show', $facility->id_fasilitas) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                                Cek Jadwal Slot &rarr;
                            </a>
                            @auth
                                @if($facility->facility_status === 'aktif')
                                    <a href="{{ route('reservations.create', ['facility_id' => $facility->id_fasilitas]) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-xs font-semibold text-white rounded-md hover:bg-indigo-700">
                                        Reservasi
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $facilities->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
