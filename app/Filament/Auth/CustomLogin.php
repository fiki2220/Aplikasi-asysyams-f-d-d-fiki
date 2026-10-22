<?php

namespace App\Filament\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Custom Login Page — Admin Panel YPTQ Asy-Syams.
 * ------------------------------------------------------------
 * Kelas ini meng-override halaman login Filament default
 * dengan view kustom: `filament.auth.custom-login`.
 *
 * Kalau tidak pakai kelas ini, file `custom-login.blade.php`
 * TIDAK akan pernah dipakai (Filament pakai view default).
 * ------------------------------------------------------------
 */
class CustomLogin extends BaseLogin
{
    /**
     * View yang dipakai halaman login ini.
     * Path view: resources/views/filament/auth/custom-login.blade.php
     */
    protected static string $view = 'filament.auth.custom-login';

    /**
     * Judul utama di atas form login.
     */
    public function getHeading(): string|Htmlable
    {
        return 'Admin Asy-Syams';
    }

    /**
     * Subjudul kecil di bawah judul.
     */
    public function getSubheading(): string|Htmlable|null
    {
        return 'Masuk untuk mengelola sistem YPTQ Asy-Syams';
    }

    /**
     * Redirect setelah login berhasil.
     * Override kalau mau redirect ke halaman tertentu.
     */
    // protected function getRedirectUrl(): string
    // {
    //     return $this->getPanel()->getUrl();
    // }
}