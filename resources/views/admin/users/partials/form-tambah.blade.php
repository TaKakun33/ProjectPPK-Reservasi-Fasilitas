{{-- Form pendaftaran akun baru oleh admin. Dipakai pop-up x-modal-akun. --}}
<form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
    @csrf
    {{-- Penanda: bila validasi gagal, pop-up dibuka kembali --}}
    <input type="hidden" name="_modal" value="akun">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="name" class="block text-sm font-semibold text-charcoal-dark mb-1">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required
                   class="w-full rounded-xl border-cream-border shadow-sm focus:border-maroon-700 focus:ring-maroon-700 text-sm">
        </div>
        <div>
            <label for="email" class="block text-sm font-semibold text-charcoal-dark mb-1">Alamat Surel (Email)</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                   class="w-full rounded-xl border-cream-border shadow-sm focus:border-maroon-700 focus:ring-maroon-700 text-sm">
        </div>
        <div>
            <label for="password" class="block text-sm font-semibold text-charcoal-dark mb-1">Kata Sandi</label>
            <input id="password" type="password" name="password" required
                   class="w-full rounded-xl border-cream-border shadow-sm focus:border-maroon-700 focus:ring-maroon-700 text-sm">
        </div>
        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-charcoal-dark mb-1">Konfirmasi Kata Sandi</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                   class="w-full rounded-xl border-cream-border shadow-sm focus:border-maroon-700 focus:ring-maroon-700 text-sm">
        </div>
        <div>
            <label for="role" class="block text-sm font-semibold text-charcoal-dark mb-1">Hak Akses (Peran)</label>
            <select id="role" name="role" required class="w-full rounded-xl border-cream-border shadow-sm focus:border-maroon-700 focus:ring-maroon-700 text-sm">
                <option value="pengguna" @selected(old('role') === 'pengguna')>Pengguna (Civitas Akademika)</option>
                <option value="petugas" @selected(old('role') === 'petugas')>Petugas Fasilitas Kampus</option>
            </select>
        </div>
    </div>
    <div class="flex justify-end space-x-3 pt-2">
        <button type="button" x-on:click="$dispatch('close-modal', 'akun')" class="px-4 py-2 border border-cream-border rounded-xl text-charcoal-medium hover:bg-cream-50 text-sm">Batalkan</button>
        <button type="submit" class="px-4 py-2 bg-maroon-700 text-cream-100 font-semibold rounded-xl hover:brightness-110 text-sm shadow-sm">
            Simpan & Verifikasi Pengguna
        </button>
    </div>
</form>
