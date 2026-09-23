<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        {{-- Fonts --}}
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

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
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            {{-- Page Heading --}}
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
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
