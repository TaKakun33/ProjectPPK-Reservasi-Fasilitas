<header class="pf-card-head" style="border-color:#FECACA;">
    <h2 class="pf-card-title" style="color:#991B1B;">Zona Berbahaya</h2>
    <p class="pf-card-sub">Tindakan di bagian ini tidak dapat dibatalkan.</p>
</header>

<div class="pf-notice pf-notice-danger" style="max-width:34rem;">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0zM12 9v4M12 17h.01"/></svg>
    <span>
        <strong>Hapus akun secara permanen.</strong> Akun Anda tidak dapat digunakan lagi dan semua reservasi aktif (menunggu atau disetujui) akan dibatalkan otomatis.
    </span>
</div>

<div class="pf-actions" style="margin-top:1rem;">
    <button type="button" class="pf-btn pf-btn-danger" x-on:click="$dispatch('open-modal', 'hapus-akun')">Hapus Akun Saya</button>
</div>

<x-dialog-form name="hapus-akun" title="Hapus Akun?"
               subtitle="Masukkan password Anda untuk mengonfirmasi penghapusan akun"
               max-width="lg" :show="$errors->userDeletion->isNotEmpty()">
    <form method="post" action="{{ route('profile.destroy') }}" class="pf-form" style="max-width:none;">
        @csrf
        @method('delete')

        <div class="pf-notice pf-notice-danger">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0zM12 9v4M12 17h.01"/></svg>
            <span>Setelah dihapus, Anda akan keluar dan tidak dapat masuk kembali dengan akun ini.</span>
        </div>

        <div class="pf-field" x-data="{ lihat: false }">
            <label for="password_hapus">Password</label>
            <div class="pf-input-wrap">
                <input id="password_hapus" name="password" :type="lihat ? 'text' : 'password'"
                       class="pf-input {{ $errors->userDeletion->has('password') ? 'has-error' : '' }}"
                       placeholder="Masukkan password Anda" autocomplete="current-password" required>
                <button type="button" class="pf-eye" x-on:click="lihat = !lihat" :aria-label="lihat ? 'Sembunyikan password' : 'Tampilkan password'">
                    <svg x-show="!lihat" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg x-show="lihat" x-cloak viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.9 17.9A10.1 10.1 0 0112 20c-7 0-11-8-11-8a18.5 18.5 0 015.1-5.9M9.9 4.2A9.1 9.1 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.2 3.2M1 1l22 22"/></svg>
                </button>
            </div>
            @foreach((array) $errors->userDeletion->get('password') as $pesan)
                <ul class="pf-error"><li>{{ $pesan }}</li></ul>
            @endforeach
        </div>

        <div class="pf-actions" style="justify-content:flex-end;">
            <button type="button" class="pf-btn pf-btn-ghost" x-on:click="$dispatch('close-modal', 'hapus-akun')">Batal</button>
            <button type="submit" class="pf-btn pf-btn-danger">Ya, Hapus Akun</button>
        </div>
    </form>
</x-dialog-form>
