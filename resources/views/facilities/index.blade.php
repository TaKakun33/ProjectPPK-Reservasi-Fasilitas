<x-app-layout>
    {{-- Hero dengan Gradien Maroon (#380F17 ke #8F0B13) --}}
    <div class="relative overflow-hidden" style="background: linear-gradient(135deg, #380F17 0%, #4D0E17 30%, #6B101C 65%, #8F0B13 100%);">
        {{-- Aksen lingkaran dekoratif subtle --}}
        <div class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full blur-3xl pointer-events-none" style="background:rgba(239, 223, 197, 0.08);"></div>
        <div class="absolute right-1/4 -top-20 w-64 h-64 rounded-full blur-2xl pointer-events-none" style="background:rgba(143, 11, 19, 0.45);"></div>

        <img src="https://images.pexels.com/photos/11456020/pexels-photo-11456020.jpeg?auto=compress&cs=tinysrgb&w=1600"
             alt="Perpustakaan kampus" class="absolute inset-0 w-full h-full object-cover opacity-20 mix-blend-overlay"
             onerror="this.style.display='none'">
        <div class="absolute inset-0" style="background: linear-gradient(180deg, rgba(56, 15, 23, 0.35) 0%, rgba(56, 15, 23, 0.85) 100%);"></div>

        <div class="relative w-full px-4 sm:px-6 lg:px-8 py-4 sm:py-5">
            <span class="inline-block text-[10px] font-bold tracking-widest uppercase px-2.5 py-0.5 rounded mb-1"
                  style="background:rgba(239, 223, 197, 0.15); color:#EFDFC5; border:1px solid rgba(239, 223, 197, 0.25);">
                Katalog Fasilitas Kampus
            </span>
            <div>
                <h1 class="text-base sm:text-lg font-extrabold text-white tracking-tight leading-tight">
                    Reservasi Ruang &amp; Fasilitas
                </h1>
                <p class="text-xs mt-0.5" style="color:#EFDFC5; opacity:0.9;">
                    Periksa jadwal operasional dan ketersediaan sarana kampus, lalu ajukan peminjaman.
                </p>
            </div>
        </div>
    </div>

    {{-- Konten Penuh Layar (Kanan-Kiri Terisi, Tidak Kosong) --}}
    <div class="w-full px-4 sm:px-6 lg:px-8">
        {{-- Search card di latar belakang di bawah banner maroon --}}
        <div class="relative mt-2 mb-1.5 bg-white rounded-xl shadow-xs p-2 sm:p-2.5"
             style="border:1px solid #EAE0D3; box-shadow: 0 2px 8px rgba(56, 15, 23, 0.04);">
            <form method="GET" action="{{ route('facilities.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-2 sm:gap-2.5">
                <div class="rounded-lg px-2.5 py-1 transition"
                     style="border:1px solid #EAE0D3; background:#FAF6F0;">
                    <label class="block text-[9px] font-bold uppercase tracking-wider mb-0.5" style="color:#4C4F54;">Tipe Fasilitas</label>
                    <select name="type" class="w-full border-0 p-0 text-xs font-semibold focus:ring-0 bg-transparent cursor-pointer" style="color:#252B2B;">
                        <option value="">Semua Tipe</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="rounded-lg px-2.5 py-1 transition"
                     style="border:1px solid #EAE0D3; background:#FAF6F0;">
                    <label class="block text-[9px] font-bold uppercase tracking-wider mb-0.5" style="color:#4C4F54;">Lokasi Gedung</label>
                    <select name="location" class="w-full border-0 p-0 text-xs font-semibold focus:ring-0 bg-transparent cursor-pointer" style="color:#252B2B;">
                        <option value="">Semua Lokasi</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc }}" {{ request('location') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="rounded-lg px-2.5 py-1 transition"
                     style="border:1px solid #EAE0D3; background:#FAF6F0;">
                    <label class="block text-[9px] font-bold uppercase tracking-wider mb-0.5" style="color:#4C4F54;">Kapasitas Minimum</label>
                    <input type="number" name="capacity" value="{{ request('capacity') }}" placeholder="Contoh: 30"
                           class="w-full border-0 p-0 text-xs font-semibold placeholder:text-[#4C4F54]/50 focus:ring-0 bg-transparent" style="color:#252B2B;">
                </div>
                <div class="flex items-stretch gap-2">
                    <button type="submit"
                            class="flex-1 inline-flex justify-center items-center gap-1.5 px-3 py-1 rounded-lg font-bold text-xs shadow-xs transition duration-150"
                            style="background:#8F0B13; color:#EFDFC5; border:1px solid #70090F;"
                            onmouseover="this.style.background='#380F17';"
                            onmouseout="this.style.background='#8F0B13';">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/></svg>
                        <span>Cari Fasilitas</span>
                    </button>
                    <a href="{{ route('facilities.index') }}" title="Atur Ulang Filter" aria-label="Atur Ulang Filter"
                       class="px-2.5 py-1 rounded-lg transition duration-150 font-bold flex items-center justify-center shadow-xs"
                       style="background:#FAF6F0; border:1px solid #EAE0D3; color:#4C4F54;"
                       onmouseover="this.style.borderColor='#8F0B13'; this.style.color='#8F0B13'; this.style.background='#FFFFFF';"
                       onmouseout="this.style.borderColor='#EAE0D3'; this.style.color='#4C4F54'; this.style.background='#FAF6F0';">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </a>
                </div>
            </form>
        </div>

        {{-- Listing Header --}}
        <div class="pt-1 pb-1 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
            <div>
                <h2 class="text-xs sm:text-sm font-extrabold tracking-tight" style="color:#252B2B;">
                    Rekomendasi Fasilitas Kampus
                    <span class="text-[11px] font-medium text-[#4C4F54] ml-1.5">({{ $facilities->total() }} sarana terdaftar)</span>
                </h2>
            </div>
            <div class="flex items-center gap-1.5 text-[10px] font-semibold">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full shadow-2xs"
                      style="background:#D1FAE5; color:#065F46; border:1px solid #A7F3D0;">
                    <span class="w-1.5 h-1.5 rounded-full inline-block bg-emerald-500"></span> Tersedia
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full shadow-2xs"
                      style="background:#FEF3C7; color:#92400E; border:1px solid #FDE68A;">
                    <span class="w-1.5 h-1.5 rounded-full inline-block bg-amber-500"></span> Pemeliharaan
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full shadow-2xs"
                      style="background:#FEE2E2; color:#991B1B; border:1px solid #FECACA;">
                    <span class="w-1.5 h-1.5 rounded-full inline-block bg-rose-500"></span> Nonaktif
                </span>
            </div>
        </div>

        {{-- Listing Cards --}}
        @if($facilities->isEmpty())
            <div class="bg-white p-6 text-center rounded-xl shadow-xs my-2" style="border:1px solid #EAE0D3; color:#4C4F54;">
                <p class="text-xs">Tidak ada fasilitas kampus yang sesuai dengan kriteria pencarian Anda.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
                @foreach($facilities as $facility)
                    <article class="group bg-white rounded-xl overflow-hidden hover:-translate-y-0.5 transition duration-150 flex flex-col shadow-xs"
                             style="border:1px solid #EAE0D3;">
                        {{-- Foto cover proporsional (h-28 sm:h-30) + badge --}}
                        <div class="relative h-28 sm:h-30 overflow-hidden bg-slate-100 shrink-0">
                            <img src="{{ $facility->photo_url }}" alt="{{ $facility->facility_name }}" loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                 onerror="this.src='https://picsum.photos/seed/{{ $facility->id_fasilitas }}/800/500'">
                            <span class="absolute top-1.5 left-1.5 text-[9.5px] font-bold px-2 py-0.5 rounded-full shadow-xs backdrop-blur-sm"
                                  style="background: linear-gradient(135deg, #380F17 0%, #8F0B13 100%); color:#EFDFC5; border:1px solid rgba(239, 223, 197, 0.25);">
                                {{ $facility->type }}
                            </span>
                            @if($facility->facility_status === 'aktif')
                                <span class="absolute top-1.5 right-1.5 inline-flex items-center gap-1 text-[9.5px] font-bold px-2 py-0.5 rounded-full shadow-xs"
                                      style="background:#D1FAE5; color:#065F46; border:1px solid #A7F3D0;">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Tersedia
                                </span>
                            @elseif($facility->facility_status === 'dalam perbaikan')
                                <span class="absolute top-1.5 right-1.5 inline-flex items-center gap-1 text-[9.5px] font-bold px-2 py-0.5 rounded-full shadow-xs"
                                      style="background:#FEF3C7; color:#92400E; border:1px solid #FDE68A;">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Perbaikan
                                </span>
                            @else
                                <span class="absolute top-1.5 right-1.5 inline-flex items-center gap-1 text-[9.5px] font-bold px-2 py-0.5 rounded-full shadow-xs"
                                      style="background:#FEE2E2; color:#991B1B; border:1px solid #FECACA;">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Nonaktif
                                </span>
                            @endif
                            <span class="absolute bottom-1.5 left-1.5 text-[9.5px] font-bold px-1.5 py-0.5 rounded-md backdrop-blur-md shadow-xs"
                                  style="background: rgba(37, 43, 43, 0.85); color:#EFDFC5; border:1px solid rgba(239, 223, 197, 0.2);">
                                {{ $facility->capacity }} Orang
                            </span>
                        </div>

                        <div class="p-2 sm:p-2.5 flex flex-col flex-1">
                            <h3 class="font-extrabold text-xs sm:text-[13px] leading-snug line-clamp-1 group-hover:text-[#8F0B13] transition-colors"
                                style="color:#252B2B;" title="{{ $facility->facility_name }}">
                                {{ $facility->facility_name }}
                            </h3>
                            <p class="mt-0.5 text-[10.5px] flex items-center gap-1 text-[#4C4F54] line-clamp-1" title="{{ $facility->location }}">
                                <svg class="h-3 w-3 shrink-0 text-[#8F0B13]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="truncate">{{ $facility->location }}</span>
                            </p>

                            {{-- Amenitas compact --}}
                            @if(!empty($facility->amenities) && is_array($facility->amenities))
                                <div class="mt-1 flex flex-wrap gap-1">
                                    @foreach(array_slice($facility->amenities, 0, 2) as $am)
                                        <span class="text-[9.5px] font-medium px-1.5 py-0.5 rounded shadow-2xs truncate max-w-[120px]"
                                              style="background:#FAF6F0; border:1px solid #EAE0D3; color:#4C4F54;" title="{{ $am }}">
                                            {{ $am }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <p class="mt-0.5 text-[10.5px] leading-tight line-clamp-1 text-[#4C4F54]">
                                {{ $facility->description ?? 'Sarana kampus.' }}
                            </p>

                            <div class="mt-1.5 pt-1.5 flex items-center justify-between gap-1.5 mt-auto"
                                 style="border-top:1px dashed #EAE0D3;">
                                <a href="{{ route('facilities.show', $facility->id_fasilitas) }}"
                                   class="text-[11px] font-bold transition inline-flex items-center gap-0.5"
                                   style="color:#8F0B13;"
                                   onmouseover="this.style.color='#380F17';"
                                   onmouseout="this.style.color='#8F0B13';">
                                    Detail &rarr;
                                </a>

                                <div class="flex items-center gap-1">
                                    @if(auth()->check() && auth()->user()->role === \App\Enums\UserRole::Pengguna && $facility->facility_status === 'aktif')
                                        <a href="{{ route('reservations.create', ['facility_id' => $facility->id_fasilitas]) }}"
                                           class="inline-flex items-center px-2.5 py-1 text-[11px] font-extrabold rounded-lg transition shadow-2xs"
                                           style="background:#8F0B13; color:#EFDFC5; border:1px solid #70090F;"
                                           onmouseover="this.style.background='#380F17';"
                                           onmouseout="this.style.background='#8F0B13';">
                                            Booking
                                        </a>
                                    @else
                                        <a href="{{ route('facilities.show', $facility->id_fasilitas) }}"
                                           class="inline-flex items-center px-2.5 py-1 text-[11px] font-bold rounded-lg transition shadow-2xs"
                                           style="background:#FAF6F0; color:#380F17; border:1px solid #EAE0D3;"
                                           onmouseover="this.style.background='#8F0B13'; this.style.color='#EFDFC5'; this.style.borderColor='#8F0B13';"
                                           onmouseout="this.style.background='#FAF6F0'; this.style.color='#380F17'; this.style.borderColor='#EAE0D3';">
                                            Detail
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Bagian Pindah Halaman (Pagination) langsung terlihat di bawah --}}
            <div class="pt-1.5 pb-2">
                {{ $facilities->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
