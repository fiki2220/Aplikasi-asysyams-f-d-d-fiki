<?php

namespace App\Features\Grades\Controllers;

use App\Features\Academic\Models\ClassGroup;
use App\Features\Grades\Models\Grade;
use App\Features\Grades\Services\GradeReportService;
use App\Features\Reports\Services\RaporVerificationService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class GradeReportController extends Controller
{
    // ==========================================================
    // SHOW — Halaman HTML view (Tabel Nilai)
    // ==========================================================

    public function show(Request $request, User $user, GradeReportService $service): Response
    {
        $grade = $this->resolveGrade($request, $user);

        abort_unless(
            $service->canAccessStudentReport($request->user(), $user, $grade),
            403
        );

        return response()->view(
            'grades.student-control-report',
            $service->buildStudentControlReport($user, $grade)
        );
    }

    // ==========================================================
    // PDF — Tabel Nilai (buku harian) per santri
    // ==========================================================

    public function pdf(Request $request, User $user, GradeReportService $service): Response
    {
        $grade = $this->resolveGrade($request, $user);

        abort_unless(
            $service->canAccessStudentReport($request->user(), $user, $grade),
            403
        );

        $data = $service->buildStudentControlReport($user, $grade);
        $fileName = 'Buku-Harian-' . Str::slug($user->name) . '-' . now()->format('Ymd') . '.pdf';

        return Pdf::loadView('pdf.student-grade-control', $data)
            ->setPaper('a4', 'portrait')
            ->download($fileName);
    }

    // ==========================================================
    // RAPOR SINGLE — Rapor santri (semester) dengan filter periode
    // ==========================================================

    public function raporSingle(
        Request $request,
        ClassGroup $classGroup,
        User $user,
        RaporVerificationService $verifyService,
    ): Response {
        abort_unless($this->canAccessClassReport($request->user(), $classGroup), 403);

        $month = (int) $request->query('month', now()->month);
        $year  = (int) $request->query('year', now()->year);

        // Eager load relasi penting pada ClassGroup
        $classGroup->loadMissing(['semester', 'teacher', 'subject']);

        // Pastikan santri adalah anggota kelas ini
        $isMember = $classGroup->students()->where('users.id', $user->id)->exists();
        abort_unless($isMember, 404, 'Santri tidak terdaftar di kelas ini.');

        // QR Code Verifikasi
        $qrSvg = null;
        $verifyUrl = null;

        if ($classGroup->semester) {
            $qrSvg = $verifyService->buildQrSvg($user, $classGroup->semester, 110);
            $verifyUrl = $verifyService->buildVerifyUrl($user, $classGroup->semester);
        }

        $pdf = Pdf::loadView('filament.report.rapor-pdf', [
            'student'     => $user,
            'classGroup'  => $classGroup,
            'filterMonth' => $month,
            'filterYear'  => $year,
            'qrSvg'       => $qrSvg,
            'verifyUrl'   => $verifyUrl,
        ])->setPaper('a4', 'portrait');

        $fileName = 'Rapor-' . Str::slug($user->name) . '-' . $month . '-' . $year . '.pdf';

        return $pdf->stream($fileName);
    }

    // ==========================================================
    // RAPOR CLASS — Download ZIP semua santri dalam 1 kelas
    // ==========================================================

    public function raporClass(
        Request $request,
        ClassGroup $classGroup,
        RaporVerificationService $verifyService,
    ): Response {
        abort_unless($this->canAccessClassReport($request->user(), $classGroup), 403);

        // Beri batas waktu ekstra (3 menit) agar render puluhan PDF tidak timeout di hosting
        set_time_limit(180);

        $month = (int) $request->query('month', now()->month);
        $year  = (int) $request->query('year', now()->year);

        // Eager load relasi kelas & semester sekali di awal (Anti N+1)
        $classGroup->loadMissing(['semester', 'teacher', 'subject']);
        $students = $classGroup->students()->get();

        if ($students->isEmpty()) {
            abort(404, 'Kelas ini belum memiliki santri.');
        }

        // Folder temp untuk ZIP (gunakan permission 0755 yang aman untuk server Linux)
        $tempDir = storage_path('app/temp');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $zipFileName = 'Rapor-' . Str::slug($classGroup->name) . '-' . $month . '-' . $year . '.zip';
        $zipPath = $tempDir . '/rapor-class-' . uniqid('', true) . '.zip';

        $zip = new \ZipArchive();

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat file ZIP.');
        }

        foreach ($students as $student) {
            $qrSvg = null;
            $verifyUrl = null;

            if ($classGroup->semester) {
                $qrSvg = $verifyService->buildQrSvg($student, $classGroup->semester, 110);
                $verifyUrl = $verifyService->buildVerifyUrl($student, $classGroup->semester);
            }

            $pdfContent = Pdf::loadView('filament.report.rapor-pdf', [
                'student'     => $student,
                'classGroup'  => $classGroup,
                'filterMonth' => $month,
                'filterYear'  => $year,
                'qrSvg'       => $qrSvg,
                'verifyUrl'   => $verifyUrl,
            ])
                ->setPaper('a4', 'portrait')
                ->output();

            // Tambahkan ID santri agar jika ada nama kembar tidak saling menimpa di dalam ZIP
            $pdfName = Str::slug($student->name) . '-' . $student->id . '.pdf';

            $zip->addFromString($pdfName, $pdfContent);
        }

        $zip->close();

        return response()
            ->download($zipPath, $zipFileName)
            ->deleteFileAfterSend(true);
    }

    // ==========================================================
    // HELPERS
    // ==========================================================

    private function canAccessClassReport(?User $actor, ClassGroup $classGroup): bool
    {
        if (! $actor) {
            return false;
        }

        if ($actor->role === 'superadmin') {
            return true;
        }

        if ($actor->role === 'guru' && $actor->hasAnyAccess(['reports.download', 'grades.manage'])) {
            return (int) $classGroup->teacher_id === (int) $actor->id;
        }

        return false;
    }

    private function resolveGrade(Request $request, User $user): ?Grade
    {
        $gradeId = $request->integer('grade');

        if (! $gradeId) {
            return null;
        }

        return Grade::query()
            ->where('user_id', $user->id)
            ->find($gradeId);
    }
}