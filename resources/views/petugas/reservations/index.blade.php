<x-petugas-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-extrabold text-lg leading-tight" style="color:#252B2B;">Antrian & Riwayat Reservasi</h2>
                <p class="text-xs mt-0.5" style="color:#4C4F54;">Verifikasi, persetujuan, dan pengelolaan jadwal peminjaman fasilitas</p>
            </div>
            <div class="hidden sm:flex items-center gap-2">
                <a href="{{ route('petugas.dashboard') }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition shadow-xs hover:brightness-110"
                   style="background:#8F0B13; color:#EFDFC5;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="px-4 sm:px-6 py-5 space-y-4" style="background:#FAF6F0;">

        @error('alasan_ditolak')
            <div class="p-3.5 rounded-xl text-xs sm:text-sm font-semibold flex items-center gap-2.5 shadow-xs"
                 style="background:#FEE2E2; color:#991B1B; border:1px solid #FECACA;">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <span>{{ $message }}</span>
            </div>
        @enderror

        @error('cancellation_reason')
            <div class="p-3.5 rounded-xl text-xs sm:text-sm font-semibold flex items-center gap-2.5 shadow-xs"
                 style="background:#FEE2E2; color:#991B1B; border:1px solid #FECACA;">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <span>{{ $message }}</span>
            </div>
        @enderror

        {{-- Pencarian --}}
        <x-search-bar :action="route('petugas.reservations.index')"
                      :hidden="['status' => $selectedStatus !== 'all' ? $selectedStatus : null]"
                      placeholder="Cari nama/surel pemohon, fasilitas, atau tujuan..." />

        {{-- Filter Tab Status --}}
        <div class="flex items-center gap-2 pb-1 overflow-x-auto">
            @php
                $statusTabs = [
                    'all'       => 'Semua',
                    'pending'   => 'Menunggu (Pending)',
                    'approved'  => 'Disetujui',
                    'rejected'  => 'Ditolak',
                    'cancelled' => 'Dibatalkan',
                ];
            @endphp
            @foreach($statusTabs as $key => $label)
                @php $isActive = $selectedStatus === $key; @endphp
                <a href="{{ route('petugas.reservations.index', array_filter(['status' => $key === 'all' ? null : $key, 'search' => request('search')], fn ($v) => filled($v))) }}"
                   class="px-3.5 py-2 text-xs sm:text-sm font-bold rounded-xl transition whitespace-nowrap shadow-xs"
                   style="{{ $isActive
                            ? 'background:#8F0B13; color:#EFDFC5; border:1px solid #8F0B13;'
                            : 'background:white; color:#4C4F54; border:1px solid #EAE0D3;' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Card Tabel Reservasi --}}
        <div class="bg-white rounded-xl shadow-xs overflow-hidden" style="border:1px solid #EAE0D3;">
            @if($reservations->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-12 h-12 rounded-full mx-auto flex items-center justify-center mb-3"
                         style="background:#FAF6F0; color:#4C4F54; border:1px solid #EAE0D3;">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-bold" style="color:#252B2B;">Tidak ada reservasi ditemukan</p>
                    <p class="text-xs mt-1" style="color:#4C4F54;">Belum ada riwayat atau antrian reservasi yang sesuai dengan filter ini.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs sm:text-sm">
                        <thead>
                            <tr class="font-bold border-b" style="background:#FAF6F0; color:#252B2B; border-color:#EAE0D3;">
                                <th class="py-3 px-4">Pemohon</th>
                                <th class="py-3 px-4">Fasilitas</th>
                                <th class="py-3 px-4">Tanggal & Waktu</th>
                                <th class="py-3 px-4">Tujuan</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Keterangan / Catatan</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y" style="border-color:#EAE0D3;">
                            @foreach($reservations as $res)
                                <tr class="hover:bg-[#FAF6F0]/60 transition">
                                    {{-- Pemohon --}}
                                    <td class="py-3 px-4 font-bold" style="color:#252B2B;">
                                        {{ $res->user->name ?? '-' }}
                                    </td>

                                    {{-- Fasilitas --}}
                                    <td class="py-3 px-4">
                                        <div class="font-bold" style="color:#252B2B;">{{ $res->facility->facility_name ?? '-' }}</div>
                                        <div class="text-[11px]" style="color:#4C4F54;">{{ $res->facility->type ?? '' }}</div>
                                    </td>

                                    {{-- Tanggal & Waktu --}}
                                    <td class="py-3 px-4">
                                        <div class="font-semibold" style="color:#252B2B;">{{ $res->date->format('d M Y') }}</div>
                                        <div class="text-[11px] font-mono mt-0.5" style="color:#4C4F54;">
                                            {{ substr($res->start_time, 0, 5) }} - {{ substr($res->end_time, 0, 5) }} WIB
                                        </div>
                                    </td>

                                    {{-- Tujuan --}}
                                    <td class="py-3 px-4 max-w-xs truncate" style="color:#4C4F54;" title="{{ $res->purpose }}">
                                        {{ $res->purpose }}
                                    </td>

                                    {{-- Status Badge --}}
                                    <td class="py-3 px-4">
                                        <x-status-badge :status="$res->reservation_status" />
                                    </td>

                                    {{-- Keterangan / Alasan --}}
                                    <td class="py-3 px-4 text-xs max-w-xs" style="color:#4C4F54;">
                                        @if($res->reservation_status === 'rejected' && $res->alasan_ditolak)
                                            <span class="font-bold text-red-700">Ditolak:</span> {{ $res->alasan_ditolak }}
                                        @elseif($res->reservation_status === 'cancelled' && $res->cancellation_reason)
                                            <span class="font-bold text-gray-700">Dibatalkan:</span> {{ $res->cancellation_reason }}
                                        @else
                                            <span style="color:#9CA3AF;">-</span>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="py-3 px-4">
                                        <div class="flex flex-wrap justify-center items-center gap-1.5">
                                            <a href="{{ route('petugas.reservations.show', $res->id_reservasi) }}"
                                               x-data x-on:click.prevent="$dispatch('buka-ajax', { name: 'detail-reservasi-petugas', url: @js(route('petugas.reservations.show', $res->id_reservasi)) })"
                                               class="inline-flex items-center gap-1 px-3 py-1 text-xs font-bold rounded-lg transition shadow-xs hover:brightness-110"
                                               style="background:#8F0B13; color:#EFDFC5;">
                                                Detail
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4" style="border-top:1px solid #EAE0D3; background:#FAF6F0/40;">
                    {{ $reservations->links() }}
                </div>
            @endif
        </div>
    </div>

    <x-modal-detail name="detail-reservasi-petugas" title="Detail Reservasi"
                    subtitle="Periksa data pemohon dan jadwal sebelum menyetujui atau menolak" max-width="3xl" />

    @include('petugas.reservations.partials.modal-aksi')
</x-petugas-layout>