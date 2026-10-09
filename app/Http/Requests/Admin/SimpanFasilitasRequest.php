<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use App\Models\Facility;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        ];
    }

    public function messages(): array
    {
        return [
            'facility_name.unique' => 'Nama fasilitas sudah dipakai fasilitas lain.',
        ];
    }
}
