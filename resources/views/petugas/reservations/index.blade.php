<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Antrian Reservasi') }}
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

        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
            @if($reservations->isEmpty())
                <div class="p-12 text-center text-gray-500">
                    Tidak ada reservasi yang menunggu diproses.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 font-semibold border-b">
                                <th class="p-4">Pemohon</th>
                                <th class="p-4">Fasilitas</th>
                                <th class="p-4">Tanggal</th>
                                <th class="p-4">Waktu</th>
                                <th class="p-4">Tujuan</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($reservations as $res)
                                <tr class="hover:bg-gray-50">
                                    {{-- Pemohon --}}
                                    <td class="p-4 font-medium text-gray-900">{{ $res->user->name ?? '-' }}</td>

                                    {{-- Fasilitas --}}
                                    <td class="p-4">{{ $res->facility->facility_name ?? '-' }}</td>

                                    {{-- Tanggal --}}
                                    <td class="p-4">{{ $res->date->format('d M Y') }}</td>

                                    {{-- Waktu --}}
                                    <td class="p-4">{{ substr($res->start_time, 0, 5) }} - {{ substr($res->end_time, 0, 5) }}</td>

                                    {{-- Tujuan --}}
                                    <td class="p-4 text-gray-600">{{ $res->purpose }}</td>

                                    {{-- Aksi --}}
                                    <td class="p-4">
                                        <div class="flex justify-center gap-2">
                                            <form method="POST"
                                                  action="{{ route('petugas.reservations.approve', $res->id_reservasi) }}"
                                                  onsubmit="return confirm('Setujui reservasi ini?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="px-3 py-1 text-xs font-semibold text-white bg-green-600 rounded hover:bg-green-700">
                                                    Setujui
                                                </button>
                                            </form>

                                            <button type="button"
                                                    onclick="openRejectModal('{{ route('petugas.reservations.reject', $res->id_reservasi) }}')"
                                                    class="px-3 py-1 text-xs font-semibold text-white bg-red-600 rounded hover:bg-red-700">
                                                Tolak
                                            </button>
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

    {{-- Modal alasan penolakan (US #9): wajib diisi sebelum reservasi ditolak --}}
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
    </script>
</x-app-layout>