<?php

namespace App\Filament\Resources\CharacterAssessmentResource\Pages;

use App\Filament\Resources\CharacterAssessmentResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCharacterAssessment extends EditRecord
{
    protected static string $resource = CharacterAssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Hapus')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation(),
        ];
    }

    protected function getSaveFormAction(): Actions\Action
    {
        return parent::getSaveFormAction()
            ->label('💾 Simpan Perubahan')
            ->color('success');
    }

    protected function getCancelFormAction(): Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Batal')
            ->url($this->getResource()::getUrl('index'));
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('✅ Perubahan Berhasil Disimpan')
            ->body("Penilaian karakter untuk \"{$this->record->student?->name}\" telah diperbarui.")
            ->duration(3000);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string
    {
        return "Edit Karakter • {$this->record->student?->name}";
    }
}