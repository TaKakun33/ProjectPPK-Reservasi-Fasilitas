{{-- Isi detail reservasi. Dipakai halaman penuh (reservations.show) dan pop-up (AJAX, $modal = true). --}}
@php $modal = $modal ?? false; @endphp
<div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200/70 border-t-4 border-t-maroon-800">
    <div class="mp-pad p-6 space-y-5">
        <div class="flex justify-between items-start gap-4">
            <div>
                <p class="text-sm text-gray-500">Fasilitas</p>
                <p class="font-semibold text-gray-900">{{ $reservation->facility->facility_name ?? '-' }}</p>
                <p class="text-sm text-gray-500">{{ $reservation->facility->type ?? '' }} - {{ $reservation->facility->location ?? '' }}</p>
            </div>
            <x-status-badge :status="$reservation->reservation_status" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Tanggal</p>
                <p class="font-medium text-gray-800">{{ $reservation->date->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Waktu</p>
                <p class="font-medium text-gray-800">{{ substr($reservation->start_time, 0, 5) }} - {{ substr($reservation->end_time, 0, 5) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Diajukan pada</p>
                <p class="font-medium text-gray-800">{{ $reservation->created_at->format('d M Y H:i') }}</p>
            </div>
            @if($reservation->processedBy)
                <div>
                    <p class="text-sm text-gray-500">Diproses oleh</p>
                    <p class="font-medium text-gray-800">{{ $reservation->processedBy->name }}</p>
                </div>
            @endif
        </div>

        <div>
            <p class="text-sm text-gray-500">Tujuan Penggunaan</p>
            <p class="text-gray-800">{{ $reservation->purpose }}</p>
        </div>

        {{-- US #9: alasan penolakan petugas, biar user tahu kenapa ditolak --}}
        @if($reservation->reservation_status === 'rejected' && $reservation->alasan_ditolak)
            <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded">
                <p class="text-sm font-semibold text-red-800">Alasan Ditolak</p>
                <p class="mt-1 text-sm text-red-900">{{ $reservation->alasan_ditolak }}</p>
            </div>
        @endif

        {{-- Alasan pembatalan, baik dibatalkan sendiri maupun oleh petugas --}}
        @if($reservation->reservation_status === 'cancelled' && $reservation->cancellation_reason)
            <div class="p-4 bg-gray-50 border-l-4 border-gray-400 rounded">
                <p class="text-sm font-semibold text-gray-700">Alasan Pembatalan</p>
                <p class="mt-1 text-sm text-gray-700">{{ $reservation->cancellation_reason }}</p>
            </div>
        @endif

        @if($reservation->logs->isNotEmpty())
            <div>
                <p class="text-sm text-gray-500 mb-2">Riwayat Status</p>
                <ol class="space-y-3 border-l-2 border-gray-200 pl-4">
                    @foreach($reservation->logs as $log)
                        <li class="relative">
                            <span class="absolute -left-[21px] top-1 w-2.5 h-2.5 rounded-full bg-maroon-700"></span>
                            <p class="text-sm font-medium text-gray-800">
                                {{ $log->status_before ? ucfirst($log->status_before) . ' -> ' : '' }}{{ ucfirst($log->status_after) }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y H:i') }}
                                @if($log->changedBy) &middot; oleh {{ $log->changedBy->name }} @endif
                            </p>
                            @if($log->notes)
                                <p class="text-xs text-gray-600 mt-0.5">{{ $log->notes }}</p>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </div>
        @endif

        <style>
            .rd-actions{display:flex;flex-direction:column;gap:.6rem;padding-top:1rem;border-top:1px solid #EAE0D3}
            .rd-btn{display:inline-flex;align-items:center;justify-content:center;gap:.45rem;width:100%;min-height:2.75rem;padding:0 1.1rem;border-radius:.75rem;font:inherit;font-size:.875rem;font-weight:700;text-decoration:none;cursor:pointer;transition:background .15s,border-color .15s,color .15s}
            .rd-btn svg{width:1.05rem;height:1.05rem;flex-shrink:0}
            .rd-btn-close{background:#fff;color:#5A121D;border:1px solid #D9CBB4}
            .rd-btn-close:hover{background:#FAF6F0}
            .rd-btn-cancel{background:#FEF2F2;color:#B91C1C;border:1px solid #FECACA}
            .rd-btn-cancel:hover{background:#B91C1C;border-color:#B91C1C;color:#fff}
            .rd-note{display:flex;gap:.5rem;align-items:flex-start;padding:.65rem .8rem;border-radius:.75rem;background:#F5F5F4;border:1px solid #E7E5E4;color:#57534E;font-size:.78rem;line-height:1.4}
            .rd-note svg{width:1rem;height:1rem;flex-shrink:0;margin-top:.1rem}
            /* Tombol batal di atas, tombol tutup di bawah (mobile); berdampingan di layar lebar */
            .rd-actions form{display:contents}
            @media (min-width:640px){
                .rd-actions{flex-direction:row;justify-content:space-between;align-items:center}
                .rd-btn-close{order:-1}
                .rd-btn{width:auto}
                .rd-note{flex:1}
            }
        </style>
        <div class="rd-actions">
            @if(in_array($reservation->reservation_status, ['pending', 'approved']))
                @php
                    $canCancel = \Carbon\Carbon::today()->lt(\Carbon\Carbon::parse($reservation->date)->startOfDay());
                @endphp
                @if($canCancel)
                    <form method="POST" action="{{ route('reservations.destroy', $reservation->id_reservasi) }}"
                          data-confirm-type="danger" data-confirm-title="Batalkan Reservasi?" data-confirm-ok="Ya, Batalkan" data-confirm="Apakah Anda yakin ingin membatalkan reservasi ini?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rd-btn rd-btn-cancel">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
                            Batalkan Reservasi
                        </button>
                    </form>
                @else
                    <p class="rd-note">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                        <span>Batas pembatalan mandiri sudah berakhir (maks. H-1 pukul 23:59 WIB).</span>
                    </p>
                @endif
            @endif

            @if($modal)
                <button type="button" x-on:click="$dispatch('close-modal', 'detail-reservasi')" class="rd-btn rd-btn-close">Tutup</button>
            @else
                <a href="{{ route('reservations.index') }}" class="rd-btn rd-btn-close">Kembali ke Riwayat</a>
            @endif
        </div>
    </div>
</div>
