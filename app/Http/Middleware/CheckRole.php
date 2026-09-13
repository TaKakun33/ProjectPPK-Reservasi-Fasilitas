<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * Usage in routes: ->middleware(['auth', 'role:admin'])
     * or multiple roles: ->middleware(['auth', 'role:admin,petugas'])
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // $roles datang sebagai string dari definisi route (mis. 'admin', 'petugas'),
        // sedangkan $user->role sekarang berupa enum UserRole (lihat User::casts()).
        // Jadi bandingkan nilai string-nya (->value), bukan objek enum-nya langsung —
        // in_array(objek_enum, [string], true) tidak akan pernah cocok.
        if (! $user || ! in_array($user->role->value, $roles, true)) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}