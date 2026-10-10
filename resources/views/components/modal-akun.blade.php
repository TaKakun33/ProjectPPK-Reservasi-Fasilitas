{{-- Pop-up pendaftaran akun baru oleh admin. Buka dengan: $dispatch('open-modal', 'akun') --}}
<x-dialog-form name="akun" title="Pendaftaran Pengguna Baru"
               subtitle="Akun yang dibuat admin langsung berstatus terverifikasi"
               max-width="xl"
               :show="request('create') === '1' || ($errors->any() && old('_modal') === 'akun')">
    @include('admin.users.partials.form-tambah')
</x-dialog-form>
