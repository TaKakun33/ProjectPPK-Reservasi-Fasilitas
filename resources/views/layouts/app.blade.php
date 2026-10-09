<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        {{-- Fonts: Plus Jakarta Sans --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        {{-- Scripts --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @auth
            <script>
                (function() {
                    @if(session('just_logged_in'))
                        sessionStorage.setItem('tab_session_active', '1');
                    @endif

                    if (!sessionStorage.getItem('tab_session_active')) {
                        // Jika tab baru dibuka terpisah (setelah tab sebelumnya diclose),
                        // otomatis logout dan arahkan kembali ke home daftar fasilitas.
                        fetch("{{ route('logout') }}", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Content-Type": "application/json",
                                "Accept": "application/json"
                            }
                        }).finally(function() {
                            window.location.replace("{{ route('welcome') }}");
                        });
                    } else {
                        sessionStorage.setItem('tab_session_active', '1');
                    }
                })();
            </script>
        @endauth
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
                <header class="bg-white shadow-sm border-b-4 border-maroon-800">
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
    </body>
</html>

