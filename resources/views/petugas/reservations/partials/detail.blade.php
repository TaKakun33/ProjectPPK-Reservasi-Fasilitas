{{-- Isi detail reservasi untuk petugas (pemohon, fasilitas, jadwal, riwayat status, aksi).
     Dipakai halaman penuh (petugas.reservations.show) dan pop-up (AJAX, $modal = true). --}}
@php
    $modal = $modal ?? false;
    $f = $reservation->facility;
    $statusLabel = ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'cancelled' => 'Dibatalkan'];
@endphp
<div class="space-y-5">
    <div class="bg-white rounded-xl shadow-xs overflow-hidden" style="border:1px solid #EAE0D3;">
        <div class="p-5 sm:p-6 space-y-5">
            {{-- Fasilitas + status --}}
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4 pb-4" style="border-bottom:1px solid #EAE0D3;">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider" style="color:#7C7F84;">Fasilitas Kampus</span>
                    <h3 class="text-lg font-black mt-0.5" style="color:#252B2B;">{{ $f->facility_name ?? '-' }}</h3>
                    <p class="text-xs mt-0.5" style="color:#4C4F54;">
                        {{ $f->type ?? '' }} &bull; {{ $f->location ?? '' }}
                        @if(!empty($f?->capacity)) &bull; Kapasitas {{ $f->capacity }} orang @endif
                    </p>
                </div>
                <div class="flex items-center shrink-0">
                    <span class="text-xs font-semibold mr-1" style="color:#4C4F54;">Status:</span>
                    <x-status-badge :status="$reservation->reservation_status" />
                </div>
            </div>

            {{-- Peringatan untuk reservasi pending --}}
            @if($bentrok)
                <div class="p-3.5 rounded-xl text-xs sm:text-sm font-semibold" style="background:#FEE2E2; color:#991B1B; border:1px solid #FECACA;">
                    Jadwal ini bentrok dengan reservasi lain yang sudah disetujui, sehingga tidak dapat disetujui.
                </div>
            @endif
            @if($reservation->reservation_status === 'pending' && $f && ! $f->isReservable())
                <div class="p-3.5 rounded-xl text-xs sm:text-sm font-semibold" style="background:#FEF3C7; color:#92400E; border:1px solid #FDE68A;">
                    Fasilitas sedang nonaktif atau dalam perbaikan, sehingga reservasi ini tidak dapat disetujui.
                </div>
            @endif

            {{-- Pemohon + jadwal --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="p-3 rounded-xl" style="background:#FAF6F0; border:1px solid #EAE0D3;">
                    <p class="text-[11px] font-semibold" style="color:#7C7F84;">Pemohon</p>
                    <p class="font-bold text-xs sm:text-sm mt-0.5" style="color:#252B2B;">{{ $reservation->user->name ?? '-' }}</p>
                    <p class="text-[11px] mt-0.5 break-all" style="color:#4C4F54;">{{ $reservation->user->email ?? '' }}</p>
                </div>
                <div class="p-3 rounded-xl" style="background:#FAF6F0; border:1px solid #EAE0D3;">
                    <p class="text-[11px] font-semibold" style="color:#7C7F84;">Diajukan pada</p>
                    <p class="font-bold text-xs sm:text-sm mt-0.5" style="color:#252B2B;">{{ $reservation->created_at->format('d M Y, H:i') }} WIB</p>
                </div>
                <div class="p-3 rounded-xl" style="background:#FAF6F0; border:1px solid #EAE0D3;">
                    <p class="text-[11px] font-semibold" style="color:#7C7F84;">Tanggal Penggunaan</p>
                    <p class="font-bold text-xs sm:text-sm mt-0.5" style="color:#252B2B;">{{ $reservation->date->format('d M Y') }}</p>
                </div>
                <div class="p-3 rounded-xl" style="background:#FAF6F0; border:1px solid #EAE0D3;">
                    <p class="text-[11px] font-semibold" style="color:#7C7F84;">Waktu</p>
                    <p class="font-bold text-xs sm:text-sm mt-0.5 font-mono" style="color:#252B2B;">{{ substr($reservation->start_time, 0, 5) }} - {{ substr($reservation->end_time, 0, 5) }} WIB</p>
                </div>
                @if($reservation->processedBy)
                    <div class="p-3 rounded-xl sm:col-span-2" style="background:#FAF6F0; border:1px solid #EAE0D3;">
                        <p class="text-[11px] font-semibold" style="color:#7C7F84;">Diproses oleh</p>
                        <p class="font-bold text-xs sm:text-sm mt-0.5" style="color:#252B2B;">{{ $reservation->processedBy->name }}</p>
                    </div>
                @endif
            </div>

            {{-- Tujuan --}}
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider mb-1.5" style="color:#7C7F84;">Tujuan Penggunaan</h4>
                <div class="p-4 rounded-xl text-xs sm:text-sm leading-relaxed" style="background:#FAF6F0; border:1px solid #EAE0D3; color:#252B2B;">
                    {{ $reservation->purpose }}
                </div>
            </div>

            {{-- Riwayat pemohon --}}
            @if($riwayatPemohon->isNotEmpty())
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider mb-1.5" style="color:#7C7F84;">Reservasi Lain dari Pemohon Ini</h4>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($riwayatPemohon as $st => $jumlah)
                            <span class="px-2.5 py-0.5 text-[11px] font-semibold rounded-full" style="background:#FAF6F0; color:#4C4F54; border:1px solid #EAE0D3;">
                                {{ $statusLabel[$st] ?? ucfirst($st) }}: {{ $jumlah }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Alasan ditolak / dibatalkan --}}
            @if($reservation->reservation_status === 'rejected' && $reservation->alasan_ditolak)
                <div class="p-4 rounded-xl" style="background:#FEE2E2; border:1px solid #FECACA;">
                    <p class="text-xs font-bold" style="color:#991B1B;">Alasan Ditolak</p>
                    <p class="mt-1 text-xs sm:text-sm" style="color:#7F1D1D;">{{ $reservation->alasan_ditolak }}</p>
                </div>
            @endif
            @if($reservation->reservation_status === 'cancelled' && $reservation->cancellation_reason)
                <div class="p-4 rounded-xl" style="background:#F3F4F6; border:1px solid #D1D5DB;">
                    <p class="text-xs font-bold" style="color:#374151;">Alasan Pembatalan</p>
                    <p class="mt-1 text-xs sm:text-sm" style="color:#374151;">{{ $reservation->cancellation_reason }}</p>
                </div>
            @endif

            {{-- Riwayat status --}}
            @if($reservation->logs->isNotEmpty())
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider mb-2" style="color:#7C7F84;">Riwayat Status</h4>
                    <ol class="space-y-3 pl-4" style="border-left:2px solid #EAE0D3;">
                        @foreach($reservation->logs as $log)
                            <li class="relative">
                                <span class="absolute -left-[21px] top-1 w-2.5 h-2.5 rounded-full" style="background:#8F0B13;"></span>
                                <p class="text-xs sm:text-sm font-semibold" style="color:#252B2B;">
                                    {{ $log->status_before ? ($statusLabel[$log->status_before] ?? ucfirst($log->status_before)) . ' → ' : '' }}{{ $statusLabel[$log->status_after] ?? ucfirst($log->status_after) }}
                                </p>
                                <p class="text-[11px]" style="color:#7C7F84;">
                                    {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y H:i') }}
                                    @if($log->changedBy) &middot; oleh {{ $log->changedBy->name }} @endif
                                </p>
                                @if($log->notes)
                                    <p class="text-xs mt-0.5" style="color:#4C4F54;">{{ $log->notes }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </div>
            @endif

            {{-- Aksi --}}
            <div class="flex flex-wrap justify-between items-center gap-2 pt-4" style="border-top:1px solid #EAE0D3;">
                @if($modal)
                    <button type="button" x-on:click="$dispatch('close-modal', 'detail-reservasi-petugas')"
                            class="px-4 py-2 text-xs font-bold rounded-xl transition hover:bg-[#FAF6F0]"
                            style="background:white; color:#4C4F54; border:1px solid #EAE0D3;">
                        Tutup
                    </button>
                @else
                    <a href="{{ route('petugas.reservations.index') }}"
                       class="px-4 py-2 text-xs font-bold rounded-xl transition hover:bg-[#FAF6F0]"
                       style="background:white; color:#4C4F54; border:1px solid #EAE0D3;">
                        &larr; Kembali ke Daftar
                    </a>
                @endif

                <div class="flex items-center gap-2">
                    @if($reservation->reservation_status === 'pending')
                        <form method="POST" action="{{ route('petugas.reservations.approve', $reservation->id_reservasi) }}"
                              data-confirm-type="success" data-confirm-title="Setujui Reservasi?" data-confirm-ok="Ya, Setujui" data-confirm="Setujui reservasi ini?">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-4 py-2 text-xs font-bold text-white rounded-xl transition shadow-xs hover:brightness-110" style="background:#059669;">
                                Setujui
                            </button>
                        </form>
                        <button type="button" onclick="openRejectModal('{{ route('petugas.reservations.reject', $reservation->id_reservasi) }}')"
                                class="px-4 py-2 text-xs font-bold text-white rounded-xl transition shadow-xs hover:brightness-110" style="background:#8F0B13;">
                            Tolak
                        </button>
                    @elseif($reservation->reservation_status === 'approved' && ! $reservation->sudahSelesai())
                        <button type="button" onclick="openCancelModal('{{ route('petugas.reservations.cancel', $reservation->id_reservasi) }}')"
                                class="px-4 py-2 text-xs font-bold rounded-xl transition shadow-xs hover:bg-gray-100"
                                style="background:white; color:#374151; border:1px solid #D1D5DB;">
                            Batalkan Reservasi
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
