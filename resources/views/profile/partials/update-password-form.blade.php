<header class="pf-card-head">
    <h2 class="pf-card-title">Keamanan</h2>
    <p class="pf-card-sub">Ganti password secara berkala dan gunakan kombinasi yang panjang serta sulit ditebak.</p>
</header>

<form method="post" action="{{ route('password.update') }}" class="pf-form"
      x-data="{
          lama: false, baru: false, ulang: false,
          pw: '', konfirmasi: '',
          get aturan() {
              return {
                  panjang: this.pw.length >= 8,
                  huruf: /[a-z]/.test(this.pw) && /[A-Z]/.test(this.pw),
                  angka: /[0-9]/.test(this.pw),
                  simbol: /[^A-Za-z0-9]/.test(this.pw)
              };
          },
          get skor() { return Object.values(this.aturan).filter(Boolean).length; },
          get warna() { return ['#EAE0D3', '#DC2626', '#F59E0B', '#84CC16', '#059669'][this.skor]; },
          get label() { return ['', 'Lemah', 'Cukup', 'Baik', 'Kuat'][this.skor]; }
      }">
    @csrf
    @method('put')

    <div class="pf-notice pf-notice-warn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        <span>Setelah password diganti, Anda akan tetap masuk di perangkat ini, sedangkan sesi di perangkat lain akan otomatis keluar.</span>
    </div>

    <div class="pf-field">
        <label for="update_password_current_password">Password Saat Ini</label>
        <div class="pf-input-wrap">
            <input id="update_password_current_password" name="current_password" :type="lama ? 'text' : 'password'"
                   class="pf-input {{ $errors->updatePassword->has('current_password') ? 'has-error' : '' }}"
                   autocomplete="current-password" required>
            <button type="button" class="pf-eye" x-on:click="lama = !lama" :aria-label="lama ? 'Sembunyikan password' : 'Tampilkan password'">
                <svg x-show="!lama" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg x-show="lama" x-cloak viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.9 17.9A10.1 10.1 0 0112 20c-7 0-11-8-11-8a18.5 18.5 0 015.1-5.9M9.9 4.2A9.1 9.1 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.2 3.2M1 1l22 22"/></svg>
            </button>
        </div>
        @foreach((array) $errors->updatePassword->get('current_password') as $pesan)
            <ul class="pf-error"><li>{{ $pesan }}</li></ul>
        @endforeach
    </div>

    <div class="pf-field">
        <label for="update_password_password">Password Baru</label>
        <div class="pf-input-wrap">
            <input id="update_password_password" name="password" :type="baru ? 'text' : 'password'" x-model="pw"
                   class="pf-input {{ $errors->updatePassword->has('password') ? 'has-error' : '' }}"
                   autocomplete="new-password" required>
            <button type="button" class="pf-eye" x-on:click="baru = !baru" :aria-label="baru ? 'Sembunyikan password' : 'Tampilkan password'">
                <svg x-show="!baru" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg x-show="baru" x-cloak viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.9 17.9A10.1 10.1 0 0112 20c-7 0-11-8-11-8a18.5 18.5 0 015.1-5.9M9.9 4.2A9.1 9.1 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.2 3.2M1 1l22 22"/></svg>
            </button>
        </div>
        @foreach((array) $errors->updatePassword->get('password') as $pesan)
            <ul class="pf-error"><li>{{ $pesan }}</li></ul>
        @endforeach

        <div x-show="pw.length > 0" x-cloak style="display:none">
            <div class="pf-meter" aria-hidden="true">
                <template x-for="i in 4" :key="i"><span :style="i <= skor ? 'background:' + warna : ''"></span></template>
            </div>
            <p class="pf-hint">Kekuatan password: <strong x-text="label" :style="'color:' + warna"></strong></p>
            <ul class="pf-rules">
                <li :class="{ ok: aturan.panjang }">Minimal 8 karakter</li>
                <li :class="{ ok: aturan.huruf }">Huruf besar dan kecil</li>
                <li :class="{ ok: aturan.angka }">Mengandung angka</li>
                <li :class="{ ok: aturan.simbol }">Mengandung simbol</li>
            </ul>
        </div>
    </div>

    <div class="pf-field">
        <label for="update_password_password_confirmation">Konfirmasi Password Baru</label>
        <div class="pf-input-wrap">
            <input id="update_password_password_confirmation" name="password_confirmation" :type="ulang ? 'text' : 'password'" x-model="konfirmasi"
                   class="pf-input" autocomplete="new-password" required>
            <button type="button" class="pf-eye" x-on:click="ulang = !ulang" :aria-label="ulang ? 'Sembunyikan password' : 'Tampilkan password'">
                <svg x-show="!ulang" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg x-show="ulang" x-cloak viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.9 17.9A10.1 10.1 0 0112 20c-7 0-11-8-11-8a18.5 18.5 0 015.1-5.9M9.9 4.2A9.1 9.1 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.2 3.2M1 1l22 22"/></svg>
            </button>
        </div>
        <p class="pf-match" x-show="konfirmasi.length > 0" x-cloak style="display:none"
           :style="konfirmasi === pw ? 'color:#047857' : 'color:#B91C1C'"
           x-text="konfirmasi === pw ? 'Password cocok' : 'Password belum cocok'"></p>
        @foreach((array) $errors->updatePassword->get('password_confirmation') as $pesan)
            <ul class="pf-error"><li>{{ $pesan }}</li></ul>
        @endforeach
    </div>

    <div class="pf-actions">
        <button type="submit" class="pf-btn pf-btn-primary">Perbarui Password</button>
    </div>
</form>
