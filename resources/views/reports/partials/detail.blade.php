{{-- Isi detail laporan kerusakan. Dipakai halaman penuh (reports.show) dan pop-up (AJAX, $modal = true). --}}
@php $modal = $modal ?? false; @endphp
<div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
    <div class="p-6 space-y-5">
        <div class="flex justify-between items-start gap-4">
            <div>
                <p class="text-sm text-gray-500">Fasilitas</p>
                <p class="font-semibold text-gray-900">{{ $laporan->facility->facility_name ?? '-' }}</p>
                <p class="text-sm text-gray-500">{{ $laporan->facility->type ?? '' }} - {{ $laporan->facility->location ?? '' }}</p>
            </div>
            <x-status-badge :status="$laporan->report_status" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Kategori</p>
                <p class="font-medium text-gray-800">{{ $laporan->category->category_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Tanggal Laporan</p>
                <p class="font-medium text-gray-800">{{ $laporan->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>

        <div>
            <p class="text-sm text-gray-500">Deskripsi Kerusakan</p>
            <p class="text-gray-800">{{ $laporan->description }}</p>
        </div>

        @if($laporan->photos->isNotEmpty())
            <div>
                <p class="text-sm text-gray-500 mb-2">Foto Kerusakan ({{ $laporan->photos->count() }})</p>
                <x-galeri-lightbox :urls="$laporan->photos->map(fn($f) => route('reports.photo', $f->id_foto))->values()" />
            </div>
        @endif

        @if($laporan->resolution_notes)
            <div class="p-4 bg-maroon-50 border-l-4 border-maroon-600 rounded">
                <p class="text-sm font-semibold text-maroon-800">Catatan Resolusi Petugas</p>
                <p class="mt-1 text-sm text-maroon-900">{{ $laporan->resolution_notes }}</p>
            </div>
        @endif

        <div class="flex justify-end pt-2 border-t">
            @if($modal)
                <button type="button" x-on:click="$dispatch('close-modal', 'detail-laporan')" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50">
                    Tutup
                </button>
            @else
                <a href="{{ route('reports.index') }}" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50">
                    Kembali ke Riwayat
                </a>
            @endif
        </div>
    </div>
</div>
