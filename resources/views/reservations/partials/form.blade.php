{{-- Form pengajuan reservasi. Dipakai halaman penuh (reservations.create) dan pop-up (x-modal-reservasi). --}}
@php $modal = $modal ?? false; @endphp
<form method="POST" action="{{ route('reservations.store') }}" class="space-y-6"
      x-data="{ mengirim: false }" @submit="mengirim = true">
    @csrf
    @if($modal)
        {{-- Penanda: bila validasi gagal, halaman dimuat ulang dan pop-up dibuka kembali --}}
        <input type="hidden" name="_modal" value="reservasi">
    @endif

    {{-- Aturan Reservasi --}}
    <div class="rounded-xl p-3.5 sm:p-4" style="background:#FAF6F0; border:1px solid #EAE0D3;">
        <p class="text-xs sm:text-sm font-extrabold text-[#380F17] flex items-center gap-1.5">
            <svg class="w-4 h-4 text-[#8F0B13]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Aturan Reservasi
        </p>
        <ul class="mt-2.5 space-y-1.5 text-[11px] sm:text-xs text-[#4C4F54] leading-relaxed">
            <li class="flex gap-2"><span class="w-1.5 h-1.5 mt-1.5 rounded-full bg-[#8F0B13] shrink-0"></span><span>Reservasi dilayani <strong>Senin – Sabtu</strong>. <strong>Hari Minggu libur</strong> dan tidak dapat dipesan.</span></li>
            <li class="flex gap-2"><span class="w-1.5 h-1.5 mt-1.5 rounded-full bg-[#8F0B13] shrink-0"></span><span>Jam operasional <strong>07:00 – 20:00 WIB</strong>, dengan pilihan jam dalam kelipatan 30 menit.</span></li>
            <li class="flex gap-2"><span class="w-1.5 h-1.5 mt-1.5 rounded-full bg-[#8F0B13] shrink-0"></span><span>Durasi bebas, bisa <strong>seharian penuh</strong> selama masih dalam jam operasional.</span></li>
            <li class="flex gap-2"><span class="w-1.5 h-1.5 mt-1.5 rounded-full bg-[#8F0B13] shrink-0"></span><span>Pengajuan minimal <strong>1 jam sebelum</strong> kegiatan dimulai dan maksimal <strong>60 hari</strong> ke depan.</span></li>
            <li class="flex gap-2"><span class="w-1.5 h-1.5 mt-1.5 rounded-full bg-[#8F0B13] shrink-0"></span><span>Setiap pengajuan berstatus <strong>menunggu</strong> hingga disetujui petugas. Pengajuan yang tidak diproses sampai jam mulai ditolak otomatis.</span></li>
            <li class="flex gap-2"><span class="w-1.5 h-1.5 mt-1.5 rounded-full bg-[#8F0B13] shrink-0"></span><span>Jadwal tidak boleh bentrok dengan reservasi lain, dan Anda tidak dapat memesan dua fasilitas pada jam yang beririsan.</span></li>
            <li class="flex gap-2"><span class="w-1.5 h-1.5 mt-1.5 rounded-full bg-[#8F0B13] shrink-0"></span><span>Maksimal <strong>3</strong> reservasi menunggu persetujuan dan <strong>5</strong> reservasi aktif per pengguna.</span></li>
            <li class="flex gap-2"><span class="w-1.5 h-1.5 mt-1.5 rounded-full bg-[#8F0B13] shrink-0"></span><span>Reservasi yang sudah disetujui hanya dapat dibatalkan paling lambat <strong>H-1</strong> (sampai pukul 23:59 WIB).</span></li>
        </ul>
    </div>

    {{-- Pilihan Fasilitas --}}
    <div>
        <x-input-label for="id_fasilitas" value="Pilih Fasilitas" />
        <x-select id="id_fasilitas" name="id_fasilitas" required class="mt-1 block w-full">
            <option value="">-- Pilih Fasilitas --</option>
            @foreach($facilities as $facility)
                <option value="{{ $facility->id_fasilitas }}" @selected(old('id_fasilitas', $selectedFacilityId) == $facility->id_fasilitas)>
                    {{ $facility->facility_name }} ({{ $facility->type }} - {{ $facility->location }})
                </option>
            @endforeach
        </x-select>
        <x-input-error :messages="$errors->get('id_fasilitas')" class="mt-2" />
    </div>

    {{-- Tanggal --}}
    <div>
        <x-input-label for="reservation_date" value="Tanggal Kegiatan" />
        <x-text-input type="date" id="reservation_date" name="date" class="mt-1 block w-full"
                      :value="old('date', $selectedDate)"
                      min="{{ date('Y-m-d') }}"
                      max="{{ now()->addDays(\App\Http\Requests\SimpanReservasiRequest::MAKS_HARI_KEDEPAN)->toDateString() }}"
                      required />
        <p id="peringatan_minggu" class="mt-1 text-xs font-semibold text-rose-700" hidden>Hari Minggu libur, reservasi tidak dapat diajukan. Pilih hari Senin – Sabtu.</p>
        <x-input-error :messages="$errors->get('date')" class="mt-2" />
    </div>

    {{-- Jam Mulai & Selesai (Slot Kelipatan 30 Menit) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-input-label for="start_time" value="Jam Mulai" />
            <x-select name="start_time" id="start_time" required class="mt-1 block w-full" aria-describedby="bantuan_jam_mulai">
                @for($h = 7; $h <= 19; $h++)
                    @foreach(['00', '30'] as $m)
                        @php $time = sprintf('%02d:%s', $h, $m); @endphp
                        <option value="{{ $time }}" {{ old('start_time', request('start_time')) == $time ? 'selected' : '' }}>{{ $time }}</option>
                    @endforeach
                @endfor
            </x-select>
            <p id="bantuan_jam_mulai" class="mt-1 text-xs text-charcoal-light">Tersedia pukul 07:00 – 19:30</p>
            <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="end_time" value="Jam Selesai" />
            <x-select name="end_time" id="end_time" required class="mt-1 block w-full" aria-describedby="bantuan_jam_selesai">
                @for($h = 7; $h <= 20; $h++)
                    @foreach(['00', '30'] as $m)
                        @if($h == 7 && $m == '00') @continue @endif
                        @if($h == 20 && $m == '30') @continue @endif
                        @php $time = sprintf('%02d:%s', $h, $m); @endphp
                        <option value="{{ $time }}" {{ old('end_time') == $time ? 'selected' : '' }}>{{ $time }}</option>
                    @endforeach
                @endfor
            </x-select>
            <p id="bantuan_jam_selesai" class="mt-1 text-xs text-charcoal-light">Tersedia pukul 07:30 – 20:00</p>
            <x-input-error :messages="$errors->get('end_time')" class="mt-2" />
        </div>
    </div>

    {{-- Tujuan Reservasi --}}
    <div>
        <x-input-label for="purpose" value="Tujuan Penggunaan" />
        <textarea id="purpose" name="purpose" rows="3" required placeholder="Contoh: Rapat Kerja Anggota..."
                  class="mt-1 block w-full rounded-xl border-cream-border shadow-sm focus:border-maroon-700 focus:ring-maroon-700 text-sm text-charcoal-dark">{{ old('purpose') }}</textarea>
        <x-input-error :messages="$errors->get('purpose')" class="mt-2" />
    </div>

    <div class="flex justify-end gap-3 pt-4 border-t border-dashed border-cream-border">
        @if($modal)
            <x-button type="button" variant="secondary" x-on:click="$dispatch('close-modal', 'reservasi')">Batal</x-button>
        @else
            <x-button variant="secondary" :href="route('facilities.index')">Batal</x-button>
        @endif
        <x-button x-bind:disabled="mengirim">
            <span x-text="mengirim ? 'Mengirim...' : 'Kirim Pengajuan'">Kirim Pengajuan</span>
        </x-button>
    </div>
</form>

<script>
    (function () {
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

        // Hari Minggu libur: tampilkan peringatan dan cegah pengiriman form (server tetap memvalidasi ulang)
        function cekHariMinggu() {
            const peringatan = document.getElementById('peringatan_minggu');
            const [y, m, d] = (dateInput.value || '').split('-').map(Number);
            const minggu = !!y && new Date(y, m - 1, d).getDay() === 0;
            dateInput.setCustomValidity(minggu ? 'Hari Minggu libur, reservasi tidak dapat diajukan.' : '');
            if (peringatan) peringatan.hidden = !minggu;
        }

        function filterTodayStartTimes() {
            cekHariMinggu();
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

        // Pengisian awal dari tombol di halaman lain (fasilitas, tanggal, jam mulai terpilih)
        window.addEventListener('isi-reservasi', function (e) {
            const d = e.detail || {};
            const pilihFasilitas = document.getElementById('id_fasilitas');
            if (d.facility_id && pilihFasilitas) pilihFasilitas.value = d.facility_id;
            if (d.date) dateInput.value = d.date;
            filterTodayStartTimes();
            if (d.start_time) {
                const opsi = Array.from(startTimeSelect.options).find(o => o.value === d.start_time && !o.disabled);
                if (opsi) startTimeSelect.value = d.start_time;
            }
            updateEndTimes();
        });

        filterTodayStartTimes();
        updateEndTimes();
    })();
</script>
