<?php

namespace App\Features\Grades\Services;

use App\Features\Academic\Models\ClassGroup;
use App\Features\Grades\Models\Assessment;
use App\Features\Grades\Models\Evaluation;
use App\Features\Grades\Models\Grade;
use App\Features\Meetings\Models\Attendance;
use App\Features\SiteSettings\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class GradeReportService
{
    public function buildStudentControlReport(User $student, ?Grade $grade = null): array
    {
        $student->loadMissing('classGroups.semester');

        $classGroup = $this->resolveClassGroup($student, $grade);
        $assessments = $this->assessmentQuery($student, $classGroup)->get();
        $rows = $this->buildRows($assessments);

        // ⭐ Kumpulkan semua pengajar unik untuk legenda TTD
        $creatorLegend = $rows
            ->pluck('creator_name')
            ->filter()
            ->unique()
            ->mapWithKeys(function ($name) {
                return [$this->getInitials($name) => $name];
            })
            ->toArray();

        return [
            'student' => $student,
            'classGroup' => $classGroup,
            'institution' => [
                'name' => 'RUMAH QUR\'AN ASY-SYAMS',
                'subtitle' => 'TILAWAH CENTER PEKANBARU',
                'address' => $this->setting('contact_address'),
                'phone' => $this->setting('contact_phone'),
                'email' => $this->setting('contact_email'),
            ],
            'rows' => $rows,
            'learningNotes' => $this->buildLearningNotes($rows),
            'evaluationSummary' => $this->buildEvaluationSummary($student, $classGroup),
            'attendanceSummary' => $this->buildAttendanceSummary($student, $classGroup),
            'capaianSetoran' => $this->buildCapaianSetoran($student, $classGroup),
            'creatorLegend' => $creatorLegend,  // ⭐ BARU
            'generatedAt' => now(),
        ];
    }

    public function canAccessStudentReport(User $actor, User $student, ?Grade $grade = null): bool
    {
        if ($actor->role === 'superadmin') {
            return true;
        }

        if ($actor->role !== 'guru' || ! $actor->hasAnyAccess(['grades.view', 'grades.manage', 'reports.download'])) {
            return false;
        }

        if ($grade && (int) $grade->user_id !== (int) $student->id) {
            return false;
        }

        return $student->classGroups()
            ->where('teacher_id', $actor->id)
            ->exists();
    }

    // ==========================================================
    // BUILD CAPAIAN SETORAN
    // ==========================================================

    private function buildCapaianSetoran(User $student, ?ClassGroup $classGroup): array
    {
        if (! $classGroup) return [];

        $assessments = Assessment::query()
            ->where('user_id', $student->id)
            ->where('class_group_id', $classGroup->id)
            ->orderBy('created_at')
            ->get();

        $allItems = [];
        foreach ($assessments as $assessment) {
            $data = $assessment->data ?? [];
            if (! is_array($data)) continue;
            foreach ($data as $item) {
                if (is_array($item)) $allItems[] = $item;
            }
        }

        $jenisList = $this->jenisUntukKelas($classGroup->name ?? '');
        $result = [];

        foreach ($jenisList as $jenis) {
            $items = array_values(array_filter(
                $allItems,
                fn ($i) => ($i['jenis'] ?? '') === $jenis
            ));

            $seen = [];
            $surahList = [];

            foreach ($items as $item) {
                $surah = trim((string) ($item['surah'] ?? ''));
                $ayat  = trim((string) ($item['ayat'] ?? ''));

                if ($surah === '') continue;

                $key = $surah . '|' . $ayat;
                if (isset($seen[$key])) continue;
                $seen[$key] = true;

                $surahList[] = [
                    'surah' => $surah,
                    'ayat'  => $ayat,
                    'label' => $ayat !== '' ? "QS. {$surah} : {$ayat}" : "QS. {$surah}",
                ];
            }

            $result[] = [
                'jenis'       => $jenis,
                'label'       => $this->labelUntukRapor($jenis),
                'surah_list'  => $surahList,
                'total'       => count($surahList),
            ];
        }

        return $result;
    }

    private function jenisUntukKelas(string $className): array
    {
        $lower = strtolower($className);

        if (str_contains($lower, 'tahsin')) return ['tahsin', 'ziyadah'];
        if (str_contains($lower, 'murottal') || str_contains($lower, 'murotal')) return ['murottal', 'tahsin', 'ziyadah'];
        if (str_contains($lower, 'tilawah')) return ['tilawah', 'tahsin', 'ziyadah'];

        return ['ziyadah', 'murojaah', 'murottal', 'tahsin', 'tilawah'];
    }

    private function labelUntukRapor(string $jenis): string
    {
        return match ($jenis) {
            'ziyadah'  => 'Tahfizh',
            'murojaah' => 'Murojaah',
            'murottal' => 'Murottal',
            'tahsin'   => 'Tahsin',
            'tilawah'  => 'Tilawah',
            default    => ucfirst($jenis),
        };
    }

    // ==========================================================
    // RESOLVE CLASS GROUP
    // ==========================================================

    private function resolveClassGroup(User $student, ?Grade $grade = null): ?ClassGroup
    {
        $latestAssessment = Assessment::query()
            ->where('user_id', $student->id)
            ->latest('created_at')
            ->first();

        if ($latestAssessment && $latestAssessment->class_group_id) {
            $found = $student->classGroups->firstWhere('id', $latestAssessment->class_group_id);
            if ($found) return $found;
        }

        if ($grade) {
            $found = $student->classGroups->firstWhere('semester_id', $grade->semester_id);
            if ($found) return $found;
        }

        return $student->classGroups
            ->sortByDesc(fn (ClassGroup $classGroup) =>
                $classGroup->pivot?->joined_at ?? $classGroup->created_at)
            ->first();
    }

    // ==========================================================
    // ASSESSMENT QUERY
    // ==========================================================

    private function assessmentQuery(User $student, ?ClassGroup $classGroup = null)
    {
        return Assessment::query()
            ->where('user_id', $student->id)
            ->when($classGroup, fn ($query) => $query->where('class_group_id', $classGroup->id))
            ->with('creator')  // ⭐ Eager load creator
            ->orderBy('created_at')
            ->orderBy('id');
    }

    // ==========================================================
    // ⭐ BUILD ROWS — dengan tracking creator per row
    // ==========================================================

    private function buildRows(Collection $assessments): Collection
    {
        $rows = [];

        foreach ($assessments as $assessment) {
            $assessmentTypes = explode(',', $assessment->assessment_type ?? '');
            $batches = [];

            foreach ($this->assessmentItems($assessment) as $item) {
                $batchId = $item['_batch_id']
                    ?? $item['_created_at']
                    ?? ('legacy_assessment_' . $assessment->id);
                $batches[$batchId][] = $item;
            }

            if (empty($batches)) continue;

            foreach ($batches as $batchId => $items) {
                $firstItem = $items[0] ?? [];
                $timestamp = $firstItem['_created_at'] ?? null;

                try {
                    $date = $timestamp ? Carbon::parse($timestamp) : $assessment->created_at;
                } catch (\Throwable $e) {
                    $date = $assessment->created_at;
                }

                $rowKey = $assessment->id . '::' . $batchId;

                // ⭐ Ambil creator dari item pertama batch
                $creatorName = $firstItem['_created_by_name']
                    ?? $assessment->creator?->name
                    ?? null;

                $rows[$rowKey] = [
                    'date' => $date,
                    'ziyadah' => '',
                    'ziyadah_score' => '',
                    'murojaah' => '',
                    'murojaah_score' => '',
                    'murottal' => '',
                    'murottal_score' => '',
                    'tahsin' => '',
                    'notes' => '',
                    'signature' => '',
                    'creator_name' => $creatorName,                          // ⭐ BARU
                    'creator_initials' => $this->getInitials($creatorName),  // ⭐ BARU
                    '_sort' => $date?->timestamp ?? 0,
                ];

                foreach ($items as $item) {
                    $jenis = trim($item['jenis'] ?? '');

                    if ($jenis === '') {
                        $jenis = count($assessmentTypes) === 1
                            ? trim($assessmentTypes[0])
                            : 'ziyadah';
                    }

                    match ($jenis) {
                        'ziyadah', 'tahfizh' => $this->fillZiyadah($rows[$rowKey], $item),
                        'murojaah' => $this->fillMurojaah($rows[$rowKey], $item),
                        'murottal' => $this->fillMurottal($rows[$rowKey], $item),
                        'tahsin', 'tilawah', 'tajwid' => $this->fillTahsin($rows[$rowKey], $item),
                        default => null,
                    };
                }
            }
        }

        $sorted = collect($rows)->sortBy('_sort')->values();
        $lastDateKey = null;

        return $sorted->map(function (array $row, int $index) use (&$lastDateKey): array {
            $row['number'] = $index + 1;
            $row['date_real'] = $row['date'];

            $dateKey = $row['date']?->format('Y-m-d');
            if ($dateKey !== null && $dateKey === $lastDateKey) {
                $row['date'] = null;
            } else {
                $lastDateKey = $dateKey;
            }

            return $row;
        });
    }

    // ==========================================================
    // BUILD LEARNING NOTES
    // ==========================================================

    private function buildLearningNotes(Collection $rows): Collection
    {
        $grouped = [];

        foreach ($rows as $row) {
            $rawNotes = trim((string) ($row['notes'] ?? ''));
            if ($rawNotes === '') continue;

            $date = $row['date_real'] ?? $row['date'] ?? null;
            $dateKey = $date ? $date->format('Y-m-d') : 'unknown';

            if (! isset($grouped[$dateKey])) {
                $grouped[$dateKey] = ['date' => $date, 'notes' => []];
            }

            $notes = array_filter(
                array_map('trim', explode(';', $rawNotes)),
                fn ($n) => $n !== ''
            );

            foreach ($notes as $n) {
                if (! in_array($n, $grouped[$dateKey]['notes'], true)) {
                    $grouped[$dateKey]['notes'][] = $n;
                }
            }
        }

        return collect($grouped)
            ->map(fn (array $item): array => [
                'date'  => $item['date'],
                'notes' => $item['notes'],
                'note'  => implode(' • ', $item['notes']),
            ])
            ->sortBy(fn (array $item) => $item['date']?->timestamp ?? 0)
            ->values();
    }

    // ==========================================================
    // BUILD EVALUATION SUMMARY
    // ==========================================================

    private function buildEvaluationSummary(User $student, ?ClassGroup $classGroup): Collection
    {
        return Evaluation::query()
            ->where('user_id', $student->id)
            ->when($classGroup, fn ($query) => $query->where('class_group_id', $classGroup->id))
            ->orderBy('created_at', 'desc')
            ->orderBy('id')
            ->get()
            ->flatMap(function (Evaluation $evaluation): array {
                $items = is_array($evaluation->items) ? $evaluation->items : [];
                $jenis = $evaluation->jenis ?? 'tilawah';
                $maqomLabel = $this->formatMaqom($evaluation->song_name);

                if ($items === []) {
                    return [[
                        'jenis'      => $jenis,
                        'surah_name' => $evaluation->surah_name ?: '—',
                        'song_name'  => $maqomLabel,
                        'item'       => '—',
                        'score'      => '—',
                        'status'     => '—',
                    ]];
                }

                return collect($items)
                    ->map(fn (array $item): array => [
                        'jenis'      => $jenis,
                        'surah_name' => $evaluation->surah_name ?: '—',
                        'song_name'  => $maqomLabel,
                        'item'       => (string) ($item['name'] ?? $item['catatan'] ?? '—'),
                        'score'      => $this->formatScore($item['score'] ?? null),
                        'status'     => $this->evaluationStatus($item),
                    ])
                    ->all();
            })
            ->values();
    }

    private function formatMaqom(?string $kode): string
    {
        if (! $kode) return '—';

        if (class_exists(\App\Filament\Resources\EvaluationResource::class)) {
            $label = \App\Filament\Resources\EvaluationResource::maqomLabel($kode);
            if ($label !== '—' && $label !== $kode) {
                return $label;
            }
        }

        return $kode;
    }

    // ==========================================================
    // BUILD ATTENDANCE SUMMARY
    // ==========================================================

    private function buildAttendanceSummary(User $student, ?ClassGroup $classGroup): array
    {
        $query = Attendance::query()
            ->where('user_id', $student->id)
            ->when($classGroup, fn ($query) =>
                $query->whereHas('meeting', fn ($meetingQuery) =>
                    $meetingQuery->where('class_group_id', $classGroup->id)));

        $summary = [
            'present' => (clone $query)->where('status', 'present')->count(),
            'sick' => (clone $query)->where('status', 'sick')->count(),
            'permission' => (clone $query)->where('status', 'permission')->count(),
            'alpha' => (clone $query)->where('status', 'alpha')->count(),
        ];

        $summary['total'] = array_sum($summary);
        $summary['percentage'] = $summary['total'] > 0
            ? round(($summary['present'] / $summary['total']) * 100)
            : 0;

        return $summary;
    }

    // ==========================================================
    // ⭐ HELPER: Initials dari nama lengkap
    // ==========================================================

    private function getInitials(?string $name): string
    {
        if (! $name) return '—';

        // Skip gelar umum
        $titles = ['ustadz', 'ustad', 'ustadzah', 'ust.', 'h.', 'hj.', 'dr.', 'ir.'];
        $words = preg_split('/\s+/', trim($name));
        $words = array_values(array_filter(
            $words,
            fn ($w) => ! in_array(strtolower($w), $titles, true)
        ));

        if (empty($words)) return '—';

        // Kalau cuma 1 kata → 2 huruf pertama
        if (count($words) === 1) {
            return strtoupper(substr($words[0], 0, 2));
        }

        // Ambil huruf pertama dari 2 kata
        return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
    }

    // ==========================================================
    // HELPERS
    // ==========================================================

    private function assessmentItems(Assessment $assessment): array
    {
        $data = $assessment->data ?? [];
        if (! is_array($data)) return [];
        return array_values($data);
    }

    private function fillZiyadah(array &$row, array $item): void
    {
        $row['ziyadah'] = $this->mergeCell($row['ziyadah'], $this->surahAyat($item));
        $row['ziyadah_score'] = $this->normalizeScore($item['nilai'] ?? null);
        $this->fillNote($row, $item);
    }

    private function fillMurojaah(array &$row, array $item): void
    {
        $row['murojaah'] = $this->mergeCell($row['murojaah'], $this->surahAyat($item));
        $row['murojaah_score'] = $this->scoreToBaikKurang($item['nilai'] ?? $item['nilai_penyetoran'] ?? null);
        $this->fillNote($row, $item);
    }

    private function fillMurottal(array &$row, array $item): void
    {
        $row['murottal'] = $this->mergeCell($row['murottal'], $this->surahAyat($item));
        $row['murottal_score'] = $this->scoreToBaikKurang($item['nilai'] ?? $item['nilai_penyetoran'] ?? null);
        $this->fillNote($row, $item);
    }

    private function fillTahsin(array &$row, array $item): void
    {
        $row['tahsin'] = $this->mergeCell($row['tahsin'], $this->surahAyat($item));
        $this->fillNote($row, $item);
    }

    private function fillNote(array &$row, array $item): void
    {
        $row['notes'] = $this->mergeCell($row['notes'] ?? '', $this->noteFromItem($item));
    }

    private function noteFromItem(array $item): string
    {
        return trim((string) ($item['catatan'] ?? $item['note'] ?? $item['notes'] ?? $item['keterangan'] ?? ''));
    }

    private function surahAyat(array $item): string
    {
        $surah = trim((string) ($item['surah'] ?? $item['name'] ?? ''));
        $ayat = trim((string) ($item['ayat'] ?? ''));

        if ($surah === '' && $ayat === '') {
            return trim((string) ($item['catatan'] ?? $item['nilai_penyetoran'] ?? '-'));
        }

        return trim($surah . ($ayat !== '' ? ' : ' . $ayat : ''));
    }

    private function normalizeScore(mixed $value): string
    {
        $score = strtoupper(trim((string) $value));
        return in_array($score, ['L', 'C', 'TL'], true) ? $score : '';
    }

    private function scoreToBaikKurang(mixed $value): string
    {
        $score = strtoupper(trim((string) $value));

        if ($score === 'L' || $score === 'B') return 'B';
        if (in_array($score, ['C', 'TL', 'K'], true)) return 'K';
        if (is_numeric($score)) return (float) $score >= 75 ? 'B' : 'K';

        return '';
    }

    private function formatScore(mixed $score): string
    {
        if ($score === null || $score === '') return '—';

        return is_numeric($score)
            ? rtrim(rtrim(number_format((float) $score, 2, '.', ''), '0'), '.')
            : (string) $score;
    }

    private function evaluationStatus(array $item): string
    {
        if (! empty($item['status'])) return (string) $item['status'];
        if (array_key_exists('checked', $item)) return $item['checked'] ? 'Tercapai' : 'Belum Tercapai';

        $score = $item['score'] ?? null;
        if (is_numeric($score)) return (float) $score >= 75 ? 'Tercapai' : 'Perlu Perbaikan';

        return '—';
    }

    private function mergeCell(string $current, string $value): string
    {
        if ($value === '') return $current;
        if ($current === '') return $value;
        return $current . '; ' . $value;
    }

    private function setting(string $key): ?string
    {
        return SiteSetting::query()->where('key', $key)->value('value');
    }
}