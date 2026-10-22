<?php

namespace App\Filament\Resources\EvaluationResource\Pages;

use App\Filament\Resources\EvaluationResource;
use App\Features\Grades\Models\Evaluation;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateEvaluation extends CreateRecord
{
    protected static string $resource = EvaluationResource::class;

    public int $createdCount = 0;

    protected function handleRecordCreation(array $data): Model
    {
        $userIds = $data['user_ids'] ?? [];
        $firstRecord = null;
        $this->createdCount = 0;
        $jenis = $data['jenis'] ?? 'tilawah';
        
        // ⭐ Ambil nilai dari form, ubah menjadi Array JSON secara paksa
        $nilai = $data['nilai_evaluasi'] ?? null;
        $items = [];
        if ($nilai !== null && $nilai !== '') {
            $items = [
                [
                    'name'   => 'Nilai Evaluasi',
                    'status' => 'Tercapai',
                    'score'  => (float) $nilai,
                ],
            ];
        }

        foreach ($userIds as $userId) {
            $lastNumber = Evaluation::where('class_group_id', $data['class_group_id'])
                ->where('user_id', $userId)->where('jenis', $jenis)->max('evaluation_number') ?? 0;

            $record = Evaluation::create([
                'class_group_id'    => $data['class_group_id'],
                'user_id'           => $userId,
                'jenis'             => $jenis,
                'evaluation_number' => $lastNumber + 1,
                'surah_name'        => $data['surah_name'] ?? null,
                'song_name'         => $data['song_name'] ?? null,
                'items'             => $items, // ✅ Simpan JSON yang sudah kita buat manual
            ]);

            $this->createdCount++;
            if (! $firstRecord) $firstRecord = $record;
        }

        return $firstRecord;
    }

    protected function getCreateFormAction(): Actions\Action
    {
        return parent::getCreateFormAction()->label('💾 Simpan Evaluasi')->color('success');
    }

    protected function getCreatedNotification(): ?Notification
    {
        $count = $this->createdCount ?: 1;
        $title = $count > 1 ? "{$count} santri berhasil diberi evaluasi." : "Santri \"{$this->record->student?->name}\" berhasil diberi evaluasi.";
        return Notification::make()->success()->title('✅ Evaluasi Berhasil Disimpan')->body($title)->duration(4000);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}