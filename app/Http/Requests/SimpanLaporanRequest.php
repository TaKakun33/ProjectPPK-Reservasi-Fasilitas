<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Form Request pengajuan laporan kerusakan oleh pengguna (ReportController@store).
class SimpanLaporanRequest extends FormRequest
{
    // Hanya role pengguna yang boleh melapor. Dicek SEBELUM validasi, sehingga admin/petugas
    // tidak bisa memakai pesan validasi (rule exists) sebagai oracle untuk menebak ID fasilitas/kategori.
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Pengguna;
    }

    public function rules(): array
    {
        return [
            // Hanya fasilitas yang tampil (tidak nonaktif & belum soft-deleted)
            'id_fasilitas' => ['required', 'uuid',
                Rule::exists('facilities', 'id_fasilitas')
                    ->where('facility_status', '!=', 'nonaktif')
                    ->whereNull('deleted_at')],
            'id_kategori'  => ['required', 'uuid',
                Rule::exists('report_categories', 'id_kategori')->where('is_active', true)],
            'description'  => ['required', 'string', 'min:10', 'max:2000'],
            'photos'       => ['nullable', 'array', 'max:5'],
            // Batas dimensi mencegah gambar "decompression bomb" (file kecil, piksel raksasa)
            'photos.*'     => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048', 'dimensions:max_width=6000,max_height=6000'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_fasilitas.exists'     => 'Fasilitas yang dipilih tidak valid atau sudah dinonaktifkan.',
            'id_kategori.exists'      => 'Kategori laporan tidak valid.',
            'description.min'         => 'Deskripsi minimal :min karakter agar petugas memahami masalahnya.',
            'photos.*.max'            => 'Ukuran tiap foto maksimal 2 MB.',
            'photos.*.image'          => 'Berkas harus berupa gambar.',
            'photos.*.mimes'          => 'Foto harus berformat jpeg, png, jpg, gif, atau webp.',
            'photos.*.dimensions'     => 'Dimensi foto maksimal 6000 x 6000 piksel.',
        ];
    }
}
