<x-app-layout>
    <x-slot name="header">
        <div class="mp-head-row flex justify-between items-center">
            <h2 class="font-extrabold text-xl text-maroon-800 leading-tight">
                {{ __('Riwayat Laporan Kerusakan Saya') }}
            </h2>
            <a href="{{ route('reports.create') }}" x-data @click.prevent="$dispatch('open-modal', 'laporan')" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-maroon-800 rounded-xl font-extrabold text-cream-100 hover:bg-maroon-900 transition text-sm shadow-sm">
                + Laporkan Kerusakan
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
            @if($reports->isEmpty())
                <div class="p-12 text-center text-gray-500">
                    Anda belum memiliki laporan kerusakan fasilitas.
                </div>
            @else
                {{-- Desktop / tablet: tabel --}}
                <div class="mp-d overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 font-semibold border-b">
                                <th class="p-4">Fasilitas</th>
                                <th class="p-4">Kategori</th>
                                <th class="p-4">Deskripsi</th>
                                <th class="p-4">Status</th>
                                <th class="p-4">Tanggal</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($reports as $laporan)
                                <tr class="hover:bg-gray-50">
                                    <td class="p-4 font-medium text-gray-900">{{ $laporan->facility->facility_name ?? '-' }}</td>
                                    <td class="p-4 text-gray-600">{{ $laporan->category->category_name ?? '-' }}</td>
                                    <td class="p-4 text-gray-600">{{ Str::limit($laporan->description, 60) }}</td>
                                    <td class="p-4">
                                        <x-status-badge :status="$laporan->report_status" />
                                    </td>
                                    <td class="p-4">{{ $laporan->created_at->format('d M Y H:i') }}</td>
                                    <td class="p-4 text-center">
                                        <a href="{{ route('reports.show', $laporan) }}"
                                           x-data x-on:click.prevent="$dispatch('buka-ajax', { name: 'detail-laporan', url: @js(route('reports.show', $laporan)) })"
                                           class="inline-flex items-center gap-1 px-3 py-1 text-xs font-bold rounded-lg transition shadow-xs hover:brightness-110"
                                           style="background:#8F0B13; color:#EFDFC5;">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile: daftar kartu (tanpa geser horizontal) --}}
                <div class="mp-m mp-cardlist">
                    @foreach($reports as $laporan)
                        <article class="mp-rcard">
                            <div class="mp-rcard-top">
                                <h3>{{ $laporan->facility->facility_name ?? '-' }}</h3>
                                <x-status-badge :status="$laporan->report_status" />
                            </div>
                            <p class="mp-rcard-meta">
                                <span>{{ $laporan->category->category_name ?? '-' }}</span>
                                <span>{{ $laporan->created_at->format('d M Y H:i') }}</span>
                            </p>
                            <p class="mp-rcard-text">{{ $laporan->description }}</p>
                            <a href="{{ route('reports.show', $laporan) }}"
                               x-data x-on:click.prevent="$dispatch('buka-ajax', { name: 'detail-laporan', url: @js(route('reports.show', $laporan)) })"
                               class="mp-rcard-btn">
                                Lihat Detail
                            </a>
                        </article>
                    @endforeach
                </div>

                <div class="p-4 border-t">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>

    <x-modal-laporan />

    <x-modal-detail name="detail-laporan" title="Detail Laporan Kerusakan" max-width="2xl" />
</x-app-layout>
