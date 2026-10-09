<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use App\Services\ReservationAvailability;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

// Form Request untuk pengajuan reservasi (E3, E4, E9): seluruh validasi server dikumpulkan di sini.
class SimpanReservasiRequest extends FormRequest
{
    // Batas durasi satu reservasi (menit) dan horizon tanggal pemesanan (hari) agar slot tidak di-hoard
    public const MAKS_DURASI_MENIT = 240;
    public const MAKS_HARI_KEDEPAN = 60;

    // Hanya role pengguna yang boleh mengajukan reservasi. Dicek SEBELUM validasi dijalankan,
    // sehingga admin/petugas langsung mendapat 403 dan tidak bisa memakai pesan validasi
    // (mis. rule exists pada id_fasilitas) sebagai oracle untuk menebak ID fasilitas.
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Pengguna;
    }

    protected function failedAuthorization(): void
    {
        throw new AuthorizationException('Reservasi hanya dapat diajukan oleh pengguna.');
    }

    public function rules(): array
    {
        return [
            'id_fasilitas' => [
                'required',
                'uuid',
                // Hanya fasilitas aktif & belum di-soft-delete yang boleh direservasi
                Rule::exists('facilities', 'id_fasilitas')
                    ->where('facility_status', 'aktif')
                    ->whereNull('deleted_at'),
            ],
            // date_format ketat: menolak string seperti "next monday" yang lolos aturan 'date'
            'date' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:today',
                'before_or_equal:' . now(config('app.timezone'))->addDays(self::MAKS_HARI_KEDEPAN)->toDateString(),
            ],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time'   => ['required', 'date_format:H:i', 'after:start_time'],
            'purpose'    => ['required', 'string', 'min:5', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_fasilitas.exists'     => 'Fasilitas tidak tersedia untuk reservasi (nonaktif, dalam perbaikan, atau tidak ditemukan).',
            'date.date_format'        => 'Format tanggal tidak valid (gunakan YYYY-MM-DD).',
            'date.after_or_equal'     => 'Tanggal reservasi tidak boleh di masa lalu.',
            'date.before_or_equal'    => 'Reservasi hanya dapat diajukan maksimal ' . self::MAKS_HARI_KEDEPAN . ' hari ke depan.',
            'end_time.after'          => 'Jam selesai harus lebih akhir dari jam mulai.',
            'purpose.required'        => 'Tujuan penggunaan fasilitas wajib diisi.',
            'purpose.min'             => 'Tujuan penggunaan minimal :min karakter.',
        ];
    }

    // Validasi lintas-field: jam operasional, kelipatan 30 menit, durasi, buffer 1 jam
    public function after(): array
    {
        return [function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $zona    = config('app.timezone', 'Asia/Jakarta');
            $mulaiStr   = $this->input('start_time') . ':00';
            $selesaiStr = $this->input('end_time') . ':00';

            if (! ReservationAvailability::isValidSlotTime($mulaiStr)
                || ! ReservationAvailability::isValidSlotTime($selesaiStr)) {
                $validator->errors()->add('time', 'Jam harus di antara 07:00 - 20:00 dan dalam kelipatan 30 menit (contoh: 08:00, 08:30).');
                return;
            }

            $mulai   = Carbon::createFromFormat('Y-m-d H:i', $this->input('date') . ' ' . $this->input('start_time'), $zona);
            $selesai = Carbon::createFromFormat('Y-m-d H:i', $this->input('date') . ' ' . $this->input('end_time'), $zona);

            if ($mulai->diffInMinutes($selesai) > self::MAKS_DURASI_MENIT) {
                $validator->errors()->add('time', 'Durasi maksimal satu reservasi adalah ' . (self::MAKS_DURASI_MENIT / 60) . ' jam.');
            }

            if ($mulai->lt(now($zona)->addHour())) {
                $validator->errors()->add('time', 'Reservasi harus diajukan minimal 1 jam sebelum waktu kegiatan dimulai (terdapat buffer waktu 1 jam).');
            }
        }];
    }
}
