<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="text-lg font-semibold">{{ __('Selamat datang, Admin!') }}</p>
                    <p>
                        TODO(Abhista): ganti isi ini jadi rekap ringkas lintas fasilitas
                        (jumlah reservasi pending, laporan belum diproses, dll).
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
