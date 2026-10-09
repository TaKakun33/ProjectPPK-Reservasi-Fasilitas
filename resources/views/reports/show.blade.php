<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Laporan Kerusakan') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
            <div class="p-6 space-y-5">
                <div class="flex justify-between items-start gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Fasilitas</p>
                        <p class="font-semibold text-gray-900">{{ $laporan->facility->facility_name ?? '-' }}</p>
                        <p class="text-sm text-gray-500">{{ $laporan->facility->type ?? '' }} - {{ $laporan->facility->location ?? '' }}</p>
                    </div>
                    @php
                        $badges = [
                            'baru'     => 'bg-maroon-100 text-maroon-800',
                            'diproses' => 'bg-yellow-100 text-yellow-800',
                            'selesai'  => 'bg-green-100 text-green-800',
                            'ditolak'  => 'bg-red-100 text-red-800',
                        ];
                    @endphp
                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $badges[$laporan->report_status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($laporan->report_status) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Kategori</p>
                        <p class="font-medium text-gray-800">{{ $laporan->category->category_name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tanggal Laporan</p>
                        <p class="font-medium text-gray-800">{{ $laporan->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Deskripsi Kerusakan</p>
                    <p class="text-gray-800">{{ $laporan->description }}</p>
                </div>

                @if($laporan->photos->isNotEmpty())
                    <div>
                        <p class="text-sm text-gray-500 mb-2">Foto Kerusakan ({{ $laporan->photos->count() }})</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($laporan->photos as $i => $foto)
                                <button type="button"
                                    onclick="bukaLightbox({{ $i }})"
                                    class="focus:outline-none group">
                                    <img src="{{ route('reports.photo', $foto->id_foto) }}" alt="Foto kerusakan"
                                        class="w-full h-32 object-cover rounded-lg border border-gray-200 group-hover:opacity-80 group-hover:ring-2 group-hover:ring-maroon-500 transition cursor-zoom-in">
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Lightbox Modal --}}
                    <div id="lightbox" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/80 backdrop-blur-sm" onclick="tutupLightboxJikaBg(event)">
                        <div class="relative max-w-4xl w-full mx-4 flex flex-col items-center">
                            {{-- Tombol Close --}}
                            <button onclick="tutupLightbox()" class="absolute -top-10 right-0 text-white hover:text-red-400 transition text-4xl font-bold leading-none" title="Tutup (Esc)">&times;</button>

                            {{-- Gambar --}}
                            <img id="lightbox-img" src="" alt="Foto kerusakan" class="max-h-[80vh] max-w-full rounded-xl shadow-2xl object-contain">

                            {{-- Navigasi & Counter --}}
                            <div class="flex items-center gap-6 mt-4">
                                <button onclick="gantiFoto(-1)" id="lb-prev" class="text-white bg-white/20 hover:bg-white/40 rounded-full p-2 transition disabled:opacity-30" title="Sebelumnya">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <span id="lb-counter" class="text-white text-sm font-medium"></span>
                                <button onclick="gantiFoto(1)" id="lb-next" class="text-white bg-white/20 hover:bg-white/40 rounded-full p-2 transition disabled:opacity-30" title="Selanjutnya">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <script>
                        const fotoUrls = @json($laporan->photos->map(fn($f) => route('reports.photo', $f->id_foto)));
                        let fotoAktif = 0;

                        function bukaLightbox(index) {
                            fotoAktif = index;
                            updateLightbox();
                            document.getElementById('lightbox').classList.remove('hidden');
                            document.body.style.overflow = 'hidden';
                        }

                        function tutupLightbox() {
                            document.getElementById('lightbox').classList.add('hidden');
                            document.body.style.overflow = '';
                        }

                        function tutupLightboxJikaBg(e) {
                            if (e.target === document.getElementById('lightbox')) tutupLightbox();
                        }

                        function gantiFoto(arah) {
                            fotoAktif = (fotoAktif + arah + fotoUrls.length) % fotoUrls.length;
                            updateLightbox();
                        }

                        function updateLightbox() {
                            document.getElementById('lightbox-img').src = fotoUrls[fotoAktif];
                            document.getElementById('lb-counter').textContent = (fotoAktif + 1) + ' / ' + fotoUrls.length;
                            document.getElementById('lb-prev').disabled = fotoUrls.length <= 1;
                            document.getElementById('lb-next').disabled = fotoUrls.length <= 1;
                        }

                        document.addEventListener('keydown', function(e) {
                            if (document.getElementById('lightbox').classList.contains('hidden')) return;
                            if (e.key === 'Escape') tutupLightbox();
                            if (e.key === 'ArrowLeft') gantiFoto(-1);
                            if (e.key === 'ArrowRight') gantiFoto(1);
                        });
                    </script>
                @endif
                
                @if($laporan->resolution_notes)
                    <div class="p-4 bg-maroon-50 border-l-4 border-maroon-600 rounded">
                        <p class="text-sm font-semibold text-maroon-800">Catatan Resolusi Petugas</p>
                        <p class="mt-1 text-sm text-maroon-900">{{ $laporan->resolution_notes }}</p>
                    </div>
                @endif

                <div class="flex justify-end pt-2 border-t">
                    <a href="{{ route('reports.index') }}" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50">
                        Kembali ke Riwayat
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
