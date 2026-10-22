<?php

namespace App\Filament\Resources\EvaluationResource\Pages;

use App\Filament\Resources\EvaluationResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditEvaluation extends EditRecord
{
    protected static string $resource = EvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Hapus')->icon('heroicon-o-trash')->requiresConfirmation(),
        ];
    }

    // ⭐ Ambil data JSON, ubah ke angka agar muncul di kotak input saat edit
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['jenis'] = $data['jenis'] ?? 'tilawah';
        
        $items = $data['items'] ?? null;
        if (is_string($items)) $items = json_decode($items, true) ?? [];

        if (is_array($items) && ! empty($items)) {
            $scores = array_filter(array_map(fn($i) => $i['score'] ?? null, $items), fn($s) => $s !== null && $s !== '');
            $data['nilai_evaluasi'] = empty($scores) ? null : round(array_sum($scores) / count($scores), 2);
        } else {
            $data['nilai_evaluasi'] = null;
        }

        return $data;
    }

    // ⭐ Ambil angka dari input, ubah ke JSON secara paksa sebelum disave ke database
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $nilai = $data['nilai_evaluasi'] ?? null;

        if ($nilai !== null && $nilai !== '') {
            $data['items'] = [
                [
                    'name'   => 'Nilai Evaluasi',
                    'status' => 'Tercapai',
                    'score'  => (float) $nilai,
                ],
            ];
        } else {
            $data['items'] = null;
        }

        unset($data['nilai_evaluasi']);
        return $data;
    }

    protected function getSaveFormAction(): Actions\Action
    {
        return parent::getSaveFormAction()->label('💾 Simpan Perubahan')->color('success');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()->success()->title('✅ Perubahan Berhasil Disimpan')->duration(4000);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}