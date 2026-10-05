<?php

namespace App\Http\Requests\Petugas;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

// Form Request perubahan status laporan oleh petugas (Petugas\ReportController@updateStatus).
class UbahStatusLaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Petugas;
    }

    public function rules(): array
    {
        // Catatan resolusi WAJIB saat laporan ditutup ('selesai' atau 'ditolak'); untuk 'diproses' opsional.
        return [
            'report_status'     => ['required', 'in:diproses,selesai,ditolak'],
            // Keputusan petugas: apakah penanganan ini menutup fasilitas (dalam perbaikan)?
            'menutup_fasilitas' => ['nullable', 'boolean'],
            'resolution_notes'  => ['nullable', 'string', 'max:1000', 'required_if:report_status,selesai,ditolak'],
        ];
    }

    public function messages(): array
    {
        return [
            'resolution_notes.required_if' => 'Catatan resolusi wajib diisi saat laporan ditutup (selesai atau ditolak).',
        ];
    }
}
