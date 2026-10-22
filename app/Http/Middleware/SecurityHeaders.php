<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 1. X-Frame-Options — mencegah clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // 2. X-Content-Type-Options — mencegah MIME sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // 3. Referrer-Policy — kontrol info referrer
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // 4. Permissions-Policy — batasi akses fitur browser
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // 5. Content-Security-Policy — cegah XSS
        $response->headers->set('Content-Security-Policy', implode('; ', [
            "default-src 'self'",

            // Script: Google, Maps, CDN umum
            // ⭐ TAMBAH 'blob:' → untuk Livewire/Filament worker (preview file)
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' blob: " .
                "https://www.google.com " .
                "https://www.gstatic.com " .
                "https://maps.googleapis.com " .
                "https://www.googletagmanager.com " .
                "https://www.google-analytics.com " .
                "https://cdn.jsdelivr.net " .
                "https://unpkg.com " .
                "https://cdn.tailwindcss.com",

            // Style
            // ⭐ TAMBAH fonts.bunny.net
            "style-src 'self' 'unsafe-inline' " .
                "https://fonts.googleapis.com " .
                "https://fonts.bunny.net " .
                "https://cdn.jsdelivr.net " .
                "https://cdnjs.cloudflare.com",

            // Font
            // ⭐ TAMBAH fonts.bunny.net
            "font-src 'self' data: " .
                "https://fonts.gstatic.com " .
                "https://fonts.bunny.net " .
                "https://cdn.jsdelivr.net",

            // Image
            "img-src 'self' data: blob: https:",

            // ⭐ WORKER-SRC (BARIS BARU) → wajib untuk Livewire file preview
            // Tanpa ini, upload preview akan gray karena worker diblokir
            "worker-src 'self' blob:",

            // Connect (API calls, AJAX)
            // ⭐ TAMBAH ws: wss: → untuk Livewire WebSocket / dev HMR
            "connect-src 'self' ws: wss: " .
                "https://www.google-analytics.com " .
                "https://www.googletagmanager.com " .
                "https:",

            // Frame: Google Maps, YouTube, Vimeo
            "frame-src 'self' " .
                "https://www.google.com " .
                "https://maps.google.com " .
                "https://www.youtube.com " .
                "https://youtube.com " .
                "https://www.youtube-nocookie.com " .
                "https://player.vimeo.com " .
                "https://www.google.com/maps",

            // Object
            "object-src 'none'",

            // Base URI
            "base-uri 'self'",

            // Form
            "form-action 'self'",
        ]));

        // 6. Cross-Origin-Opener-Policy — isolasi origin
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');

        // 7. Cross-Origin-Resource-Policy — kontrol sharing resource
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');

        // 8. Strict-Transport-Security — hanya aktif di HTTPS
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // 9. Hapus header yang bikin fingerprint
        $response->headers->remove('X-Powered-By');
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        return $response;
    }
}