<x-admin-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-extrabold text-lg leading-tight" style="color:#252B2B;">
                {{ __('Pendaftaran Fasilitas Kampus Baru') }}
            </h2>
            <p class="text-xs mt-0.5" style="color:#4C4F54;">Lengkapi data fasilitas yang akan dibuka untuk reservasi</p>
        </div>
    </x-slot>

    <div class="px-4 sm:px-6 py-5 max-w-3xl mx-auto">
        <div class="bg-white p-8 rounded-xl shadow-xs border border-cream-border">

            @include('admin.facilities.partials.form-tambah', ['modal' => false])
        </div>
    </div>
</x-admin-layout>
