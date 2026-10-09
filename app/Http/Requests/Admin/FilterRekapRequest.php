<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

// Form Request filter periode rekap & ekspor (?dari=YYYY-MM-DD&sampai=YYYY-MM-DD&format=csv|excel|pdf).
class FilterRekapRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Admin;
    }

    public function rules(): array
    {
        $aturanSampai = ['nullable', 'date_format:Y-m-d'];

        if ($this->filled('dari')) {
            $aturanSampai[] = 'after_or_equal:dari';
        }

        return [
            'dari'   => ['nullable', 'date_format:Y-m-d'],
            'sampai' => $aturanSampai,
        ];
    }

    public function messages(): array
    {
        return [
            'dari.date_format'      => 'Format tanggal awal harus YYYY-MM-DD.',
            'sampai.date_format'    => 'Format tanggal akhir harus YYYY-MM-DD.',
            'sampai.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal awal.',
        ];
    }
}
