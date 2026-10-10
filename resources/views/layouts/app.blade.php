<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('layouts.partials.head')
        @include('layouts.partials.mobile-pengguna')
        @include('layouts.partials.session-guard')

        @guest
            <script>
                sessionStorage.removeItem('tab_session_active');
            </script>
        @endguest
    </head>
    <body class="antialiased" style="background:#FAF6F0; font-family:'Plus Jakarta Sans', sans-serif; color:#252B2B;">
        <div class="min-h-screen" style="background:#FAF6F0;">
            @include('layouts.navigation')

            {{-- Page Heading : cream + border maroon --}}
            @isset($header)
                <header class="mp-header bg-white shadow-sm border-b-4 border-maroon-800">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 text-maroon-800">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            {{-- Page Content --}}
            <main>
                {{ $slot }}
            </main>
        </div>

        <x-popup :validasi="true" />
    </body>
</html>

