<?php

namespace App\Http\Requests\Petugas;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

// Form Request penolakan reservasi oleh petugas (Petugas\ReservationController@reject).
class TolakReservasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Petugas;
    }

    public function rules(): array
    {
        // Wajib isi alasan agar pengguna tahu kenapa reservasinya ditolak
        return [
            'alasan_ditolak' => ['required', 'string', 'max:500'],
        ];
    }
}
