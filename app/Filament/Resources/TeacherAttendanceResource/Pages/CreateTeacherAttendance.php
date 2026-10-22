<?php

namespace App\Filament\Resources\TeacherAttendanceResource\Pages;

use App\Filament\Resources\TeacherAttendanceResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CreateTeacherAttendance extends CreateRecord
{
    protected static string $resource = TeacherAttendanceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id();

        // ⏰ OTOMATISASI STATUS UNTUK INPUT MANUAL
        // Jika form diisi manual, dan jam check-in lewat dari jam 08:00, otomatis jadi 'late'
        if (!empty($data['check_in_at'])) {
            $checkInTime = Carbon::parse($data['check_in_at']);
            $batasMasuk = Carbon::parse($data['check_in_at'])->setTime(8, 0);
            
            // Jika tidak diubah manual oleh admin, biarkan sistem yang menentukan
            if ($data['status'] === 'present' && $checkInTime->gt($batasMasuk)) {
                $data['status'] = 'late';
            }
        }

        return $data;
    }

    protected function getCreateFormAction(): Actions\Action
    {
        return parent::getCreateFormAction()->label('💾 Simpan Absensi')->color('success');
    }

    protected function getCancelFormAction(): Actions\Action
    {
        return parent::getCancelFormAction()->label('Batal')->url($this->getResource()::getUrl('index'));
    }

    protected function getCreatedNotification(): ?Notification
    {
        $name = $this->record->user?->name ?? '-';
        $date = $this->record->date?->format('d M Y') ?? '-';
        return Notification::make()->success()->title('✅ Absensi Berhasil Dicatat')->body("Absensi untuk {$name} pada {$date}.")->duration(4000);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string
    {
        return 'Tambah Absensi Ustad';
    }
}