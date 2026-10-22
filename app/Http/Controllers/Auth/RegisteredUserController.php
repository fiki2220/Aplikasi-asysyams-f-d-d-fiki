<?php

namespace App\Http\Controllers\Auth;

use App\Features\Auth\Services\SpmbRegistrationService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Daftar field upload dokumen SPMB.
     * Format: [name => folder penyimpanan]
     * CATATAN: file_ijazah & file_foto sudah dihapus sesuai permintaan.
     */
    protected const UPLOAD_FIELDS = [
        'file_kk'     => 'ppdb-documents/kk',
        'file_akta'   => 'ppdb-documents/akta',
    ];

    /**
     * Tampilkan halaman form pendaftaran SPMB.
     */
    public function create(SpmbRegistrationService $spmbService): View|Response
    {
        if (! $spmbService->isOpen()) {
            return response()->view('auth.spmb-closed', [
                'deadline' => $spmbService->getDeadline(),
            ], 403);
        }

        return view('auth.register');
    }

    /**
     * Proses submit pendaftaran SPMB.
     */
    public function store(Request $request, SpmbRegistrationService $spmbService): RedirectResponse|Response
    {
        // ────────────────────────────────────────────────────────
        // 1. GUARD — periode pendaftaran masih buka?
        // ────────────────────────────────────────────────────────
        if (! $spmbService->isOpen()) {
            return response()->view('auth.spmb-closed', [
                'deadline' => $spmbService->getDeadline(),
            ], 403);
        }

        // ────────────────────────────────────────────────────────
        // 2. HONEYPOT — kalau terisi, berarti bot
        // ────────────────────────────────────────────────────────
        if ($request->filled('website')) {
            // Silent reject — tampilkan error generic biar bot tidak belajar
            return back()
                ->withInput()
                ->withErrors(['website' => 'Pendaftaran tidak dapat diproses. Coba lagi.']);
        }

        // ────────────────────────────────────────────────────────
        // 3. VALIDASI
        // ────────────────────────────────────────────────────────
        $validated = $request->validate(
            rules: [
                // ── Akun login ──────────────────────────────────
                'name'     => ['required', 'string', 'max:255'],
                'email'    => [
                    'required', 'string', 'lowercase', 'email', 'max:255',
                    Rule::unique(User::class, 'email'),
                ],
                'password' => [
                    'required',
                    'confirmed',
                    Rules\Password::defaults(),
                ],

                // ── Data Santri ─────────────────────────────────
                'nisn'        => [
                    'required',
                    'string',
                    'digits_between:8,12',
                    Rule::unique(User::class, 'nisn'),
                ],
                'gender'      => ['required', Rule::in(['L', 'P'])],
                'birth_place' => ['required', 'string', 'max:100'],
                'birth_date'  => ['required', 'date', 'before:today'],
                'phone'       => ['nullable', 'string', 'max:20'],

                // ── Orang Tua / Wali ────────────────────────────
                'father_name'  => ['required', 'string', 'max:255'],
                'mother_name'  => ['required', 'string', 'max:255'],
                'parent_name'  => ['nullable', 'string', 'max:255'],
                'parent_phone' => ['required', 'string', 'max:20'],

                // ── Akademik & Domisili ─────────────────────────
                'program' => [
                    'required',
                    Rule::in(['Tahsin', 'Tilawah', 'Murottal', 'Baca_Tulis']),
                ],
                'grade_level'   => ['nullable', 'string', 'max:100'],
                'school_origin' => ['required', 'string', 'max:255'],
                'address'       => ['required', 'string', 'max:500'],

                // ── Dokumen Persyaratan (Ijazah & Foto dihapus) ─
                'file_kk'     => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
                'file_akta'   => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],

                // ── Persetujuan ─────────────────────────────────
                'agree_terms' => ['accepted'],
            ],
            messages: [
                // ── Akun ────────────────────────────────────────
                'name.required'         => 'Nama lengkap wajib diisi.',
                'email.required'        => 'Email wajib diisi.',
                'email.email'           => 'Format email tidak valid.',
                'email.unique'          => 'Email sudah terdaftar. Silakan login atau gunakan email lain.',
                'password.required'     => 'Password wajib diisi.',
                'password.confirmed'    => 'Konfirmasi password tidak cocok.',
                'password.min'          => 'Password minimal 8 karakter.',

                // ── Data Santri ─────────────────────────────────
                'nisn.required'         => 'NISN wajib diisi.',
                'nisn.digits_between'   => 'NISN harus 8–12 digit angka.',
                'nisn.unique'           => 'NISN sudah terdaftar.',
                'gender.required'       => 'Jenis kelamin wajib dipilih.',
                'gender.in'             => 'Jenis kelamin tidak valid.',
                'birth_place.required'  => 'Tempat lahir wajib diisi.',
                'birth_date.required'   => 'Tanggal lahir wajib diisi.',
                'birth_date.before'     => 'Tanggal lahir harus sebelum hari ini.',

                // ── Orang Tua ───────────────────────────────────
                'father_name.required'  => 'Nama ayah wajib diisi.',
                'mother_name.required'  => 'Nama ibu wajib diisi.',
                'parent_phone.required' => 'No. HP orang tua/wali wajib diisi.',

                // ── Akademik ────────────────────────────────────
                'program.required'      => 'Program wajib dipilih.',
                'program.in'            => 'Program yang dipilih tidak valid.',
                'grade_level.max'       => 'Jenjang / Kelas maksimal 100 karakter.',
                'school_origin.required'=> 'Asal sekolah wajib diisi.',
                'address.required'      => 'Alamat lengkap wajib diisi.',

                // ── Dokumen ─────────────────────────────────────
                'file_kk.required'      => 'File Kartu Keluarga (KK) wajib diupload.',
                'file_kk.mimes'         => 'File KK harus berupa PDF, JPG, atau PNG.',
                'file_kk.max'           => 'Ukuran file KK maksimal 2MB.',
                'file_akta.required'    => 'File Akta Kelahiran wajib diupload.',
                'file_akta.mimes'       => 'File Akta harus berupa PDF, JPG, atau PNG.',
                'file_akta.max'         => 'Ukuran file Akta maksimal 2MB.',

                // ── Persetujuan ─────────────────────────────────
                'agree_terms.accepted'  => 'Anda harus menyetujui Syarat & Ketentuan untuk melanjutkan.',
            ]
        );

        // ────────────────────────────────────────────────────────
        // 4. SIMPAN — bungkus DB transaction
        // ────────────────────────────────────────────────────────
        $uploadedFiles = []; // track untuk cleanup kalau gagal

        try {
            $user = DB::transaction(function () use ($validated, $request, &$uploadedFiles) {
                // Upload file satu per satu
                $filePaths = [];
                foreach (self::UPLOAD_FIELDS as $field => $folder) {
                    if ($request->hasFile($field)) {
                        $path = $request->file($field)->store($folder, 'public');
                        $filePaths[$field] = $path;
                        $uploadedFiles[] = $path;
                    }
                }

                $user = User::create([
                    // ── Akun ────────────────────────────────────
                    'name'     => $validated['name'],
                    'email'    => $validated['email'],
                    'password' => Hash::make($validated['password']),

                    // ── Role & Status ───────────────────────────
                    'role'      => 'student',
                    'is_active' => false,

                    // ── Data Santri ─────────────────────────────
                    'nisn'        => $validated['nisn'],
                    'gender'      => $validated['gender'],
                    'birth_place' => $validated['birth_place'],
                    'birth_date'  => $validated['birth_date'],
                    'phone'       => $validated['phone'] ?? null,

                    // ── Orang Tua / Wali ────────────────────────
                    'father_name'  => $validated['father_name'],
                    'mother_name'  => $validated['mother_name'],
                    'parent_name'  => $validated['parent_name'] ?? null,
                    'parent_phone' => $validated['parent_phone'],

                    // ── Akademik & Domisili ─────────────────────
                    'program'       => $validated['program'],
                    'grade_level'   => $validated['grade_level'] ?? null,
                    'school_origin' => $validated['school_origin'],
                    'address'       => $validated['address'],

                    // ── Dokumen Persyaratan (Ijazah & Foto dihapus) ─
                    'file_kk'     => $filePaths['file_kk'] ?? null,
                    'file_akta'   => $filePaths['file_akta'] ?? null,

                    // ── Persetujuan ─────────────────────────────
                    'terms_accepted_at' => now(),
                ]);

                event(new Registered($user));

                return $user;
            });
        } catch (\Throwable $e) {
            // Cleanup file yang sudah terupload kalau ada error
            foreach ($uploadedFiles as $path) {
                Storage::disk('public')->delete($path);
            }

            // Re-throw supaya Laravel handler yang urus
            throw $e;
        }

        // ────────────────────────────────────────────────────────
        // 5. AUTO LOGIN
        // ────────────────────────────────────────────────────────
        Auth::login($user);

        // ────────────────────────────────────────────────────────
        // 6. REDIRECT
        // ────────────────────────────────────────────────────────
        return redirect()
            ->route('approval.notice')
            ->with('status', 'Pendaftaran berhasil dikirim. Mohon tunggu verifikasi dari admin.');
    }
}