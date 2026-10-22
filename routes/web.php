<?php

use App\Features\Contacts\Controllers\ContactController;
use App\Features\Grades\Controllers\GradeReportController;
use App\Features\Payments\Controllers\PaymentController;
use App\Features\Posts\Controllers\PostController;
use App\Features\Posts\Models\Post;
use App\Features\Reports\Controllers\RaporVerificationController;
use App\Features\SiteSettings\Models\SiteSetting;
use App\Features\Students\Controllers\StudentController;
use App\Features\TeacherAttendances\Controllers\TeacherAttendanceController;
use App\Http\Middleware\EnsureUserHasPermission;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/* A. HALAMAN PUBLIK */
Route::get('/', function () {
    $bgUrl = null;
    try {
        if (class_exists(SiteSetting::class)) {
            $bgUrl = Cache::remember('home_hero_bg_url', 300, function () {
                $setting = SiteSetting::where('key', 'hero_bg')->first();
                return $setting?->first_url;
            });
        }
    } catch (\Throwable $e) { $bgUrl = null; }

    $posts = collect();
    try {
        if (class_exists(Post::class)) {
            $posts = Cache::remember('home_latest_posts', 300, function () {
                return Post::where('is_published', true)->whereNotNull('published_at')->where('published_at', '<=', now())
                    ->with('author:id,name')->latest('published_at')->take(3)->get();
            });
        }
    } catch (\Throwable $e) { $posts = collect(); }

    $deadline = now()->endOfYear()->format('Y-m-d H:i:s');
    try {
        if (class_exists(SiteSetting::class)) {
            $setting = Cache::remember('home_spmb_deadline', 300, function () {
                return SiteSetting::where('key', 'spmb_deadline')->value('value');
            });
            if ($setting) $deadline = is_array($setting) ? (reset($setting) ?: $deadline) : (string) $setting;
        }
    } catch (\Throwable $e) {}

    try { $deadlineISO = Carbon::parse($deadline)->format('Y-m-d\TH:i:s'); }
    catch (\Throwable $e) { $deadlineISO = Carbon::parse(now()->endOfYear())->format('Y-m-d\TH:i:s'); }

    return view('pages.home', compact('bgUrl', 'posts', 'deadline', 'deadlineISO'));
})->name('home');

Route::view('/spmb', 'pages.spmb')->name('spmb.index');
Route::view('/profil-yayasan', 'pages.profile-yayasan')->name('profile.yayasan');

Route::get('/news', function () {
    $posts = new LengthAwarePaginator([], 0, 9, request('page', 1), ['path' => request()->url()]);
    try {
        if (class_exists(Post::class)) {
            $posts = Post::where('is_published', true)->whereNotNull('published_at')->where('published_at', '<=', now())
                ->with('author:id,name')->latest('published_at')->paginate(9);
        }
    } catch (\Throwable $e) {}
    return view('pages.news', compact('posts'));
})->name('news.index');

Route::get('/berita/{slug}', [PostController::class, 'show'])->name('post.show');
Route::post('/contact/send', [ContactController::class, 'send'])->middleware('throttle:3,1')->name('contact.send');
Route::get('/verify/rapor/{token}', [RaporVerificationController::class, 'show'])->middleware('throttle:30,1')->name('rapor.verify');

/* B. HALAMAN AUTH DASAR */
Route::middleware(['auth'])->group(function () {
    Route::get('/approval', fn () => view('pages.approval'))->name('approval.notice');
    Route::get('/registration/pending', fn () => view('pages.approval'))->name('registration.pending');
    Route::get('/access-denied', fn () => view('pages.access-denied'))->name('access.denied');
});

/* C. ROUTE FILE PPDB */
Route::get('/ppdb-file/{path}', function (string $path) {
    if (! auth()->check()) abort(403, 'Anda harus login untuk mengakses dokumen ini.');
    $safePath = ltrim(str_replace(['..', '\\', '//'], '', $path), '/');
    $fullPath = Str::startsWith($safePath, ['ppdb-documents/', 'students/']) ? $safePath : 'ppdb-documents/' . $safePath;
    $user = auth()->user();
    $isAdmin = $user->isAdmin() || $user->isSuperadmin() || $user->hasAnyAccess(['users.view', 'users.manage', 'spmb.view', 'spmb.manage']);
    $isOwner = in_array($fullPath, array_filter([$user->file_kk, $user->file_akta, $user->file_ijazah, $user->file_foto]), true);
    if (! $isAdmin && ! $isOwner) abort(403, 'Anda tidak berhak mengakses dokumen ini.');
    if (! Storage::disk('public')->exists($fullPath)) abort(404, 'File tidak ditemukan.');
    return Storage::disk('public')->response($fullPath);
})->where('path', '.*')->name('ppdb.file');

