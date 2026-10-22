<?php

namespace App\Features\Payments\Observers;

use App\Features\Academic\Models\Semester;
use App\Features\Payments\Services\PaymentGeneratorService;
use Filament\Notifications\Notification;

class SemesterObserver
{
    public function __construct(
        protected PaymentGeneratorService $paymentGenerator
    ) {}

    /**
     * Trigger saat semester di-update.
     * Fokus: saat `is_active` berubah false → true (aktifkan semester baru).
     */
    public function updated(Semester $semester): void
    {
        if (! $semester->wasChanged('is_active')) {
            return;
        }

        if (! $semester->is_active) {
            return;
        }

        // Generate tagihan untuk SEMUA siswa aktif
        $count = $this->paymentGenerator->generateForAllStudents($semester);

        // Notifikasi via Filament
        if ($count > 0) {
            Notification::make()
                ->success()
                ->title('✅ Tagihan Berhasil Dibuat')
                ->body("{$count} tagihan baru dibuat untuk semester {$semester->name}.")
                ->duration(6000)
                ->send();
        }
    }
}