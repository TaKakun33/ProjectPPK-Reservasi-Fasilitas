<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-extrabold text-lg leading-tight" style="color:#252B2B;">
                    {{ __('Manajemen Pengguna Sistem') }}
                                        @if($pendingCount > 0)
                                            <span class="ml-2 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">{{ $pendingCount }} Menunggu Verifikasi</span>
                                        @endif
                </h2>
                <p class="text-xs mt-0.5" style="color:#4C4F54;">Verifikasi akun, atur hak akses, dan kelola data pengguna sistem</p>
            </div>
            <a href="{{ route('admin.users.index') }}?create=1" x-data x-on:click.prevent="$dispatch('open-modal', 'akun')" class="mb-fab px-4 py-2 bg-maroon-700 text-cream-100 text-sm font-semibold rounded-xl hover:brightness-110 shadow-sm">
                + Pendaftaran Pengguna Baru
            </a>
        </div>
    </x-slot>

    <div class="px-4 sm:px-6 py-5" style="background:#FAF6F0;">
        {{-- Filter --}}
        <div class="bg-white p-4 rounded-xl shadow-xs border border-cream-border mb-6">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label for="search" class="block text-sm font-semibold text-charcoal-dark mb-1">Pencarian</label>
                    <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan nama atau alamat surel..."
                           class="w-full rounded-xl border-cream-border shadow-sm focus:border-maroon-700 focus:ring-maroon-700 text-sm">
                </div>
                <div>
                    <label for="status" class="block text-sm font-semibold text-charcoal-dark mb-1">Status Akun</label>
                    <select id="status" name="status" class="rounded-xl border-cream-border shadow-sm focus:border-maroon-700 focus:ring-maroon-700 text-sm">
                        <option value="">Semua Status Akun</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Terverifikasi Aktif</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Pendaftaran Ditolak</option>
                        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Akun Ditangguhkan</option>
                    </select>
                </div>
                <div>
                    <label for="role_2" class="block text-sm font-semibold text-charcoal-dark mb-1">Hak Akses</label>
                    <select id="role_2" name="role" class="rounded-xl border-cream-border shadow-sm focus:border-maroon-700 focus:ring-maroon-700 text-sm">
                        <option value="">Semua Hak Akses</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="petugas" {{ request('role') === 'petugas' ? 'selected' : '' }}>Petugas Fasilitas</option>
                        <option value="pengguna" {{ request('role') === 'pengguna' ? 'selected' : '' }}>Pengguna Kampus</option>
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-maroon-700 text-cream-100 text-sm font-semibold rounded-xl hover:brightness-110 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Cari</span>
                </button>
                <a href="{{ route('admin.users.index') }}" title="Atur Ulang" aria-label="Atur Ulang"
                   class="inline-flex items-center justify-center p-2 bg-cream-50 border border-cream-border text-charcoal-medium rounded-xl hover:bg-cream-100 hover:text-charcoal-dark transition h-[38px] w-[38px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </a>
            </form>
        </div>

        {{-- Tabel --}}
        <div class="bg-white overflow-hidden shadow-xs rounded-xl border border-cream-border">
            @if($users->isEmpty())
                <div class="p-12 text-center text-charcoal-medium">Tidak ditemukan data pengguna yang sesuai dengan kriteria.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-cream-50 text-charcoal-dark font-bold border-b border-cream-border">
                                <th class="p-4">Nama Pengguna</th>
                                <th class="p-4">Alamat Surel</th>
                                <th class="p-4 text-center">Hak Akses</th>
                                <th class="p-4 text-center">Status Akun</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cream-border">
                            @foreach($users as $u)
                                <tr class="hover:bg-cream-50/60 {{ $u->account_status === 'pending' ? 'bg-orange-50' : '' }}">
                                    <td class="p-4 font-medium text-charcoal-dark">{{ $u->name }}</td>
                                    <td class="p-4 text-charcoal-medium">{{ $u->email }}</td>
                                    <td class="p-4 text-center">
                                        @php
                                            $roleBadges = [
                                                'admin'   => ['class' => 'bg-purple-100 text-purple-700', 'label' => 'Administrator'],
                                                'petugas' => ['class' => 'bg-maroon-100 text-maroon-800', 'label' => 'Petugas Fasilitas'],
                                                'pengguna'=> ['class' => 'bg-gray-100 text-gray-700',     'label' => 'Pengguna Kampus'],
                                            ];
                                            $rBadge = $roleBadges[$u->role->value] ?? ['class' => 'bg-gray-100 text-gray-700', 'label' => ucfirst($u->role->value)];
                                        @endphp
                                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $rBadge['class'] }}">
                                            {{ $rBadge['label'] }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        @php
                                            $statusBadges = [
                                                'pending'   => ['class' => 'bg-yellow-100 text-yellow-800', 'label' => 'Menunggu Verifikasi'],
                                                'verified'  => ['class' => 'bg-green-100 text-green-800',   'label' => 'Terverifikasi'],
                                                'rejected'  => ['class' => 'bg-red-100 text-red-800',       'label' => 'Ditolak'],
                                                'suspended' => ['class' => 'bg-gray-200 text-gray-700',     'label' => 'Ditangguhkan'],
                                            ];
                                            $sBadge = $statusBadges[$u->account_status] ?? ['class' => 'bg-gray-100 text-gray-700', 'label' => ucfirst($u->account_status)];
                                        @endphp
                                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $sBadge['class'] }}">
                                            {{ $sBadge['label'] }}
                                        </span>
                                    </td>
                                    <td class="p-4"><div class="flex flex-wrap justify-center items-center gap-1.5">
                                        @if($u->account_status === 'pending')
                                            <form method="POST" action="{{ route('admin.users.verify', $u->id_user) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-3 py-1 text-xs font-bold text-white rounded-lg transition shadow-xs hover:brightness-110" style="background:#059669;">Verifikasi Akun</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.users.reject', $u->id_user) }}" class="inline" data-confirm-type="danger" data-confirm-title="Tolak Pendaftaran?" data-confirm-ok="Ya, Tolak" data-confirm="Apakah Anda yakin ingin menolak permohonan pendaftaran akun ini?">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-3 py-1 text-xs font-bold text-white rounded-lg transition shadow-xs hover:brightness-110" style="background:#8F0B13;">Tolak Pendaftaran</button>
                                            </form>
                                        @elseif($u->account_status === 'verified')
                                            <form method="POST" action="{{ route('admin.users.suspend', $u->id_user) }}" class="inline" data-confirm-type="danger" data-confirm-title="Tangguhkan Akun?" data-confirm-ok="Ya, Tangguhkan" data-confirm="Apakah Anda yakin ingin menangguhkan akun ini? Pengguna yang ditangguhkan tidak akan dapat mengakses sistem.">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-3 py-1 text-xs font-bold text-white rounded-lg transition shadow-xs hover:brightness-110" style="background:#D97706;">Tangguhkan Akun</button>
                                            </form>
                                        @elseif($u->account_status === 'suspended')
                                            <form method="POST" action="{{ route('admin.users.reactivate', $u->id_user) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-3 py-1 text-xs font-bold text-white rounded-lg transition shadow-xs hover:brightness-110" style="background:#059669;">Aktifkan Kembali</button>
                                            </form>
                                        @elseif($u->account_status === 'rejected')
                                            <form method="POST" action="{{ route('admin.users.verify', $u->id_user) }}" class="inline" data-confirm-type="info" data-confirm-title="Verifikasi Ulang Akun?" data-confirm-ok="Ya, Verifikasi" data-confirm="Apakah Anda yakin ingin memverifikasi ulang pendaftaran akun yang sebelumnya ditolak ini?">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-3 py-1 text-xs font-bold text-white rounded-lg transition shadow-xs hover:brightness-110" style="background:#2563EB;">Verifikasi Ulang</button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </div></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Pop-up pendaftaran akun baru (terbuka otomatis bila memakai tautan ?create=1 atau validasi gagal) --}}
    <x-modal-akun />
</x-admin-layout>
