<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Features\Payments\Models\Payment;
use App\Features\Grades\Models\Grade;
use App\Features\Grades\Models\Assessment;
use App\Features\Grades\Models\Evaluation;
use App\Features\Grades\Models\CharacterAssessment; // ⭐ TAMBAHAN
use App\Features\Meetings\Models\Attendance;
use App\Features\Meetings\Models\Meeting;
use App\Features\Academic\Models\ClassGroup;
use App\Features\Permissions\Models\RolePermission;
use App\Features\TeacherAttendances\Models\TeacherAttendance;

// ── Filament ──────────────────────────────────────────────
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    // ==========================================================
    // KONSTANTA — Permissions & Fallback Routes Filament
    // ==========================================================

    public const PANEL_PERMISSIONS = [
        'dashboard.view',
        'users.view', 'users.create', 'users.update', 'users.delete', 'users.manage',
        'spmb.view', 'spmb.create', 'spmb.update', 'spmb.delete', 'spmb.approve', 'spmb.manage',
        'classes.view', 'classes.create', 'classes.update', 'classes.delete', 'classes.manage',
        'semesters.view', 'semesters.create', 'semesters.update', 'semesters.delete', 'semesters.manage',
        'meetings.view', 'meetings.create', 'meetings.update', 'meetings.delete', 'meetings.manage',
        'attendances.view', 'attendances.create', 'attendances.update', 'attendances.delete', 'attendances.manage',
        'teacher-attendances.view', 'teacher-attendances.create', 'teacher-attendances.update',
        'teacher-attendances.delete', 'teacher-attendances.manage',
        'teacher-attendances.check-in', 'teacher-attendances.report',
        'assessments.view', 'assessments.create', 'assessments.update', 'assessments.delete', 'assessments.manage',
        'evaluations.view', 'evaluations.create', 'evaluations.update', 'evaluations.delete', 'evaluations.manage',
        'grades.view', 'grades.create', 'grades.update', 'grades.delete', 'grades.manage',
        'reports.view', 'reports.download',
        'payments.view', 'payments.create', 'payments.update', 'payments.delete', 'payments.manage',
        'posts.view', 'posts.create', 'posts.update', 'posts.delete', 'posts.manage',
        'settings.view', 'settings.update', 'settings.manage',
    ];

    public const FILAMENT_FALLBACK_ROUTES = [
        'dashboard.view' => 'dashboard',
        'classes.view' => 'filament.admin.resources.class-groups.index',
        'classes.manage' => 'filament.admin.resources.class-groups.index',
        'meetings.view' => 'filament.admin.resources.meetings.index',
        'meetings.manage' => 'filament.admin.resources.meetings.index',
        'attendances.view' => 'filament.admin.resources.meetings.index',
        'attendances.manage' => 'filament.admin.resources.meetings.index',
        'teacher-attendances.view' => 'filament.admin.resources.teacher-attendances.index',
        'teacher-attendances.manage' => 'filament.admin.resources.teacher-attendances.index',
        'assessments.view' => 'filament.admin.resources.assessments.index',
        'assessments.manage' => 'filament.admin.resources.assessments.index',
        'evaluations.view' => 'filament.admin.resources.evaluations.index',
        'evaluations.manage' => 'filament.admin.resources.evaluations.index',
        'grades.view' => 'filament.admin.resources.grades.index',
        'grades.manage' => 'filament.admin.resources.grades.index',
        'reports.view' => 'filament.admin.resources.rapor.index',
        'payments.view' => 'filament.admin.resources.payments.index',
        'payments.manage' => 'filament.admin.resources.payments.index',
        'posts.view' => 'filament.admin.resources.posts.index',
        'posts.manage' => 'filament.admin.resources.posts.index',
        'spmb.view' => 'filament.admin.resources.candidates.index',
        'spmb.manage' => 'filament.admin.resources.candidates.index',
        'settings.view' => 'filament.admin.resources.site-settings.index',
        'settings.manage' => 'filament.admin.resources.site-settings.index',
        'semesters.view' => 'filament.admin.resources.semesters.index',
        'semesters.manage' => 'filament.admin.resources.semesters.index',
        'users.view' => 'filament.admin.resources.users.index',
        'users.manage' => 'filament.admin.resources.users.index',
    ];

    // ==========================================================
    // MASS ASSIGNMENT — Daftar kolom yang boleh diisi massal
    // ==========================================================
    protected $fillable = [
        // ── Akun dasar ─────────────────────────────────
        'name',
        'email',
        'password',
        'role',          // superadmin | admin | guru | student
        'is_active',     // status approve

        // ── Data Santri (PPDB) ─────────────────────────
        'nisn',
        'gender',
        'birth_place',
        'birth_date',
        'phone',

        // ── Orang Tua / Wali ───────────────────────────
        'father_name',
        'mother_name',
        'parent_name',
        'parent_phone',

        // ── Akademik ───────────────────────────────────
        'grade_level',
        'school_origin',
        'address',

        // ── Dokumen Persyaratan PPDB ───────────────────
        'file_kk',
        'file_akta',
        'file_ijazah',
        'file_foto',

        // ── Program & Persetujuan ──────────────────────
        'program',
        'terms_accepted_at',

        // ── Data Guru / Staff ──────────────────────────
        'nip',
        'position',
        'subject',

        // ── Profil ─────────────────────────────────────
        'profile_photo',
        'profile_photo_path',
    ];

    /**
     * Field yang disembunyikan saat serialization (JSON).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting tipe data kolom.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active'         => 'boolean',
            'birth_date'        => 'date',
            'terms_accepted_at' => 'datetime',
            // 'password' => 'hashed',   // ← JANGAN aktifkan selama controller pakai Hash::make()
        ];
    }

    // ==========================================================
    // RELASI — SISWA
    // ==========================================================

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function characterAssessments()
    {
        return $this->hasMany(CharacterAssessment::class, 'user_id');
    }

    // ==========================================================
    // RELASI — GURU
    // ==========================================================

    public function teachingMeetings()
    {
        return $this->hasMany(Meeting::class, 'user_id');
    }

    public function classGroupsAsTeacher()
    {
        return $this->hasMany(ClassGroup::class, 'teacher_id');
    }

    public function teacherAttendances()
    {
        return $this->hasMany(TeacherAttendance::class, 'user_id');
    }

    public function createdTeacherAttendances()
    {
        return $this->hasMany(TeacherAttendance::class, 'created_by');
    }

    // ==========================================================
    // RELASI — SANTRI
    // ==========================================================

    public function classGroups()
    {
        return $this->belongsToMany(ClassGroup::class, 'class_group_student', 'user_id', 'class_group_id')
            ->wherePivot('deleted_at', null)
            ->withPivot('joined_at', 'deleted_at')
            ->withTimestamps();
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    // ==========================================================
    // SCOPES — Query helper
    // ==========================================================

    public function scopeOfRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeStudents($query)
    {
        return $query->where('role', 'student');
    }

    public function scopeTeachers($query)
    {
        return $query->where('role', 'guru');
    }

    // ==========================================================
    // ACCESSOR — Virtual attribute
    // ==========================================================

    public function getParentFullNameAttribute(): string
    {
        return collect([$this->father_name, $this->mother_name])
            ->filter()
            ->implode(' & ');
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birth_date
            ? $this->birth_date->age
            : null;
    }

    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo_path) {
            return \Illuminate\Support\Facades\Storage::url($this->profile_photo_path);
        }

        if ($this->profile_photo) {
            return \Illuminate\Support\Facades\Storage::url($this->profile_photo);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=166534&color=fff';
    }

    // ==========================================================
    // ACCESSOR — Dokumen PPDB (via Route aman)
    // ==========================================================

    protected function buildPpdbFileUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $relative = str_replace('ppdb-documents/', '', $path);

        return route('ppdb.file', ['path' => $relative]);
    }

    public function getFileKkUrlAttribute(): ?string
    {
        return $this->buildPpdbFileUrl($this->file_kk);
    }

    public function getFileAktaUrlAttribute(): ?string
    {
        return $this->buildPpdbFileUrl($this->file_akta);
    }

    public function getFileIjazahUrlAttribute(): ?string
    {
        return $this->buildPpdbFileUrl($this->file_ijazah);
    }

    public function getFileFotoUrlAttribute(): ?string
    {
        return $this->buildPpdbFileUrl($this->file_foto);
    }

    // ==========================================================
    // HELPER — Cek role
    // ==========================================================

    public function isSuperadmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isAdmin(): bool
    {
        // ✅ Cek apakah role-nya superadmin ATAU admin
        return in_array($this->role, ['superadmin', 'admin'], true);
    }

    public function isTeacher(): bool
    {
        return $this->role === 'guru';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    // ==========================================================
    // PERMISSIONS
    // ==========================================================

    public function hasAccess(string $permission): bool
    {
        // ✅ Admin dan Superadmin otomatis punya akses ke semua fitur
        if ($this->isAdmin()) {
            return true;
        }

        if (! in_array($this->role, ['guru', 'student'], true)) {
            return false;
        }

        return RolePermission::query()
            ->where('role', $this->role)
            ->where('permission', $permission)
            ->where('is_allowed', true)
            ->exists();
    }

    public function hasAnyAccess(array $permissions): bool
    {
        // ✅ Admin dan Superadmin otomatis punya akses ke semua fitur
        if ($this->isAdmin()) {
            return true;
        }

        if (! in_array($this->role, ['guru', 'student'], true)) {
            return false;
        }

        return RolePermission::query()
            ->where('role', $this->role)
            ->whereIn('permission', $permissions)
            ->where('is_allowed', true)
            ->exists();
    }

    public function getFirstAllowedFilamentRoute(): ?string
    {
        // ✅ Admin dan Superadmin langsung masuk ke Dashboard Admin
        if ($this->isAdmin()) {
            return route('filament.admin.home');
        }

        if ($this->role === 'student') {
            return route('dashboard');
        }

        if ($this->role !== 'guru') {
            return null;
        }

        foreach (self::FILAMENT_FALLBACK_ROUTES as $permission => $routeName) {
            if ($this->hasAccess($permission) && app('router')->has($routeName)) {
                return route($routeName);
            }
        }

        return null;
    }

    // ==========================================================
    // FILAMENT — Kontrol akses panel admin
    // ==========================================================

    public function canAccessPanel(Panel $panel): bool
    {
        // ✅ Admin dan Superadmin bisa masuk panel
        if ($this->isAdmin()) {
            return true;
        }

        return $this->role === 'guru'
            && $this->hasAnyAccess(self::PANEL_PERMISSIONS);
    }
}