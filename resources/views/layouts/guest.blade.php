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
    </head>
    <body class="antialiased" style="font-family: 'Plus Jakarta Sans', sans-serif;">
        <div class="min-h-screen flex flex-col sm:justify-center items-center py-8 sm:py-12 px-4 relative overflow-hidden"
             style="background: linear-gradient(135deg, #24050A 0%, #380F17 35%, #590B13 70%, #8F0B13 100%);">
            
            {{-- Decorative Soft Ambient Glows --}}
            <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full blur-3xl pointer-events-none opacity-25" style="background:#EFDFC5;"></div>
            <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full blur-3xl pointer-events-none opacity-20" style="background:#8F0B13;"></div>

            {{-- Brand Logo & Header (Cream text on Maroon) --}}
            <div class="relative z-10 flex flex-col items-center text-center">
                <a href="/" class="flex flex-col items-center gap-2.5 transition hover:opacity-95 group">
                    <x-application-logo class="w-16 h-16 drop-shadow-xl transition-transform duration-200 group-hover:scale-105" />
                    <span>
                        <span class="block text-2xl sm:text-3xl font-black tracking-tight text-[#EFDFC5] drop-shadow-sm">SyncSpace</span>
                        <span class="block text-xs font-semibold tracking-[0.2em] uppercase text-[#EFDFC5]/80 mt-1">Reservasi Fasilitas Kampus</span>
                    </span>
                </a>
            </div>

            {{-- Main Form Card --}}
            <div class="relative z-10 w-full sm:max-w-md mt-6 px-6 py-7 sm:px-8 bg-white shadow-2xl overflow-hidden rounded-2xl border"
                 style="border-color: rgba(239, 223, 197, 0.4); box-shadow: 0 20px 45px -15px rgba(0,0,0,0.5);">
                {{ $slot }}
            </div>

            <p class="relative z-10 mt-6 text-xs text-[#EFDFC5]/75 font-medium tracking-wide text-center">
                Ruang kelas &bull; Auditorium &bull; Laboratorium &bull; Lapangan
            </p>
        </div>
    </body>
</html>

