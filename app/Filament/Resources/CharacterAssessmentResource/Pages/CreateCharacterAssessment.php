<?php

namespace App\Filament\Resources\CharacterAssessmentResource\Pages;

use App\Filament\Resources\CharacterAssessmentResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateCharacterAssessment extends CreateRecord
{
    protected static string $resource = CharacterAssessmentResource::class;

    protected function getCreateFormAction(): Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('💾 Simpan Penilaian')
            ->color('success');
    }

    protected function getCreateAnotherFormAction(): Actions\Action
    {
        return parent::getCreateAnotherFormAction()
            ->label('Simpan & Tambah Lagi');
    }

    protected function getCancelFormAction(): Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Batal')
            ->url($this->getResource()::getUrl('index'));
    }

    protected function getCreatedNotification(): ?Notification
    {
        $student = $this->record->student?->name ?? '-';
        $avg = number_format($this->record->average_score, 1);

        return Notification::make()
            ->success()
            ->title('✅ Penilaian Karakter Berhasil Disimpan')
            ->body("Santri: {$student} • Rata-rata: {$avg}")
            ->duration(5000);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string
    {
        return 'Tambah Penilaian Karakter';
    }
}