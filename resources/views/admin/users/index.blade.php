<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Kelola User') }}
                @if($pendingCount > 0)
                    <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-orange-100 text-orange-700">{{ $pendingCount }} pending</span>
                @endif
            </h2>
            <a href="{{ route('admin.users.index') }}?create=1" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700">
                + Tambah Akun
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        {{-- Form Tambah Akun (muncul jika ?create=1) --}}
        @if(request('create') === '1')
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
                <h3 class="font-semibold text-gray-900 mb-4">Daftarkan Akun Baru</h3>

                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm">
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input type="password" name="password" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                            <select name="role" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="pengguna">Pengguna</option>
                                <option value="petugas">Petugas</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 pt-2">
                        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50 text-sm">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700 text-sm">
                            Simpan (Langsung Verified)
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- Filter --}}
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau email..."
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Semua</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Semua</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="petugas" {{ request('role') === 'petugas' ? 'selected' : '' }}>Petugas</option>
                        <option value="pengguna" {{ request('role') === 'pengguna' ? 'selected' : '' }}>Pengguna</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700">Filter</button>
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm rounded-md hover:bg-gray-200">Reset</a>
            </form>
        </div>

        {{-- Tabel --}}
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
            @if($users->isEmpty())
                <div class="p-12 text-center text-gray-500">Tidak ada user ditemukan.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 font-semibold border-b">
                                <th class="p-4">Nama</th>
                                <th class="p-4">Email</th>
                                <th class="p-4 text-center">Role</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4 text-center">Aksi</th>
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
                                                'admin'   => 'bg-purple-100 text-purple-700',
                                                'petugas' => 'bg-blue-100 text-blue-700',
                                                'pengguna'=> 'bg-gray-100 text-gray-700',
                                            ];
                                        @endphp
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $roleBadges[$u->role->value] ?? 'bg-gray-100' }}">
                                            {{ ucfirst($u->role->value) }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        @php
                                            $statusBadges = [
                                                'pending'  => 'bg-yellow-100 text-yellow-800',
                                                'verified' => 'bg-green-100 text-green-800',
                                                'rejected' => 'bg-red-100 text-red-800',
                                            ];
                                        @endphp
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $statusBadges[$u->account_status] ?? 'bg-gray-100' }}">
                                            {{ ucfirst($u->account_status) }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-center space-x-2">
                                        @if($u->account_status === 'pending')
                                            <form method="POST" action="{{ route('admin.users.verify', $u->id_user) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-xs font-semibold text-green-600 hover:text-green-800 hover:underline">Verify</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.users.reject', $u->id_user) }}" class="inline" onsubmit="return confirm('Tolak akun ini?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800 hover:underline">Tolak</button>
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
</x-app-layout>
