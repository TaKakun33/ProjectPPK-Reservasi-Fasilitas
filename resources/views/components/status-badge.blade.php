@props(['status'])

@php
    // Peta status ke label Indonesia dan warna. Mencakup status reservasi dan status laporan kerusakan
    $peta = [
        // Status reservasi
        'pending'   => ['label' => 'Menunggu',   'kelas' => 'bg-amber-100 text-amber-800 border-amber-200'],
        'approved'  => ['label' => 'Disetujui',  'kelas' => 'bg-green-100 text-green-800 border-green-200'],
        'rejected'  => ['label' => 'Ditolak',    'kelas' => 'bg-red-100 text-red-800 border-red-200'],
        'cancelled' => ['label' => 'Dibatalkan', 'kelas' => 'bg-gray-100 text-gray-700 border-gray-200'],
        // Status laporan kerusakan
        'baru'      => ['label' => 'Baru',       'kelas' => 'bg-red-100 text-red-800 border-red-200'],
        'diproses'  => ['label' => 'Diproses',   'kelas' => 'bg-amber-100 text-amber-800 border-amber-200'],
        'selesai'   => ['label' => 'Selesai',    'kelas' => 'bg-green-100 text-green-800 border-green-200'],
        'ditolak'   => ['label' => 'Ditolak',    'kelas' => 'bg-gray-100 text-gray-700 border-gray-200'],
    ];

    $item = $peta[$status] ?? ['label' => ucfirst((string) $status), 'kelas' => 'bg-gray-100 text-gray-700 border-gray-200'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold ' . $item['kelas']]) }}>
    {{ $item['label'] }}
</span>
