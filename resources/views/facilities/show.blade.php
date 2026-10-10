<x-app-layout>
    @php
        $gallery = $facility->gallery; // daftar URL foto dari database (0-5 foto)
        $available = collect($slots)->where('is_available', true)->count();
        $total = count($slots);
        $isSunday = \Carbon\Carbon::parse($selectedDate)->isSunday(); // Minggu libur: tidak ada layanan reservasi
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-6">
        {{-- Breadcrumb navigasi --}}
        <nav class="text-xs flex items-center gap-1.5 mb-3 text-[#4C4F54]" aria-label="Breadcrumb">
            <a href="{{ route('facilities.index') }}" class="hover:text-[#8F0B13] font-semibold transition-colors">Beranda</a>
            <span class="text-gray-400">/</span>
            <a href="{{ route('facilities.index', ['type' => $facility->type]) }}" class="hover:text-[#8F0B13] font-semibold transition-colors">{{ $facility->type }}</a>
            <span class="text-gray-400">/</span>
            <span class="font-bold truncate text-[#252B2B]">{{ $facility->facility_name }}</span>
        </nav>

        {{-- Header Fasilitas --}}
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full shadow-xs"
                          style="background: linear-gradient(135deg, #380F17 0%, #8F0B13 100%); color:#EFDFC5; border:1px solid rgba(239, 223, 197, 0.25);">
                        {{ $facility->type }}
                    </span>
                    @if($facility->facility_status === 'aktif')
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full shadow-2xs inline-flex items-center gap-1"
                              style="background:#D1FAE5; color:#065F46; border:1px solid #A7F3D0;">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Tersedia
                        </span>
                    @elseif($facility->facility_status === 'dalam perbaikan')
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full shadow-2xs inline-flex items-center gap-1"
                              style="background:#FEF3C7; color:#92400E; border:1px solid #FDE68A;">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Dalam Perbaikan
                        </span>
                    @else
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full shadow-2xs inline-flex items-center gap-1"
                              style="background:#FEE2E2; color:#991B1B; border:1px solid #FECACA;">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Nonaktif
                        </span>
                    @endif
                </div>
                <h1 class="mt-2 text-2xl sm:text-3xl font-extrabold tracking-tight text-[#252B2B]">
                    {{ $facility->facility_name }}
                </h1>
                <p class="mt-1 text-xs sm:text-sm text-[#4C4F54] flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1 text-[#8F0B13] font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $facility->location }}
                    </span>
                    <span>&bull;</span>
                    <span class="inline-flex items-center gap-1 font-medium">
                        👥 Kapasitas {{ $facility->capacity }} orang
                    </span>
                </p>
            </div>
        </div>

        {{-- Konten Utama: 2 Kolom (Kiri Konten, Kanan Sticky Card) --}}
        @php
            // Tombol reservasi hanya relevan untuk pengguna biasa pada fasilitas yang aktif
            $bisaReservasi = auth()->check()
                && auth()->user()->role === \App\Enums\UserRole::Pengguna
                && $facility->facility_status === 'aktif';
        @endphp
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6 items-start {{ $bisaReservasi ? 'pb-20 lg:pb-0' : '' }}"
             x-data="{
                 selectedSlot: null,
                 urlReservasi: @js(route('reservations.create', ['facility_id' => $facility->id_fasilitas, 'date' => $selectedDate])),
                 // Buka pop-up reservasi dengan fasilitas, tanggal, dan jam mulai terpilih sudah terisi
                 bukaReservasi() {
                     window.dispatchEvent(new CustomEvent('isi-reservasi', { detail: {
                         facility_id: @js($facility->id_fasilitas),
                         date: @js($selectedDate),
                         start_time: this.selectedSlot
                     } }));
                     window.dispatchEvent(new CustomEvent('open-modal', { detail: 'reservasi' }));
                 }
             }">
            {{-- Kolom Kiri --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Galeri foto fasilitas (dari database, maksimal 5): foto utama utuh (tidak dipotong), thumbnail di sisi kanan --}}
                @if(count($gallery) > 0)
                    <section class="bg-white rounded-2xl p-3 sm:p-4 shadow-xs" style="border:1px solid #EAE0D3;"
                             x-data="{ aktif: 0, foto: @js($gallery) }">
                        <div class="flex flex-col sm:flex-row gap-3">
                            {{-- Foto utama: object-contain agar seluruh foto terlihat --}}
                            <div class="relative flex-1 min-w-0 h-56 sm:h-72 rounded-xl overflow-hidden bg-cream-50 flex items-center justify-center"
                                 style="border:1px solid #EAE0D3;">
                                <img :src="foto[aktif]" alt="{{ $facility->facility_name }}" class="max-w-full max-h-full object-contain">
                                <span x-show="foto.length > 1" x-cloak x-text="(aktif + 1) + ' / ' + foto.length"
                                      class="absolute bottom-2 right-2 text-[11px] font-bold px-2 py-1 rounded-md shadow-xs"
                                      style="background:rgba(255, 255, 255, 0.95); color:#380F17; border:1px solid #EAE0D3;"></span>
                            </div>

                            {{-- Foto lainnya: kolom di kanan (desktop) / baris di bawah (mobile) --}}
                            <div x-show="foto.length > 1" x-cloak
                                 class="flex sm:flex-col gap-2 shrink-0 overflow-x-auto sm:overflow-x-visible sm:overflow-y-auto sm:w-24 sm:max-h-72">
                                <template x-for="(f, i) in foto" :key="i">
                                    <button type="button" @click="aktif = i" :aria-label="'Lihat foto ' + (i + 1)"
                                            :class="aktif === i ? 'ring-2 ring-[#8F0B13] ring-offset-1' : 'opacity-70 hover:opacity-100'"
                                            class="shrink-0 w-20 h-14 sm:w-24 sm:h-[3.2rem] rounded-lg overflow-hidden bg-cream-50 flex items-center justify-center transition"
                                            style="border:1px solid #EAE0D3;">
                                        <img :src="f" alt="" class="max-w-full max-h-full object-contain">
                                    </button>
                                </template>
                            </div>
                        </div>
                    </section>
                @else
                    <x-facility-placeholder class="h-48 rounded-2xl border" style="border-color:#EAE0D3;">
                        <x-slot name="label">Foto fasilitas belum tersedia</x-slot>
                    </x-facility-placeholder>
                @endif

                {{-- Seksi Tentang Fasilitas --}}
                <section class="bg-white rounded-2xl p-5 sm:p-6 shadow-xs" style="border:1px solid #EAE0D3;">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-1.5 h-5 rounded-full" style="background:#8F0B13;"></span>
                        <h2 class="font-extrabold text-base sm:text-lg text-[#252B2B]">Tentang Fasilitas Ini</h2>
                    </div>
                    <p class="text-xs sm:text-sm text-[#4C4F54] leading-relaxed">
                        {{ $facility->description ?? 'Sarana kampus resmi untuk menunjang kegiatan akademik, organisasi kemahasiswaan, dan agenda universitas.' }}
                    </p>

                    @if(!empty($facility->amenities) && is_array($facility->amenities))
                        <div class="mt-4 pt-4 border-t border-dashed" style="border-color:#EAE0D3;">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-[#4C4F54] mb-2.5">Fasilitas &amp; Sarana Penunjang</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-center">
                                @foreach($facility->amenities as $am)
                                    <div class="rounded-xl px-3 py-3 transition shadow-2xs hover:border-[#8F0B13] flex items-center justify-center text-center"
                                         style="background:#FAF6F0; border:1px solid #EAE0D3;">
                                        <p class="text-xs font-bold text-[#252B2B] leading-snug break-words">{{ $am }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </section>

                {{-- Seksi Jadwal & Ketersediaan Slot (Model 3: Visual Timeline Bar) --}}
                <section class="bg-white rounded-2xl p-5 sm:p-6 shadow-xs" style="border:1px solid #EAE0D3;">
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 mb-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-1.5 h-5 rounded-full" style="background:#8F0B13;"></span>
                                <h2 class="font-extrabold text-base sm:text-lg text-[#252B2B]">Ketersediaan Slot (07:00 – 20:00)</h2>
                            </div>
                            <p class="text-xs text-[#4C4F54] mt-0.5">
                                <span class="font-bold text-emerald-700">{{ $available }}</span> dari {{ $total }} slot waktu tersedia pada tanggal terpilih.
                            </p>
                        </div>

                        {{-- Filter Pemilihan Tanggal --}}
                        <form method="GET" action="{{ route('facilities.show', $facility->id_fasilitas) }}"
                              class="flex items-center gap-2 rounded-xl px-3 py-1.5 shadow-2xs"
                              style="background:#FAF6F0; border:1px solid #EAE0D3;">
                            <label for="date" class="text-xs font-bold text-[#252B2B] shrink-0">Tanggal:</label>
                            <input type="date" id="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()"
                                   class="border-0 bg-transparent text-xs sm:text-sm font-bold text-[#380F17] focus:ring-0 p-0 cursor-pointer">
                        </form>
                    </div>

                    @if($isSunday)
                        <div class="mb-4 p-3 rounded-xl text-xs sm:text-sm font-semibold" style="background:#F1F5F9; color:#334155; border:1px solid #CBD5E1;">
                            Hari Minggu libur. Reservasi fasilitas tidak dilayani pada tanggal ini, silakan pilih hari Senin – Sabtu.
                        </div>
                    @endif

                    {{-- VISUAL TIMELINE BAR (Ala Bioskop & Studio Booking) --}}
                    <div class="p-3.5 sm:p-4 rounded-xl mb-5" style="background:#FAF6F0; border:1px solid #EAE0D3;">
                        <div class="mb-2">
                            <span class="text-xs font-extrabold text-[#380F17] flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-[#8F0B13]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Waktu Operasional (07:00 – 20:00)
                            </span>
                        </div>

                        {{-- Segmen Timeline Bar --}}
                        <div class="relative w-full">
                            <div class="h-7 sm:h-8 flex gap-0.5 sm:gap-1 p-1 bg-white rounded-lg border shadow-inner" style="border-color:#EAE0D3;">
                                @foreach($slots as $slot)
                                    @php
                                        $statusKey = strtolower($slot['status'] ?? ($slot['is_available'] ? 'tersedia' : 'terisi'));
                                        $isPending = in_array($statusKey, ['pending', 'menunggu', 'diproses', 'menunggu persetujuan']);
                                        $isPast = $slot['is_past'] ?? ($statusKey === 'berlalu');
                                        
                                        $segmentColor = 'bg-emerald-500 hover:bg-emerald-600';
                                        $statusLabel = 'Tersedia';
                                        if ($slot['is_available']) {
                                            $segmentColor = 'bg-emerald-500 hover:bg-emerald-600';
                                            $statusLabel = 'Tersedia';
                                        } elseif ($isPast) {
                                            $segmentColor = 'bg-gray-300 hover:bg-gray-400';
                                            $statusLabel = 'Waktu Berlalu';
                                        } elseif ($statusKey === 'libur') {
                                            $segmentColor = 'bg-slate-400 hover:bg-slate-500';
                                            $statusLabel = 'Libur (Minggu)';
                                        } elseif ($isPending) {
                                            $segmentColor = 'bg-amber-400 hover:bg-amber-500';
                                            $statusLabel = 'Menunggu Persetujuan';
                                        } else {
                                            $segmentColor = 'bg-rose-500 hover:bg-rose-600';
                                            $statusLabel = $statusKey === 'perbaikan' ? 'Perbaikan' : 'Sudah Dibooking';
                                        }
                                    @endphp
                                    <div class="flex-1 {{ $segmentColor }} rounded-[3px] transition-all {{ $slot['is_available'] ? 'cursor-pointer' : 'cursor-default' }} relative group"
                                         @if($slot['is_available']) @click="selectedSlot = '{{ $slot['start'] }}'" @endif
                                         :class="{ 'ring-2 ring-[#8F0B13] scale-y-110 z-10': selectedSlot === '{{ $slot['start'] }}' }">
                                        {{-- Tooltip Popover --}}
                                        <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 hidden group-hover:flex flex-col items-center z-30 pointer-events-none whitespace-nowrap">
                                            <div class="bg-[#380F17] text-[#EFDFC5] text-[11px] font-bold px-2.5 py-1 rounded-md shadow-xl border border-[#EFDFC5]/20">
                                                <span>{{ $slot['start'] }} - {{ $slot['end'] }}</span>
                                                <span class="block text-[11px] font-normal text-white/80">{{ $statusLabel }}</span>
                                            </div>
                                            <div class="w-1.5 h-1.5 bg-[#380F17] rotate-45 -mt-0.5"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Sumbu Waktu (Time Markers Axis) --}}
                            <div class="flex justify-between text-[11px] font-semibold text-[#4C4F54] mt-1.5 px-0.5">
                                <span>07:00</span>
                                <span class="hidden sm:inline">09:00</span>
                                <span>11:00</span>
                                <span class="hidden sm:inline">13:00</span>
                                <span>15:00</span>
                                <span class="hidden sm:inline">17:00</span>
                                <span>19:00</span>
                                <span>20:00</span>
                            </div>
                        </div>
                    </div>

                    {{-- Grid Slot Waktu Compact Chips (Ramping & Elegan) --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                        @foreach($slots as $slot)
                            @php
                                $statusKey = strtolower($slot['status'] ?? ($slot['is_available'] ? 'tersedia' : 'terisi'));
                                $isPending = in_array($statusKey, ['pending', 'menunggu', 'diproses', 'menunggu persetujuan']);
                                $isPast = $slot['is_past'] ?? ($statusKey === 'berlalu');
                            @endphp
                            <div @if($slot['is_available']) @click="selectedSlot = '{{ $slot['start'] }}'" @endif
                                 class="px-3 py-2 rounded-xl border text-xs transition duration-150 flex items-center justify-between {{ $slot['is_available'] ? 'cursor-pointer' : 'cursor-default' }}
                                 @if($slot['is_available'])
                                     bg-emerald-50/60 border-emerald-200/80 text-emerald-950 hover:bg-emerald-100/70 hover:border-emerald-300
                                 @elseif($isPast)
                                     bg-gray-100/80 border-gray-200 text-gray-500
                                 @elseif($statusKey === 'libur')
                                     bg-slate-100/80 border-slate-300 text-slate-600
                                 @elseif($isPending)
                                     bg-amber-50/70 border-amber-200 text-amber-950
                                 @else
                                     bg-rose-50/70 border-rose-200 text-rose-950
                                 @endif"
                                 :class="{ 'ring-2 ring-[#8F0B13] border-[#8F0B13] shadow-xs': selectedSlot === '{{ $slot['start'] }}' }">
                                
                                <span class="font-extrabold text-[12px] text-[#252B2B]">
                                    {{ $slot['start'] }} – {{ $slot['end'] }}
                                </span>

                                <div class="flex items-center gap-1.5 shrink-0">
                                    @if($slot['is_available'])
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                        <span class="text-[11px] font-bold text-emerald-700">Tersedia</span>
                                    @elseif($isPast)
                                        <span class="w-2 h-2 rounded-full bg-gray-400 shrink-0"></span>
                                        <span class="text-[11px] font-bold text-gray-500">Berlalu</span>
                                    @elseif($statusKey === 'libur')
                                        <span class="w-2 h-2 rounded-full bg-slate-400 shrink-0"></span>
                                        <span class="text-[11px] font-bold text-slate-600">Libur</span>
                                    @elseif($isPending)
                                        <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                                        <span class="text-[11px] font-bold text-amber-700">Menunggu</span>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span>
                                        <span class="text-[11px] font-bold text-rose-700">{{ $statusKey === 'perbaikan' ? 'Perbaikan' : 'Terisi' }}</span>
                                    @endif

                                    @if(!empty($slot['is_mine']))
                                        <span class="text-[11px] font-bold text-rose-800 bg-rose-100 px-1 py-0.5 rounded ml-1">Saya</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Legend Keterangan Warna --}}
                    <div class="mt-4 pt-3 text-xs text-[#4C4F54] flex flex-wrap gap-4 border-t border-dashed" style="border-color:#EAE0D3;">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full inline-block" style="background:#059669"></span>
                            <span class="font-medium">Slot Tersedia</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full inline-block" style="background:#D97706"></span>
                            <span class="font-medium">Menunggu Persetujuan</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full inline-block" style="background:#DC2626"></span>
                            <span class="font-medium">Sudah Dibooking</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full inline-block bg-gray-400"></span>
                            <span class="font-medium">Waktu Berlalu</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full inline-block bg-slate-400"></span>
                            <span class="font-medium">Libur (Minggu)</span>
                        </span>
                    </div>
                </section>
            </div>

            {{-- Kolom Kanan: diam (sticky) saat halaman di-scroll; hanya kolom kiri yang bergerak.
                 top-20 = tinggi navbar (h-16) + jarak 1rem. Di layar pendek, kolom ini bisa digulir sendiri. --}}
            <aside class="space-y-4 lg:sticky lg:top-20 lg:max-h-[calc(100vh-6rem)] lg:overflow-y-auto lg:pr-1 lg:[scrollbar-width:thin]">
                <div class="bg-white rounded-2xl shadow-md overflow-hidden" style="border:1px solid #EAE0D3;">
                    {{-- Header Ringkasan --}}
                    <div class="p-4 sm:p-5 border-b border-dashed" style="border-color:#EAE0D3; background:#FAF6F0;">
                        <div class="flex gap-3">
                            @if(count($gallery) > 0)
                                <img src="{{ $gallery[0] }}" class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl object-cover shrink-0 shadow-2xs border"
                                     style="border-color:#EAE0D3;" alt="{{ $facility->facility_name }}">
                            @else
                                <x-facility-placeholder class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl shrink-0 border" style="border-color:#EAE0D3;" />
                            @endif
                            <div class="min-w-0 flex flex-col justify-center">
                                <p class="text-xs sm:text-sm font-extrabold text-[#252B2B] line-clamp-2 leading-snug">
                                    {{ $facility->facility_name }}
                                </p>
                                <p class="text-[11px] text-[#4C4F54] mt-0.5 line-clamp-1">
                                    📍 {{ $facility->location }}
                                </p>
                                <p class="text-[11px] font-bold text-emerald-700 mt-1 flex items-center">
                                    <span class="w-1.5 h-1.5 rounded-full inline-block bg-emerald-500 mr-1.5"></span>
                                    {{ $available }} slot siap digunakan
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Body Ringkasan & Form Action --}}
                    <div class="p-4 sm:p-5">
                        <dl class="space-y-2 text-xs">
                            <div class="flex justify-between">
                                <dt class="text-[#4C4F54]">Tanggal Reservasi</dt>
                                <dd class="font-bold text-[#252B2B]">{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-[#4C4F54]">Jam Operasional</dt>
                                <dd class="font-bold text-[#252B2B]">07:00 – 20:00 WIB</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-[#4C4F54]">Durasi Tiap Slot</dt>
                                <dd class="font-bold text-[#252B2B]">30 Menit</dd>
                            </div>
                        </dl>

                        <div class="mt-5 space-y-2.5">
                            @if(auth()->check() && auth()->user()->role === \App\Enums\UserRole::Pengguna)
                                @if($isSunday)
                                    <span class="block text-center text-xs sm:text-sm font-bold px-3 py-2.5 bg-slate-100 text-slate-600 border border-slate-300 rounded-xl">
                                        Hari Minggu Libur, Pilih Hari Lain
                                    </span>
                                @elseif($facility->facility_status === 'aktif')
                                    <a href="{{ route('reservations.create', ['facility_id' => $facility->id_fasilitas, 'date' => $selectedDate]) }}"
                                       x-bind:href="selectedSlot ? urlReservasi + '&start_time=' + selectedSlot : urlReservasi"
                                       @click.prevent="bukaReservasi()"
                                       class="block text-center px-4 py-2.5 rounded-xl font-extrabold text-xs sm:text-sm shadow-sm transition duration-150 bg-maroon-700 text-cream border border-maroon-800 hover:bg-maroon-900">
                                        Ajukan Reservasi Sekarang
                                    </a>
                                    <p x-show="selectedSlot" x-cloak class="text-center text-xs text-charcoal-medium">
                                        Jam mulai terpilih: <span class="font-bold" x-text="selectedSlot"></span>
                                    </p>
                                @else
                                    <span class="block text-center text-xs sm:text-sm font-bold px-3 py-2.5 bg-rose-100 text-rose-800 border border-rose-200 rounded-xl">
                                        Fasilitas Dalam Perbaikan
                                    </span>
                                @endif
                            @elseif(!auth()->check())
                                <a href="{{ route('login') }}"
                                   class="block text-center px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm shadow-sm transition duration-150 bg-maroon-900 text-cream hover:bg-maroon-700">
                                    Login untuk Ajukan Reservasi
                                </a>
                            @endif

                            <a href="{{ route('facilities.index') }}"
                               class="block text-center text-xs font-bold transition-colors hover:underline"
                               style="color:#8F0B13;">
                                &larr; Kembali ke Daftar Fasilitas
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Banner Helpdesk Bantuan (Nuansa Maroon Elegan) --}}
                <div class="rounded-2xl p-4 sm:p-5 shadow-sm text-white"
                     style="background: linear-gradient(135deg, #380F17 0%, #4D0E17 30%, #6B101C 65%, #8F0B13 100%); border:1px solid rgba(239, 223, 197, 0.2);">
                    <p class="text-xs sm:text-sm font-extrabold text-[#EFDFC5]">Butuh Bantuan Reservasi?</p>
                    <p class="mt-1 text-[11px] sm:text-xs text-[#EFDFC5]/85 leading-relaxed">
                        Petugas siap membantu perizinan sarana, perubahan jadwal, dan koordinasi peminjaman.
                    </p>
                    <div class="mt-3 pt-2.5 border-t border-white/15 space-y-1.5">
                        <p class="text-xs font-bold text-[#EFDFC5] flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#EFDFC5] shrink-0 inline-block"></span>
                            <span>Helpdesk: 0812-3456-7890</span>
                        </p>
                        <p class="text-xs font-bold text-[#EFDFC5] flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#EFDFC5] shrink-0 inline-block"></span>
                            <span>Layanan: Senin – Jumat (08.00 – 16.00 WIB)</span>
                        </p>
                    </div>
                </div>
            </aside>

            {{-- Bar CTA menempel di bawah layar, hanya tampil di mobile --}}
            @if($bisaReservasi && ! $isSunday)
                <div class="fixed inset-x-0 bottom-0 z-40 border-t border-cream-border bg-white p-3 shadow-lg lg:hidden">
                    <x-button class="w-full"
                              :href="route('reservations.create', ['facility_id' => $facility->id_fasilitas, 'date' => $selectedDate])"
                              x-bind:href="selectedSlot ? urlReservasi + '&start_time=' + selectedSlot : urlReservasi"
                              @click.prevent="bukaReservasi()">
                        Ajukan Reservasi
                    </x-button>
                </div>
            @endif
        </div>
    </div>

    {{-- Pop-up form ajukan reservasi (tautan biasa tetap jadi cadangan bila JavaScript mati) --}}
    @if($bisaReservasi)
        <x-modal-reservasi :facility-id="$facility->id_fasilitas" :date="$selectedDate" />
    @endif
</x-app-layout>