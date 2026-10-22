<?php

namespace App\Filament\Resources;

use App\Features\Academic\Models\ClassGroup;
use App\Features\Academic\Models\Semester;
use App\Features\Grades\Models\Assessment;
use App\Features\Grades\Models\Grade;
use App\Features\Grades\Services\GradeReportService;
use App\Filament\Resources\GradeResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class GradeResource extends Resource
{
    use \App\Filament\Concerns\ChecksResourcePermission;

    protected static ?string $model = Grade::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Akademik (Ustad)';
    protected static ?string $navigationLabel = 'Rekapan Buku Harian Santri';
    protected static ?string $modelLabel = 'Rekapan Buku Harian';
    protected static ?string $pluralModelLabel = 'Rekapan Buku Harian Santri';
    protected static ?int $navigationSort = 4;

    protected static function permission(): string
    {
        return 'grades.manage';
    }

    // ==========================================================
    // FORM
    // ==========================================================

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Nilai')
                    ->description('Pilih santri, jenis nilai, dan semester.')
                    ->icon('heroicon-o-book-open')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('student', 'name')
                            ->label('Santri')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required(),

                        Forms\Components\Select::make('subject_id')
                            ->relationship('subject', 'name')
                            ->label('Jenis Nilai')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required()
                            ->helperText('Pilih: Tilawah, Tahsin, atau Tahfizh.'),

                        Forms\Components\Select::make('semester_id')
                            ->relationship('semester', 'name')
                            ->label('Semester')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required(),

                        Forms\Components\TextInput::make('score')
                            ->label('Nilai')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->required()
                            ->suffix('/ 100')
                            ->helperText('Masukkan nilai 0-100.'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Catatan Tambahan')
                    ->description('Catatan evaluasi untuk santri (opsional).')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->placeholder('Contoh: Bacaan makin lancar, masih perlu latihan mad.')
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    // ==========================================================
    // TABLE
    // ==========================================================

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                static::getEloquentQuery()
                    ->select('grades.user_id', 'grades.semester_id')
                    ->selectRaw('MIN(grades.id) as id')
                    ->groupBy('grades.user_id', 'grades.semester_id')
                    ->with(['student:id,name,nisn', 'semester:id,name'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Santri')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->icon('heroicon-m-user-circle'),

                Tables\Columns\TextColumn::make('class_names')
                    ->label('Kelas')
                    ->getStateUsing(fn(Grade $record): string => static::getClassNamesForStudent($record))
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-m-academic-cap')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('nilai_tilawah')
                    ->label('Tilawah')
                    ->getStateUsing(fn(Grade $record): ?float => static::getScoreForSubject($record, 'Tilawah'))
                    ->badge()
                    ->color(fn($state): string => match (true) {
                        $state === null => 'gray',
                        $state >= 90 => 'success',
                        $state >= 75 => 'info',
                        $state >= 60 => 'warning',
                        default => 'danger',
                    })
                    ->icon(fn($state): string => match (true) {
                        $state === null => 'heroicon-m-minus',
                        $state >= 90 => 'heroicon-m-star',
                        $state >= 75 => 'heroicon-m-check-circle',
                        default => 'heroicon-m-exclamation-circle',
                    })
                    ->alignCenter()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('nilai_tahsin')
                    ->label('Tahsin')
                    ->getStateUsing(fn(Grade $record): ?float => static::getScoreForSubject($record, 'Tahsin'))
                    ->badge()
                    ->color(fn($state): string => match (true) {
                        $state === null => 'gray',
                        $state >= 90 => 'success',
                        $state >= 75 => 'info',
                        $state >= 60 => 'warning',
                        default => 'danger',
                    })
                    ->icon(fn($state): string => match (true) {
                        $state === null => 'heroicon-m-minus',
                        $state >= 90 => 'heroicon-m-star',
                        $state >= 75 => 'heroicon-m-check-circle',
                        default => 'heroicon-m-exclamation-circle',
                    })
                    ->alignCenter()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('nilai_tahfizh')
                    ->label('Tahfizh')
                    ->getStateUsing(fn(Grade $record): ?float => static::getScoreForSubject($record, 'Tahfizh'))
                    ->badge()
                    ->color(fn($state): string => match (true) {
                        $state === null => 'gray',
                        $state >= 90 => 'success',
                        $state >= 75 => 'info',
                        $state >= 60 => 'warning',
                        default => 'danger',
                    })
                    ->icon(fn($state): string => match (true) {
                        $state === null => 'heroicon-m-minus',
                        $state >= 90 => 'heroicon-m-star',
                        $state >= 75 => 'heroicon-m-check-circle',
                        default => 'heroicon-m-exclamation-circle',
                    })
                    ->alignCenter()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('average')
                    ->label('Rata-rata')
                    ->getStateUsing(function (Grade $record): ?float {
                        $scores = [
                            static::getScoreForSubject($record, 'Tilawah'),
                            static::getScoreForSubject($record, 'Tahsin'),
                            static::getScoreForSubject($record, 'Tahfizh'),
                        ];

                        $valid = array_filter($scores, fn($s) => $s !== null);

                        return count($valid) > 0 ? round(array_sum($valid) / count($valid), 1) : null;
                    })
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-m-chart-bar')
                    ->alignCenter()
                    ->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('semester_id')
                    ->label('Semester')
                    ->relationship('semester', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->actions([
                // ⭐ Lihat Tabel (modal)
                Tables\Actions\Action::make('lihat_tabel_santri')
                    ->label('Lihat Tabel')
                    ->icon('heroicon-o-table-cells')
                    ->iconButton()
                    ->tooltip('Lihat Tabel Nilai Santri')
                    ->color('info')
                    ->visible(fn(Grade $record): bool => static::canAccessStudentControl($record))
                    ->modalHeading(fn(Grade $record): string => 'Tabel Nilai ' . $record->student?->name)
                    ->modalWidth('7xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(function (Grade $record) {
                        $student = $record->student;
                        if (! $student) {
                            return null;
                        }

                        $assessment = Assessment::query()
                            ->with('classGroup:id,semester_id')
                            ->where('user_id', $record->user_id)
                            ->latest('created_at')
                            ->first();

                        if (! $assessment) {
                            return null;
                        }

                        $semesterId = $assessment->classGroup?->semester_id ?? $record->semester_id;

                        $gradeForReport = Grade::query()
                            ->where('user_id', $record->user_id)
                            ->where('semester_id', $semesterId)
                            ->first() ?? $record;

                        return view(
                            'grades.student-control-modal',
                            app(GradeReportService::class)->buildStudentControlReport($student, $gradeForReport)
                        );
                    }),

                // ⭐ Download PDF
                Tables\Actions\Action::make('download_pdf')
                    ->label('Download Tabel Nilai')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->iconButton()
                    ->tooltip('Download Tabel Nilai (PDF)')
                    ->color('success')
                    ->visible(fn(Grade $record): bool => static::canAccessStudentControl($record))
                    ->url(fn(Grade $record): string => route('admin.grades.student-control.pdf', [
                        'user' => $record->user_id,
                        'grade' => $record->id,
                    ]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([])
            ->defaultSort('grades.user_id', 'asc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(10)
            ->emptyStateHeading('Belum Ada Rekapan Buku Harian')
            ->emptyStateDescription('Data nilai akan muncul setelah diinput dari menu "Input Penilaian Santri".')
            ->emptyStateIcon('heroicon-o-book-open');
    }

    // ==========================================================
    // HELPERS (Preloaded per Semester — Anti N+1 Query)
    // ==========================================================

    protected static function getClassNamesForStudent(Grade $record): string
    {
        static $cache = [];
        $semesterId = (int) $record->semester_id;

        if (! isset($cache[$semesterId])) {
            $cache[$semesterId] = [];

            $groups = ClassGroup::query()
                ->where('semester_id', $semesterId)
                ->with(['students:users.id'])
                ->get(['id', 'name', 'semester_id']);

            foreach ($groups as $group) {
                foreach ($group->students as $student) {
                    $cache[$semesterId][$student->id][] = $group->name;
                }
            }
        }

        return implode(', ', $cache[$semesterId][$record->user_id] ?? []);
    }

    protected static function getScoreForSubject(Grade $record, string $subjectName): ?float
    {
        static $cache = [];
        $semesterId = (int) $record->semester_id;

        if (! isset($cache[$semesterId])) {
            $cache[$semesterId] = [];

            $grades = Grade::query()
                ->where('semester_id', $semesterId)
                ->with(['subject:id,name'])
                ->get(['user_id', 'subject_id', 'semester_id', 'score']);

            foreach ($grades as $g) {
                if ($g->subject) {
                    $cache[$semesterId]["{$g->user_id}_{$g->subject->name}"] = (float) $g->score;
                }
            }
        }

        return $cache[$semesterId]["{$record->user_id}_{$subjectName}"] ?? null;
    }

    // ==========================================================
    // QUERY
    // ==========================================================

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user && $user->role === 'guru') {
            return $query->whereExists(function ($sub) use ($user) {
                $sub->selectRaw(1)
                    ->from('class_groups')
                    ->join('class_group_student', 'class_groups.id', '=', 'class_group_student.class_group_id')
                    ->where('class_groups.teacher_id', $user->id)
                    ->whereColumn('class_groups.semester_id', 'grades.semester_id')
                    ->whereColumn('class_group_student.user_id', 'grades.user_id')
                    ->whereNull('class_group_student.deleted_at');
            });
        }

        return $query;
    }

    // ==========================================================
    // AUTHORIZATION
    // ==========================================================

    protected static function canTeacherAccessGrade(?User $user, ?Grade $record): bool
    {
        if (! $user || ! $record) {
            return false;
        }

        if ($user->role !== 'guru') {
            return true;
        }

        return ClassGroup::query()
            ->where('teacher_id', $user->id)
            ->where('semester_id', $record->semester_id)
            ->whereHas('students', fn($q) => $q->where('users.id', $record->user_id))
            ->exists();
    }

    public static function canView($record): bool
    {
        if (! static::canAccessAction('view')) {
            return false;
        }
        $user = auth()->user();
        if ($user && $user->role === 'guru') {
            return static::canTeacherAccessGrade($user, $record);
        }
        return true;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    private static function canAccessStudentControl(Grade $record): bool
    {
        $user = Auth::user();

        if (! $user || ! $record->student) {
            return false;
        }

        if ($user->role === 'guru' && ! static::canTeacherAccessGrade($user, $record)) {
            return false;
        }

        return app(GradeReportService::class)->canAccessStudentReport($user, $record->student, $record);
    }

    // ==========================================================
    // PAGES
    // ==========================================================

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGrades::route('/'),
            'create' => Pages\CreateGrade::route('/create'),
            'edit' => Pages\EditGrade::route('/{record}/edit'),
        ];
    }
}
