<?php

namespace App\Filament\Resources\AssessmentResource\Pages;

use App\Features\Academic\Models\Subject;
use App\Features\Grades\Models\Grade;
use App\Filament\Resources\AssessmentResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditAssessment extends EditRecord
{
    protected static string $resource = AssessmentResource::class;

    public int $nilaiSavedCount = 0;

    // ==========================================================
    // HEADER ACTIONS
    // ==========================================================

    protected function getHeaderActions(): array
    {
        return [
            // ⭐ Print Rapor PDF
            Actions\Action::make('print_rapor')
                ->label('Print Rapor PDF')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn () => route('rapor.pdf', [
                    'class_group' => $this->record->class_group_id,
                    'user'        => $this->record->user_id,
                ]))
                ->openUrlInNewTab()
                ->visible(fn (): bool => auth()->user()?->hasAccess('reports.download') ?? false),

            // Lihat detail
            Actions\ViewAction::make()
                ->label('Lihat')
                ->icon('heroicon-o-eye')
                ->color('info'),

            // Hapus
            Actions\DeleteAction::make()
                ->label('Hapus')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading('Hapus Penilaian?')
                ->modalDescription(fn () =>
                    "Penilaian untuk \"{$this->record->student?->name}\" akan dihapus permanen.")
                ->modalSubmitActionLabel('Ya, Hapus')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('✅ Penilaian Dihapus')
                        ->body("Data penilaian santri {$this->record->student?->name} berhasil dihapus.")
                ),
        ];
    }

    // ==========================================================
    // LOAD EXISTING NILAI KE FORM
    // ==========================================================

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $classGroup = $this->record->classGroup;

        if (! $classGroup?->semester_id) {
            return $data;
        }

        // ⭐ Filter mapel sesuai kelas
        $mapelDiizinkan = AssessmentResource::mapelUntukKelas($classGroup->name);

        foreach (AssessmentResource::NILAI_PENINGKATAN_MAPEL as $fieldName => $subjectName) {
            if (! in_array($subjectName, $mapelDiizinkan, true)) {
                continue;
            }

            $subject = Subject::where('name', $subjectName)->first();

            if (! $subject) {
                continue;
            }

            $grade = Grade::query()
                ->where('user_id', $this->record->user_id)
                ->where('subject_id', $subject->id)
                ->where('semester_id', $classGroup->semester_id)
                ->first();

            if ($grade) {
                $data[$fieldName] = $grade->score;
            }
        }

        // ⭐ Pastikan field maqra_wajib & maqom ada di data (kalau dari record lama)
        if (isset($data['data']) && is_array($data['data'])) {
            $data['data'] = array_map(function (array $item): array {
                // Default null kalau tidak ada — agar repeater tetap konsisten
                $item['maqra_wajib'] = $item['maqra_wajib'] ?? null;
                $item['maqom']       = $item['maqom'] ?? null;
                return $item;
            }, $data['data']);
        }

        return $data;
    }

    // ==========================================================
    // BACKEND SAFEGUARD — Kategori & jenis
    // ==========================================================

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['data'] = collect($data['data'] ?? [])
            ->map(function (array $item): array {
                // ⭐ Recalculate kategori dari nilai
                $item['nilai'] = AssessmentResource::kategoriDariNilai(
                    $item['nilai_penyetoran'] ?? null
                );

                // ⭐ Pastikan field baru tersimpan (preserve kalau ada)
                $item['maqra_wajib'] = $item['maqra_wajib'] ?? null;
                $item['maqom']       = $item['maqom'] ?? null;

                // ⭐ Kalau item baru tanpa jenis → default 'ziyadah'
                if (empty($item['jenis'])) {
                    $item['jenis'] = 'ziyadah';
                }

                return $item;
            })
            ->toArray();

        return $data;
    }

    // ==========================================================
    // SAVE NILAI PENINGKATAN
    // ==========================================================

    protected function afterSave(): void
    {
        $data = $this->form->getState();
        $this->nilaiSavedCount = $this->saveNilaiPeningkatan($data);
    }

    protected function saveNilaiPeningkatan(array $data): int
    {
        $classGroup = $this->record->classGroup;

        if (! $classGroup?->semester_id) {
            return 0;
        }

        // ⭐ Filter mapel sesuai kelas
        $mapelDiizinkan = AssessmentResource::mapelUntukKelas($classGroup->name);

        $userId = (int) $this->record->user_id;
        $savedCount = 0;

        foreach (AssessmentResource::NILAI_PENINGKATAN_MAPEL as $fieldName => $subjectName) {
            // ⭐ Skip mapel yang tidak berlaku untuk kelas ini
            if (! in_array($subjectName, $mapelDiizinkan, true)) {
                continue;
            }

            $score = $data[$fieldName] ?? null;

            // ⭐ Fix: slug wajib diisi saat insert Subject baru
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
    // NOTIFIKASI — dengan info maqom
    // ==========================================================

    protected function getSavedNotification(): ?Notification
    {
        if (! $this->record) {
            return null;
        }

        $jenisRaw = $this->record->assessment_type ?? '';
        $jenisAktif = collect(explode(',', $jenisRaw))
            ->map(fn ($j) => trim($j))
            ->filter()
            ->values()
            ->all();

        $summary = collect($jenisAktif)
            ->map(fn ($j) => AssessmentResource::JENIS_LABELS[$j] ?? ucfirst($j))
            ->implode(', ');

        $data = is_array($this->record->data) ? $this->record->data : [];
        $count = count($data);
        $batchCount = AssessmentResource::countBatches($data);

        // ⭐ Hitung maqom yang dipakai
        $maqomUsed = collect($data)
            ->pluck('maqom')
            ->filter()
            ->unique()
            ->count();

        $santri = $this->record->student?->name ?? '—';

        $lines = [];
        $lines[] = "**{$santri}**";
        $lines[] = '';

        if ($summary !== '') {
            $lines[] = "Jenis: {$summary}  ";
        }

        $lines[] = "Total setoran: **{$count}** baris  ";
        $lines[] = "Sesi input: **{$batchCount}**×  ";

        if ($maqomUsed > 0) {
            $lines[] = "Maqom dipakai: **{$maqomUsed}** variasi  ";
        }

        if ($this->nilaiSavedCount > 0) {
            $lines[] = "Nilai rapor: **{$this->nilaiSavedCount}** mapel";
        }

        $body = implode("\n", $lines);

        return Notification::make()
            ->success()
            ->title('✅ Perubahan Disimpan')
            ->body($body)
            ->duration(5000);
    }

    // ==========================================================
    // REDIRECT & TITLE
    // ==========================================================

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string
    {
        return "Edit Penilaian • {$this->record->student?->name}";
    }

    public function getSubheading(): ?string
    {
        if (! $this->record) {
            return null;
        }

        $classGroup = $this->record->classGroup;
        $className = $classGroup?->name ?? '-';
        $teacherName = $classGroup?->teacher?->name ?? '-';

        $data = is_array($this->record->data) ? $this->record->data : [];
        $count = count($data);
        $batchCount = AssessmentResource::countBatches($data);

        return "{$className} • Ustad: {$teacherName} • {$count} setoran ({$batchCount}× input)";
    }
}