<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-maroon-800 leading-tight">
            {{ __('Detail Reservasi') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        @include('reservations.partials.detail', ['modal' => false])
    </div>
</x-app-layout>

