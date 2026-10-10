{{-- Bagian <head> bersama untuk semua layout. Parameter opsional: $judul (mis. 'Admin') --}}
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ isset($judul) ? $judul . ' – ' : '' }}{{ config('app.name', 'SyncSpace') }}</title>

{{-- Font: Plus Jakarta Sans --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

{{-- Aset Vite --}}
@vite(['resources/css/app.css', 'resources/js/app.js'])
