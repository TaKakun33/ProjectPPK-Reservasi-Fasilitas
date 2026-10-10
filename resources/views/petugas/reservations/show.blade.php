<x-petugas-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-extrabold text-lg leading-tight" style="color:#252B2B;">Detail Reservasi</h2>
                <p class="text-xs mt-0.5" style="color:#4C4F54;">Periksa data pemohon dan jadwal sebelum menyetujui atau menolak</p>
            </div>
            <a href="{{ route('petugas.reservations.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-xs hover:bg-[#FAF6F0]"
               style="background:white; color:#380F17; border:1px solid #EAE0D3;">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="px-4 sm:px-6 py-5 max-w-4xl mx-auto space-y-5" style="background:#FAF6F0;">
        @include('petugas.reservations.partials.detail', ['modal' => false])
    </div>

    @include('petugas.reservations.partials.modal-aksi')
</x-petugas-layout>
