<?php

namespace App\Filament\Resources\ReportResource\RelationManagers;

use App\Features\Academic\Models\ClassGroup;
use App\Models\User;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'students';

    protected static ?string $title = 'Daftar Nilai & Kehadiran Santri';

    protected static ?string $icon = 'heroicon-o-users';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                // Nama Santri
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Santri')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->icon('heroicon-m-user-circle')
                    ->description(fn (User $record): ?string =>
                        $record->nisn ? 'NISN: ' . $record->nisn : null),

                // NISN (toggleable)
                Tables\Columns\TextColumn::make('nisn')
                    ->label('NISN')
                    ->fontFamily('mono')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                // ==========================================================
                // KOLOM KEHADIRAN — pakai status yang benar: present/sick/permission/alpha
                // ==========================================================

                Tables\Columns\TextColumn::make('hadir')
                    ->label('H')
                    ->badge()
                    ->color('success')
                    ->alignCenter()
                    ->tooltip('Hadir')
                    ->getStateUsing(fn (User $record): int =>
                        $this->countAttendance($record, 'present')),

                Tables\Columns\TextColumn::make('sakit')
                    ->label('S')
                    ->badge()
                    ->color('info')
                    ->alignCenter()
                    ->tooltip('Sakit')
                    ->getStateUsing(fn (User $record): int =>
                        $this->countAttendance($record, 'sick')),

                Tables\Columns\TextColumn::make('izin')
                    ->label('I')
                    ->badge()
                    ->color('warning')
                    ->alignCenter()
                    ->tooltip('Izin')
                    ->getStateUsing(fn (User $record): int =>
                        $this->countAttendance($record, 'permission')),

                Tables\Columns\TextColumn::make('alpha')
                    ->label('A')
                    ->badge()
                    ->color('danger')
                    ->alignCenter()
                    ->tooltip('Alpha')
                    ->getStateUsing(fn (User $record): int =>
                        $this->countAttendance($record, 'alpha')),

                // ==========================================================
                // PERSENTASE KEHADIRAN
                // ==========================================================

                Tables\Columns\TextColumn::make('persentase')
                    ->label('% Kehadiran')
                    ->badge()
                    ->alignCenter()
                    ->icon('heroicon-m-chart-bar')
                    ->color(fn (string $state): string => match (true) {
                        $state === '—' => 'gray',
                        (int) $state >= 80 => 'success',
                        (int) $state >= 60 => 'warning',
                        default => 'danger',
                    })
                    ->getStateUsing(function (User $record): string {
                        $total = $this->getOwnerRecord()->meetings()->count();

                        if ($total === 0) {
                            return '—';
                        }

                        $hadir = $this->countAttendance($record, 'present');

                        return round(($hadir / $total) * 100) . '%';
                    }),

                // ==========================================================
                // NILAI RATA-RATA (dari tabel grades)
                // ==========================================================

                Tables\Columns\TextColumn::make('avg_score')
                    ->label('Nilai')
                    ->badge()
                    ->alignCenter()
                    ->icon('heroicon-m-star')
                    ->color(fn (string $state): string => match (true) {
                        $state === '—' => 'gray',
                        (float) $state >= 90 => 'success',
                        (float) $state >= 75 => 'info',
                        (float) $state >= 60 => 'warning',
                        default => 'danger',
                    })
                    ->getStateUsing(function (User $record): string {
                        $classGroup = $this->getOwnerRecord();

                        $avg = $record->grades()
                            ->where('subject_id', $classGroup->subject_id)
                            ->where('semester_id', $classGroup->semester_id)
                            ->avg('score');

                        return $avg ? number_format($avg, 1) : '—';
                    }),
            ])
            ->filters([
                Tables\Filters\Filter::make('below_kkm')
                    ->label('⚠️ Nilai Di Bawah KKM (<60)')
                    ->query(function ($query) {
                        $classGroup = $this->getOwnerRecord();

                        return $query->whereHas('grades', fn ($q) =>
                            $q->where('subject_id', $classGroup->subject_id)
                              ->where('semester_id', $classGroup->semester_id)
                              ->where('score', '<', 60)
                        );
                    })
                    ->toggle(),
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\Action::make('lihat_rapor')
                    ->label('Lihat Rapor')
                    ->icon('heroicon-o-document-chart-bar')
                    ->iconButton()
                    ->tooltip('Lihat Rapor Santri')
                    ->color('info')
                    ->modalHeading(fn (User $record): string => 'Rapor Santri: ' . $record->name)
                    ->modalWidth('7xl')
                    ->modalContent(fn (User $record) => view('filament.report.rapor-modal', [
                        'student' => $record,
                        'classGroup' => $this->getOwnerRecord(),
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),
            ])
            ->bulkActions([])
            ->defaultSort('name', 'asc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(10)
            ->emptyStateHeading('Belum Ada Santri')
            ->emptyStateDescription('Kelas ini belum memiliki santri. Tambahkan santri di menu "Kelola Kelas".')
            ->emptyStateIcon('heroicon-o-user-group');
    }

    // ==========================================================
    // HELPER — Hitung kehadiran (cache agar tidak query berulang)
    // ==========================================================

    /**
     * Hitung kehadiran per status, dengan cache per record.
     * Query hanya sekali per (user, status) — bukan N+1.
     */
    protected function countAttendance(User $record, string $status): int
    {
        $cacheKey = "attendance_{$record->id}_{$status}";

        if (isset($this->attendanceCache[$cacheKey])) {
            return $this->attendanceCache[$cacheKey];
        }

        $meetingIds = $this->getOwnerRecord()->meetings()->pluck('id');

        $count = $record->attendances()
            ->where('status', $status)
            ->whereIn('meeting_id', $meetingIds)
            ->count();

        $this->attendanceCache[$cacheKey] = $count;

        return $count;
    }

    /**
     * Cache array per instance.
     */
    protected array $attendanceCache = [];
}