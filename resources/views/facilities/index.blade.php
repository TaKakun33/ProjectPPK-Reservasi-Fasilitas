<x-app-layout>
    {{-- Hero ala tiket.com: navy + foto kampus + search card putih overlap --}}
    <div class="relative bg-navy-800 overflow-hidden">
        <img src="https://images.pexels.com/photos/11456020/pexels-photo-11456020.jpeg?auto=compress&cs=tinysrgb&w=1600"
             alt="Perpustakaan kampus yang kosong" class="absolute inset-0 w-full h-full object-cover opacity-30"
             onerror="this.style.display='none'">
        <div class="absolute inset-0 bg-gradient-to-b from-navy-900/70 via-navy-800/80 to-navy-900/90"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-24">
            <p class="text-xs font-bold tracking-[0.2em] uppercase text-gold-300">Portal Akademik Kampus</p>
            <h1 class="mt-2 text-2xl sm:text-3xl font-extrabold text-white leading-tight">Sewa Ruang &amp; Fasilitas Kampus</h1>
            <p class="mt-1 text-sm text-blue-100/90">Reservasi ruang, auditorium, laboratorium, dan lapangan. Cek jadwal secara waktu nyata, lalu ajukan izin.</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Search card overlap ala tiket.com --}}
        <div class="relative -mt-14 bg-white rounded-2xl shadow-lg border border-slate-200/70 p-4 sm:p-6">
            <form method="GET" action="{{ route('facilities.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div class="rounded-xl border border-slate-200 px-4 py-2.5 focus-within:border-navy-700 focus-within:ring-2 focus-within:ring-navy-700/20">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Tipe Fasilitas</label>
                    <select name="type" class="w-full border-0 p-0 text-sm font-semibold text-navy-800 focus:ring-0">
                        <option value="">Semua Tipe</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="rounded-xl border border-slate-200 px-4 py-2.5 focus-within:border-navy-700 focus-within:ring-2 focus-within:ring-navy-700/20">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Lokasi</label>
                    <select name="location" class="w-full border-0 p-0 text-sm font-semibold text-navy-800 focus:ring-0">
                        <option value="">Semua Lokasi</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc }}" {{ request('location') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="rounded-xl border border-slate-200 px-4 py-2.5 focus-within:border-navy-700 focus-within:ring-2 focus-within:ring-navy-700/20">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Min. Kapasitas</label>
                    <input type="number" name="capacity" value="{{ request('capacity') }}" placeholder="Cth: 30"
                           class="w-full border-0 p-0 text-sm font-semibold text-navy-800 placeholder:text-slate-400 focus:ring-0">
                </div>
                <div class="flex items-stretch gap-2">
                    <button type="submit" class="flex-1 inline-flex justify-center items-center px-4 py-3 bg-gold-400 rounded-xl font-extrabold text-navy-900 hover:bg-gold-500 transition text-sm shadow-sm">
                        🔍 Cari Fasilitas
                    </button>
                    <a href="{{ route('facilities.index') }}" title="Reset" class="px-4 py-3 bg-white border border-slate-200 text-slate-500 rounded-xl hover:bg-slate-50 transition text-sm font-bold">✕</a>
                </div>
            </form>
        </div>

        {{-- Listing --}}
        <div class="py-8">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-4">
                <div>
                    <h2 class="text-lg font-extrabold text-navy-800">Rekomendasi untukmu</h2>
                    <p class="text-sm text-slate-500">{{ $facilities->total() }} fasilitas ditemukan</p>
                </div>
                {{-- Legenda status slot: di sini menjelaskan badge di kartu, bukan di hero --}}
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-600">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white border border-slate-200 shadow-sm"><span class="w-2.5 h-2.5 rounded-full inline-block" style="background:#22C55E"></span> Tersedia</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white border border-slate-200 shadow-sm"><span class="w-2.5 h-2.5 rounded-full inline-block" style="background:#EAB308"></span> Menunggu</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white border border-slate-200 shadow-sm"><span class="w-2.5 h-2.5 rounded-full inline-block" style="background:#EF4444"></span> Terisi</span>
                </div>
            </div>

            @if($facilities->isEmpty())
                <div class="bg-white p-12 text-center rounded-2xl border border-slate-200/70 text-slate-500">
                    <img src="https://images.pexels.com/photos/36159716/pexels-photo-36159716.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Ruang kelas kosong" class="w-40 h-28 object-cover rounded-xl mx-auto mb-4 opacity-70">
                    Tidak ada fasilitas yang sesuai dengan pencarian Anda.
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($facilities as $facility)
                        <article class="group bg-white rounded-2xl shadow-sm border border-slate-200/70 overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition flex flex-col">
                            {{-- Foto cover 16:10 + badge --}}
                            <div class="relative h-48 overflow-hidden bg-slate-100">
                                <img src="{{ $facility->photo_url }}" alt="{{ $facility->facility_name }}" loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                     onerror="this.src='https://picsum.photos/seed/{{ $facility->id_fasilitas }}/800/500'">
                                <span class="absolute top-3 left-3 text-[11px] font-bold px-2.5 py-1 rounded-full bg-navy-900/90 text-white backdrop-blur">
                                    {{ $facility->type }}
                                </span>
                                @if($facility->facility_status === 'aktif')
                                    <span class="absolute top-3 right-3 inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full text-white" style="background:#22C55E">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> Tersedia
                                    </span>
                                @else
                                    <span class="absolute top-3 right-3 inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full text-white" style="background:#EF4444">
                                        {{ ucfirst($facility->facility_status) }}
                                    </span>
                                @endif
                                <span class="absolute bottom-3 left-3 text-[11px] font-bold px-2 py-1 rounded-lg bg-black/60 text-white backdrop-blur">
                                    👥 {{ $facility->capacity }} orang
                                </span>
                            </div>

                            <div class="p-5 flex flex-col flex-1">
                                <h3 class="font-extrabold text-navy-800 leading-snug line-clamp-1">{{ $facility->facility_name }}</h3>
                                <p class="mt-1 text-xs text-slate-500 flex items-center gap-1">📍 <span class="line-clamp-1">{{ $facility->location }}</span></p>

                                {{-- Amenitas ala tiket.com --}}
                                <div class="mt-3 flex flex-wrap gap-1.5">
                                    @foreach(array_slice($facility->amenities, 0, 3) as $am)
                                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-cream border border-slate-200 text-slate-600">✓ {{ $am }}</span>
                                    @endforeach
                                </div>

                                <p class="mt-3 text-[13px] text-slate-600 line-clamp-2">{{ $facility->description ?? 'Tidak ada deskripsi.' }}</p>

                                <div class="mt-4 pt-4 border-t border-dashed border-slate-200 flex items-center justify-between gap-2 mt-auto">
                                    <div>
                                        <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wide">Izin Akademik</p>
                                        <p class="text-sm font-extrabold text-navy-800">Gratis <span class="text-[11px] font-semibold text-green-700">• Bebas biaya</span></p>
                                    </div>
                                    @if(auth()->check() && auth()->user()->role === \App\Enums\UserRole::Pengguna && $facility->facility_status === 'aktif')
                                        <a href="{{ route('reservations.create', ['facility_id' => $facility->id_fasilitas]) }}" class="inline-flex items-center px-4 py-2 bg-gold-400 text-xs font-extrabold text-navy-900 rounded-xl hover:bg-gold-500 transition shadow-sm">
                                            Booking
                                        </a>
                                    @else
                                        <a href="{{ route('facilities.show', $facility->id_fasilitas) }}" class="inline-flex items-center px-4 py-2 bg-navy-700 text-xs font-bold text-white rounded-xl hover:bg-navy-800 transition">
                                            Lihat Detail
                                        </a>
                                    @endif
                                </div>
                                <a href="{{ route('facilities.show', $facility->id_fasilitas) }}" class="mt-2 text-xs font-bold text-navy-700 hover:text-navy-900 hover:underline">Cek Jadwal Slot &rarr;</a>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $facilities->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
