<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Laporan Kerusakan') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        @if(session('success'))
            <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="p-4 bg-yellow-50 border-l-4 text-yellow-800 rounded text-sm font-medium" style="border-left-color:#eab308">
                {{ session('warning') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm">
                <p class="font-semibold mb-1">Terdapat kesalahan pengisian:</p>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Info laporan (sama gayanya dengan halaman detail milik pengguna) --}}
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
                            'baru'     => 'bg-blue-100 text-blue-800',
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
                        <p class="text-sm text-gray-500">Pelapor</p>
                        <p class="font-medium text-gray-800">{{ $laporan->user->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Kategori</p>
                        <p class="font-medium text-gray-800">{{ $laporan->category->category_name ?? '-' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-sm text-gray-500">Tanggal Laporan</p>
                        <p class="font-medium text-gray-800">{{ $laporan->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Deskripsi Kerusakan</p>
                    <p class="text-gray-800">{{ $laporan->description }}</p>
                </div>

                {{-- Galeri foto (bisa 1 sampai banyak foto) --}}
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
                @else
                    <p class="text-sm text-gray-400 italic">Tidak ada foto dilampirkan.</p>
                @endif

                @if($laporan->resolution_notes)
                    <div class="p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
                        <p class="text-sm font-semibold text-blue-800">Catatan untuk Pelapor (terakhir)</p>
                        <p class="mt-1 text-sm text-blue-900">{{ $laporan->resolution_notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Indikasi laporan ganda: laporan lain yang masih terbuka untuk fasilitas + kategori yang sama --}}
        @if(($laporanSerupa ?? 0) > 0)
            <div class="p-4 bg-yellow-50 border-l-4 text-yellow-800 rounded text-sm" style="border-left-color:#eab308">
                Ada <span class="font-semibold">{{ $laporanSerupa }}</span> laporan lain yang masih terbuka untuk fasilitas dan kategori yang sama.
                Periksa daftar laporan, kemungkinan ini kerusakan yang sama.
            </div>
        @endif

        {{-- Reservasi disetujui yang akan terdampak bila fasilitas masuk perbaikan --}}
        @if(isset($reservasiTerdampak) && $reservasiTerdampak->isNotEmpty())
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <div class="p-6 space-y-3">
                    <h3 class="font-semibold text-gray-900">Reservasi disetujui di fasilitas ini</h3>
                    <p class="text-sm text-gray-600">
                        Reservasi berikut masih akan berlangsung. Bila Anda memilih menutup fasilitas saat memproses laporan ini,
                        reservasi disetujui di bawah ini akan dibatalkan otomatis (reservasi yang masih menunggu ditolak otomatis),
                        lengkap dengan alasan yang terlihat oleh pemesan. Bila fasilitas tidak ditutup, reservasi tidak berubah.
                    </p>
                    <ul class="divide-y divide-gray-100 text-sm">
                        @foreach($reservasiTerdampak as $res)
                            <li class="py-2 flex justify-between gap-4">
                                <span class="text-gray-800">{{ $res->user->name ?? '-' }}</span>
                                <span class="text-gray-600">{{ $res->date->format('d M Y') }}, {{ substr($res->start_time, 0, 5) }} - {{ substr($res->end_time, 0, 5) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Aksi petugas: dipisah dari tabel, satu catatan wajib dipakai untuk semua aksi --}}
        @if(in_array($laporan->report_status, ['baru', 'diproses']))
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <div class="p-6 space-y-4">
                    <h3 class="font-semibold text-gray-900">Proses Laporan</h3>

                    <form method="POST" action="{{ route('petugas.reports.update-status', $laporan->id_laporan) }}" id="statusForm" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Catatan untuk Pelapor
                                <span class="text-xs font-normal text-gray-400">(wajib diisi saat menolak atau menyelesaikan laporan, opsional untuk Proses)</span>
                            </label>
                            <textarea name="resolution_notes" id="resolutionNotes" rows="3"
                                      placeholder="Opsional untuk Proses. Wajib diisi saat Selesai (apa yang diperbaiki) atau Tolak (alasannya)..."
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('resolution_notes') }}</textarea>
                        </div>

                        @if($laporan->report_status === 'baru')
                            <div class="p-3 bg-gray-50 border border-gray-200 rounded-md">
                                <label class="flex items-start gap-2 text-sm text-gray-700">
                                    <input type="checkbox" name="menutup_fasilitas" value="1" id="menutupFasilitas"
                                           {{ old('menutup_fasilitas') ? 'checked' : '' }}
                                           class="mt-0.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span>
                                        <span class="font-medium">Tutup fasilitas untuk perbaikan</span><br>
                                        <span class="text-xs text-gray-500">
                                            Centang bila kerusakan membuat fasilitas tidak layak dipakai. Fasilitas berstatus dalam perbaikan,
                                            reservasi disetujui yang belum berlangsung dibatalkan, dan reservasi yang menunggu ditolak otomatis.
                                            Biarkan kosong bila fasilitas masih bisa dipakai selama laporan ditangani.
                                        </span>
                                    </span>
                                </label>
                            </div>
                        @endif

                        {{-- Aksi langsung berupa tombol; setiap tombol sekaligus jadi tombol simpan (tidak ada tombol "Simpan" terpisah) --}}
                        <div class="flex justify-end gap-2 pt-2 border-t">
                            @if($laporan->report_status === 'baru')
                                <button type="submit" name="report_status" value="ditolak"
                                        class="px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-md hover:bg-red-700 transition">
                                    Tolak
                                </button>
                                <button type="submit" name="report_status" value="diproses"
                                        class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700 transition">
                                    Proses
                                </button>
                            @elseif($laporan->report_status === 'diproses')
                                <button type="submit" name="report_status" value="selesai"
                                        class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700 transition">
                                    Selesai
                                </button>
                            @endif
                        </div>
                    </form>

                    <script>
                        document.getElementById('statusForm').addEventListener('submit', function (e) {
                            var submitter = e.submitter;
                            var action = submitter ? submitter.value : null;
                            var notesEl = document.getElementById('resolutionNotes');
                            var notes = notesEl.value.trim();

                            // Catatan wajib saat laporan ditutup: aksi "Tolak" atau "Selesai".
                            if ((action === 'ditolak' || action === 'selesai') && notes === '') {
                                e.preventDefault();
                                alert('Catatan resolusi wajib diisi saat laporan ditutup (selesai atau ditolak).');
                                notesEl.focus();
                                return;
                            }

                            var confirmMsgs = {
                                ditolak: 'Tolak laporan ini? Catatan akan dikirim ke pelapor.',
                                diproses: 'Proses laporan ini?',
                                selesai: 'Tandai laporan selesai? Bila fasilitas sedang ditutup karena laporan ini, fasilitas akan kembali aktif.'
                            };

                            var tutup = document.getElementById('menutupFasilitas');
                            if (action === 'diproses' && tutup && tutup.checked) {
                                confirmMsgs.diproses = 'Proses laporan ini dan TUTUP fasilitas? Reservasi disetujui yang belum berlangsung akan dibatalkan dan reservasi menunggu akan ditolak otomatis.';
                            }
                            if (confirmMsgs[action] && !confirm(confirmMsgs[action])) {
                                e.preventDefault();
                            }
                        });
                    </script>
                </div>
            </div>
        @endif

        <div class="flex justify-end">
            <a href="{{ route('petugas.reports.index') }}" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50 bg-white">
                Kembali ke Daftar Laporan
            </a>
        </div>
    </div>
</x-app-layout>
