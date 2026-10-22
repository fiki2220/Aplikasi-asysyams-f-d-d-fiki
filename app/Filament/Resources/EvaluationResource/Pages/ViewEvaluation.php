<?php

namespace App\Filament\Resources\EvaluationResource\Pages;

use App\Filament\Resources\EvaluationResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewEvaluation extends ViewRecord
{
    protected static string $resource = EvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('input_lagi')->label('Input Lagi')->icon('heroicon-o-plus-circle')->color('primary')
                ->url(fn () => EvaluationResource::getUrl('create'))->visible(fn (): bool => EvaluationResource::canCreate()),
            Actions\EditAction::make()->label('Edit')->icon('heroicon-o-pencil-square')->color('warning'),
            Actions\DeleteAction::make()->label('Hapus')->icon('heroicon-o-trash')->requiresConfirmation()
                ->modalHeading('Hapus Evaluasi?')->modalDescription(fn () => "Evaluasi untuk \"{$this->record->student?->name}\" akan dihapus permanen.")
                ->modalSubmitActionLabel('Ya, Hapus')->successNotification(Notification::make()->success()->title('✅ Evaluasi Dihapus')),
            Actions\Action::make('back')->label('Kembali')->icon('heroicon-o-arrow-left')->color('gray')->url(EvaluationResource::getUrl('index')),
        ];
    }

    public function getTitle(): string
    {
        return "Detail Evaluasi • {$this->record->student?->name}";
    }

    public function getSubheading(): ?string
    {
        $record = $this->record;
        if (! $record) return null;

        $class = $record->classGroup?->name ?? '-';
        $teacher = $record->classGroup?->teacher?->name ?? '-';
        $jenis = EvaluationResource::JENIS_OPTIONS[$record->jenis] ?? '-';

        $parts = [$class, "Ustad: {$teacher}", "Jenis: {$jenis}"];

        if ($record->jenis === 'tilawah' && $record->surah_name) $parts[] = "Surah: {$record->surah_name}";
        if ($record->song_name) {
            $maqom = EvaluationResource::maqomLabel($record->song_name);
            $parts[] = ($record->jenis === 'murottal' ? "Irama: " : "Maqom: ") . $maqom;
        }

        return implode(' • ', $parts);
    }

    public function getBreadcrumbs(): array
    {
        return [
            EvaluationResource::getUrl('index') => 'Input Evaluasi',
            '' => $this->record->student?->name ?? 'Detail',
        ];
    }

    protected function resolveRecord(int|string $key): \Illuminate\Database\Eloquent\Model
    {
        $record = parent::resolveRecord($key);
        $record->loadMissing(['classGroup.teacher', 'student']);
        return $record;
    }
}