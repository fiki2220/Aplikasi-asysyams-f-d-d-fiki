<?php

namespace App\Features\Payments\Controllers;

use App\Features\Payments\Enums\PaymentStatus;
use App\Features\Payments\Models\Payment;
use App\Features\Payments\Services\MidtransService;
use App\Features\Academic\Models\Semester;
use App\Features\SiteSettings\Models\SiteSetting;
use App\Models\User;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Throwable;

class PaymentController extends Controller
{
    // ==========================================================
    // CHECKOUT — Buat / Ambil Snap Token
    // ==========================================================

    public function checkout()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role !== 'student') {
            return response()->json(['message' => 'Hanya siswa yang bisa melakukan pembayaran.'], 403);
        }

        if (! $user->is_active) {
            return response()->json(['message' => 'Akun Anda belum diverifikasi.'], 403);
        }

        if (! $user->hasAccess('payments.checkout')) {
            return response()->json(['message' => 'Anda tidak memiliki akses untuk pembayaran.'], 403);
        }

        $activeSemester = Semester::where('is_active', true)->first();

        if (! $activeSemester) {
            return response()->json(['message' => 'Tidak ada semester aktif'], 404);
        }

        if ((int) $activeSemester->tuition_fee <= 0) {
            return response()->json([
                'message' => 'Nominal tagihan semester aktif belum diatur.',
            ], 422);
        }

        $midtransConfigError = $this->getMidtransConfigError();

        if ($midtransConfigError) {
            Log::warning('Midtrans checkout blocked: missing config', [
                'user_id' => $user->id,
                'reason'  => $midtransConfigError,
            ]);

            return response()->json(['message' => $midtransConfigError], 500);
        }

        $this->configureMidtrans();

        $payment = Payment::where('user_id', $user->id)
            ->where('semester_id', $activeSemester->id)
            ->first();

        if (! $payment) {
            $orderId = 'SPP-' . $user->id . '-' . Str::uuid()->toString();

            $payment = Payment::create([
                'user_id'     => $user->id,
                'semester_id' => $activeSemester->id,
                'order_id'    => $orderId,
                'amount'      => $activeSemester->tuition_fee,
                'status'      => PaymentStatus::Pending->value,
            ]);
        }

        if (in_array($payment->status, [PaymentStatus::Paid->value, 'success'], true)) {
            return response()->json([
                'message'      => 'Tagihan ini sudah lunas.',
                'already_paid' => true,
            ], 400);
        }

        if ($payment->snap_token && $payment->updated_at && $payment->updated_at->gt(now()->subHours(23))) {
            Log::info('Midtrans reuse existing snap token', [
                'user_id'    => $user->id,
                'payment_id' => $payment->id,
                'order_id'   => $payment->order_id,
            ]);

            return response()->json(['snap_token' => $payment->snap_token]);
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $payment->order_id,
                'gross_amount' => (int) $payment->amount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email'      => $user->email,
                'phone'      => $user->phone,
            ],
            'item_details' => [[
                'id'       => 'SPP-' . $activeSemester->id,
                'price'    => (int) $payment->amount,
                'quantity' => 1,
                'name'     => 'SPP Semester ' . $activeSemester->name,
            ]],
            'callbacks' => [
                'finish' => route('payment.success'),
            ],
        ];

        try {
            Config::$curlOptions = [
                CURLOPT_TIMEOUT        => 15,
                CURLOPT_CONNECTTIMEOUT => 5,
            ];

            $snapToken = Snap::getSnapToken($params);
        } catch (Throwable $exception) {
            Log::error('Failed to create Midtrans Snap token', [
                'user_id'    => $user->id,
                'payment_id' => $payment->id,
                'order_id'   => $payment->order_id,
                'exception'  => $exception::class,
                'message'    => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => config('app.debug')
                    ? 'Gagal membuat token pembayaran: ' . $exception->getMessage()
                    : 'Gagal membuat token pembayaran. Silakan coba lagi.',
            ], 500);
        }

        $payment->update(['snap_token' => $snapToken]);

        Log::info('Midtrans Snap token created', [
            'user_id'    => $user->id,
            'payment_id' => $payment->id,
            'order_id'   => $payment->order_id,
        ]);

        return response()->json(['snap_token' => $payment->snap_token]);
    }

    // ==========================================================
    // SUCCESS — Callback dari Midtrans (finish redirect)
    // ==========================================================

    public function success(Request $request)
    {
        $orderId = $request->query('order_id');

        if (! Auth::check()) {
            return redirect()->route('dashboard');
        }

        if (! $orderId) {
            $payment = Payment::where('user_id', Auth::id())
                ->where('status', PaymentStatus::Pending->value)
                ->latest()
                ->first();

            $orderId = $payment?->order_id;
        }

        if ($orderId) {
            $payment = Payment::where('order_id', $orderId)
                ->where('user_id', Auth::id())
                ->first();

            if ($payment && ! in_array($payment->status, [PaymentStatus::Paid->value, 'success'], true)) {
                $status = [];
                $transactionStatus = $request->query('transaction_status');

                try {
                    $this->configureMidtrans();
                    $status = (array) Transaction::status($orderId);
                    $transactionStatus = $status['transaction_status'] ?? $transactionStatus;
                } catch (Throwable $exception) {
                    Log::warning('Unable to verify Midtrans status on finish redirect', [
                        'user_id'  => Auth::id(),
                        'order_id' => $orderId,
                        'message'  => $exception->getMessage(),
                    ]);
                }

                if (in_array($transactionStatus, ['capture', 'settlement'], true)) {
                    $payment->update([
                        'status'         => PaymentStatus::Paid->value,
                        'payment_type'   => $status['payment_type'] ?? $request->query('payment_type') ?? $payment->payment_type,
                        'payment_detail' => $status !== [] ? $status : [
                            'order_id'           => $orderId,
                            'transaction_status' => $transactionStatus,
                            'status_code'        => $request->query('status_code'),
                            'payment_type'       => $request->query('payment_type'),
                            'source'             => 'finish_redirect',
                        ],
                    ]);
                } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel', 'failure'], true)) {
                    $payment->update([
                        'status'         => PaymentStatus::Failed->value,
                        'payment_type'   => $status['payment_type'] ?? $request->query('payment_type') ?? $payment->payment_type,
                        'payment_detail' => $status !== [] ? $status : [
                            'order_id'           => $orderId,
                            'transaction_status' => $transactionStatus,
                            'status_code'        => $request->query('status_code'),
                            'payment_type'       => $request->query('payment_type'),
                            'source'             => 'finish_redirect',
                        ],
                    ]);
                }
            }
        }

        return redirect()->route('dashboard')
            ->with('success', 'Pembayaran sedang diproses atau telah berhasil!');
    }

    // ==========================================================
    // WEBHOOK — Callback dari Midtrans (server-to-server)
    // ==========================================================

    public function webhook(Request $request, MidtransService $midtransService)
    {
        $payload = $request->all();

        $handled = $midtransService->handleWebhook($payload);

        if (! $handled) {
            Log::warning('Midtrans webhook not handled', [
                'order_id' => $payload['order_id'] ?? null,
            ]);
        }

        return response()->json(['message' => 'OK'], 200);
    }

    // ==========================================================
    // KWITANSI — PDF (ukuran thermal 80mm)
    // ==========================================================

    public function kwitansiPdf(Payment $payment)
    {
        $this->authorizeKwitansi($payment);

        $data = $this->buildKwitansiData($payment);

        $filename = 'Struk-SPP-'
            . Str::slug($payment->student->name ?? 'santri')
            . '-' . $payment->id . '.pdf';

        return Pdf::loadView('pdf.kwitansi', $data)
            ->setPaper([0, 0, 226.77, 623.62], 'portrait')  // 80mm × 220mm
            ->stream($filename);
    }

    // ==========================================================
    // KWITANSI — PRINT VIEW
    // ==========================================================

    public function kwitansiPrint(Payment $payment)
    {
        $this->authorizeKwitansi($payment);

        $data = $this->buildKwitansiData($payment);

        return response()->view('pages.kwitansi-print', $data);
    }

    // ==========================================================
    // HELPERS — Kwitansi
    // ==========================================================

    private function authorizeKwitansi(Payment $payment): void
    {
        $user = Auth::user();

        if (! $user) {
            abort(403, 'Anda harus login.');
        }

        $isOwner = (int) $payment->user_id === (int) $user->id;
        $isAdmin = $user->hasAccess('payments.manage');

        if (! $isOwner && ! $isAdmin) {
            abort(403, 'Anda tidak berhak mengakses kwitansi ini.');
        }

        if (! in_array($payment->status, [PaymentStatus::Paid->value, 'success'], true)) {
            abort(404, 'Kwitansi hanya tersedia untuk pembayaran yang sudah lunas.');
        }
    }

    private function buildKwitansiData(Payment $payment): array
    {
        $payment->load(['student.classGroups', 'semester']);

        $logoBase64 = null;
        try {
            $path = public_path('images/logo.PNG');
            if (file_exists($path)) {
                $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($path));
            }
        } catch (Throwable $e) {
            // ignore
        }

        // ⭐ Pakai helper biar aman (handle value array / null / string)
        $kepalaNama    = $this->getSettingString('kepala_rq_nama', 'Erwin Syamsudin, SQ. M.Pd');
        $kepalaJabatan = $this->getSettingString('kepala_rq_jabatan', 'Kepala RQ Asy-Syams');

        return [
            'payment'      => $payment,
            'student'      => $payment->student,
            'semester'     => $payment->semester,
            'institution'  => [
                'name'     => 'RUMAH QUR\'AN ASY-SYAMS',
                'subtitle' => 'TILAWAH CENTER PEKANBARU',
                'address'  => 'Jl. Tegal Sari Gg. Kemuning No. 9',
                'phone'    => '+62 853-7637-4040',
                'email'    => 'rqasysyams23@gmail.com',
            ],
            'kepalaNama'    => $kepalaNama,
            'kepalaJabatan' => $kepalaJabatan,
            'terbilang'     => $this->terbilang((float) $payment->amount),
            'logoBase64'    => $logoBase64,
            'printedAt'     => now(),
        ];
    }

    /**
     * ⭐ Ambil value setting yang dipastikan string (bukan array).
     * Handle: array (ambil first), null, string kosong → fallback default.
     */
    private function getSettingString(string $key, string $default = ''): string
    {
        $setting = SiteSetting::where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        $value = $setting->value;

        // Kalau array, ambil elemen pertama
        if (is_array($value)) {
            $first = reset($value);

            return $first ? (string) $first : $default;
        }

        // Kalau string dan tidak kosong
        if (is_string($value) && trim($value) !== '') {
            return trim($value);
        }

        return $default;
    }

    private function terbilang(float $angka): string
    {
        $angka = (int) $angka;

        $huruf = [
            '', 'satu', 'dua', 'tiga', 'empat', 'lima',
            'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas',
        ];

        if ($angka < 0) return 'minus ' . $this->terbilang(abs($angka));
        if ($angka < 12) return $huruf[$angka] ?: 'nol';
        if ($angka < 20) return trim($this->terbilang($angka - 10) . ' belas');
        if ($angka < 100) return trim($this->terbilang((int) ($angka / 10)) . ' puluh ' . $this->terbilang($angka % 10));
        if ($angka < 200) return trim('seratus ' . $this->terbilang($angka - 100));
        if ($angka < 1000) return trim($this->terbilang((int) ($angka / 100)) . ' ratus ' . $this->terbilang($angka % 100));
        if ($angka < 2000) return trim('seribu ' . $this->terbilang($angka - 1000));
        if ($angka < 1000000) return trim($this->terbilang((int) ($angka / 1000)) . ' ribu ' . $this->terbilang($angka % 1000));
        if ($angka < 1000000000) return trim($this->terbilang((int) ($angka / 1000000)) . ' juta ' . $this->terbilang($angka % 1000000));
        if ($angka < 1000000000000) return trim($this->terbilang((int) ($angka / 1000000000)) . ' miliar ' . $this->terbilang($angka % 1000000000));

        return '';
    }

    // ==========================================================
    // HELPERS — Midtrans
    // ==========================================================

    private function configureMidtrans(): void
    {
        Config::$serverKey    = config('services.midtrans.server_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
    }

    private function getMidtransConfigError(): ?string
    {
        $serverKey = (string) config('services.midtrans.server_key');
        $clientKey = (string) config('services.midtrans.client_key');

        if ($serverKey === '' || $clientKey === '') {
            return 'Konfigurasi pembayaran belum lengkap.';
        }

        return null;
    }
}