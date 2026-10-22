<?php

namespace App\Filament\Resources\AssessmentResource\Pages;

use App\Filament\Resources\AssessmentResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewAssessment extends ViewRecord
{
    protected static string $resource = AssessmentResource::class;

    // ==========================================================
    // HEADER ACTIONS
    // ==========================================================

    protected function getHeaderActions(): array
    {
        return [
            // ⭐ Print Rapor PDF santri ini
            Actions\Action::make('print_rapor')
                ->label('Print Rapor PDF')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn () => route('rapor.pdf', [
                    'class_group' => $this->record->class_group_id,
                    'user'        => $this->record->user_id,
                ]))
                ->openUrlInNewTab()
                ->visible(fn (): bool => auth()->user()?->hasAccess('reports.download') ?? false),

            // ⭐ Input Lagi — untuk santri yang sama
            Actions\Action::make('input_lagi')
                ->label('Input Lagi')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->url(fn () => AssessmentResource::getUrl('create'))
                ->visible(fn (): bool => AssessmentResource::canCreate()),

            // Edit
            Actions\EditAction::make()
                ->label('Edit')
                ->icon('heroicon-o-pencil-square')
                ->color('warning'),

            // Delete
            Actions\DeleteAction::make()
                ->label('Hapus')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading('Hapus Penilaian?')
                ->modalDescription(fn () =>
                    "Penilaian untuk \"{$this->record->student?->name}\" akan dihapus permanen. "
                    . "Tindakan ini tidak bisa dibatalkan.")
                ->modalSubmitActionLabel('Ya, Hapus')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('✅ Penilaian Dihapus')
                        ->body("Data penilaian santri {$this->record->student?->name} berhasil dihapus.")
                ),

            // Back
            Actions\Action::make('back')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(AssessmentResource::getUrl('index')),
        ];
    }

    // ==========================================================
    // TITLE & SUBHEADING
    // ==========================================================

    public function getTitle(): string
    {
        return "Detail Penilaian • {$this->record->student?->name}";
    }

    public function getSubheading(): ?string
    {
        $record = $this->record;

        if (! $record) {
            return null;
        }

        $className = $record->classGroup?->name ?? '-';
        $teacherName = $record->classGroup?->teacher?->name ?? '-';
        $data = is_array($record->data) ? $record->data : [];
        $count = count($data);
        $batches = AssessmentResource::countBatches($data);

        $bulan = match ((int) $record->month) {
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
            default => '—',
        };

        return "{$className} • Ustad: {$teacherName} • Periode: {$bulan} {$record->year} • "
             . "{$count} setoran ({$batches}× input)";
    }

    // ==========================================================
    // BREADCRUMBS
    // ==========================================================

    public function getBreadcrumbs(): array
    {
        return [
            AssessmentResource::getUrl('index') => 'Input Penilaian',
            '' => $this->record->student?->name ?? 'Detail',
        ];
    }

    // ==========================================================
    // VIEW DATA — Inject nilai ke record (opsional)
    // ==========================================================

    protected function resolveRecord(int|string $key): \Illuminate\Database\Eloquent\Model
    {
        $record = parent::resolveRecord($key);

        // Pastikan relasi di-load untuk performa
        $record->loadMissing(['classGroup.teacher', 'student']);

        return $record;
    }
}