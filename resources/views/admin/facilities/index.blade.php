<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-extrabold text-lg leading-tight" style="color:#252B2B;">
                    {{ __('Manajemen Fasilitas Kampus') }}
                </h2>
                <p class="text-xs mt-0.5" style="color:#4C4F54;">Kelola data, status operasional, dan ketersediaan fasilitas kampus</p>
            </div>
            <a href="{{ route('admin.fasilitas.create') }}" x-data @click.prevent="$dispatch('open-modal', 'fasilitas')" class="mb-fab px-4 py-2 bg-maroon-700 text-cream-100 text-sm font-bold rounded-xl hover:brightness-110 shadow-sm">
                + Daftarkan Fasilitas Baru
            </a>
        </div>
    </x-slot>

    <div class="px-4 sm:px-6 py-5" style="background:#FAF6F0;">
        {{-- Filter --}}
        <div class="bg-white p-4 rounded-xl shadow-xs border border-cream-border mb-6">
            <form method="GET" action="{{ route('admin.fasilitas.index') }}" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label for="search" class="block text-sm font-semibold text-charcoal-dark mb-1">Pencarian</label>
                    <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama fasilitas, kategori, atau lokasi gedung..."
                           class="w-full rounded-xl border-cream-border shadow-sm focus:border-maroon-700 focus:ring-maroon-700 text-sm">
                </div>
                <div>
                    <label for="status" class="block text-sm font-semibold text-charcoal-dark mb-1">Status Operasional</label>
                    <select id="status" name="status" class="rounded-xl border-cream-border shadow-sm focus:border-maroon-700 focus:ring-maroon-700 text-sm">
                        <option value="">Semua Status Operasional</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Berstatus Aktif</option>
                        <option value="dalam perbaikan" {{ request('status') === 'dalam perbaikan' ? 'selected' : '' }}>Dalam Pemeliharaan</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-maroon-700 text-cream-100 text-sm font-semibold rounded-xl hover:brightness-110 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Cari</span>
                </button>
                <a href="{{ route('admin.fasilitas.index') }}" title="Atur Ulang" aria-label="Atur Ulang"
                   class="inline-flex items-center justify-center p-2 bg-cream-50 border border-cream-border text-charcoal-medium rounded-xl hover:bg-cream-100 hover:text-charcoal-dark transition h-[38px] w-[38px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </a>
            </form>
        </div>

        {{-- Tabel --}}
        <div class="bg-white overflow-hidden shadow-xs rounded-xl border border-cream-border">
            @if($facilities->isEmpty())
                <div class="p-12 text-center text-charcoal-medium">Tidak ditemukan data fasilitas yang sesuai dengan kriteria.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-cream-50 text-charcoal-dark font-bold border-b border-cream-border">
                                <th class="p-4">Dokumentasi Sarana</th>
                                <th class="p-4">Nama Fasilitas</th>
                                <th class="p-4">Kategori</th>
                                <th class="p-4">Lokasi Gedung</th>
                                <th class="p-4 text-center">Kapasitas (Orang)</th>
                                <th class="p-4 text-center">Status Operasional</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cream-border">
                            @foreach($facilities as $f)
                                <tr class="hover:bg-cream-100 {{ $f->facility_status === 'nonaktif' ? 'bg-red-50' : '' }}">
                                    <td class="p-4">@if($f->photo_url)
                                            <img src="{{ $f->photo_url }}" alt="" loading="lazy" class="w-20 h-14 rounded-lg object-cover bg-cream-50 border border-cream-border">
                                        @else
                                            <x-facility-placeholder class="w-20 h-14 rounded-lg border border-cream-border" />
                                        @endif</td>
                                    <td class="p-4 font-medium text-charcoal-dark">{{ $f->facility_name }}</td>
                                    <td class="p-4">
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-maroon-50 text-maroon-700">{{ $f->type }}</span>
                                    </td>
                                    <td class="p-4 text-charcoal-medium">{{ $f->location }}</td>
                                    <td class="p-4 text-center">{{ $f->capacity }}</td>
                                    <td class="p-4 text-center">
                                        @php
                                            $statusBadge = match($f->facility_status) {
                                                'aktif' => ['class' => 'bg-green-100 text-green-700', 'label' => 'Aktif'],
                                                'dalam perbaikan' => ['class' => 'bg-yellow-100 text-yellow-800', 'label' => 'Dalam Pemeliharaan'],
                                                default => ['class' => 'bg-red-100 text-red-700', 'label' => 'Nonaktif'],
                                            };
                                        @endphp
                                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $statusBadge['class'] }}">{{ $statusBadge['label'] }}</span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <a href="{{ route('admin.fasilitas.edit', $f->id_fasilitas) }}"
                                           x-data x-on:click.prevent="$dispatch('buka-ajax', { name: 'sunting', url: @js(route('admin.fasilitas.edit', $f->id_fasilitas)) })"
                                           class="inline-flex items-center gap-1 px-3 py-1 text-xs font-bold rounded-lg transition shadow-xs hover:brightness-110"
                                           style="background:#8F0B13; color:#EFDFC5;">Update Data</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t">
                    {{ $facilities->links() }}
                </div>
            @endif
        </div>
    </div>

    <x-modal-fasilitas />

    <x-modal-sunting-fasilitas />
</x-admin-layout>
