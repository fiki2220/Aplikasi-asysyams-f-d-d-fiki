<?php

namespace App\Features\Payments\Observers;

use App\Features\Payments\Services\PaymentGeneratorService;
use App\Models\User;

class UserObserver
{
    public function __construct(
        protected PaymentGeneratorService $paymentGenerator
    ) {}

    /**
     * Trigger saat user di-update.
     * Fokus: saat `is_active` berubah false → true (approve siswa).
     */
    public function updated(User $user): void
    {
        // Hanya untuk role student
        if ($user->role !== 'student') {
            return;
        }

        // Cek apakah is_active berubah
        if (! $user->wasChanged('is_active')) {
            return;
        }

        // Hanya kalau jadi active (approve)
        if (! $user->is_active) {
            return;
        }

        // Generate tagihan semester aktif
        $this->paymentGenerator->generateForUser($user);
    }
}