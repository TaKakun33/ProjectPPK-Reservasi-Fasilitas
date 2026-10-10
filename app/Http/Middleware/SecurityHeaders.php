<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Header keamanan dasar untuk semua respons web (anti-clickjacking, anti MIME-sniffing, privasi referrer).
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // CSP minimal yang aman bagi skrip inline Alpine/Blade: mencegah clickjacking modern,
        // injeksi <base>, plugin/objek, dan form yang diarahkan ke domain lain.
        $response->headers->set(
            'Content-Security-Policy',
            "base-uri 'self'; object-src 'none'; frame-ancestors 'self'; form-action 'self'"
        );

        // HSTS hanya dikirim lewat HTTPS agar tidak mengunci lingkungan lokal (http://localhost).
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
