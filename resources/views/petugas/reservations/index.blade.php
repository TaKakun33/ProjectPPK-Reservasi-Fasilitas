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

        @if(session('success'))
            <div class="p-3.5 rounded-xl text-xs sm:text-sm font-semibold flex items-center gap-2.5 shadow-xs"
                 style="background:#D1FAE5; color:#065F46; border:1px solid #A7F3D0;">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-3.5 rounded-xl text-xs sm:text-sm font-semibold flex items-center gap-2.5 shadow-xs"
                 style="background:#FEE2E2; color:#991B1B; border:1px solid #FECACA;">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

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
                <a href="{{ $key === 'all' ? route('petugas.reservations.index') : route('petugas.reservations.index', ['status' => $key]) }}"
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
                                        @php
                                            $badges = [
                                                'pending'   => 'background:#FEF3C7; color:#92400E; border:1px solid #FDE68A;',
                                                'approved'  => 'background:#D1FAE5; color:#065F46; border:1px solid #A7F3D0;',
                                                'rejected'  => 'background:#FEE2E2; color:#991B1B; border:1px solid #FECACA;',
                                                'cancelled' => 'background:#F3F4F6; color:#374151; border:1px solid #E5E7EB;',
                                            ];
                                        @endphp
                                        <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-full inline-block"
                                              style="{{ $badges[$res->reservation_status] ?? 'background:#F3F4F6; color:#374151;' }}">
                                            {{ ucfirst($res->reservation_status) }}
                                        </span>
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
                                        <div class="flex justify-center items-center gap-1.5">
                                            @if($res->reservation_status === 'pending')
                                                <form method="POST"
                                                      action="{{ route('petugas.reservations.approve', $res->id_reservasi) }}"
                                                      onsubmit="return confirm('Setujui reservasi ini?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="px-2.5 py-1 text-xs font-bold text-white rounded-lg transition shadow-xs hover:brightness-110"
                                                            style="background:#059669;">
                                                        Setujui
                                                    </button>
                                                </form>

                                                <button type="button"
                                                        onclick="openRejectModal('{{ route('petugas.reservations.reject', $res->id_reservasi) }}')"
                                                        class="px-2.5 py-1 text-xs font-bold text-white rounded-lg transition shadow-xs hover:brightness-110"
                                                        style="background:#8F0B13;">
                                                    Tolak
                                                </button>
                                            @elseif($res->reservation_status === 'approved' && ! $res->sudahSelesai())
                                                <button type="button"
                                                        onclick="openCancelModal('{{ route('petugas.reservations.cancel', $res->id_reservasi) }}')"
                                                        class="px-2.5 py-1 text-xs font-bold rounded-lg transition shadow-xs hover:bg-gray-100"
                                                        style="background:white; color:#374151; border:1px solid #D1D5DB;">
                                                    Batalkan
                                                </button>
                                            @else
                                                <span class="text-xs font-medium" style="color:#9CA3AF;">Selesai</span>
                                            @endif
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

    {{-- Modal Alasan Penolakan --}}
    <div id="reject-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-xs px-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6" style="border:1px solid #EAE0D3;">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h3 class="text-base font-bold" style="color:#252B2B;">Tolak Reservasi</h3>
                    <p class="text-xs mt-0.5" style="color:#4C4F54;">
                        Berikan alasan penolakan agar pemohon memahami alasan keputusan ini.
                    </p>
                </div>
                <button type="button" onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="reject-form" method="POST" class="mt-4">
                @csrf
                @method('PATCH')

                <label for="alasan_ditolak" class="block text-xs font-bold mb-1" style="color:#252B2B;">
                    Alasan Penolakan <span class="text-red-600">*</span>
                </label>
                <textarea id="alasan_ditolak" name="alasan_ditolak" rows="4" required maxlength="500"
                          class="w-full rounded-xl text-xs sm:text-sm p-3 transition focus:outline-none"
                          style="border:1px solid #EAE0D3; background:#FAF6F0;"
                          placeholder="Contoh: Fasilitas sedang dalam pemeliharaan berkala pada jadwal yang diajukan."></textarea>

                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" onclick="closeRejectModal()"
                            class="px-4 py-2 text-xs font-bold rounded-xl transition"
                            style="background:#FAF6F0; color:#4C4F54; border:1px solid #EAE0D3;">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-xs font-bold text-white rounded-xl transition shadow-xs hover:brightness-110"
                            style="background:#8F0B13;">
                        Tolak Reservasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Alasan Pembatalan --}}
    <div id="cancel-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-xs px-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6" style="border:1px solid #EAE0D3;">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h3 class="text-base font-bold" style="color:#252B2B;">Batalkan Reservasi</h3>
                    <p class="text-xs mt-0.5" style="color:#4C4F54;">
                        Jelaskan alasan pembatalan reservasi yang sebelumnya telah disetujui.
                    </p>
                </div>
                <button type="button" onclick="closeCancelModal()" class="text-gray-400 hover:text-gray-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="cancel-form" method="POST" class="mt-4">
                @csrf
                @method('PATCH')

                <label for="cancellation_reason" class="block text-xs font-bold mb-1" style="color:#252B2B;">
                    Alasan Pembatalan <span class="text-red-600">*</span>
                </label>
                <textarea id="cancellation_reason" name="cancellation_reason" rows="4" required maxlength="500"
                          class="w-full rounded-xl text-xs sm:text-sm p-3 transition focus:outline-none"
                          style="border:1px solid #EAE0D3; background:#FAF6F0;"
                          placeholder="Contoh: Terjadi kendala teknis kelistrikan mendadak pada ruangan."></textarea>

                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" onclick="closeCancelModal()"
                            class="px-4 py-2 text-xs font-bold rounded-xl transition"
                            style="background:#FAF6F0; color:#4C4F54; border:1px solid #EAE0D3;">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-xs font-bold text-white rounded-xl transition shadow-xs hover:brightness-110"
                            style="background:#8F0B13;">
                        Proses Pembatalan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRejectModal(actionUrl) {
            const modal = document.getElementById('reject-modal');
            const form = document.getElementById('reject-form');
            form.setAttribute('action', actionUrl);
            document.getElementById('alasan_ditolak').value = '';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeRejectModal() {
            const modal = document.getElementById('reject-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function openCancelModal(actionUrl) {
            const modal = document.getElementById('cancel-modal');
            const form = document.getElementById('cancel-form');
            form.setAttribute('action', actionUrl);
            document.getElementById('cancellation_reason').value = '';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeCancelModal() {
            const modal = document.getElementById('cancel-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
</x-petugas-layout>