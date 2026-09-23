<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        if ($request->user()->account_status !== 'verified') {
            $status = $request->user()->account_status;

            Auth::guard('web')->logout();
            $request->session()->invalidate();

            $message = match ($status) {
                'pending'   => 'Akun Anda belum diverifikasi admin.',
                'rejected'  => 'Pendaftaran akun Anda ditolak admin.',
                'suspended' => 'Akun Anda telah dibekukan oleh admin.',
                default     => 'Akun Anda belum diverifikasi admin atau telah ditolak.',
            };

            return back()->withErrors(['email' => $message]);
        }

        $request->session()->regenerate();

        return match ($request->user()->role) {
            UserRole::Admin => redirect()->intended('/admin/dashboard'),
            UserRole::Petugas => redirect()->intended('/petugas/dashboard'),
            default => redirect()->intended(route('facilities.index', absolute: false)),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
