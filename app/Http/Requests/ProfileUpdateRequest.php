<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Batas 100 karakter disamakan dengan kolom varchar(100) di tabel users
            'name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:100',
                // PERBAIKAN E1: primary key tabel users adalah id_user, bukan id.
                // Sebelumnya ->ignore($this->user()->id) bernilai null sehingga email
                // milik sendiri dianggap "sudah dipakai" dan profil tidak bisa disimpan.
                Rule::unique(User::class, 'email')->ignore($this->user()->id_user, 'id_user'),
            ],
            // Ganti email = pintu pengambilalihan akun (lewat reset password), jadi wajib konfirmasi
            // password saat ini. Bila email tidak berubah, kolom ini boleh kosong.
            'current_password' => [
                'nullable',
                Rule::requiredIf(fn () => Str::lower((string) $this->input('email')) !== Str::lower((string) $this->user()->email)),
                'current_password',
            ],
        ];
    }
}
