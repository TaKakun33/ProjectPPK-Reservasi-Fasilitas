<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-maroon-800 leading-tight">
            {{ __('Ajukan Reservasi Fasilitas') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-[#EAE0D3] border-t-4 border-t-maroon-800">

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-r-xl">
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
                    <label class="block text-sm font-bold text-[#252B2B] mb-1">Pilih Fasilitas</label>
                    <select name="id_fasilitas" required class="w-full rounded-xl border-[#EAE0D3] shadow-xs focus:border-maroon-700 focus:ring-maroon-700 text-sm text-[#252B2B]">
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
                    <label class="block text-sm font-bold text-[#252B2B] mb-1">Tanggal Kegiatan</label>
                    <input type="date" id="reservation_date" name="date" value="{{ old('date', $selectedDate) }}" min="{{ date('Y-m-d') }}" max="{{ now()->addDays(\App\Http\Requests\SimpanReservasiRequest::MAKS_HARI_KEDEPAN)->toDateString() }}" required
                           class="w-full rounded-xl border-[#EAE0D3] shadow-xs focus:border-maroon-700 focus:ring-maroon-700 text-sm text-[#252B2B]">
                </div>

                {{-- Jam Mulai & Selesai (Slot Kelipatan 30 Menit) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-[#252B2B] mb-1">Jam Mulai (07:00 - 19:30)</label>
                        <select name="start_time" id="start_time" required class="w-full rounded-xl border-[#EAE0D3] shadow-xs focus:border-maroon-700 focus:ring-maroon-700 text-sm text-[#252B2B]">
                            @for($h = 7; $h <= 19; $h++)
                                @foreach(['00', '30'] as $m)
                                    @php $time = sprintf('%02d:%s', $h, $m); @endphp
                                    <option value="{{ $time }}" {{ old('start_time') == $time ? 'selected' : '' }}>{{ $time }}</option>
                                @endforeach
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-[#252B2B] mb-1">Jam Selesai (07:30 - 20:00)</label>
                        <select name="end_time" id="end_time" required class="w-full rounded-xl border-[#EAE0D3] shadow-xs focus:border-maroon-700 focus:ring-maroon-700 text-sm text-[#252B2B]">
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
                    <label class="block text-sm font-bold text-[#252B2B] mb-1">Tujuan Penggunaan</label>
                    <textarea name="purpose" rows="3" required placeholder="Contoh: Rapat Kerja Anggota..."
                              class="w-full rounded-xl border-[#EAE0D3] shadow-xs focus:border-maroon-700 focus:ring-maroon-700 text-sm text-[#252B2B]">{{ old('purpose') }}</textarea>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-dashed" style="border-color:#EAE0D3;">
                    <a href="{{ route('facilities.index') }}"
                       class="px-4 py-2.5 border rounded-xl font-bold text-xs text-[#4C4F54] hover:bg-[#FAF6F0] transition"
                       style="border-color:#EAE0D3;">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl font-extrabold text-xs shadow-sm transition duration-150"
                            style="background:#8F0B13; color:#EFDFC5; border:1px solid #70090F;"
                            onmouseover="this.style.background='#380F17';"
                            onmouseout="this.style.background='#8F0B13';">
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

            // Waktu acuan = Asia/Jakarta (sama dengan app.timezone di server), bukan jam perangkat
            // pengguna, supaya buffer 1 jam di sisi client konsisten dengan validasi server.
            function jakartaNow(offsetMs = 0) {
                const bagian = new Intl.DateTimeFormat('en-CA', {
                    timeZone: 'Asia/Jakarta',
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                }).formatToParts(new Date(Date.now() + offsetMs));

                const map = {};
                bagian.forEach(b => { map[b.type] = b.value; });
                return {
                    date: `${map.year}-${map.month}-${map.day}`,
                    hour: parseInt(map.hour, 10),
                    minute: parseInt(map.minute, 10)
                };
            }

            function updateEndTimes() {
                const startTime = startTimeSelect.value;
                if (!startTime) return;

                const [startH, startM] = startTime.split(':').map(Number);
                const startMinutes = startH * 60 + startM;

                let firstValid = null;
                Array.from(endTimeSelect.options).forEach(opt => {
                    const [endH, endM] = opt.value.split(':').map(Number);
                    const endMinutes = endH * 60 + endM;

                    if (endMinutes <= startMinutes) {
                        opt.disabled = true;
                        opt.classList.add('text-gray-300');
                    } else {
                        opt.disabled = false;
                        opt.classList.remove('text-gray-300');
                        if (firstValid === null) firstValid = opt.value;
                    }
                });

                const currentEnd = endTimeSelect.value;
                const [curEndH, curEndM] = (currentEnd || '00:00').split(':').map(Number);
                if (!currentEnd || (curEndH * 60 + curEndM) <= startMinutes) {
                    if (firstValid) endTimeSelect.value = firstValid;
                }
            }

            function filterTodayStartTimes() {
                const selected = dateInput.value;
                const nowJKT = jakartaNow(60 * 60 * 1000); // sekarang + 1 jam buffer
                const todayStr = jakartaNow(0).date;
                const minStartMinutes = nowJKT.hour * 60 + nowJKT.minute;

                const isToday = (selected === todayStr);

                let firstValid = null;
                Array.from(startTimeSelect.options).forEach(opt => {
                    const [h, m] = opt.value.split(':').map(Number);
                    const optMinutes = h * 60 + m;

                    if (isToday && optMinutes < minStartMinutes) {
                        opt.disabled = true;
                        opt.classList.add('text-gray-300');
                    } else {
                        opt.disabled = false;
                        opt.classList.remove('text-gray-300');
                        if (firstValid === null) firstValid = opt.value;
                    }
                });

                if (isToday) {
                    const curVal = startTimeSelect.value;
                    const [curH, curM] = (curVal || '00:00').split(':').map(Number);
                    if (!curVal || (curH * 60 + curM) < minStartMinutes) {
                        if (firstValid) {
                            startTimeSelect.value = firstValid;
                        }
                    }
                }

                updateEndTimes();
            }

            startTimeSelect.addEventListener('change', updateEndTimes);
            dateInput.addEventListener('change', filterTodayStartTimes);

            filterTodayStartTimes();
            updateEndTimes();
        });
    </script>
</x-app-layout>
