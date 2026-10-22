<?php

namespace App\Filament\Resources\AssessmentResource\Pages;

use App\Features\Academic\Models\ClassGroup;
use App\Features\Academic\Models\Subject;
use App\Features\Grades\Models\Assessment;
use App\Features\Grades\Models\Grade;
use App\Filament\Resources\AssessmentResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateAssessment extends CreateRecord
{
    protected static string $resource = AssessmentResource::class;

    public int $createdCount = 0;
    public int $totalCount = 0;
    public array $jenisSummary = [];
    public int $nilaiSavedCount = 0;

    public bool $mergedIntoExisting = false;
    public ?int $existingRecordId = null;

    // ==========================================================
    // HANDLE CREATE — Auto-merge + Track Pengajar
    // ==========================================================

    protected function handleRecordCreation(array $data): Model
    {
        $jenisAktif = $data['jenis_aktif'] ?? [];
        $allData = [];

        // ⭐ Ambil info user yang login (pengajar)
        $currentUser = auth()->user();
        $creatorId   = $currentUser?->id;
        $creatorName = $currentUser?->name ?? 'Sistem';

        // 1. Gabungkan semua item setoran dari setiap jenis
        foreach ($jenisAktif as $jenis) {
            $fieldName = "{$jenis}_data";
            $items = $data[$fieldName] ?? [];

            foreach ($items as $item) {
                $item['jenis'] = $jenis;

                // Backend safeguard
                $item['nilai'] = AssessmentResource::kategoriDariNilai(
                    $item['nilai_penyetoran'] ?? null
                );

                if (empty($item['id'])) {
                    $item['id'] = (string) Str::uuid();
                }

                $item['_created_at'] = now()->toIso8601String();

                // ⭐ BARU: Track pengajar per setoran
                $item['_created_by']      = $creatorId;
                $item['_created_by_name'] = $creatorName;

                $allData[] = $item;
            }
        }

        // 2. Validasi minimal ada 1 setoran
        if (empty($allData)) {
            Notification::make()
                ->danger()
                ->title('Tidak Ada Setoran yang Diisi')
                ->body('Pilih minimal 1 jenis setoran dan isi minimal 1 baris.')
                ->send();

            throw new \Exception('Tidak ada setoran yang diisi.');
        }

        $classGroupId = (int) $data['class_group_id'];
        $userId       = (int) $data['user_id'];
        $month        = (int) ($data['month'] ?? now()->month);
        $year         = (int) ($data['year']  ?? now()->year);

        $this->createdCount = count($allData);
        $this->jenisSummary = $jenisAktif;

        // 3. Cek existing record
        $existing = Assessment::query()
            ->where('class_group_id', $classGroupId)
            ->where('user_id', $userId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        if ($existing) {
            $existingData = is_array($existing->data) ? $existing->data : [];
            $mergedData = array_merge($existingData, $allData);

            $existingJenis = array_filter(array_map('trim', explode(',', $existing->assessment_type ?? '')));
            $mergedJenis = array_values(array_unique(array_merge($existingJenis, $jenisAktif)));

            $existing->update([
                'assessment_type' => implode(',', $mergedJenis),
                'data'            => $mergedData,
                'created_by'      => $creatorId,  // ⭐ Update ke pengajar terakhir
            ]);

            $this->mergedIntoExisting = true;
            $this->existingRecordId = $existing->id;
            $this->totalCount = count($mergedData);

            $this->nilaiSavedCount = $this->saveNilaiPeningkatan($data);

            return $existing;
        }

        // 4. Buat record baru
        $assessment = Assessment::create([
            'class_group_id'  => $classGroupId,
            'user_id'         => $userId,
            'created_by'      => $creatorId,  // ⭐ Track pengajar
            'assessment_type' => implode(',', $jenisAktif),
            'month'           => $month,
            'year'            => $year,
            'data'            => $allData,
        ]);

        $this->totalCount = count($allData);

        $this->nilaiSavedCount = $this->saveNilaiPeningkatan($data);

        return $assessment;
    }

    // ==========================================================
    // SAVE NILAI PENINGKATAN
    // ==========================================================

    protected function saveNilaiPeningkatan(array $data): int
    {
        $classGroup = ClassGroup::find($data['class_group_id']);

        if (! $classGroup?->semester_id) {
            return 0;
        }

        $mapelDiizinkan = AssessmentResource::mapelUntukKelas($classGroup->name);

        $userId = (int) $data['user_id'];
        $savedCount = 0;

        foreach (AssessmentResource::NILAI_PENINGKATAN_MAPEL as $fieldName => $subjectName) {
            if (! in_array($subjectName, $mapelDiizinkan, true)) {
                continue;
            }

            $score = $data[$fieldName] ?? null;

            $subject = Subject::firstOrCreate(
                ['name' => $subjectName],
                ['slug' => Str::slug($subjectName)]
            );

            if ($score === null || $score === '') {
                Grade::query()
                    ->where('user_id', $userId)
                    ->where('subject_id', $subject->id)
                    ->where('semester_id', $classGroup->semester_id)
                    ->delete();

                continue;
            }

            Grade::updateOrCreate(
                [
                    'user_id'     => $userId,
                    'subject_id'  => $subject->id,
                    'semester_id' => $classGroup->semester_id,
                ],
                ['score' => (float) $score]
            );

            $savedCount++;
        }

        return $savedCount;
    }

    // ==========================================================
    // FORM ACTIONS
    // ==========================================================

    protected function getCreateFormAction(): Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('💾 Simpan Semua')
            ->color('success');
    }

    protected function getCreateAnotherFormAction(): Actions\Action
    {
        return parent::getCreateAnotherFormAction()
            ->label('Simpan & Input Lagi');
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
        if (! $this->record) {
            return null;
        }

        $jenisLabels = AssessmentResource::JENIS_LABELS;

        $summary = collect($this->jenisSummary)
            ->map(fn ($j) => $jenisLabels[$j] ?? $j)
            ->implode(' + ');

        $santri = $this->record->student?->name ?? '—';

        if ($this->mergedIntoExisting) {
            $body = "Santri: {$santri}"
                  . " • Setoran baru: {$this->createdCount} baris"
                  . " • Total sekarang: {$this->totalCount} baris"
                  . " • Jenis: {$summary}";

            if ($this->nilaiSavedCount > 0) {
                $body .= " • Nilai Rapor: {$this->nilaiSavedCount} mapel";
            }

            return Notification::make()
                ->success()
                ->title('🔄 Setoran Ditambahkan')
                ->body($body)
                ->duration(6000);
        }

        $body = "Santri: {$santri}"
              . " • Setoran: {$this->createdCount} baris"
              . " • Jenis: {$summary}";

        if ($this->nilaiSavedCount > 0) {
            $body .= " • Nilai Rapor: {$this->nilaiSavedCount} mapel";
        }

        return Notification::make()
            ->success()
            ->title('✅ Data Berhasil Disimpan')
            ->body($body)
            ->duration(5000);
    }

    protected function getRedirectUrl(): string
    {
        if ($this->mergedIntoExisting && $this->existingRecordId) {
            return $this->getResource()::getUrl('edit', ['record' => $this->existingRecordId]);
        }

        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string
    {
        return 'Input Penilaian Santri';
    }
}