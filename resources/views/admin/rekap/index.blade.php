<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-extrabold text-lg leading-tight" style="color:#252B2B;">
                    {{ __('Rekapitulasi Okupansi & Kerusakan Fasilitas Kampus') }}
                </h2>
                <p class="text-xs mt-0.5" style="color:#4C4F54;">Laporan analitik tingkat okupansi sarana dan rekapitulasi riwayat penanganan kerusakan fasilitas.</p>
            </div>
            <div class="mb-hide flex items-center gap-2">
                {{-- CSV (Soft Approved Emerald) --}}
                <a href="{{ route('admin.rekap.export', ['format' => 'csv', 'dari' => $periode['dari'], 'sampai' => $periode['sampai']]) }}"
                   class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg shadow-xs transition duration-150 bg-emerald-100 text-emerald-800 border border-emerald-200 hover:bg-emerald-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                    CSV
                </a>

                {{-- Excel (Forest Emerald) --}}
                <a href="{{ route('admin.rekap.export', ['format' => 'excel', 'dari' => $periode['dari'], 'sampai' => $periode['sampai']]) }}"
                   class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg shadow-xs transition duration-150 bg-emerald-800 text-cream border border-emerald-900 hover:bg-emerald-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Excel
                </a>

                {{-- PDF (Signature Crimson Maroon) --}}
                <a href="{{ route('admin.rekap.export', ['format' => 'pdf', 'dari' => $periode['dari'], 'sampai' => $periode['sampai']]) }}"
                   class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-white shadow-xs transition duration-150 bg-maroon-700 border border-maroon-800 hover:bg-maroon-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#EFDFC5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    PDF
                </a>
            </div>
        </div>
    </x-slot>


    <div class="px-4 sm:px-6 py-5" style="background:#FAF6F0;">
        {{-- Tombol ekspor untuk mobile (di desktop ada di judul halaman) --}}
        <div class="mb-show-flex gap-2 mb-4">
            <a href="{{ route('admin.rekap.export', ['format' => 'csv', 'dari' => $periode['dari'], 'sampai' => $periode['sampai']]) }}"
               class="flex-1 inline-flex items-center justify-center gap-1.5 text-sm font-semibold rounded-xl shadow-xs bg-emerald-100 text-emerald-800 border border-emerald-200" style="min-height:44px;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                CSV
            </a>
            <a href="{{ route('admin.rekap.export', ['format' => 'excel', 'dari' => $periode['dari'], 'sampai' => $periode['sampai']]) }}"
               class="flex-1 inline-flex items-center justify-center gap-1.5 text-sm font-semibold rounded-xl shadow-xs bg-emerald-800 text-cream border border-emerald-900" style="min-height:44px;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                Excel
            </a>
            <a href="{{ route('admin.rekap.export', ['format' => 'pdf', 'dari' => $periode['dari'], 'sampai' => $periode['sampai']]) }}"
               class="flex-1 inline-flex items-center justify-center gap-1.5 text-sm font-semibold rounded-xl shadow-xs text-white bg-maroon-700 border border-maroon-800" style="min-height:44px;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                PDF
            </a>
        </div>
        {{-- Filter periode --}}
        <div class="bg-white p-4 rounded-xl shadow-xs border mb-6" style="border-color:#EAE0D3;">
            <form method="GET" action="{{ route('admin.rekap.index') }}" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label for="dari" class="block text-sm font-semibold mb-1" style="color:#252B2B;">Tanggal Awal</label>
                    <input id="dari" type="date" name="dari" value="{{ $periode['dari'] }}"
                           class="rounded-lg shadow-xs text-sm border-slate-300 focus:border-[#8F0B13] focus:ring-[#8F0B13]">
                </div>
                <div>
                    <label for="sampai" class="block text-sm font-semibold mb-1" style="color:#252B2B;">Tanggal Akhir</label>
                    <input id="sampai" type="date" name="sampai" value="{{ $periode['sampai'] }}"
                           class="rounded-lg shadow-xs text-sm border-slate-300 focus:border-[#8F0B13] focus:ring-[#8F0B13]">
                </div>
                <button type="submit"
                        class="px-5 py-2 text-white text-sm font-semibold rounded-lg shadow-xs transition duration-150 bg-maroon-700 hover:bg-maroon-900">
                    Terapkan Periode
                </button>
                <a href="{{ route('admin.rekap.index') }}"
                   class="px-4 py-2 text-sm font-medium rounded-lg transition duration-150 bg-cream-50 border border-cream-border text-charcoal-medium hover:border-maroon-700 hover:text-maroon-900 hover:bg-white">
                    Periode Bulan Berjalan
                </a>
            </form>
            <p class="mt-3 text-xs leading-relaxed" style="color:#4C4F54;">
                Rentang analisis mencakup {{ $periode['hari'] }} hari kalender. Persentase tingkat okupansi dihitung berdasarkan perbandingan total jam reservasi disetujui yang telah selesai terhadap jam operasional resmi (07.00 – 20.00 WIB) pada hari kerja efektif (Senin s/d Jumat). Fasilitas berstatus nonaktif tidak diikutsertakan dalam kalkulasi kecuali pada tanggal dengan riwayat penggunaan riil.
            </p>
        </div>

        <div class="bg-white overflow-hidden shadow-xs rounded-xl border border-cream-border">
            @if($rekap->isEmpty())
                <div class="p-12 text-center text-charcoal-medium">Belum ada data rekapitulasi fasilitas untuk periode yang dipilih.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="mb-noaction w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-cream-50 text-charcoal-dark font-bold border-b border-cream-border">
                                <th class="p-4">Nama Fasilitas</th>
                                <th class="p-4 text-center">Status Operasional</th>
                                <th class="p-4 text-center">Total Permohonan</th>
                                <th class="p-4 text-center">Reservasi Disetujui</th>
                                <th class="p-4 text-center">Menunggu Persetujuan</th>
                                <th class="p-4 text-center">Total Jam Terpakai</th>
                                <th class="p-4 text-center">Tingkat Okupansi</th>
                                <th class="p-4 text-center">Total Laporan</th>
                                <th class="p-4 text-center">Laporan Baru</th>
                                <th class="p-4 text-center">Laporan Selesai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cream-border">
                            @foreach($rekap as $r)
                                <tr class="hover:bg-cream-50/60 {{ $r['status'] === 'nonaktif' ? 'bg-red-50' : '' }}">
                                    <td class="p-4">
                                        <p class="font-medium text-charcoal-dark">{{ $r['nama'] }}</p>
                                        <p class="text-xs text-charcoal-medium">{{ $r['tipe'] }} &bull; {{ $r['lokasi'] }}</p>
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($r['status'] === 'aktif')
                                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-700">Aktif</span>
                                        @elseif($r['status'] === 'dalam perbaikan')
                                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Dalam Pemeliharaan</span>
                                        @else
                                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center font-semibold text-charcoal-dark">{{ $r['total_reservasi'] }}</td>
                                    <td class="p-4 text-center text-green-700 font-medium">{{ $r['reservasi_approved'] }}</td>
                                    <td class="p-4 text-center text-amber-700 font-medium">{{ $r['reservasi_pending'] }}</td>
                                    <td class="p-4 text-center text-charcoal-dark">{{ number_format($r['jam_terpakai'], 1, ',', '.') }} jam</td>
                                    <td class="p-4 text-center font-semibold text-charcoal-dark">{{ number_format($r['okupansi'], 1, ',', '.') }}%</td>
                                    <td class="p-4 text-center font-semibold text-charcoal-dark">{{ $r['total_laporan'] }}</td>
                                    <td class="p-4 text-center text-maroon-700 font-medium">{{ $r['laporan_baru'] }}</td>
                                    <td class="p-4 text-center text-green-700 font-medium">{{ $r['laporan_selesai'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($rekap->hasPages())
                    <div class="p-4 border-t border-cream-border">
                        {{ $rekap->links() }}
                    </div>
                @endif
            @endif
        </div>

        {{-- Rekap per lokasi --}}
        @if($perLokasi->isNotEmpty())
            <div class="mt-6 bg-white overflow-hidden shadow-xs rounded-xl border border-cream-border">
                <div class="p-4 border-b">
                    <h3 class="font-semibold text-charcoal-dark">Rekapitulasi Penggunaan Berdasarkan Lokasi Gedung</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="mb-noaction w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-cream-50 text-charcoal-dark font-bold border-b border-cream-border">
                                <th class="p-4">Lokasi Gedung</th>
                                <th class="p-4 text-center">Jumlah Fasilitas</th>
                                <th class="p-4 text-center">Reservasi Disetujui</th>
                                <th class="p-4 text-center">Total Jam Terpakai</th>
                                <th class="p-4 text-center">Tingkat Okupansi</th>
                                <th class="p-4 text-center">Total Laporan Kerusakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cream-border">
                            @foreach($perLokasi as $l)
                                <tr class="hover:bg-cream-50/60">
                                    <td class="p-4 font-medium text-charcoal-dark">{{ $l['lokasi'] }}</td>
                                    <td class="p-4 text-center">{{ $l['jumlah_fasilitas'] }}</td>
                                    <td class="p-4 text-center text-green-700 font-medium">{{ $l['reservasi_approved'] }}</td>
                                    <td class="p-4 text-center text-charcoal-dark">{{ number_format($l['jam_terpakai'], 1, ',', '.') }} jam</td>
                                    <td class="p-4 text-center font-semibold text-charcoal-dark">{{ number_format($l['okupansi'], 1, ',', '.') }}%</td>
                                    <td class="p-4 text-center text-charcoal-dark font-medium">{{ $l['total_laporan'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($perLokasi->hasPages())
                    <div class="p-4 border-t border-cream-border">
                        {{ $perLokasi->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-admin-layout>