/* D. HALAMAN USER LOGIN (dengan permission) */
Route::middleware(['auth', 'verified', 'is_active'])->group(function () {

    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/pembayaran', [StudentController::class, 'payment'])->name('student.payment');
    Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
    Route::match(['put', 'patch'], '/profile', [StudentController::class, 'updateProfile'])->name('profile.update');

    Route::get('/transcript', [StudentController::class, 'transcript'])->middleware(EnsureUserHasPermission::class . ':reports.view')->name('student.transcript');
    Route::get('/attendance', [StudentController::class, 'attendance'])->middleware(EnsureUserHasPermission::class . ':dashboard.view')->name('student.attendance');

    // Absensi Harian Guru
    Route::post('/teacher-attendances/check-in', [TeacherAttendanceController::class, 'checkIn'])->middleware(EnsureUserHasPermission::class . ':teacher-attendances.check-in')->name('teacher-attendances.check-in');
    Route::post('/teacher-attendances/check-out', [TeacherAttendanceController::class, 'checkOut'])->middleware(EnsureUserHasPermission::class . ':teacher-attendances.check-in')->name('teacher-attendances.check-out');

    // ⭐ CHECK IN / OUT KELAS (Per Pertemuan)
    Route::post('/meetings/{meeting}/check-in', function (\App\Features\Meetings\Models\Meeting $meeting) {
        $meeting->update(['teacher_check_in_at' => now()]);
        return back()->with('status', 'Berhasil Check In untuk kelas ' . $meeting->classGroup->name);
    })->name('meetings.check-in');

    Route::post('/meetings/{meeting}/check-out', function (\App\Features\Meetings\Models\Meeting $meeting) {
        $meeting->update(['teacher_check_out_at' => now()]);
        return back()->with('status', 'Berhasil Check Out untuk kelas ' . $meeting->classGroup->name);
    })->name('meetings.check-out');

    // ⭐ SIMPAN ABSENSI SISWA DARI DASHBOARD
    Route::post('/meetings/{meeting}/attendance', function (\Illuminate\Http\Request $request, \App\Features\Meetings\Models\Meeting $meeting) {
        $validated = $request->validate([
            'attendances' => 'required|array',
            'attendances.*' => 'required|in:present,sick,permission,alpha',
        ]);

        foreach ($validated['attendances'] as $userId => $status) {
            \App\Features\Meetings\Models\Attendance::updateOrCreate(
                ['meeting_id' => $meeting->id, 'user_id' => $userId],
                ['status' => $status]
            );
        }

        return back()->with('status', 'Absensi santri berhasil disimpan.');
    })->name('meetings.attendance.store');

    // Payment Midtrans
    Route::get('/payment/checkout', [PaymentController::class, 'checkout'])->middleware([EnsureUserHasPermission::class . ':payments.checkout', 'throttle:10,1'])->name('payment.checkout');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/kwitansi/{payment}/pdf', [PaymentController::class, 'kwitansiPdf'])->name('payment.kwitansi.pdf');
    Route::get('/kwitansi/{payment}/print', [PaymentController::class, 'kwitansiPrint'])->name('payment.kwitansi.print');

    // Laporan Kontrol Santri
    Route::get('/grades-control/{user}', [GradeReportController::class, 'show'])->middleware(EnsureUserHasPermission::class . ':grades.view|grades.manage')->name('admin.grades.student-control');
    Route::get('/grades-control/{user}/pdf', [GradeReportController::class, 'pdf'])->middleware(EnsureUserHasPermission::class . ':grades.view|grades.manage')->name('admin.grades.student-control.pdf');
});

/* E. WEBHOOK & PUBLIC POST */
Route::post('/payment/webhook', [PaymentController::class, 'webhook'])->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])->name('payment.webhook');

/* F. CETAK RAPOR PDF */
Route::get('/rapor-pdf/{class_group}/{user}', [GradeReportController::class, 'raporSingle'])->middleware(['auth', EnsureUserHasPermission::class . ':reports.download'])->name('rapor.pdf');
Route::get('/rapor-pdf-kelas/{class_group}', [GradeReportController::class, 'raporClass'])->middleware(['auth', EnsureUserHasPermission::class . ':reports.download'])->name('rapor.pdf.class');

/* G. DEBUG ROUTES */
if (app()->environment('local')) {
    Route::get('/clear-cache-sekarang', function () {
        Artisan::call('optimize:clear'); Artisan::call('cache:clear'); Artisan::call('route:clear');
        Artisan::call('view:clear'); Artisan::call('config:clear'); Artisan::call('filament:clear-cached-components');
        return 'Mantap! Semua cache berhasil dibersihkan.';
    });
}

require __DIR__ . '/auth.php';