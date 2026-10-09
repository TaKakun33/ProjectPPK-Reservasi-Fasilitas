<?php

namespace App\Http\Requests\Petugas;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

// Form Request persetujuan reservasi oleh petugas (Petugas\ReservationController@approve).
class SetujuiReservasiRequest extends FormRequest
{
    // Defense in depth: route sudah dilindungi middleware role:petugas
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Petugas;
    }

    public function rules(): array
    {
        return [
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
