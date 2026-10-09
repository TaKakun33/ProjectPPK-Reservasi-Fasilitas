<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Pengguna Sistem') }}
                @if($pendingCount > 0)
                    <span class="ml-2 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">{{ $pendingCount }} Menunggu Verifikasi</span>
                @endif
            </h2>
            <a href="{{ route('admin.users.index') }}?create=1" class="px-4 py-2 bg-maroon-800 text-cream-100 text-sm font-semibold rounded-md hover:bg-maroon-900 shadow-sm">
                + Pendaftaran Pengguna Baru
            </a>
        </div>
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

        {{-- Form Tambah Akun (muncul jika ?create=1) --}}
        @if(request('create') === '1')
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
                <h3 class="font-semibold text-gray-900 mb-4">Formulir Pendaftaran Pengguna Baru</h3>

                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm">
                        <p class="font-semibold mb-1">Terdapat kesalahan pengisian data formulir:</p>
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-maroon-700 focus:ring-maroon-700 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Surel (Email)</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-maroon-700 focus:ring-maroon-700 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi</label>
                            <input type="password" name="password" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-maroon-700 focus:ring-maroon-700 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Kata Sandi</label>
                            <input type="password" name="password_confirmation" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-maroon-700 focus:ring-maroon-700 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hak Akses (Peran)</label>
                            <select name="role" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-maroon-700 focus:ring-maroon-700 text-sm">
                                <option value="pengguna">Pengguna (Civitas Akademika)</option>
                                <option value="petugas">Petugas Fasilitas Kampus</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 pt-2">
                        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50 text-sm">Batalkan</a>
                        <button type="submit" class="px-4 py-2 bg-maroon-800 text-cream-100 font-semibold rounded-md hover:bg-maroon-900 text-sm shadow-sm">
                            Simpan & Verifikasi Pengguna
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- Filter --}}
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan nama atau alamat surel..."
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-maroon-700 focus:ring-maroon-700 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Akun</label>
                    <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-maroon-700 focus:ring-maroon-700 text-sm">
                        <option value="">Semua Status Akun</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Terverifikasi Aktif</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Pendaftaran Ditolak</option>
                        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Akun Ditangguhkan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hak Akses</label>
                    <select name="role" class="rounded-md border-gray-300 shadow-sm focus:border-maroon-700 focus:ring-maroon-700 text-sm">
                        <option value="">Semua Hak Akses</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="petugas" {{ request('role') === 'petugas' ? 'selected' : '' }}>Petugas Fasilitas</option>
                        <option value="pengguna" {{ request('role') === 'pengguna' ? 'selected' : '' }}>Pengguna Kampus</option>
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-maroon-800 text-cream-100 text-sm font-semibold rounded-md hover:bg-maroon-900 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Cari</span>
                </button>
                <a href="{{ route('admin.users.index') }}" title="Atur Ulang" aria-label="Atur Ulang"
                   class="inline-flex items-center justify-center p-2 bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 hover:text-gray-900 transition h-[38px] w-[38px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </a>
            </form>
        </div>

        {{-- Tabel --}}
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
            @if($users->isEmpty())
                <div class="p-12 text-center text-gray-500">Tidak ditemukan data pengguna yang sesuai dengan kriteria.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 font-semibold border-b">
                                <th class="p-4">Nama Pengguna</th>
                                <th class="p-4">Alamat Surel</th>
                                <th class="p-4 text-center">Hak Akses</th>
                                <th class="p-4 text-center">Status Akun</th>
                                <th class="p-4 text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($users as $u)
                                <tr class="hover:bg-gray-50 {{ $u->account_status === 'pending' ? 'bg-orange-50' : '' }}">
                                    <td class="p-4 font-medium text-gray-900">{{ $u->name }}</td>
                                    <td class="p-4 text-gray-600">{{ $u->email }}</td>
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
                                    <td class="p-4 text-center space-x-2">
                                        @if($u->account_status === 'pending')
                                            <form method="POST" action="{{ route('admin.users.verify', $u->id_user) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-xs font-semibold text-green-700 hover:text-green-900 hover:underline">Verifikasi Akun</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.users.reject', $u->id_user) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menolak permohonan pendaftaran akun ini?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800 hover:underline">Tolak Pendaftaran</button>
                                            </form>
                                        @elseif($u->account_status === 'verified')
                                            <form method="POST" action="{{ route('admin.users.suspend', $u->id_user) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menangguhkan akun ini? Pengguna yang ditangguhkan tidak akan dapat mengakses sistem.')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-xs font-semibold text-amber-700 hover:text-amber-900 hover:underline">Tangguhkan Akun</button>
                                            </form>
                                        @elseif($u->account_status === 'suspended')
                                            <form method="POST" action="{{ route('admin.users.reactivate', $u->id_user) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-xs font-semibold text-green-700 hover:text-green-900 hover:underline">Aktifkan Kembali</button>
                                            </form>
                                        @elseif($u->account_status === 'rejected')
                                            <form method="POST" action="{{ route('admin.users.verify', $u->id_user) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin memverifikasi ulang pendaftaran akun yang sebelumnya ditolak ini?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-xs font-semibold text-green-700 hover:text-green-900 hover:underline">Verifikasi Ulang</button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
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
</x-admin-layout>
