<x-app-layout>
    @php
        $gallery = $facility->gallery ?? [$facility->photo_url];
        $available = collect($slots)->where('is_available', true)->count();
        $total = count($slots);
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{-- Breadcrumb ala tiket.com --}}
        <nav class="text-xs text-slate-500 flex items-center gap-1.5 mb-3">
            <a href="{{ route('facilities.index') }}" class="hover:text-maroon-700 font-semibold">Beranda</a>
            <span>/</span>
            <a href="{{ route('facilities.index', ['type' => $facility->type]) }}" class="hover:text-maroon-700 font-semibold">{{ $facility->type }}</a>
            <span>/</span>
            <span class="text-maroon-800 font-bold truncate">{{ $facility->facility_name }}</span>
        </nav>

        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-maroon-800/10 text-maroon-800 border border-maroon-700/20">{{ $facility->type }}</span>
                    @if($facility->facility_status === 'aktif')
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full text-white" style="background:#22C55E">&#10003; Bisa dipesan</span>
                    @else
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full text-white" style="background:#EF4444">{{ ucfirst($facility->facility_status) }}</span>
                    @endif
                </div>
                <h1 class="mt-2 text-2xl font-extrabold text-maroon-800">{{ $facility->facility_name }}</h1>
                <p class="mt-1 text-sm text-slate-500">{{ $facility->location }} &nbsp;&bull;&nbsp; Kapasitas {{ $facility->capacity }} orang &nbsp;&bull;&nbsp; Rating 4.8/5</p>
            </div>
        </div>

        {{-- Galeri ala tiket.com: 1 besar + 2 kecil --}}
        <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-2 h-auto md:h-80">
            <div class="md:col-span-2 md:row-span-2 h-64 md:h-full overflow-hidden rounded-2xl rounded-b-none md:rounded-b-2xl md:rounded-r-none bg-slate-100">
                <img src="{{ $gallery[0] }}" alt="{{ $facility->facility_name }}" class="w-full h-full object-cover hover:scale-105 transition duration-500"
                     onerror="this.src='https://picsum.photos/seed/{{ $facility->id_fasilitas }}a/1000/600'">
            </div>
            <div class="hidden md:block h-full overflow-hidden bg-slate-100">
                <img src="{{ $gallery[1] ?? $gallery[0] }}" alt="Foto 2" class="w-full h-full object-cover hover:scale-105 transition duration-500"
                     onerror="this.src='https://picsum.photos/seed/{{ $facility->id_fasilitas }}b/800/500'">
            </div>
            <div class="hidden md:block h-full overflow-hidden rounded-r-2xl bg-slate-100 relative">
                <img src="{{ $gallery[2] ?? $gallery[0] }}" alt="Foto 3" class="w-full h-full object-cover hover:scale-105 transition duration-500"
                     onerror="this.src='https://picsum.photos/seed/{{ $facility->id_fasilitas }}c/800/500'">
                <span class="absolute bottom-3 right-3 text-xs font-bold px-3 py-1.5 rounded-lg bg-white/95 text-maroon-800 shadow">Lihat Semua Foto</span>
            </div>
            {{-- Mobile: 2 foto kecil --}}
            <div class="grid grid-cols-2 gap-2 md:hidden">
                <img src="{{ $gallery[1] ?? $gallery[0] }}" alt="Foto 2" class="w-full h-32 object-cover rounded-xl bg-slate-100">
                <img src="{{ $gallery[2] ?? $gallery[0] }}" alt="Foto 3" class="w-full h-32 object-cover rounded-xl bg-slate-100">
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            {{-- Kolom kiri: konten --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Tentang --}}
                <section class="bg-white rounded-2xl border border-slate-200/70 p-6">
                    <h2 class="font-extrabold text-maroon-800">Tentang fasilitas ini</h2>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ $facility->description ?? 'Tidak ada deskripsi.' }}</p>
                    <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-2 text-center">
                        @foreach($facility->amenities as $am)
                            <div class="rounded-xl bg-cream-100 border border-slate-200 px-2 py-2.5">
                                <p class="text-base">&#10003;</p>
                                <p class="text-[11px] font-bold text-maroon-800">{{ $am }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>

                {{-- Slot ketersediaan --}}
                <section class="bg-white rounded-2xl border border-slate-200/70 p-6">
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 mb-4">
                        <div>
                            <h2 class="font-extrabold text-maroon-800">Ketersediaan Slot (07:00 - 20:00)</h2>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $available }}/{{ $total }} slot tersedia &mdash; hijau kosong, kuning menunggu, merah terisi.</p>
                        </div>
                        <form method="GET" action="{{ route('facilities.show', $facility->id_fasilitas) }}" class="flex items-center gap-2 bg-cream-100 border border-slate-200 rounded-xl px-3 py-2">
                            <label for="date" class="text-xs font-bold text-maroon-800">Tanggal</label>
                            <input type="date" id="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()"
                                   class="border-0 bg-transparent text-sm font-semibold text-maroon-800 focus:ring-0 p-0">
                        </form>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-2.5">
                        @foreach($slots as $slot)
                            @php
                                $statusKey = strtolower($slot['status'] ?? ($slot['is_available'] ? 'tersedia' : 'terisi'));
                                $isPending = in_array($statusKey, ['pending', 'menunggu', 'diproses', 'menunggu persetujuan']);
                            @endphp
                            <div class="p-2.5 rounded-xl border text-center text-xs
                                @if($slot['is_available']) bg-green-50 border-green-200 text-green-900
                                @elseif($isPending) bg-yellow-50 border-yellow-300 text-yellow-900
                                @else bg-red-50 border-red-200 text-red-900 @endif">
                                <div class="font-extrabold text-[13px]">{{ $slot['start'] }} - {{ $slot['end'] }}</div>
                                <div class="mt-1.5">
                                    @if($slot['is_available'])
                                        <span class="px-2 py-0.5 rounded-md text-[11px] font-bold text-white" style="background:#22C55E;">Tersedia</span>
                                    @elseif($isPending)
                                        <span class="px-2 py-0.5 rounded-md text-[11px] font-bold" style="background:#EAB308;color:#101F4A;">Menunggu</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md text-[11px] font-bold text-white" style="background:#EF4444;">{{ ucfirst($slot['status']) }}</span>
                                        @auth
                                            @if($slot['booking'] && $slot['booking']->id_user === auth()->id())
                                                <div class="text-[10px] mt-1 font-bold">(Milik Anda)</div>
                                            @endif
                                        @endauth
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 text-xs text-slate-500 flex flex-wrap gap-4">
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full inline-block" style="background:#22C55E"></span> Tersedia / Kosong</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full inline-block" style="background:#EAB308"></span> Menunggu Persetujuan</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full inline-block" style="background:#EF4444"></span> Sudah Dibooking</span>
                    </div>
                </section>
            </div>

            {{-- Kolom kanan: sticky booking card ala tiket.com --}}
            <aside class="lg:sticky lg:top-6 space-y-4">
                <div class="bg-white rounded-2xl border border-slate-200/70 shadow-lg overflow-hidden">
                    <div class="p-5 border-b border-dashed border-slate-200">
                        <div class="flex gap-3">
                            <img src="{{ $gallery[0] }}" class="w-20 h-20 rounded-xl object-cover bg-slate-100" alt="">
                            <div class="min-w-0">
                                <p class="text-sm font-extrabold text-maroon-800 line-clamp-2">{{ $facility->facility_name }}</p>
                                <p class="text-xs text-slate-500 mt-0.5 line-clamp-1">{{ $facility->location }}</p>
                                <p class="text-xs font-bold text-green-700 mt-1">{{ $available }} slot tersedia</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-5">
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between"><dt class="text-slate-500">Tanggal</dt><dd class="font-bold text-maroon-800">{{ $selectedDate }}</dd></div>
                            <div class="flex justify-between"><dt class="text-slate-500">Jam operasional</dt><dd class="font-bold text-maroon-800">07:00&ndash;20:00</dd></div>
                            <div class="flex justify-between"><dt class="text-slate-500">Durasi slot</dt><dd class="font-bold text-maroon-800">30 menit</dd></div>
                        </dl>
                        <div class="mt-5 space-y-2">
                            @if(auth()->check() && auth()->user()->role === \App\Enums\UserRole::Pengguna)
                                @if($facility->facility_status === 'aktif')
                                    <a href="{{ route('reservations.create', ['facility_id' => $facility->id_fasilitas, 'date' => $selectedDate]) }}"
                                       class="block text-center px-5 py-3 bg-maroon-800 text-cream-100 font-extrabold rounded-xl shadow hover:bg-maroon-900 transition">
                                        Ajukan Reservasi
                                    </a>
                                @else
                                    <span class="block text-center text-sm font-bold px-3 py-2.5 bg-red-100 text-red-800 border border-red-200 rounded-xl">Dalam Perbaikan</span>
                                @endif
                            @elseif(!auth()->check())
                                <a href="{{ route('login') }}" class="block text-center px-4 py-3 bg-maroon-800 text-white text-sm font-bold rounded-xl hover:bg-maroon-800 transition">
                                    Login untuk Booking
                                </a>
                            @endif
                            <a href="{{ route('facilities.index') }}" class="block text-center text-xs font-bold text-maroon-700 hover:underline">&larr; Kembali ke daftar</a>
                        </div>
                    </div>
                </div>

                <div class="bg-maroon-800 rounded-2xl p-5 text-white">
                    <p class="text-sm font-extrabold">Butuh bantuan?</p>
                    <p class="mt-1 text-xs text-blue-100/80">Petugas siap membantu izin, perubahan jadwal, dan laporan kerusakan fasilitas.</p>
                    <p class="mt-3 text-xs font-bold text-cream-300">Tel. Helpdesk Kampus &bull; 08.00&ndash;16.00 WIB</p>
                </div>
            </aside>
        </div>
    </div>
</x-app-layout>

