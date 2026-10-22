<?php

namespace App\Console\Commands;

use App\Features\Academic\Models\Semester;
use App\Features\Payments\Services\PaymentGeneratorService;
use Illuminate\Console\Command;

class BackfillPaymentsCommand extends Command
{
    protected $signature = 'payments:backfill {--semester= : ID semester (opsional)}';
    protected $description = 'Generate tagihan SPP untuk semua siswa aktif di semester aktif';

    public function handle(PaymentGeneratorService $service): int
    {
        $semesterId = $this->option('semester');

        $semester = $semesterId
            ? Semester::find($semesterId)
            : Semester::where('is_active', true)->first();

        if (! $semester) {
            $this->error('❌ Semester tidak ditemukan / tidak ada semester aktif.');
            return self::FAILURE;
        }

        $this->info("🔄 Generate tagihan untuk semester: {$semester->name}");
        $this->newLine();

        $count = $service->generateForAllStudents($semester);

        $this->info("✅ Selesai! {$count} tagihan baru dibuat.");
        return self::SUCCESS;
    }
}