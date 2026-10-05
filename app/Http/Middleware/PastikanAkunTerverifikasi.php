<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

// PERBAIKAN E2: status akun sebelumnya hanya dicek saat login. Akun yang di-suspend/ditolak
// admin tetap bisa memakai sesi lama. Middleware ini mengecek ulang di setiap request.
class PastikanAkunTerverifikasi
{
    public function handle(Request $request, Closure $next): Response
    {
        $pengguna = $request->user();

        if ($pengguna && $pengguna->account_status !== 'verified') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Akun Anda tidak aktif atau belum diverifikasi. Hubungi admin.']);
        }

        return $next($request);
    }
}
