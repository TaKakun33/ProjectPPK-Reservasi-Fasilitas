<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    // Batas percobaan per kombinasi email+IP, per IP (credential stuffing dengan banyak email),
    // dan per email (serangan terdistribusi dari banyak IP). Per email sengaja lebih longgar
    // supaya orang lain tidak mudah mengunci akun korban.
    private const MAKS_PER_EMAIL_IP = 5;
    private const MAKS_PER_IP       = 20;
    private const MAKS_PER_EMAIL    = 30;

    /**
     * Autentikasi kredensial TANPA membuat sesi lebih dulu.
     *
     * Auth::attempt() langsung login dan mencatat sesi/cookie remember, baru status akun
     * dicek setelahnya. Di sini kredensial dan status akun diperiksa dulu, dan
     * login() baru dipanggil jika semuanya lolos.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $guard    = Auth::guard('web');
        $provider = $guard->getProvider();

        $user = $provider->retrieveByCredentials($this->only('email'));

        if (! $user || ! $provider->validateCredentials($user, $this->only('password'))) {
            $this->hitRateLimiters();

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Kata sandi benar, tetapi akun belum boleh dipakai: tolak SEBELUM sesi dibuat
        if ($user->account_status !== 'verified') {
            throw ValidationException::withMessages([
                'email' => match ($user->account_status) {
                    'pending'   => 'Akun Anda belum diverifikasi admin.',
                    'rejected'  => 'Pendaftaran akun Anda ditolak admin.',
                    'suspended' => 'Akun Anda telah dibekukan oleh admin.',
                    default     => 'Akun Anda belum diverifikasi admin atau telah ditolak.',
                },
            ]);
        }

        $guard->login($user, $this->boolean('remember'));

        // Hanya counter email+IP yang dihapus. Counter per-IP dan per-email tidak ikut direset,
        // kalau tidak penyerang bisa menyisipkan login sah miliknya untuk mengosongkan hitungan.
        RateLimiter::clear($this->throttleKey());
    }

    private function hitRateLimiters(): void
    {
        RateLimiter::hit($this->throttleKey());
        RateLimiter::hit($this->throttleKeyIp());
        RateLimiter::hit($this->throttleKeyEmail());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        $batas = [
            $this->throttleKey()      => self::MAKS_PER_EMAIL_IP,
            $this->throttleKeyIp()    => self::MAKS_PER_IP,
            $this->throttleKeyEmail() => self::MAKS_PER_EMAIL,
        ];

        $terkunci = null;

        foreach ($batas as $kunci => $maks) {
            if (RateLimiter::tooManyAttempts($kunci, $maks)) {
                $terkunci = $kunci;
                break;
            }
        }

        if ($terkunci === null) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($terkunci);

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }

    // Kunci per IP saja (semua email dari IP yang sama)
    public function throttleKeyIp(): string
    {
        return 'login-ip|'.$this->ip();
    }

    // Kunci per email saja (semua IP untuk email yang sama)
    public function throttleKeyEmail(): string
    {
        return 'login-email|'.Str::transliterate(Str::lower($this->string('email')));
    }
}
