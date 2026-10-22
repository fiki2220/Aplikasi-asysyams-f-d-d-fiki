<?php

namespace App\Filament\Resources\MeetingResource\Pages;

use App\Filament\Resources\MeetingResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateMeeting extends CreateRecord
{
    protected static string $resource = MeetingResource::class;

    public array $attendanceData = [];

    // ==========================================================
    // SEBELUM SIMPAN — Pisahkan absensi dari data meeting
    // ==========================================================

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->attendanceData = $data['students_attendance'] ?? [];
        unset($data['students_attendance']);

        return $data;
    }

    // ==========================================================
    // SETELAH SIMPAN — Simpan absensi ke tabel attendances
    // ==========================================================

    protected function afterCreate(): void
    {
        $meeting = $this->record;

        foreach ($this->attendanceData as $row) {
            if (empty($row['user_id'])) {
                continue;
            }

            $meeting->attendances()->create([
                'user_id' => $row['user_id'],
                'status' => $row['status'],
            ]);
        }
    }

    // ==========================================================
    // FORM ACTIONS CUSTOM
    // ==========================================================

    protected function getCreateFormAction(): Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('💾 Simpan Pertemuan')
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
    // NOTIFIKASI SETELAH CREATE
    // ==========================================================

    protected function getCreatedNotification(): ?Notification
    {
        $title = $this->record->title ?? '-';
        $class = $this->record->classGroup?->name ?? '-';
        $count = count($this->attendanceData);

        return Notification::make()
            ->success()
            ->title('✅ Pertemuan Berhasil Dibuat')
            ->body("Topik: {$title} • Kelas: {$class} • {$count} santri tercatat hadir.")
            ->duration(5000);
    }

    // ==========================================================
    // REDIRECT SETELAH CREATE
    // ==========================================================

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // ==========================================================
    // TITLE
    // ==========================================================
    // ⚠️ getBreadcrumbs() DIHAPUS — biarkan Filament handle otomatis

    public function getTitle(): string
    {
        return 'Buat Pertemuan Baru';
    }
}