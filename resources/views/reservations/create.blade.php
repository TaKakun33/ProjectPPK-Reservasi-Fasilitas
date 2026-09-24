<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajukan Reservasi Fasilitas') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm">
                    <p class="font-semibold mb-1">Terdapat kesalahan pengisian:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('reservations.store') }}" class="space-y-6">
                @csrf

                {{-- Pilihan Fasilitas --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Fasilitas</label>
                    <select name="id_fasilitas" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Pilih Fasilitas --</option>
                        @foreach($facilities as $facility)
                            <option value="{{ $facility->id_fasilitas }}" {{ (old('id_fasilitas', $selectedFacilityId) == $facility->id_fasilitas) ? 'selected' : '' }}>
                                {{ $facility->facility_name }} ({{ $facility->type }} - {{ $facility->location }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tanggal --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kegiatan</label>
                    <input type="date" id="reservation_date" name="date" value="{{ old('date', $selectedDate) }}" min="{{ date('Y-m-d') }}" required
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                {{-- Jam Mulai & Selesai (Slot Kelipatan 30 Menit) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai (07:00 - 19:30)</label>
                        <select name="start_time" id="start_time" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @for($h = 7; $h <= 19; $h++)
                                @foreach(['00', '30'] as $m)
                                    @php $time = sprintf('%02d:%s', $h, $m); @endphp
                                    <option value="{{ $time }}" {{ old('start_time') == $time ? 'selected' : '' }}>{{ $time }}</option>
                                @endforeach
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai (07:30 - 20:00)</label>
                        <select name="end_time" id="end_time" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @for($h = 7; $h <= 20; $h++)
                                @foreach(['00', '30'] as $m)
                                    @if($h == 7 && $m == '00') @continue @endif
                                    @if($h == 20 && $m == '30') @continue @endif
                                    @php $time = sprintf('%02d:%s', $h, $m); @endphp
                                    <option value="{{ $time }}" {{ old('end_time') == $time ? 'selected' : '' }}>{{ $time }}</option>
                                @endforeach
                            @endfor
                        </select>
                    </div>
                </div>

                {{-- Tujuan Reservasi --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Penggunaan</label>
                    <textarea name="purpose" rows="3" required placeholder="Contoh: Rapat Kerja Anggota..."
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('purpose') }}</textarea>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <a href="{{ route('facilities.index') }}" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50">Batal</a>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700 transition">
                        Kirim Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.getElementById('reservation_date');
            const startTimeSelect = document.getElementById('start_time');
            const endTimeSelect = document.getElementById('end_time');

            function getTodayStr() {
                const now = new Date();
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            function getCurrentTimeStr() {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                return `${hours}:${minutes}`;
            }

            function filterTimeSlots() {
                const selectedDate = dateInput.value;
                const todayStr = getTodayStr();
                const currentTimeStr = getCurrentTimeStr();
                const isToday = selectedDate === todayStr;

                let firstValidStart = null;

                Array.from(startTimeSelect.options).forEach(option => {
                    const timeVal = option.value;
                    if (isToday && timeVal <= currentTimeStr) {
                        option.disabled = true;
                        if (!option.dataset.originalText) {
                            option.dataset.originalText = timeVal;
                        }
                        option.innerText = timeVal + ' (Telah Berlalu)';
                    } else {
                        option.disabled = false;
                        option.innerText = option.dataset.originalText || timeVal;
                        if (!firstValidStart) firstValidStart = timeVal;
                    }
                });

                if (startTimeSelect.selectedOptions[0] && startTimeSelect.selectedOptions[0].disabled) {
                    if (firstValidStart) {
                        startTimeSelect.value = firstValidStart;
                    }
                }

                const currentStart = startTimeSelect.value;
                let firstValidEnd = null;

                Array.from(endTimeSelect.options).forEach(option => {
                    const timeVal = option.value;
                    if (timeVal <= currentStart || (isToday && timeVal <= currentTimeStr)) {
                        option.disabled = true;
                    } else {
                        option.disabled = false;
                        if (!firstValidEnd) firstValidEnd = timeVal;
                    }
                });

                if (endTimeSelect.selectedOptions[0] && endTimeSelect.selectedOptions[0].disabled) {
                    if (firstValidEnd) {
                        endTimeSelect.value = firstValidEnd;
                    }
                }
            }

            dateInput.addEventListener('change', filterTimeSlots);
            startTimeSelect.addEventListener('change', filterTimeSlots);

            filterTimeSlots();
        });
    </script>
</x-app-layout>
