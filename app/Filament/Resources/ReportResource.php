<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Filament\Resources\ReportResource\RelationManagers;
use App\Features\Academic\Models\ClassGroup;
use App\Models\User;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReportResource extends Resource
{
    use \App\Filament\Concerns\ChecksResourcePermission;

    protected static ?string $model = ClassGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Akademik (Ustad)';
    protected static ?string $navigationLabel = 'Rapor Santri';
    protected static ?string $modelLabel = 'Rapor';
    protected static ?string $pluralModelLabel = 'Rapor Santri';
    protected static ?string $slug = 'rapor';
    protected static ?int $navigationSort = 5;

    protected static function permission(): string
    {
        return 'reports.view';
    }

    // ==========================================================
    // INFOLIST — Detail Rapor Kelas
    // ==========================================================

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Kelas')
                    ->description('Detail kelas dan pengampu.')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Nama Kelas')
                            ->weight('bold')
                            ->size('lg')
                            ->icon('heroicon-m-academic-cap'),

                        Infolists\Components\TextEntry::make('class_type')
                            ->label('Jenis Kelas')
                            ->badge()
                            ->color('primary')
                            ->formatStateUsing(fn (?string $state): string =>
                                ClassGroup::classTypeLabel($state) ?? $state ?? '—'),

                        Infolists\Components\TextEntry::make('semester.name')
                            ->label('Semester')
                            ->badge()
                            ->color('success')
                            ->icon('heroicon-m-calendar')
                            ->placeholder('—'),

                        Infolists\Components\TextEntry::make('teacher.name')
                            ->label('Ustad Pengampu')
                            ->icon('heroicon-m-user-circle')
                            ->placeholder('Belum ditentukan'),

                        Infolists\Components\TextEntry::make('students_count')
                            ->label('Jumlah Santri')
                            ->getStateUsing(fn (ClassGroup $record): int => $record->students()->count())
                            ->badge()
                            ->color('info')
                            ->icon('heroicon-m-users'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Statistik Nilai')
                    ->description('Ringkasan nilai santri di kelas ini.')
                    ->icon('heroicon-o-chart-bar-square')
                    ->schema([
                        Infolists\Components\TextEntry::make('avg_score')
                            ->label('Rata-rata Nilai')
                            ->getStateUsing(function (ClassGroup $record): string {
                                $studentIds = $record->students()->pluck('users.id')->all();

                                if (empty($studentIds)) {
                                    return '—';
                                }

                                $avg = \App\Features\Grades\Models\Grade::query()
                                    ->whereIn('user_id', $studentIds)
                                    ->where('semester_id', $record->semester_id)
                                    ->avg('score');

                                return $avg ? number_format($avg, 1) : '—';
                            })
                            ->badge()
                            ->color('success')
                            ->icon('heroicon-m-chart-bar'),

                        Infolists\Components\TextEntry::make('max_score')
                            ->label('Nilai Tertinggi')
                            ->getStateUsing(function (ClassGroup $record): string {
                                $studentIds = $record->students()->pluck('users.id')->all();

                                if (empty($studentIds)) {
                                    return '—';
                                }

                                $max = \App\Features\Grades\Models\Grade::query()
                                    ->whereIn('user_id', $studentIds)
                                    ->where('semester_id', $record->semester_id)
                                    ->max('score');

                                return $max ? number_format($max, 0) : '—';
                            })
                            ->badge()
                            ->color('info')
                            ->icon('heroicon-m-arrow-trending-up'),

                        Infolists\Components\TextEntry::make('min_score')
                            ->label('Nilai Terendah')
                            ->getStateUsing(function (ClassGroup $record): string {
                                $studentIds = $record->students()->pluck('users.id')->all();

                                if (empty($studentIds)) {
                                    return '—';
                                }

                                $min = \App\Features\Grades\Models\Grade::query()
                                    ->whereIn('user_id', $studentIds)
                                    ->where('semester_id', $record->semester_id)
                                    ->min('score');

                                return $min ? number_format($min, 0) : '—';
                            })
                            ->badge()
                            ->color('warning')
                            ->icon('heroicon-m-arrow-trending-down'),
                    ])
                    ->columns(3),
            ]);
    }

    // ==========================================================
    // TABLE
    // ==========================================================

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kelas')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->icon('heroicon-m-academic-cap')
                    ->description(fn (ClassGroup $record): ?string =>
                        $record->semester?->name
                            ? 'Semester: ' . $record->semester->name
                            : null),

                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('Ustad Pengampu')
                    ->icon('heroicon-m-user-circle')
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('students_count')
                    ->label('Santri')
                    ->counts('students')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'gray',
                        $state < 5 => 'warning',
                        default => 'success',
                    })
                    ->icon('heroicon-m-users')
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('class_type')
                    ->label('Jenis Kelas')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn (?string $state): string =>
                        ClassGroup::classTypeLabel($state) ?? $state ?? '—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('semester_id')
                    ->label('Semester')
                    ->relationship('semester', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\SelectFilter::make('teacher_id')
                    ->label('Ustad Pengampu')
                    ->options(User::whereIn('role', ['superadmin', 'guru'])->pluck('name', 'id'))
                    ->searchable()
                    ->multiple(),

                Tables\Filters\Filter::make('no_students')
                    ->label('Kelas Kosong (Tanpa Santri)')
                    ->query(fn (Builder $query): Builder => $query->doesntHave('students'))
                    ->toggle(),
            ])
            ->actions([
                // 🔵 Lihat Rapor
                Tables\Actions\Action::make('view_report')
                    ->label('Lihat Rapor')
                    ->icon('heroicon-o-document-chart-bar')
                    ->iconButton()
                    ->tooltip('Lihat Rapor Santri')
                    ->color('info')
                    ->url(fn (ClassGroup $record): string =>
                        static::getUrl('view', ['record' => $record])),

                // 🟢 Download ZIP semua rapor (filter periode dari URL)
                Tables\Actions\Action::make('download_pdf')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->iconButton()
                    ->tooltip('Download Rapor PDF (Semua Santri - ZIP)')
                    ->color('success')
                    ->url(fn (ClassGroup $record): string => route('rapor.pdf.class', [
                        'class_group' => $record->id,
                        'month'       => request()->query('month', now()->month),
                        'year'        => request()->query('year', now()->year),
                    ]))
                    ->openUrlInNewTab()
                    ->visible(fn (ClassGroup $record): bool => $record->students()->count() > 0),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->emptyStateHeading('Belum Ada Kelas')
            ->emptyStateDescription('Buat kelas terlebih dahulu di menu "Kelola Kelas" untuk melihat rapor.')
            ->emptyStateIcon('heroicon-o-document-text');
    }

    // ==========================================================
    // RELATIONS
    // ==========================================================

    public static function getRelations(): array
    {
        return [
            RelationManagers\StudentsRelationManager::class,
        ];
    }

    // ==========================================================
    // QUERY
    // ==========================================================

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user && $user->role === 'guru') {
            return $query->where('teacher_id', $user->id);
        }

        return $query;
    }

    // ==========================================================
    // AUTHORIZATION
    // ==========================================================

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

    // ==========================================================
    // PAGES
    // ==========================================================

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            'view' => Pages\ViewReport::route('/{record}'),
        ];
    }
}