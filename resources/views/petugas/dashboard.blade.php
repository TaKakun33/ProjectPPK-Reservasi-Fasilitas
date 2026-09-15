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
                    <p class="text-lg font-semibold">{{ __('Selamat datang, Petugas!') }}</p>
                    @php
                        <!-- Jumlah reservasi yang statusnya masih 'pending'(menunggu diproses) -->
                        $pendingReservations = \App\Models\Reservation::where('reservation_status', 'pending')->count();
                        <!-- Jumlah laporan yang statusnya masih 'baru'(belum diproses) -->
                        $newReports = \App\Models\Report::where('report_status', 'baru')->count();
                    @endphp

                    <!-- reservasi pending dan laporan baru -->
                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <!-- kartu reservasi pending -->
                        <div class="rounded-lg border border-yellow-300 bg-yellow-100 p-4">
                            <p class="text-sm text-gray-600">{{ __('Reservasi Pending') }}</p>
                            <p class="text-3xl font-bold text-yellow-700">{{ $pendingReservations }}</p>
                        </div>

                        <!-- kartu laporan belum diproses -->
                        <div class="rounded-lg border border-red-300 bg-red-100 p-4">
                            <p class="text-sm text-gray-600">{{ __('Laporan Belum Diproses') }}</p>
                            <p class="text-3xl font-bold text-red-700">{{ $newReports }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
