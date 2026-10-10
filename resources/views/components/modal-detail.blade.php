{{--
    Pop-up detail (read-only / aksi) yang dimuat lewat AJAX.
    Pemakaian: <x-modal-detail name="detail-reservasi" title="Detail Reservasi" />
    Buka dengan: $dispatch('buka-ajax', { name: 'detail-reservasi', url: '...' })
--}}
@props(['name', 'title', 'subtitle' => null, 'maxWidth' => '2xl'])

<x-modal-ajax :name="$name" :title="$title" :subtitle="$subtitle" :max-width="$maxWidth" />
