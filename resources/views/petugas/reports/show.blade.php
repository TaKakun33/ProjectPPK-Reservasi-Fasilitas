<x-petugas-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-extrabold text-lg leading-tight" style="color:#252B2B;">Detail Laporan Kerusakan</h2>
                <p class="text-xs mt-0.5" style="color:#4C4F54;">Investigasi bukti fisik, tentukan status penanganan, dan perbarui fasilitas</p>
            </div>
            <a href="{{ route('petugas.reports.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-xs hover:bg-[#FAF6F0]"
               style="background:white; color:#380F17; border:1px solid #EAE0D3;">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="px-4 sm:px-6 py-5 max-w-4xl mx-auto space-y-5" style="background:#FAF6F0;">

        @if(session('success'))
            <div class="p-3.5 rounded-xl text-xs sm:text-sm font-semibold flex items-center gap-2.5 shadow-xs"
                 style="background:#D1FAE5; color:#065F46; border:1px solid #A7F3D0;">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('warning'))
            <div class="p-3.5 rounded-xl text-xs sm:text-sm font-semibold flex items-center gap-2.5 shadow-xs"
                 style="background:#FEF3C7; color:#92400E; border:1px solid #FDE68A;">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-3.5 rounded-xl text-xs sm:text-sm font-semibold shadow-xs"
                 style="background:#FEE2E2; color:#991B1B; border:1px solid #FECACA;">
                <p class="font-bold mb-1">Terdapat kesalahan pengisian:</p>
                <ul class="list-disc pl-5 space-y-0.5 font-normal">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Card Info Utama Laporan --}}
        <div class="bg-white rounded-xl shadow-xs overflow-hidden" style="border:1px solid #EAE0D3;">
            <div class="p-5 sm:p-6 space-y-5">
                <div class="flex flex-col sm:flex-row justify-between items-start gap-4 pb-4" style="border-bottom:1px solid #EAE0D3;">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider" style="color:#7C7F84;">Fasilitas Kampus</span>
                        <h3 class="text-lg font-black mt-0.5" style="color:#252B2B;">{{ $laporan->facility->facility_name ?? '-' }}</h3>
                        <p class="text-xs mt-0.5" style="color:#4C4F54;">{{ $laporan->facility->type ?? '' }} &bull; {{ $laporan->facility->location ?? '' }}</p>
                    </div>
                    @php
                        $badges = [
                            'baru'     => 'background:#FEE2E2; color:#991B1B; border:1px solid #FECACA;',
                            'diproses' => 'background:#FEF3C7; color:#92400E; border:1px solid #FDE68A;',
                            'selesai'  => 'background:#D1FAE5; color:#065F46; border:1px solid #A7F3D0;',
                            'ditolak'  => 'background:#F3F4F6; color:#374151; border:1px solid #E5E7EB;',
                        ];
                    @endphp
                    <span class="px-3 py-1 text-xs font-bold rounded-full shadow-xs"
                          style="{{ $badges[$laporan->report_status] ?? 'background:#F3F4F6; color:#374151;' }}">
                        Status: {{ ucfirst($laporan->report_status) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-3 rounded-xl" style="background:#FAF6F0; border:1px solid #EAE0D3;">
                        <p class="text-[11px] font-semibold" style="color:#7C7F84;">Pelapor</p>
                        <p class="font-bold text-xs sm:text-sm mt-0.5" style="color:#252B2B;">{{ $laporan->user->name ?? '-' }}</p>
                    </div>
                    <div class="p-3 rounded-xl" style="background:#FAF6F0; border:1px solid #EAE0D3;">
                        <p class="text-[11px] font-semibold" style="color:#7C7F84;">Kategori Kerusakan</p>
                        <p class="font-bold text-xs sm:text-sm mt-0.5" style="color:#252B2B;">{{ $laporan->category->category_name ?? '-' }}</p>
                    </div>
                    <div class="p-3 rounded-xl" style="background:#FAF6F0; border:1px solid #EAE0D3;">
                        <p class="text-[11px] font-semibold" style="color:#7C7F84;">Tanggal Dilaporkan</p>
                        <p class="font-bold text-xs sm:text-sm mt-0.5" style="color:#252B2B;">{{ $laporan->created_at->format('d M Y, H:i') }} WIB</p>
                    </div>
                </div>

                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider mb-1.5" style="color:#7C7F84;">Deskripsi Kerusakan</h4>
                    <div class="p-4 rounded-xl text-xs sm:text-sm leading-relaxed" style="background:#FAF6F0; border:1px solid #EAE0D3; color:#252B2B;">
                        {{ $laporan->description }}
                    </div>
                </div>

                {{-- Galeri Foto Kerusakan --}}
                @if($laporan->photos->isNotEmpty())
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider mb-2" style="color:#7C7F84;">
                            Lampiran Foto Kerusakan ({{ $laporan->photos->count() }})
                        </h4>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach($laporan->photos as $i => $foto)
                                <button type="button"
                                        onclick="bukaLightbox({{ $i }})"
                                        class="group rounded-xl overflow-hidden focus:outline-none transition shadow-xs hover:shadow-md"
                                        style="border:1px solid #EAE0D3;">
                                    <img src="{{ route('reports.photo', $foto->id_foto) }}" alt="Foto kerusakan"
                                         class="w-full h-28 object-cover group-hover:scale-105 transition duration-200 cursor-zoom-in">
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Lightbox Modal --}}
                    <div id="lightbox" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/85 backdrop-blur-sm" onclick="tutupLightboxJikaBg(event)">
                        <div class="relative max-w-4xl w-full mx-4 flex flex-col items-center">
                            <button onclick="tutupLightbox()" class="absolute -top-10 right-0 text-white hover:text-red-400 transition text-4xl font-bold leading-none" title="Tutup (Esc)">&times;</button>
                            <img id="lightbox-img" src="" alt="Foto kerusakan" class="max-h-[80vh] max-w-full rounded-2xl shadow-2xl object-contain">
                            <div class="flex items-center gap-6 mt-4">
                                <button onclick="gantiFoto(-1)" id="lb-prev" class="text-white bg-white/20 hover:bg-white/40 rounded-full p-2 transition disabled:opacity-30" title="Sebelumnya">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <span id="lb-counter" class="text-white text-xs sm:text-sm font-bold"></span>
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
                    <p class="text-xs italic" style="color:#9CA3AF;">Tidak ada foto dilampirkan oleh pelapor.</p>
                @endif

                @if($laporan->resolution_notes)
                    <div class="p-4 rounded-xl shadow-xs" style="background:#FAF6F0; border-left:4px solid #8F0B13; border-top:1px solid #EAE0D3; border-right:1px solid #EAE0D3; border-bottom:1px solid #EAE0D3;">
                        <p class="text-xs font-bold" style="color:#8F0B13;">Catatan untuk Pelapor (terakhir)</p>
                        <p class="mt-1 text-xs sm:text-sm" style="color:#252B2B;">{{ $laporan->resolution_notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Indikasi laporan ganda --}}
        @if(($laporanSerupa ?? 0) > 0)
            <div class="p-4 rounded-xl text-xs sm:text-sm font-medium shadow-xs"
                 style="background:#FEF3C7; color:#92400E; border:1px solid #FDE68A;">
                <span class="font-bold">Perhatian:</span> Terdapat <span class="font-bold underline">{{ $laporanSerupa }}</span> laporan lain yang masih terbuka untuk fasilitas dan kategori yang sama. Mohon tinjau apakah ini merupakan laporan kerusakan ganda.
            </div>
        @endif

        {{-- Reservasi terdampak bila fasilitas masuk perbaikan --}}
        @if(isset($reservasiTerdampak) && $reservasiTerdampak->isNotEmpty())
            <div class="bg-white rounded-xl shadow-xs overflow-hidden" style="border:1px solid #EAE0D3;">
                <div class="p-5 space-y-3">
                    <h3 class="font-bold text-sm" style="color:#252B2B;">Daftar Reservasi Disetujui di Fasilitas Ini</h3>
                    <p class="text-xs leading-relaxed" style="color:#4C4F54;">
                        Bila Anda memilih opsi <span class="font-semibold text-red-700">"Tutup fasilitas untuk perbaikan"</span>, seluruh jadwal reservasi disetujui di bawah ini akan dibatalkan otomatis dan pengguna akan menerima pemberitahuan.
                    </p>
                    <ul class="divide-y text-xs" style="border-color:#EAE0D3;">
                        @foreach($reservasiTerdampak as $res)
                            <li class="py-2.5 flex justify-between items-center gap-4">
                                <span class="font-bold" style="color:#252B2B;">{{ $res->user->name ?? '-' }}</span>
                                <span class="font-mono text-[11px]" style="color:#4C4F54;">
                                    {{ $res->date->format('d M Y') }}, {{ substr($res->start_time, 0, 5) }} - {{ substr($res->end_time, 0, 5) }} WIB
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Form Aksi Petugas --}}
        @if(in_array($laporan->report_status, ['baru', 'diproses']))
            <div class="bg-white rounded-xl shadow-xs overflow-hidden" style="border:1px solid #EAE0D3;">
                <div class="p-5 sm:p-6 space-y-4">
                    <h3 class="font-bold text-sm sm:text-base" style="color:#252B2B;">Form Tindak Lanjut Petugas</h3>

                    <form method="POST" action="{{ route('petugas.reports.update-status', $laporan->id_laporan) }}" id="statusForm" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block text-xs font-bold mb-1" style="color:#252B2B;">
                                Catatan untuk Pelapor
                                <span class="text-[11px] font-normal" style="color:#7C7F84;">(Wajib diisi saat menolak atau menyelesaikan laporan)</span>
                            </label>
                            <textarea name="resolution_notes" id="resolutionNotes" rows="3"
                                      placeholder="Tuliskan keterangan tindakan perbaikan atau alasan penolakan..."
                                      class="w-full rounded-xl text-xs sm:text-sm p-3 transition focus:outline-none"
                                      style="border:1px solid #EAE0D3; background:#FAF6F0;">{{ old('resolution_notes') }}</textarea>
                        </div>

                        @if($laporan->report_status === 'baru')
                            <div class="p-3.5 rounded-xl" style="background:#FAF6F0; border:1px solid #EAE0D3;">
                                <label class="flex items-start gap-2.5 text-xs sm:text-sm cursor-pointer" style="color:#252B2B;">
                                    <input type="checkbox" name="menutup_fasilitas" value="1" id="menutupFasilitas"
                                           {{ old('menutup_fasilitas') ? 'checked' : '' }}
                                           class="mt-0.5 rounded text-[#8F0B13] focus:ring-[#8F0B13]">
                                    <span>
                                        <span class="font-bold text-red-800">Tutup fasilitas untuk perbaikan (Status: Dalam Perbaikan)</span><br>
                                        <span class="text-[11px]" style="color:#4C4F54;">
                                            Centang jika fasilitas tidak dapat digunakan sama sekali selama proses perbaikan. Reservasi disetujui mendatang akan dibatalkan otomatis.
                                        </span>
                                    </span>
                                </label>
                            </div>
                        @endif

                        <div class="flex justify-end gap-2.5 pt-3" style="border-top:1px solid #EAE0D3;">
                            @if($laporan->report_status === 'baru')
                                <button type="submit" name="report_status" value="ditolak"
                                        class="px-4 py-2 text-xs font-bold rounded-xl text-white transition shadow-xs hover:brightness-110"
                                        style="background:#DC2626;">
                                    Tolak Laporan
                                </button>
                                <button type="submit" name="report_status" value="diproses"
                                        class="px-4 py-2 text-xs font-bold rounded-xl transition shadow-xs hover:brightness-110"
                                        style="background:#8F0B13; color:#EFDFC5;">
                                    Mulai Proses Penanganan
                                </button>
                            @elseif($laporan->report_status === 'diproses')
                                <button type="submit" name="report_status" value="selesai"
                                        class="px-4 py-2 text-xs font-bold rounded-xl text-white transition shadow-xs hover:brightness-110"
                                        style="background:#059669;">
                                    Tandai Selesai Diperbaiki
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

                            if ((action === 'ditolak' || action === 'selesai') && notes === '') {
                                e.preventDefault();
                                alert('Catatan untuk pelapor wajib diisi saat laporan ditolak atau diselesaikan.');
                                notesEl.focus();
                                return;
                            }

                            var confirmMsgs = {
                                ditolak: 'Tolak laporan ini? Catatan akan dikirimkan ke pelapor.',
                                diproses: 'Proses laporan kerusakan ini sekarang?',
                                selesai: 'Tandai laporan selesai? Bila fasilitas sebelumnya ditutup, fasilitas akan diaktifkan kembali jika tidak ada laporan lain yang aktif.'
                            };

                            var tutup = document.getElementById('menutupFasilitas');
                            if (action === 'diproses' && tutup && tutup.checked) {
                                confirmMsgs.diproses = 'Proses laporan ini dan TUTUP FASILITAS? Reservasi mendatang akan dibatalkan otomatis.';
                            }
                            if (confirmMsgs[action] && !confirm(confirmMsgs[action])) {
                                e.preventDefault();
                            }
                        });
                    </script>
                </div>
            </div>
        @endif

    </div>
</x-petugas-layout>
