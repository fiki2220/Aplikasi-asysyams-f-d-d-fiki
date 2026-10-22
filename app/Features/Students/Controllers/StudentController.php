<?php

namespace App\Features\Students\Controllers;

use App\Features\Meetings\Models\Attendance;
use App\Features\Grades\Models\Grade;
use App\Features\Payments\Models\Payment;
use App\Features\Academic\Models\Semester;
use App\Features\Students\Services\StudentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Halaman Utama Dashboard Santri
     */
    public function dashboard(StudentService $studentService)
    {
        $user = Auth::user();

        // --- LOGIC KHUSUS GURU ---
        if ($user->role === 'guru' || $user->role === 'superadmin') {
            return view('pages.student.dashboard', [
                'user' => $user,
                ...$studentService->teacherDashboardData($user),
            ]);
        }

        // --- LOGIC KHUSUS SISWA ---
        $activeSemester = Semester::where('is_active', true)->first();
        $paymentStatus = 'no_bill';
        $billAmount = 0;

        if ($activeSemester) {
            $billAmount = $activeSemester->tuition_fee;
            $payment = Payment::where('user_id', $user->id)
                ->where('semester_id', $activeSemester->id)
                ->latest()
                ->first();
            if ($payment) {
                $paymentStatus = $payment->status;
            } else {
                $paymentStatus = 'unpaid';
            }
        }

        $presentCount = Attendance::where('user_id', $user->id)->where('status', 'present')->count();
        $averageScore = Grade::where('user_id', $user->id)->avg('score') ?? 0;

        return view('pages.student.dashboard', compact(
            'user',
            'activeSemester',
            'paymentStatus',
            'billAmount',
            'presentCount',
            'averageScore'
        ));
    }

    /**
     * ⭐ Halaman Pembayaran — detail tagihan & checkout Midtrans
     */
    public function payment()
    {
        $user = Auth::user();

        $activeSemester = Semester::where('is_active', true)->first();
        $paymentStatus = 'no_bill';
        $billAmount = 0;
        $payment = null;

        if ($activeSemester) {
            $billAmount = $activeSemester->tuition_fee;
            $payment = Payment::where('user_id', $user->id)
                ->where('semester_id', $activeSemester->id)
                ->latest()
                ->first();

            $paymentStatus = $payment?->status ?? 'unpaid';
        }

        $canCheckoutPayment = $user->hasAccess('payments.checkout');
        $hasUnpaidBill = $activeSemester && ! in_array($paymentStatus, ['success', 'paid']);

        $midtransSnapUrl = config('services.midtrans.is_production')
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';

        return view('pages.student.payment', compact(
            'user',
            'activeSemester',
            'payment',
            'paymentStatus',
            'billAmount',
            'canCheckoutPayment',
            'hasUnpaidBill',
            'midtransSnapUrl',
        ));
    }

    /**
     * Halaman Transkrip Nilai
     */
    public function transcript()
    {
        $user = Auth::user();

        $gradesGrouped = Grade::with(['subject', 'semester'])
            ->where('user_id', $user->id)
            ->get()
            ->groupBy('semester.name');

        return view('pages.student.transcript', compact('user', 'gradesGrouped'));
    }

    /**
     * Halaman Riwayat Absensi
     */
    public function attendance()
    {
        $user = Auth::user();

        $attendances = Attendance::with(['meeting.classGroup.subject'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        $totalMeeting = Attendance::where('user_id', $user->id)->count();
        $totalPresent = Attendance::where('user_id', $user->id)->where('status', 'present')->count();

        $percentage = $totalMeeting > 0 ? round(($totalPresent / $totalMeeting) * 100) : 0;

        return view('pages.student.attendance', compact('user', 'attendances', 'percentage'));
    }

    // ==========================================================
    // ⭐ PROFIL SANTRI
    // ==========================================================

    /**
     * Halaman Profil Santri — view & edit.
     */
    public function profile()
    {
        $user = Auth::user();

        // Konsisten dengan logic dashboard — selain guru/superadmin = santri
        if ($user->role === 'guru' || $user->role === 'superadmin') {
            return redirect()->route('dashboard')
                ->with('error', 'Halaman ini hanya untuk santri.');
        }

        return view('pages.student.profile', [
            'user' => $user,
        ]);
    }

    /**
     * Update Profil Santri.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Konsisten: selain guru/superadmin = santri
        if ($user->role === 'guru' || $user->role === 'superadmin') {
            abort(403, 'Hanya santri yang bisa mengubah profil.');
        }

        // ⭐ Ambil daftar kolom yang BENAR-BENAR ada di tabel users
        $existingColumns = Schema::getColumnListing('users');

        // ── 1. Validasi dasar (nama & email selalu ada) ──
        $rules = [
            'name'  => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:users,email,' . $user->id,
        ];

        $messages = [
            'name.required'  => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email sudah digunakan.',
        ];

        // ── 2. Validasi dinamis field teks ──
        $optionalText = [
            'phone'        => 'nullable|string|max:20',
            // ✅ PERBAIKAN: Ubah 'laki-laki,perempuan' menjadi 'L,P' agar sinkron dengan form Blade
            'gender'       => 'nullable|in:L,P', 
            'birth_place'  => 'nullable|string|max:100',
            'birth_date'   => 'nullable|date',
            'address'      => 'nullable|string|max:500',
            'parent_name'  => 'nullable|string|max:150',
            'parent_phone' => 'nullable|string|max:20',
            'father_name'  => 'nullable|string|max:150',
            'mother_name'  => 'nullable|string|max:150',
        ];

        foreach ($optionalText as $field => $rule) {
            if (in_array($field, $existingColumns, true)) {
                $rules[$field] = $rule;
            }
        }

        // ── 3. Validasi file ──
        // ✅ PERBAIKAN: Ukuran maksimal diubah jadi 2048 (2MB), Ijazah dihapus, file KK/Akta menerima PDF
        $optionalFiles = [
            'file_foto'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'file_kk'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_akta'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];

        foreach ($optionalFiles as $field => $rule) {
            if (in_array($field, $existingColumns, true)) {
                $rules[$field] = $rule;
            }
        }

        // ── 4. Validasi password (opsional) ──
        $rules['current_password'] = 'nullable|required_with:password|current_password';
        $rules['password']         = 'nullable|string|min:8|confirmed';

        // ── 5. Custom messages ──
        $messages = array_merge($messages, [
            'file_foto.image'                   => 'Foto harus berupa gambar.',
            'file_foto.max'                     => 'Ukuran foto maks 2 MB.',
            'file_kk.mimes'                     => 'File KK harus PDF atau gambar.',
            'file_kk.max'                       => 'Ukuran file KK maks 2 MB.',
            'file_akta.mimes'                   => 'File Akta harus PDF atau gambar.',
            'file_akta.max'                     => 'Ukuran file Akta maks 2 MB.',
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'current_password.required_with'    => 'Masukkan password saat ini untuk mengubah password.',
            'password.min'                      => 'Password baru minimal 8 karakter.',
            'password.confirmed'                => 'Konfirmasi password tidak sama.',
        ]);

        $validated = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            // ── 6. Update field teks ──
            $updateData = [
                'name'  => $validated['name'],
                'email' => $validated['email'],
            ];

            foreach (array_keys($optionalText) as $field) {
                if (in_array($field, $existingColumns, true) && array_key_exists($field, $validated)) {
                    $updateData[$field] = $validated[$field];
                }
            }

            $user->fill($updateData);

            // ── 7. Upload file ──
            foreach (array_keys($optionalFiles) as $field) {
                if (! in_array($field, $existingColumns, true)) {
                    continue;
                }

                if ($request->hasFile($field)) {
                    $oldPath = $user->{$field};
                    if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }

                    $newPath = $request->file($field)->store('students/' . $user->id, 'public');
                    $user->{$field} = $newPath;
                }
            }

            // ── 8. Password (kalau diisi) ──
            if ($request->filled('password')) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            DB::commit();

            return back()->with('success', '✨ Profil berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan profil: ' . $e->getMessage());
        }
    }
}