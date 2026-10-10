{{-- Layout Admin: tampilan memakai kerangka bersama (x-portal-layout), hanya menu yang khusus admin. --}}
@php
    $adminNav = [
        [
            'route' => 'admin.dashboard',
            'match' => 'admin.dashboard',
            'icon'  => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
            'label' => 'Dashboard',
        ],
        [
            'route' => 'admin.fasilitas.index',
            'match' => 'admin.fasilitas.*',
            'icon'  => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
            'label' => 'Fasilitas',
        ],
        [
            'route' => 'admin.users.index',
            'match' => 'admin.users.*',
            'icon'  => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
            'label' => 'Data Pengguna',
            'badge' => (isset($adminPendingCount) && $adminPendingCount > 0) ? $adminPendingCount : null,
        ],
        [
            'route' => 'admin.rekap.index',
            'match' => 'admin.rekap.*',
            'icon'  => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'label' => 'Rekapitulasi Data',
        ],
    ];
@endphp

<x-portal-layout judul="Admin" portal="Portal Administrasi" home-route="admin.dashboard" :nav="$adminNav">
    @isset($header)
        <x-slot name="header">{{ $header }}</x-slot>
    @endisset

    {{ $slot }}
</x-portal-layout>
