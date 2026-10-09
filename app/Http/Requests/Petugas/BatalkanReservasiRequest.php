<?php

namespace App\Http\Requests\Petugas;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

// Form Request pembatalan reservasi (approved) oleh petugas (Petugas\ReservationController@cancel).
class BatalkanReservasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Petugas;
    }

    public function rules(): array
    {
        // Wajib mencantumkan alasan pembatalan (User Story #10)
        return [
            'cancellation_reason' => ['required', 'string', 'max:500'],
        ];
    }
}
