<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Cek ekstensi file
        $path = $request->path();
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $staticExtensions = [
            'css', 'js', 'mjs',
            'png', 'jpg', 'jpeg', 'gif', 'svg', 'webp', 'ico', 'bmp',
            'woff', 'woff2', 'ttf', 'eot', 'otf',
            'mp4', 'webm', 'mp3', 'wav',
        ];

        if (in_array($extension, $staticExtensions, true)) {
            // ⭐ Cache 1 tahun (31536000 detik)
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');

            // Hapus header yang tidak perlu
            $response->headers->remove('Pragma');
            $response->headers->remove('Cookie');
        }

        return $response;
    }
}