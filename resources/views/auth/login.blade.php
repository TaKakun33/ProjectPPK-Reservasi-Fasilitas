<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-extrabold text-maroon-800">Selamat Datang Kembali</h1>
        <p class="mt-1 text-sm text-slate-500">Masuk untuk mengajukan reservasi fasilitas akademik.</p>
    </div>

    {{-- Session Status --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" onsubmit="sessionStorage.setItem('tab_session_active', '1');">
        @csrf

        {{-- Email Address --}}
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Remember Me --}}
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-maroon-700 shadow-sm focus:ring-maroon-700" name="remember">
                <span class="ms-2 text-sm text-slate-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="mt-6">
            <button type="submit"
                    class="w-full inline-flex items-center justify-center py-2.5 px-4 rounded-xl font-extrabold text-sm shadow-md transition duration-150"
                    style="background: #8F0B13; color: #EFDFC5; border: 1px solid #70090F;"
                    onmouseover="this.style.background='#380F17';"
                    onmouseout="this.style.background='#8F0B13';">
                {{ __('Masuk Sekarang') }}
            </button>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-100 text-center text-sm text-slate-600">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-bold text-[#8F0B13] hover:text-[#380F17] hover:underline">Daftar di sini</a>
        </div>
    </form>
</x-guest-layout>

