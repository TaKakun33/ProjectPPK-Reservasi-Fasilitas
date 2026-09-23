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

        <!-- Info laporan (sama gayanya dengan halaman detail milik pengguna) -->
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
                            @foreach($laporan->photos as $foto)
                                <a href="{{ route('reports.photo', $foto->id_foto) }}" target="_blank">
                                    <img src="{{ route('reports.photo', $foto->id_foto) }}" alt="Foto kerusakan"
                                         class="w-full h-32 object-cover rounded-lg border border-gray-200 hover:opacity-80 transition">
                                </a>
                            @endforeach
                        </div>
                    </div>
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
                                <span class="text-xs font-normal text-gray-400">(wajib diisi hanya saat menolak, opsional untuk aksi lainnya)</span>
                            </label>
                            <textarea name="resolution_notes" id="resolutionNotes" rows="3"
                                      placeholder="Opsional untuk Proses/Selesai — wajib diisi jika menolak laporan..."
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('resolution_notes') }}</textarea>
                        </div>

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

                            // Catatan hanya wajib saat aksi "Tolak".
                            if (action === 'ditolak' && notes === '') {
                                e.preventDefault();
                                alert('Catatan untuk pelapor wajib diisi saat menolak laporan.');
                                notesEl.focus();
                                return;
                            }

                            var confirmMsgs = {
                                ditolak: 'Tolak laporan ini? Catatan akan dikirim ke pelapor.',
                                diproses: 'Proses laporan ini? Fasilitas akan ditandai dalam perbaikan.',
                                selesai: 'Tandai laporan selesai? Fasilitas akan kembali aktif.'
                            };
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
