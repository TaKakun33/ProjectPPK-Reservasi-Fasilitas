{{-- Isi detail laporan untuk petugas (info laporan, reservasi terdampak, form tindak lanjut).
     Dipakai halaman penuh (petugas.reports.show) dan pop-up (AJAX, $modal = true). --}}
@php $modal = $modal ?? false; @endphp
<div class="space-y-5">
    {{-- Card Info Utama Laporan --}}
    <div class="bg-white rounded-xl shadow-xs overflow-hidden" style="border:1px solid #EAE0D3;">
        <div class="p-5 sm:p-6 space-y-5">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4 pb-4" style="border-bottom:1px solid #EAE0D3;">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider" style="color:#7C7F84;">Fasilitas Kampus</span>
                    <h3 class="text-lg font-black mt-0.5" style="color:#252B2B;">{{ $laporan->facility->facility_name ?? '-' }}</h3>
                    <p class="text-xs mt-0.5" style="color:#4C4F54;">{{ $laporan->facility->type ?? '' }} &bull; {{ $laporan->facility->location ?? '' }}</p>
                </div>
                <div class="flex items-center shrink-0">
                    <span class="text-xs font-semibold text-charcoal-medium mr-1">Status:</span>
                    <x-status-badge :status="$laporan->report_status" />
                </div>
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
                    <x-galeri-lightbox :urls="$laporan->photos->map(fn($f) => route('reports.photo', $f->id_foto))->values()"
                                       grid-class="grid grid-cols-2 sm:grid-cols-4 gap-3"
                                       btn-class="group rounded-xl overflow-hidden focus:outline-none transition shadow-xs hover:shadow-md"
                                       btn-style="border:1px solid #EAE0D3;"
                                       img-class="w-full h-28 object-cover group-hover:scale-105 transition duration-200 cursor-zoom-in" />
                </div>
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

                <form method="POST" action="{{ route('petugas.reports.update-status', $laporan->id_laporan) }}" class="space-y-4"
                      x-data="{
                          // Validasi catatan + konfirmasi sebelum status diubah (menggantikan skrip lama agar jalan di dalam pop-up)
                          lolos: false,
                          async konfirmasi(e) {
                              // Pengiriman ulang setelah konfirmasi di pop-up disetujui
                              if (this.lolos) { this.lolos = false; return; }
                              e.preventDefault();
                              const pengirim = e.submitter;
                              const aksi = pengirim ? pengirim.value : null;
                              const catatan = this.$refs.catatan.value.trim();
                              if ((aksi === 'ditolak' || aksi === 'selesai') && catatan === '') {
                                  await window.Popup.alert({
                                      type: 'warning',
                                      title: 'Catatan Wajib Diisi',
                                      message: 'Catatan untuk pelapor wajib diisi saat laporan ditolak atau diselesaikan.'
                                  });
                                  this.$refs.catatan.focus();
                                  return;
                              }
                              const pesan = {
                                  ditolak:  { type: 'danger',  title: 'Tolak Laporan?', ok: 'Ya, Tolak', text: 'Tolak laporan ini? Catatan akan dikirimkan ke pelapor.' },
                                  diproses: { type: 'warning', title: 'Proses Laporan?', ok: 'Ya, Proses', text: 'Proses laporan kerusakan ini sekarang?' },
                                  selesai:  { type: 'success', title: 'Tandai Selesai?', ok: 'Ya, Selesai', text: 'Tandai laporan selesai? Bila fasilitas sebelumnya ditutup, fasilitas akan diaktifkan kembali jika tidak ada laporan lain yang aktif.' }
                              };
                              if (aksi === 'diproses' && this.$refs.tutup && this.$refs.tutup.checked) {
                                  pesan.diproses = { type: 'danger', title: 'Tutup Fasilitas?', ok: 'Ya, Proses dan Tutup', text: 'Proses laporan ini dan TUTUP FASILITAS? Reservasi mendatang akan dibatalkan otomatis.' };
                              }
                              const p = pesan[aksi];
                              if (p) {
                                  const ya = await window.Popup.confirm({ type: p.type, title: p.title, okText: p.ok, message: p.text });
                                  if (!ya) return;
                              }
                              this.lolos = true;
                              this.$el.requestSubmit(pengirim);
                          }
                      }"
                      x-on:submit="konfirmasi($event)">
                    @csrf
                    @method('PATCH')
                    @if($modal)
                        {{-- Penanda: setelah disimpan, kembali ke daftar (bukan halaman detail penuh) --}}
                        <input type="hidden" name="_modal" value="1">
                    @endif

                    <div>
                        <label class="block text-xs font-bold mb-1" style="color:#252B2B;">
                            Catatan untuk Pelapor
                            <span class="text-[11px] font-normal" style="color:#7C7F84;">(Wajib diisi saat menolak atau menyelesaikan laporan)</span>
                        </label>
                        <textarea name="resolution_notes" id="resolutionNotes" x-ref="catatan" rows="3" maxlength="1000"
                                  placeholder="Tuliskan keterangan tindakan perbaikan atau alasan penolakan..."
                                  class="w-full rounded-xl text-xs sm:text-sm p-3 transition focus:outline-none"
                                  style="border:1px solid #EAE0D3; background:#FAF6F0;">{{ old('resolution_notes') }}</textarea>
                    </div>

                    @if($laporan->report_status === 'baru')
                        <div class="p-3.5 rounded-xl" style="background:#FAF6F0; border:1px solid #EAE0D3;">
                            <label class="flex items-start gap-2.5 text-xs sm:text-sm cursor-pointer" style="color:#252B2B;">
                                <input type="checkbox" name="menutup_fasilitas" value="1" id="menutupFasilitas" x-ref="tutup"
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

                    <div class="mb-actions flex justify-end gap-2.5 pt-3" style="border-top:1px solid #EAE0D3;">
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
            </div>
        </div>
    @endif
</div>
