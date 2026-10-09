<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-extrabold text-lg leading-tight" style="color:#252B2B;">
                    {{ __('Rekapitulasi Okupansi & Kerusakan Fasilitas Kampus') }}
                </h2>
                <p class="text-xs mt-0.5" style="color:#4C4F54;">Laporan analitik tingkat okupansi sarana dan rekapitulasi riwayat penanganan kerusakan fasilitas.</p>
            </div>
            <div class="flex items-center gap-2">
                {{-- CSV (Soft Approved Emerald) --}}
                <a href="{{ route('admin.rekap.export', ['format' => 'csv', 'dari' => $periode['dari'], 'sampai' => $periode['sampai']]) }}"
                   class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg shadow-xs transition duration-150"
                   style="background:#D1FAE5; color:#065F46; border:1px solid #A7F3D0;"
                   onmouseover="this.style.background='#A7F3D0';"
                   onmouseout="this.style.background='#D1FAE5';">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                    CSV
                </a>

                {{-- Excel (Forest Emerald) --}}
                <a href="{{ route('admin.rekap.export', ['format' => 'excel', 'dari' => $periode['dari'], 'sampai' => $periode['sampai']]) }}"
                   class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg shadow-xs transition duration-150"
                   style="background:#065F46; color:#EFDFC5; border:1px solid #044E39;"
                   onmouseover="this.style.background='#044E39';"
                   onmouseout="this.style.background='#065F46';">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Excel
                </a>

                {{-- PDF (Signature Crimson Maroon) --}}
                <a href="{{ route('admin.rekap.export', ['format' => 'pdf', 'dari' => $periode['dari'], 'sampai' => $periode['sampai']]) }}"
                   class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-white shadow-xs transition duration-150"
                   style="background:#8F0B13; border:1px solid #70090F;"
                   onmouseover="this.style.background='#380F17';"
                   onmouseout="this.style.background='#8F0B13';">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#EFDFC5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    PDF
                </a>
            </div>
        </div>
    </x-slot>


    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded text-sm font-medium">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Filter periode --}}
        <div class="bg-white p-4 rounded-xl shadow-xs border mb-6" style="border-color:#EAE0D3;">
            <form method="GET" action="{{ route('admin.rekap.index') }}" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color:#252B2B;">Tanggal Awal</label>
                    <input type="date" name="dari" value="{{ $periode['dari'] }}"
                           class="rounded-lg shadow-xs text-sm border-slate-300 focus:border-[#8F0B13] focus:ring-[#8F0B13]">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color:#252B2B;">Tanggal Akhir</label>
                    <input type="date" name="sampai" value="{{ $periode['sampai'] }}"
                           class="rounded-lg shadow-xs text-sm border-slate-300 focus:border-[#8F0B13] focus:ring-[#8F0B13]">
                </div>
                <button type="submit"
                        class="px-5 py-2 text-white text-sm font-semibold rounded-lg shadow-xs transition duration-150"
                        style="background:#8F0B13;"
                        onmouseover="this.style.background='#380F17';"
                        onmouseout="this.style.background='#8F0B13';">
                    Terapkan Periode
                </button>
                <a href="{{ route('admin.rekap.index') }}"
                   class="px-4 py-2 text-sm font-medium rounded-lg transition duration-150"
                   style="background:#FAF6F0; border:1px solid #EAE0D3; color:#4C4F54;"
                   onmouseover="this.style.borderColor='#8F0B13'; this.style.color='#380F17'; this.style.background='#FFFFFF';"
                   onmouseout="this.style.borderColor='#EAE0D3'; this.style.color='#4C4F54'; this.style.background='#FAF6F0';">
                    Periode Bulan Berjalan
                </a>
            </form>
            <p class="mt-3 text-xs leading-relaxed" style="color:#4C4F54;">
                Rentang analisis mencakup {{ $periode['hari'] }} hari kalender. Persentase tingkat okupansi dihitung berdasarkan perbandingan total jam reservasi disetujui yang telah selesai terhadap jam operasional resmi (07.00 – 20.00 WIB) pada hari kerja efektif (Senin s/d Jumat). Fasilitas berstatus nonaktif tidak diikutsertakan dalam kalkulasi kecuali pada tanggal dengan riwayat penggunaan riil.
            </p>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
            @if($rekap->isEmpty())
                <div class="p-12 text-center text-gray-500">Belum ada data rekapitulasi fasilitas untuk periode yang dipilih.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 font-semibold border-b">
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
                        <tbody class="divide-y divide-gray-100">
                            @foreach($rekap as $r)
                                <tr class="hover:bg-gray-50 {{ $r['status'] === 'nonaktif' ? 'bg-red-50' : '' }}">
                                    <td class="p-4">
                                        <p class="font-medium text-gray-900">{{ $r['nama'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $r['tipe'] }} &bull; {{ $r['lokasi'] }}</p>
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
                                    <td class="p-4 text-center font-semibold text-gray-900">{{ $r['total_reservasi'] }}</td>
                                    <td class="p-4 text-center text-green-700 font-medium">{{ $r['reservasi_approved'] }}</td>
                                    <td class="p-4 text-center text-amber-700 font-medium">{{ $r['reservasi_pending'] }}</td>
                                    <td class="p-4 text-center text-gray-700">{{ number_format($r['jam_terpakai'], 1, ',', '.') }} jam</td>
                                    <td class="p-4 text-center font-semibold text-gray-900">{{ number_format($r['okupansi'], 1, ',', '.') }}%</td>
                                    <td class="p-4 text-center font-semibold text-gray-900">{{ $r['total_laporan'] }}</td>
                                    <td class="p-4 text-center text-maroon-700 font-medium">{{ $r['laporan_baru'] }}</td>
                                    <td class="p-4 text-center text-green-700 font-medium">{{ $r['laporan_selesai'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($rekap->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $rekap->links() }}
                    </div>
                @endif
            @endif
        </div>

        {{-- Rekap per lokasi --}}
        @if($perLokasi->isNotEmpty())
            <div class="mt-6 bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <div class="p-4 border-b">
                    <h3 class="font-semibold text-gray-900">Rekapitulasi Penggunaan Berdasarkan Lokasi Gedung</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 font-semibold border-b">
                                <th class="p-4">Lokasi Gedung</th>
                                <th class="p-4 text-center">Jumlah Fasilitas</th>
                                <th class="p-4 text-center">Reservasi Disetujui</th>
                                <th class="p-4 text-center">Total Jam Terpakai</th>
                                <th class="p-4 text-center">Tingkat Okupansi</th>
                                <th class="p-4 text-center">Total Laporan Kerusakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($perLokasi as $l)
                                <tr class="hover:bg-gray-50">
                                    <td class="p-4 font-medium text-gray-900">{{ $l['lokasi'] }}</td>
                                    <td class="p-4 text-center">{{ $l['jumlah_fasilitas'] }}</td>
                                    <td class="p-4 text-center text-green-700 font-medium">{{ $l['reservasi_approved'] }}</td>
                                    <td class="p-4 text-center text-gray-700">{{ number_format($l['jam_terpakai'], 1, ',', '.') }} jam</td>
                                    <td class="p-4 text-center font-semibold text-gray-900">{{ number_format($l['okupansi'], 1, ',', '.') }}%</td>
                                    <td class="p-4 text-center text-gray-900 font-medium">{{ $l['total_laporan'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($perLokasi->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $perLokasi->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-admin-layout>
