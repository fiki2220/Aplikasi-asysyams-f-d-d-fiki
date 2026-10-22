<?php

namespace App\Features\Reports\Services;

use App\Features\Academic\Models\Semester;
use App\Models\User;

class RaporVerificationService
{
    /**
     * Generate token verifikasi untuk (student, semester).
     * Format plain: "{studentId}:{semesterId}:{signature}"
     * Signature = HMAC-SHA256 dari payload dengan APP_KEY.
     */
    public function buildToken(User $student, Semester $semester): string
    {
        $payload = $student->id . ':' . $semester->id;
        $signature = substr(hash_hmac('sha256', $payload, config('app.key')), 0, 16);

        $plain = $payload . ':' . $signature;

        // base64url encode (aman untuk URL)
        return rtrim(strtr(base64_encode($plain), '+/', '-_'), '=');
    }

    /**
     * Verifikasi token → return ['student_id' => int, 'semester_id' => int] atau null.
     */
    public function verifyToken(string $token): ?array
    {
        // Decode base64url
        $padded = strtr($token, '-_', '+/');
        $padded .= str_repeat('=', (4 - strlen($padded) % 4) % 4);

        $plain = base64_decode($padded, true);

        if ($plain === false) {
            return null;
        }

        $parts = explode(':', $plain);

        if (count($parts) !== 3) {
            return null;
        }

        [$studentId, $semesterId, $signature] = $parts;

        if (! ctype_digit($studentId) || ! ctype_digit($semesterId)) {
            return null;
        }

        // Recompute signature
        $expected = substr(hash_hmac('sha256', $studentId . ':' . $semesterId, config('app.key')), 0, 16);

        if (! hash_equals($expected, $signature)) {
            return null;
        }

        return [
            'student_id'  => (int) $studentId,
            'semester_id' => (int) $semesterId,
        ];
    }

    /**
     * URL lengkap untuk di-encode ke QR Code.
     */
    public function buildVerifyUrl(User $student, Semester $semester): string
    {
        return route('rapor.verify', [
            'token' => $this->buildToken($student, $semester),
        ]);
    }

    /**
     * ⭐ QR Code sebagai SVG data URI (base64).
     * DomPDF lebih stabil dengan cara ini ketimbang inline SVG mentah.
     * Tidak butuh extension imagick.
     */
    public function buildQrSvg(User $student, Semester $semester, int $size = 100): string
    {
        $url = $this->buildVerifyUrl($student, $semester);

        $svg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size($size)
            ->margin(0)
            ->errorCorrection('M')
            ->generate($url);

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}