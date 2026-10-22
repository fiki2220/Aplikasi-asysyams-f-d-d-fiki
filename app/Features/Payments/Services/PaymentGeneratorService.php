<?php

namespace App\Features\Payments\Services;

use App\Features\Academic\Models\Semester;
use App\Features\Payments\Models\Payment;
use App\Models\User;
use Illuminate\Support\Str;

class PaymentGeneratorService
{
    /**
     * Generate tagihan untuk 1 user di semester tertentu.
     * Return Payment record (existing atau baru). Null kalau tidak ada semester aktif.
     */
    public function generateForUser(User $user, ?Semester $semester = null): ?Payment
    {
        $semester = $semester ?? Semester::where('is_active', true)->first();

        if (! $semester) {
            return null;
        }

        // Kalau sudah ada tagihan untuk user+semester ini, tidak generate lagi
        $existing = Payment::query()
            ->where('user_id', $user->id)
            ->where('semester_id', $semester->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        return Payment::create([
            'user_id'     => $user->id,
            'semester_id' => $semester->id,
            'amount'      => $semester->tuition_fee,
            'status'      => 'pending',
            'order_id'    => $this->generateOrderId($user, $semester),
        ]);
    }

    /**
     * Generate tagihan untuk SEMUA siswa aktif di semester tertentu secara efisien (Anti N+1).
     * Return jumlah tagihan baru yang dibuat.
     */
    public function generateForAllStudents(?Semester $semester = null): int
    {
        $semester = $semester ?? Semester::where('is_active', true)->first();

        if (! $semester) {
            return 0;
        }

        // Ambil hanya siswa aktif yang BELUM punya tagihan di semester ini (Subquery efisien)
        $students = User::query()
            ->select(['id', 'role', 'is_active'])
            ->where('role', 'student')
            ->where('is_active', true)
            ->whereNotExists(function ($query) use ($semester) {
                $query->selectRaw(1)
                    ->from('payments')
                    ->whereColumn('payments.user_id', 'users.id')
                    ->where('payments.semester_id', $semester->id);
            })
            ->get();

        if ($students->isEmpty()) {
            return 0;
        }

        $now = now();
        $timestamp = $now->format('YmdHis');
        $rows = [];

        foreach ($students as $student) {
            $rows[] = [
                'user_id'     => $student->id,
                'semester_id' => $semester->id,
                'amount'      => $semester->tuition_fee,
                'status'      => 'pending',
                'order_id'    => "SPP-{$semester->id}-{$student->id}-{$timestamp}-" . Str::upper(Str::random(4)),
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        // Batch insert per 200 baris agar memori & database tetap ringan untuk ribuan santri
        foreach (array_chunk($rows, 200) as $chunk) {
            Payment::insert($chunk);
        }

        return count($rows);
    }

    /**
     * Generate order_id unik.
     */
    protected function generateOrderId(User $user, Semester $semester): string
    {
        $base = 'SPP-' . $semester->id . '-' . $user->id . '-' . now()->format('YmdHis');

        if (Payment::where('order_id', $base)->exists()) {
            $base .= '-' . Str::upper(Str::random(4));
        }

        return $base;
    }
}