<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use App\Models\Facility;
use App\Models\FacilityPhoto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

// Form Request tambah & edit fasilitas (dipakai Admin\FacilityController@store dan @update).
class SimpanFasilitasRequest extends FormRequest
{
    // Defense in depth: route sudah dilindungi middleware role:admin, ini lapisan kedua.
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Admin;
    }

    public function rules(): array
    {
        // Nama fasilitas harus unik di antara fasilitas yang belum dihapus
        $unikNama = Rule::unique('facilities', 'facility_name')->whereNull('deleted_at');

        // Pada route edit, parameter {fasilitas} sudah di-bind menjadi model Facility
        $fasilitas = $this->route('fasilitas');

        if ($fasilitas instanceof Facility) {
            $unikNama->ignore($fasilitas->id_fasilitas, 'id_fasilitas');
        }

        return [
            'facility_name' => ['required', 'string', 'max:100', $unikNama],
            'type'          => ['required', 'string', 'max:50'],
            'location'      => ['required', 'string', 'max:150'],
            'capacity'      => ['required', 'integer', 'min:1', 'max:100000'],
            'description'   => ['nullable', 'string', 'max:2000'],

            // Fasilitas & Sarana Penunjang: daftar teks pendek (mis. "Wi-Fi Cepat", "Proyektor")
            'amenities'     => ['nullable', 'array', 'max:12'],
            'amenities.*'   => ['string', 'max:60'],

            // Foto baru (maks. 5 berkas sekali kirim; total per fasilitas dicek di withValidator)
            'photos'          => ['nullable', 'array', 'max:' . FacilityPhoto::MAKS_PER_FASILITAS],
            'photos.*'        => ['image', 'mimes:jpeg,png,jpg,webp', 'max:2048', 'dimensions:max_width=6000,max_height=6000'],
            // Foto lama yang akan dihapus & foto yang dijadikan foto utama (hanya saat edit)
            'remove_photos'   => ['nullable', 'array'],
            'remove_photos.*' => ['uuid'],
            'cover_photo'     => ['nullable', 'uuid'],
        ];
    }

    // Total foto (yang sudah ada - yang dihapus + yang baru) tidak boleh melebihi batas
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $fasilitas = $this->route('fasilitas');
            $ada = 0;
            $hapus = 0;

            if ($fasilitas instanceof Facility) {
                $ada = $fasilitas->photos()->count();

                $idHapus = array_values(array_filter((array) $this->input('remove_photos', []), fn ($id) => is_string($id) && Str::isUuid($id)));
                $hapus = $idHapus ? $fasilitas->photos()->whereIn('id_foto', $idHapus)->count() : 0;
            }

            $baru = count((array) $this->file('photos', []));

            if ($ada - $hapus + $baru > FacilityPhoto::MAKS_PER_FASILITAS) {
                $v->errors()->add('photos', 'Total foto per fasilitas maksimal ' . FacilityPhoto::MAKS_PER_FASILITAS . '. Hapus foto lama terlebih dahulu atau kurangi foto yang diunggah.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'facility_name.unique' => 'Nama fasilitas sudah dipakai fasilitas lain.',
            'amenities.max'        => 'Maksimal 12 item Fasilitas & Sarana Penunjang.',
            'amenities.*.max'      => 'Tiap item Fasilitas & Sarana Penunjang maksimal 60 karakter.',
            'amenities.*.string'   => 'Item Fasilitas & Sarana Penunjang harus berupa teks.',
            'photos.max'           => 'Maksimal ' . FacilityPhoto::MAKS_PER_FASILITAS . ' foto per fasilitas.',
            'photos.*.image'       => 'Berkas harus berupa gambar.',
            'photos.*.mimes'       => 'Foto harus berformat jpeg, png, jpg, atau webp.',
            'photos.*.max'         => 'Ukuran tiap foto maksimal 2 MB.',
            'photos.*.uploaded'    => 'Foto gagal diunggah. Pastikan ukuran tiap foto maksimal 2 MB.',
            'photos.*.dimensions'  => 'Dimensi foto maksimal 6000 x 6000 piksel.',
        ];
    }
}
