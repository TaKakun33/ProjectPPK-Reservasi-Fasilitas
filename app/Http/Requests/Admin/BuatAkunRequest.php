<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

// Form Request pendaftaran akun petugas/pengguna langsung oleh admin (Admin\UserController@store).
class BuatAkunRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Admin;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:100', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
            // Admin baru tidak bisa dibuat lewat form ini (mencegah eskalasi hak akses)
            'role'     => ['required', 'in:petugas,pengguna'],
        ];
    }
}
