{{--
    Pop-up sunting fasilitas (admin). Isinya dimuat lewat AJAX:
    $dispatch('buka-ajax', { name: 'sunting', url: '/admin/fasilitas/{id}/edit' })
    Bila validasi update gagal, formulir dirender ulang di sini supaya pop-up terbuka kembali dengan pesan error.
--}}
@php
    $htmlAwal = null;
    if ($errors->any() && old('_modal') === 'sunting' && old('_id')) {
        $fasilitas = \App\Models\Facility::with('photos')->find(old('_id'));
        if ($fasilitas) {
            $htmlAwal = view('admin.facilities.partials.form-sunting', ['fasilitas' => $fasilitas, 'modal' => true]
                + app(\App\Http\Controllers\Admin\FacilityController::class)->opsiIsian())->render();
        }
    }
@endphp

<x-modal-ajax name="sunting" title="Perbaruan Data Fasilitas Kampus"
              subtitle="Ubah data dan status operasional fasilitas"
              max-width="3xl" :html-awal="$htmlAwal" />
