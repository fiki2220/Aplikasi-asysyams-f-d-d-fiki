<?php

namespace App\Features\Reports\Controllers;

use App\Features\Academic\Models\Semester;
use App\Features\Reports\Services\RaporVerificationService;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RaporVerificationController extends Controller
{
    /**
     * Halaman publik verifikasi rapor.
     * URL: /verify/rapor/{token}
     */
    public function show(Request $request, string $token, RaporVerificationService $service)
    {
        $decoded = $service->verifyToken($token);

        if (! $decoded) {
            return view('pages.verify-rapor', [
                'valid'      => false,
                'reason'     => 'Token tidak valid atau rapor tidak terdaftar.',
                'student'    => null,
                'semester'   => null,
                'verifiedAt' => now(),
                'token'      => $token,
            ]);
        }

        $student  = User::find($decoded['student_id']);
        $semester = Semester::find($decoded['semester_id']);

        if (! $student || ! $semester) {
            return view('pages.verify-rapor', [
                'valid'      => false,
                'reason'     => 'Data santri atau semester tidak ditemukan.',
                'student'    => null,
                'semester'   => null,
                'verifiedAt' => now(),
                'token'      => $token,
            ]);
        }

        // Ambil kelas santri di semester tersebut (untuk display)
        $classGroup = $student->classGroups
            ->firstWhere('semester_id', $semester->id);

        return view('pages.verify-rapor', [
            'valid'      => true,
            'reason'     => null,
            'student'    => $student,
            'semester'   => $semester,
            'classGroup' => $classGroup,
            'verifiedAt' => now(),
            'token'      => $token,
        ]);
    }
}