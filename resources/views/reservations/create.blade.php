<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-maroon-800 leading-tight">
            {{ __('Ajukan Reservasi Fasilitas') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-[#EAE0D3] border-t-4 border-t-maroon-800">

            @include('reservations.partials.form', ['modal' => false])
        </div>
    </div>
</x-app-layout>
