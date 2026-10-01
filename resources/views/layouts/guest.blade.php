<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        {{-- Fonts --}}
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        {{-- Scripts --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-maroon-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-cream-100-100 px-4">
            <div class="flex flex-col items-center text-center">
                <a href="/" class="flex flex-col items-center gap-3">
                    <x-application-logo class="w-16 h-16 drop-shadow-md" />
                    <span>
                        <span class="block text-lg font-extrabold tracking-wide text-maroon-800">RESERVASI FASILITAS KAMPUS</span>
                        <span class="block text-xs font-semibold tracking-[0.2em] uppercase text-maroon-700">Akademik &bull; Terpercaya &bull; Terorganisir</span>
                    </span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-white shadow-md overflow-hidden sm:rounded-xl border-t-4 border-maroon-800">
                {{ $slot }}
            </div>

            <p class="mt-6 text-xs text-cream-400">Ruang kelas &bull; Auditorium &bull; Laboratorium &bull; Lapangan</p>
        </div>
    </body>
</html>

