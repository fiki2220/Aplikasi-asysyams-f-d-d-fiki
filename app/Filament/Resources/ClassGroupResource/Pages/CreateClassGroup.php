<?php

namespace App\Filament\Resources\ClassGroupResource\Pages;

use App\Filament\Resources\ClassGroupResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateClassGroup extends CreateRecord
{
    protected static string $resource = ClassGroupResource::class;

    // ==========================================================
    // FORM ACTIONS
    // ==========================================================

    protected function getCreateFormAction(): Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('💾 Simpan Kelas')
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

    // ==========================================================
    // NOTIFIKASI
    // ==========================================================

    protected function getCreatedNotification(): ?Notification
    {
        $name = $this->record->name ?? '-';
        $subject = $this->record->subject?->name ?? '-';
        $semester = $this->record->semester?->name ?? '-';

        return Notification::make()
            ->success()
            ->title('✅ Kelas Berhasil Dibuat')
            ->body("Kelas: {$name} • Mapel: {$subject} • Semester: {$semester}")
            ->duration(5000);
    }

    // ==========================================================
    // REDIRECT
    // ==========================================================

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string
    {
        return 'Buat Kelas Baru';
    }
}