<x-petugas-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-extrabold text-lg leading-tight" style="color:#252B2B;">Antrian & Penanganan Laporan Kerusakan</h2>
                <p class="text-xs mt-0.5" style="color:#4C4F54;">Tinjau, investigasi, dan perbarui status laporan fasilitas kampus</p>
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

        {{-- Pencarian --}}
        <x-search-bar :action="route('petugas.reports.index')"
                      :hidden="['status' => ($selectedStatus ?? 'all') !== 'all' ? $selectedStatus : null]"
                      placeholder="Cari fasilitas, pelapor, kategori, atau deskripsi kerusakan..." />

        {{-- Filter Tab Status --}}
        <div class="flex items-center gap-2 pb-1 overflow-x-auto">
            @php
                $reportTabs = [
                    'all'      => 'Semua',
                    'baru'     => 'Baru (Belum Diproses)',
                    'diproses' => 'Sedang Diproses',
                    'selesai'  => 'Selesai',
                    'ditolak'  => 'Ditolak',
                ];
            @endphp
            @foreach($reportTabs as $key => $label)
                @php $isActive = ($selectedStatus ?? 'all') === $key; @endphp
                <a href="{{ route('petugas.reports.index', array_filter(['status' => $key === 'all' ? null : $key, 'search' => request('search')], fn ($v) => filled($v))) }}"
                   class="px-3.5 py-2 text-xs sm:text-sm font-bold rounded-xl transition whitespace-nowrap shadow-xs"
                   style="{{ $isActive
                            ? 'background:#8F0B13; color:#EFDFC5; border:1px solid #8F0B13;'
                            : 'background:white; color:#4C4F54; border:1px solid #EAE0D3;' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Card Tabel Laporan --}}
        <div class="bg-white rounded-xl shadow-xs overflow-hidden" style="border:1px solid #EAE0D3;">
            @if($reports->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-12 h-12 rounded-full mx-auto flex items-center justify-center mb-3"
                         style="background:#FAF6F0; color:#4C4F54; border:1px solid #EAE0D3;">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-bold" style="color:#252B2B;">Tidak ada laporan ditemukan</p>
                    <p class="text-xs mt-1" style="color:#4C4F54;">Belum ada laporan kerusakan yang sesuai dengan filter ini.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs sm:text-sm">
                        <thead>
                            <tr class="font-bold border-b" style="background:#FAF6F0; color:#252B2B; border-color:#EAE0D3;">
                                <th class="py-3 px-4">Fasilitas</th>
                                <th class="py-3 px-4">Pelapor</th>
                                <th class="py-3 px-4">Kategori</th>
                                <th class="py-3 px-4">Deskripsi</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Tanggal Masuk</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y" style="border-color:#EAE0D3;">
                            @foreach($reports as $report)
                                <tr class="hover:bg-[#FAF6F0]/60 transition">
                                    {{-- Fasilitas --}}
                                    <td class="py-3 px-4 font-bold" style="color:#252B2B;">
                                        {{ $report->facility->facility_name ?? '-' }}
                                    </td>

                                    {{-- Pelapor --}}
                                    <td class="py-3 px-4 font-semibold" style="color:#252B2B;">
                                        {{ $report->user->name ?? '-' }}
                                    </td>

                                    {{-- Kategori --}}
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-0.5 text-[11px] font-semibold rounded-md"
                                              style="background:#FAF6F0; color:#4C4F54; border:1px solid #EAE0D3;">
                                            {{ $report->category->category_name ?? '-' }}
                                        </span>
                                    </td>

                                    {{-- Deskripsi --}}
                                    <td class="py-3 px-4 max-w-xs truncate" style="color:#4C4F54;" title="{{ $report->description }}">
                                        {{ Str::limit($report->description, 55) }}
                                    </td>

                                    {{-- Status --}}
                                    <td class="py-3 px-4">
                                        <x-status-badge :status="$report->report_status" />
                                    </td>

                                    {{-- Tanggal --}}
                                    <td class="py-3 px-4 text-xs" style="color:#4C4F54;">
                                        {{ $report->created_at->format('d M Y H:i') }}
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="py-3 px-4 text-center">
                                        <a href="{{ route('petugas.reports.show', $report->id_laporan) }}"
                                           x-data x-on:click.prevent="$dispatch('buka-ajax', { name: 'detail-laporan-petugas', url: @js(route('petugas.reports.show', $report->id_laporan)) })"
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

                <div class="p-4" style="border-top:1px solid #EAE0D3; background:#FAF6F0/40;">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>

    <x-modal-detail name="detail-laporan-petugas" title="Detail Laporan Kerusakan"
                    subtitle="Investigasi bukti fisik, tentukan status penanganan, dan perbarui fasilitas" max-width="3xl" />
</x-petugas-layout>
