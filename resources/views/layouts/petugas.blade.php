{{-- Layout Petugas: tampilan memakai kerangka bersama (x-portal-layout), hanya menu yang khusus petugas. --}}
@php
    $petugasNav = [
        [
            'route' => 'petugas.dashboard',
            'match' => 'petugas.dashboard',
            'icon'  => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
            'label' => 'Dashboard Petugas',
        ],
        [
            'route' => 'petugas.reservations.index',
            'match' => 'petugas.reservations.*',
            'icon'  => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
            'label' => 'Antrian Reservasi',
        ],
        [
            'route' => 'petugas.reports.index',
            'match' => 'petugas.reports.*',
            'icon'  => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z',
            'label' => 'Laporan Kerusakan',
        ],
    ];
@endphp

<x-portal-layout judul="Petugas" portal="Portal Petugas" home-route="petugas.dashboard" :nav="$petugasNav">
    @isset($header)
        <x-slot name="header">{{ $header }}</x-slot>
    @endisset

    {{ $slot }}
</x-portal-layout>
