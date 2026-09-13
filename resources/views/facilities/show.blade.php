<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $facility->facility_name }}
            </h2>
            <a href="{{ route('facilities.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Kembali ke Daftar</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Info Fasilitas -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between gap-4">
            <div>
                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700">{{ $facility->type }}</span>
                <p class="text-sm text-gray-500 mt-2">📍 {{ $facility->location }} | 👥 Kapasitas {{ $facility->capacity }} orang</p>
                <p class="text-gray-700 mt-2 text-sm">{{ $facility->description }}</p>
            </div>
            <div class="flex items-center">
                @auth
                    @if($facility->facility_status === 'aktif')
                        <a href="{{ route('reservations.create', ['facility_id' => $facility->id_fasilitas, 'date' => $selectedDate]) }}"
                           class="px-5 py-2.5 bg-indigo-600 text-white font-semibold rounded-lg shadow hover:bg-indigo-700 transition">
                            Ajukan Reservasi di Fasilitas Ini
                        </a>
                    @else
                        <span class="text-sm font-semibold px-3 py-1 bg-red-100 text-red-700 rounded-md">Fasilitas Dalam Perbaikan</span>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="text-sm px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                        Login untuk Reservasi
                    </a>
                @endauth
            </div>
        </div>

        <!-- Pilih Tanggal & Timeline Slot 30 Menit -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                <h3 class="text-lg font-bold text-gray-900">Ketersediaan Slot Waktu (07:00 - 20:00)</h3>
                <form method="GET" action="{{ route('facilities.show', $facility->id_fasilitas) }}" class="flex items-center space-x-2">
                    <label for="date" class="text-sm font-medium text-gray-700">Pilih Tanggal:</label>
                    <input type="date" id="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()"
                           class="rounded-md border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </form>
            </div>

            <!-- Grid Slot Waktu 30 Menit -->
            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3">
                @foreach($slots as $slot)
                    <div class="p-3 rounded-lg border text-center text-xs flex flex-col justify-between
                        {{ $slot['is_available'] ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800' }}">
                        <div class="font-bold text-sm">{{ $slot['start'] }} - {{ $slot['end'] }}</div>

                        <div class="mt-2">
                            @if($slot['is_available'])
                                <span class="px-2 py-0.5 rounded bg-green-200 text-green-900 font-semibold">Tersedia</span>
                            @else
                                <span class="px-2 py-0.5 rounded bg-red-200 text-red-900 font-semibold">
                                    {{ ucfirst($slot['status']) }}
                                </span>

                                {{-- PRIVASI: Jika login, tampilkan keterangan jika milik sendiri --}}
                                @auth
                                    @if($slot['booking'] && $slot['booking']->id_user === auth()->id())
                                        <div class="text-[10px] mt-1 text-red-700 font-medium">(Milik Anda)</div>
                                    @endif
                                @endauth
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 text-xs text-gray-500 flex items-center gap-4">
                <span class="flex items-center gap-1"><span class="w-3 h-3 bg-green-200 rounded-full inline-block"></span> Hijau = Slot Tersedia</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 bg-red-200 rounded-full inline-block"></span> Merah = Sudah Terisi</span>
            </div>
        </div>
    </div>
</x-app-layout>
