<x-app-layout>
    {{-- Hero ala tiket.com: navy + foto kampus + search card putih overlap --}}
    <div class="relative bg-maroon-800 overflow-hidden">
        <img src="https://images.pexels.com/photos/11456020/pexels-photo-11456020.jpeg?auto=compress&cs=tinysrgb&w=1600"
             alt="Perpustakaan kampus yang kosong" class="absolute inset-0 w-full h-full object-cover opacity-30"
             onerror="this.style.display='none'">
        <div class="absolute inset-0 bg-gradient-to-b from-maroon-900/70 via-maroon-800/80 to-maroon-900/90"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-24">
            <p class="text-xs font-bold tracking-[0.2em] uppercase text-cream-300">Reservasi Fasilitas Kampus</p>
            <h1 class="mt-2 text-2xl sm:text-3xl font-extrabold text-white leading-tight">Reservasi Ruang &amp; Fasilitas</h1>
            <p class="mt-1 text-sm text-blue-100/90">Reservasi ruang dan fasilitas yang tersedia di kampus. Cek jadwal, lalu ajukan peminjaman.</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Search card overlap ala tiket.com --}}
        <div class="relative -mt-14 bg-white rounded-2xl shadow-lg border border-slate-200/70 p-4 sm:p-6">
            <form method="GET" action="{{ route('facilities.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div class="rounded-xl border border-slate-200 px-4 py-2.5 focus-within:border-maroon-700 focus-within:ring-2 focus-within:ring-navy-700/20">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Tipe Fasilitas</label>
                    <select name="type" class="w-full border-0 p-0 text-sm font-semibold text-maroon-800 focus:ring-0">
                        <option value="">Semua Tipe</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="rounded-xl border border-slate-200 px-4 py-2.5 focus-within:border-maroon-700 focus-within:ring-2 focus-within:ring-navy-700/20">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Lokasi</label>
                    <select name="location" class="w-full border-0 p-0 text-sm font-semibold text-maroon-800 focus:ring-0">
                        <option value="">Semua Lokasi</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc }}" {{ request('location') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="rounded-xl border border-slate-200 px-4 py-2.5 focus-within:border-maroon-700 focus-within:ring-2 focus-within:ring-navy-700/20">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Min. Kapasitas</label>
                    <input type="number" name="capacity" value="{{ request('capacity') }}" placeholder="Cth: 30"
                           class="w-full border-0 p-0 text-sm font-semibold text-maroon-800 placeholder:text-slate-400 focus:ring-0">
                </div>
                <div class="flex items-stretch gap-2">
                    <button type="submit" class="flex-1 inline-flex justify-center items-center gap-2 px-4 py-3 bg-maroon-800 rounded-xl font-extrabold text-cream-100 hover:bg-maroon-900 transition text-sm shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/></svg> Cari Fasilitas
                    </button>
                    <a href="{{ route('facilities.index') }}" title="Reset filter" class="px-4 py-3 bg-white border border-slate-200 text-slate-500 rounded-xl hover:bg-slate-50 hover:text-maroon-800 transition font-bold flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></a>
                </div>
            </form>
        </div>

        {{-- Listing --}}
        <div class="py-8">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-4">
                <div>
                    <h2 class="text-lg font-extrabold text-maroon-800">Rekomendasi untukmu</h2>
                    <p class="text-sm text-slate-500">{{ $facilities->total() }} fasilitas ditemukan</p>
                </div>
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
                                <span class="absolute top-3 left-3 text-[11px] font-bold px-2.5 py-1 rounded-full bg-maroon-900/90 text-white backdrop-blur">
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
                                    {{ $facility->capacity }} orang
                                </span>
                            </div>

                            <div class="p-5 flex flex-col flex-1">
                                <h3 class="font-extrabold text-maroon-800 leading-snug line-clamp-1">{{ $facility->facility_name }}</h3>
                                <p class="mt-1 text-xs text-slate-500 flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg><span class="line-clamp-1">{{ $facility->location }}</span></p>

                                {{-- Amenitas --}}
                                @if(!empty($facility->amenities) && is_array($facility->amenities))
                                    <div class="mt-3 flex flex-wrap gap-1.5">
                                        @foreach(array_slice($facility->amenities, 0, 3) as $am)
                                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-cream-100 border border-slate-200 text-slate-600">&#10003; {{ $am }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                <p class="mt-3 text-[13px] text-slate-600 line-clamp-2">{{ $facility->description ?? 'Tidak ada deskripsi.' }}</p>

                                <div class="mt-4 pt-4 border-t border-dashed border-slate-200 flex items-center justify-between gap-2 mt-auto">
                                    <a href="{{ route('facilities.show', $facility->id_fasilitas) }}" class="text-xs font-bold text-maroon-700 hover:text-maroon-900 hover:underline inline-flex items-center gap-1">
                                        Cek Jadwal Slot &rarr;
                                    </a>

                                    <div class="flex items-center gap-2">
                                        @if(auth()->check() && auth()->user()->role === \App\Enums\UserRole::Pengguna && $facility->facility_status === 'aktif')
                                            <a href="{{ route('reservations.create', ['facility_id' => $facility->id_fasilitas]) }}" class="inline-flex items-center px-4 py-2 bg-maroon-800 text-xs font-extrabold text-cream-100 rounded-xl hover:bg-maroon-900 transition shadow-sm">
                                                Booking
                                            </a>
                                        @else
                                            <a href="{{ route('facilities.show', $facility->id_fasilitas) }}" class="inline-flex items-center px-4 py-2 bg-maroon-800 text-xs font-bold text-white rounded-xl hover:bg-maroon-900 transition shadow-sm">
                                                Lihat Detail
                                            </a>
                                        @endif
                                    </div>
                                </div>
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

