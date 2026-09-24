<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Antrian & Riwayat Reservasi') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        @error('alasan_ditolak')
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded text-sm font-medium">
                {{ $message }}
            </div>
        @enderror

        @error('cancellation_reason')
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded text-sm font-medium">
                {{ $message }}
            </div>
        @enderror

        {{-- Filter Tab Status --}}
        <div class="mb-6 flex space-x-2 border-b border-gray-200 pb-2 overflow-x-auto">
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
                <a href="{{ $key === 'all' ? route('petugas.reservations.index') : route('petugas.reservations.index', ['status' => $key]) }}"
                   class="px-4 py-2 text-sm font-medium rounded-lg transition whitespace-nowrap {{ $selectedStatus === $key ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
            @if($reservations->isEmpty())
                <div class="p-12 text-center text-gray-500">
                    Belum ada riwayat atau antrian reservasi yang sesuai dengan filter.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 font-semibold border-b">
                                <th class="p-4">Pemohon</th>
                                <th class="p-4">Fasilitas</th>
                                <th class="p-4">Tanggal & Waktu</th>
                                <th class="p-4">Tujuan</th>
                                <th class="p-4">Status</th>
                                <th class="p-4">Keterangan / Catatan</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($reservations as $res)
                                <tr class="hover:bg-gray-50">
                                    {{-- Pemohon --}}
                                    <td class="p-4 font-medium text-gray-900">
                                        {{ $res->user->name ?? '-' }}
                                    </td>

                                    {{-- Fasilitas --}}
                                    <td class="p-4">
                                        <div class="font-medium text-gray-900">{{ $res->facility->facility_name ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $res->facility->type ?? '' }}</div>
                                    </td>

                                    {{-- Tanggal & Waktu --}}
                                    <td class="p-4">
                                        <div class="font-medium text-gray-900">{{ $res->date->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ substr($res->start_time, 0, 5) }} - {{ substr($res->end_time, 0, 5) }} WIB</div>
                                    </td>

                                    {{-- Tujuan --}}
                                    <td class="p-4 text-gray-600 max-w-xs truncate">{{ $res->purpose }}</td>

                                    {{-- Status Badge --}}
                                    <td class="p-4">
                                        @php
                                            $badges = [
                                                'pending'   => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                'approved'  => 'bg-green-100 text-green-800 border-green-200',
                                                'rejected'  => 'bg-red-100 text-red-800 border-red-200',
                                                'cancelled' => 'bg-gray-100 text-gray-800 border-gray-200',
                                            ];
                                        @endphp
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $badges[$res->reservation_status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($res->reservation_status) }}
                                        </span>
                                    </td>

                                    {{-- Keterangan / Alasan --}}
                                    <td class="p-4 text-xs text-gray-500 max-w-xs">
                                        @if($res->reservation_status === 'rejected' && $res->alasan_ditolak)
                                            <span class="text-red-700 font-medium">Ditolak:</span> {{ $res->alasan_ditolak }}
                                        @elseif($res->reservation_status === 'cancelled' && $res->cancellation_reason)
                                            <span class="text-gray-700 font-medium">Dibatalkan:</span> {{ $res->cancellation_reason }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="p-4">
                                        <div class="flex justify-center gap-2">
                                            @if($res->reservation_status === 'pending')
                                                <form method="POST"
                                                      action="{{ route('petugas.reservations.approve', $res->id_reservasi) }}"
                                                      onsubmit="return confirm('Setujui reservasi ini?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="px-3 py-1 text-xs font-semibold text-white bg-green-600 rounded hover:bg-green-700 transition">
                                                        Setujui
                                                    </button>
                                                </form>

                                                <button type="button"
                                                        onclick="openRejectModal('{{ route('petugas.reservations.reject', $res->id_reservasi) }}')"
                                                        class="px-3 py-1 text-xs font-semibold text-white bg-red-600 rounded hover:bg-red-700 transition">
                                                    Tolak
                                                </button>
                                            @elseif($res->reservation_status === 'approved')
                                                <button type="button"
                                                        onclick="openCancelModal('{{ route('petugas.reservations.cancel', $res->id_reservasi) }}')"
                                                        class="px-3 py-1 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded transition border border-gray-300">
                                                    Batalkan
                                                </button>
                                            @else
                                                <span class="text-xs text-gray-400 font-medium">Selesai</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t">
                    {{ $reservations->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Alasan Penolakan --}}
    <div id="reject-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
            <h3 class="text-lg font-semibold text-gray-900">Tolak Reservasi</h3>
            <p class="mt-1 text-sm text-gray-500">
                Jelaskan alasan penolakan supaya pengguna tahu kenapa reservasinya ditolak.
            </p>

            <form id="reject-form" method="POST" class="mt-4">
                @csrf
                @method('PATCH')

                <label for="alasan_ditolak" class="block text-sm font-medium text-gray-700">
                    Alasan Penolakan <span class="text-red-600">*</span>
                </label>
                <textarea id="alasan_ditolak" name="alasan_ditolak" rows="4" required maxlength="500"
                          class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-red-500 focus:ring-red-500"
                          placeholder="Contoh: Fasilitas sedang dalam perbaikan pada tanggal tersebut."></textarea>

                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" onclick="closeRejectModal()"
                            class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 rounded hover:bg-gray-200">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-xs font-semibold text-white bg-red-600 rounded hover:bg-red-700">
                        Tolak Reservasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Alasan Pembatalan (Petugas membatalkan reservasi approved) --}}
    <div id="cancel-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
            <h3 class="text-lg font-semibold text-gray-900">Batalkan Reservasi</h3>
            <p class="mt-1 text-sm text-gray-500">
                Jelaskan alasan pembatalan reservasi yang sebelumnya telah disetujui.
            </p>

            <form id="cancel-form" method="POST" class="mt-4">
                @csrf
                @method('PATCH')

                <label for="cancellation_reason" class="block text-sm font-medium text-gray-700">
                    Alasan Pembatalan <span class="text-red-600">*</span>
                </label>
                <textarea id="cancellation_reason" name="cancellation_reason" rows="4" required maxlength="500"
                          class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-red-500 focus:ring-red-500"
                          placeholder="Contoh: Terjadi kendala teknis mendadak pada fasilitas."></textarea>

                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" onclick="closeCancelModal()"
                            class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 rounded hover:bg-gray-200">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-xs font-semibold text-white bg-red-600 rounded hover:bg-red-700">
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
</x-app-layout>