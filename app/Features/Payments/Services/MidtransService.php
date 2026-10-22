<?php

namespace App\Features\Payments\Services;

use App\Features\Payments\Enums\PaymentStatus;
use App\Features\Payments\Models\Payment;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    /**
     * Handle webhook dari Midtrans.
     * Return true kalau valid & diproses, false kalau invalid.
     */
    public function handleWebhook(array $payload): bool
    {
        $orderId      = $payload['order_id'] ?? '';
        $statusCode   = $payload['status_code'] ?? '';
        $grossAmount  = $payload['gross_amount'] ?? '';
        $serverKey    = config('services.midtrans.server_key');
        $signatureKey = $payload['signature_key'] ?? '';

        // 1. Verify signature (timing-safe)
        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if (! hash_equals($expected, (string) $signatureKey)) {
            Log::error('Invalid Midtrans webhook signature', [
                'order_id' => $orderId,
                'ip'       => request()->ip(),
            ]);
            return false;
        }

        // 2. Cari payment
        $payment = Payment::where('order_id', $orderId)->first();

        if (! $payment) {
            Log::warning('Midtrans webhook: payment not found', ['order_id' => $orderId]);

            // ⭐ Return true supaya Midtrans berhenti retry
            return true;
        }

        // 3. Idempotent — kalau sudah paid, jangan ubah lagi
        if (in_array($payment->status, [PaymentStatus::Paid->value, 'success'], true)) {
            return true;
        }

        // 4. Mapping status
        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus       = $payload['fraud_status'] ?? 'accept';

        $newStatus = match ($transactionStatus) {
            'capture'  => $fraudStatus === 'accept'
                ? PaymentStatus::Paid->value
                : PaymentStatus::Pending->value,
            'settlement' => PaymentStatus::Paid->value,
            'pending'    => PaymentStatus::Pending->value,
            'deny', 'cancel', 'expire', 'failure' => PaymentStatus::Failed->value,
            'refund', 'partial_refund' => PaymentStatus::Failed->value,
            default => $payment->status,
        };

        $payment->update([
            'status'         => $newStatus,
            'payment_type'   => $payload['payment_type'] ?? $payment->payment_type,
            'payment_detail' => $payload,
        ]);

        Log::info('Midtrans webhook processed', [
            'order_id'   => $orderId,
            'old_status' => $payment->getOriginal('status'),
            'new_status' => $newStatus,
        ]);

        return true;
    }
}