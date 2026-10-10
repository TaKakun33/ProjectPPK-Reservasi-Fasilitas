<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    {{-- Session Status --}}
    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        {{-- Email Address --}}
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <button type="submit"
                    class="w-full inline-flex items-center justify-center py-2.5 px-4 rounded-xl font-extrabold text-sm shadow-md transition duration-150 bg-maroon-700 text-cream border border-maroon-800 hover:bg-maroon-900">
                {{ __('Kirim Link Reset Password') }}
            </button>
        </div>

        <div class="mt-4 pt-3 border-t border-slate-100 text-center text-xs sm:text-sm text-[#4C4F54]">
            <a href="{{ route('login') }}" class="font-bold text-[#8F0B13] hover:text-[#380F17] hover:underline">&larr; Kembali ke Login</a>
        </div>
    </form>
</x-guest-layout>
