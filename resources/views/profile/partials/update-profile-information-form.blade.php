<header class="pf-card-head">
    <h2 class="pf-card-title">Informasi Akun</h2>
    <p class="pf-card-sub">Perbarui nama dan alamat email yang terhubung dengan akun Anda.</p>
</header>

<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}" class="pf-form"
      x-data="{
          emailAsli: @js(strtolower($user->email)),
          email: @js(old('email', $user->email)),
          get berubah() { return this.email.trim().toLowerCase() !== this.emailAsli; },
          lihat: false
      }">
    @csrf
    @method('patch')

    <div class="pf-field">
        <label for="name">Nama Lengkap</label>
        <input id="name" name="name" type="text" class="pf-input {{ $errors->has('name') ? 'has-error' : '' }}"
               value="{{ old('name', $user->name) }}" required maxlength="100" autocomplete="name">
        @error('name') <ul class="pf-error"><li>{{ $message }}</li></ul> @enderror
    </div>

    <div class="pf-field">
        <label for="email">Alamat Email</label>
        <input id="email" name="email" type="email" class="pf-input {{ $errors->has('email') ? 'has-error' : '' }}"
               x-model="email" required maxlength="100" autocomplete="username">
        @error('email') <ul class="pf-error"><li>{{ $message }}</li></ul> @enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <p class="pf-hint" style="color:#92400E;">
                Alamat email Anda belum diverifikasi.
                <button form="send-verification" type="submit" style="background:none;border:0;padding:0;font:inherit;font-weight:700;text-decoration:underline;color:#5A121D;cursor:pointer;">Kirim ulang email verifikasi</button>
            </p>
        @endif
    </div>

    {{-- Konfirmasi password hanya muncul saat email diubah --}}
    <div class="pf-reveal" x-show="berubah || {{ $errors->has('current_password') ? 'true' : 'false' }}"
         @if(! $errors->has('current_password')) x-cloak style="display:none" @endif
         x-transition.opacity>
        <div class="pf-notice pf-notice-info" style="margin-bottom:.9rem;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
            <span>Anda mengganti alamat email. Demi keamanan, masukkan password akun Anda untuk mengonfirmasi perubahan ini.</span>
        </div>
        <div class="pf-field">
            <label for="current_password">Konfirmasi dengan Password</label>
            <div class="pf-input-wrap">
                <input id="current_password" name="current_password" :type="lihat ? 'text' : 'password'"
                       class="pf-input {{ $errors->has('current_password') ? 'has-error' : '' }}"
                       :required="berubah" autocomplete="current-password">
                <button type="button" class="pf-eye" x-on:click="lihat = !lihat" :aria-label="lihat ? 'Sembunyikan password' : 'Tampilkan password'">
                    <svg x-show="!lihat" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg x-show="lihat" x-cloak viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.9 17.9A10.1 10.1 0 0112 20c-7 0-11-8-11-8a18.5 18.5 0 015.1-5.9M9.9 4.2A9.1 9.1 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.2 3.2M1 1l22 22"/></svg>
                </button>
            </div>
            @error('current_password') <ul class="pf-error"><li>{{ $message }}</li></ul> @enderror
        </div>
    </div>

    <div class="pf-actions">
        <button type="submit" class="pf-btn pf-btn-primary">Simpan Perubahan</button>
    </div>
</form>
