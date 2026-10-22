<?php

namespace App\Filament\Resources\MeetingResource\Pages;

use App\Filament\Resources\MeetingResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditMeeting extends EditRecord
{
    protected static string $resource = MeetingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Hapus')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading('Hapus Pertemuan?')
                ->modalDescription(fn () => "Pertemuan \"{$this->record->title}\" beserta data absensinya akan dihapus permanen.")
                ->modalSubmitActionLabel('Ya, Hapus'),
        ];
    }

    // ==========================================================
    // FORM ACTIONS CUSTOM
    // ==========================================================

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

    // ==========================================================
    // NOTIFIKASI SETELAH SAVE
    // ==========================================================

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('✅ Perubahan Berhasil Disimpan')
            ->body("Pertemuan \"{$this->record->title}\" telah diperbarui.")
            ->duration(3000);
    }

    // ==========================================================
    // REDIRECT SETELAH SAVE
    // ==========================================================

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // ==========================================================
    // TITLE
    // ==========================================================
    // ⚠️ getBreadcrumbs() DIHAPUS — biarkan Filament handle otomatis
    //    (format sebelumnya salah, bikin URL mentah muncul)

    public function getTitle(): string
    {
        return "Edit Pertemuan • {$this->record->title}";
    }
}