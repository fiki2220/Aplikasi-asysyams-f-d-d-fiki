<div class="p-4 space-y-4 bg-white border outline outline-1 outline-gray-200 shadow-sm rounded">

    {{-- ═══════════════════════════════════════════════════════
         HEADER
         ═══════════════════════════════════════════════════════ --}}
    <div class="flex justify-between items-start border-b pb-3 mb-3 gap-4">
        <div>
            <h2 class="text-xl font-bold uppercase" style="color: #166534;">Rapor Hasil Belajar</h2>
            <p class="text-sm font-semibold" style="color: #6b7280;">Rumah Qur'an Asy-Syams</p>
            <p class="text-xs" style="color: #9ca3af;">
                {{ $classGroup?->name ?? '-' }} | Semester: {{ $classGroup?->semester?->name ?? '-' }}
            </p>
        </div>

        @if(auth()->user()?->hasAccess('reports.download'))
            <a href="{{ route('rapor.pdf', ['class_group' => $classGroup->id, 'user' => $student->id]) }}"
               target="_blank"
               style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; font-size: 14px; font-weight: 600; background-color: #15803d; color: #ffffff !important; border-radius: 8px; text-decoration: none; box-shadow: 0 1px 3px rgba(0,0,0,0.1); white-space: nowrap; border: none; cursor: pointer;">
                <svg style="width: 16px; height: 16px; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span style="color: #ffffff;">Print PDF</span>
            </a>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════════
         DATA SANTRI + QR CODE
         ═══════════════════════════════════════════════════════ --}}
    @php
        $qrSvg = '';
        if ($student && $classGroup?->semester) {
            try {
                $qrSvg = app(\App\Features\Reports\Services\RaporVerificationService::class)
                    ->buildQrSvg($student, $classGroup->semester, 80);
            } catch (\Throwable $e) { $qrSvg = ''; }
        }
    @endphp

    <div class="grid grid-cols-3 gap-4 text-sm">
        <div class="space-y-1">
            <p><strong class="text-gray-600">Nama:</strong> {{ $student->name }}</p>
            <p><strong class="text-gray-600">NISN:</strong> {{ $student->nisn ?? '—' }}</p>
        </div>
        <div class="space-y-1">
            <p><strong class="text-gray-600">Kelas:</strong> {{ $classGroup?->name ?? '—' }}</p>
            <p><strong class="text-gray-600">Semester:</strong> {{ $classGroup?->semester?->name ?? '—' }}</p>
        </div>
        <div class="text-center">
            @if($qrSvg)
                <div style="display: inline-block; padding: 3px; border: 1px solid #c5d6d1; background: #fff;">
                    <img src="{{ $qrSvg }}" width="70" height="70" alt="QR">
                </div>
                <p style="font-size: 9px; color: #666; margin-top: 2px; line-height: 1.1;">Scan untuk<br>verifikasi keaslian</p>
            @endif
        </div>
    </div>

    <hr class="border-gray-200">

    {{-- ═══════════════════════════════════════════════════════
         LOGIKA PHP (DISAMAKAN DENGAN PDF)
         ═══════════════════════════════════════════════════════ --}}
    @php
        $className    = $classGroup?->name ?? '-';
        $semesterName = $classGroup?->semester?->name ?? '-';

        // ═══════════════════════════════════════════════════
        // Assessment sesuai periode (Sama dengan PDF)
        // ═══════════════════════════════════════════════════
        $filterMonth = $filterMonth ?? null;
        $filterYear  = $filterYear  ?? null;

        $assessmentQuery = \App\Features\Grades\Models\Assessment::query()
            ->where('user_id', $student->id)
            ->where('class_group_id', $classGroup?->id);

        if (! empty($filterMonth) && ! empty($filterYear)) {
            $assessmentQuery->where('month', $filterMonth)->where('year', $filterYear);
        } else {
            $assessmentQuery->orderByDesc('year')->orderByDesc('month');
        }

        $assessment = $assessmentQuery->first();
        $setoranData = ($assessment && is_array($assessment->data)) ? $assessment->data : [];

        $grouped = ['tilawah' => [], 'tahfizh' => [], 'tahsin' => [], 'murottal' => []];

        foreach ($setoranData as $item) {
            if (! is_array($item)) continue;
            $jenis = $item['jenis'] ?? null;
            if (! $jenis) continue;

            if (in_array($jenis, ['ziyadah', 'murojaah'])) {
                $grouped['tahfizh'][] = $item;
            } elseif (isset($grouped[$jenis])) {
                $grouped[$jenis][] = $item;
            }
        }

        $surahMap = \Illuminate\Support\Facades\Cache::get('alquran_surah_map', []);
        if (empty($surahMap)) {
            try {
                $reflection = new \ReflectionClass(\App\Filament\Resources\AssessmentResource::class);
                $method = $reflection->getMethod('getSurahMap');
                $method->setAccessible(true);
                $surahMap = $method->invoke(null);
            } catch (\Throwable $e) { $surahMap = []; }
        }

        $getSurahNumber = function (?string $surah) use ($surahMap): ?int {
            if (! $surah) return null;
            return $surahMap[$surah]['nomor'] ?? null;
        };

        $hitungPredikat = function ($nilai): string {
            if ($nilai === null || $nilai === '') return '—';
            $n = (float) $nilai;
            if ($n >= 91) return 'A (Sangat Baik)';
            if ($n >= 81) return 'B (Baik)';
            if ($n >= 71) return 'C (Cukup)';
            return 'D (Kurang)';
        };

        $formatSurahAyat = function (array $item): string {
            $surah = $item['surah'] ?? '';
            $ayat  = $item['ayat'] ?? '';
            if (! $surah) return '—';
            $s = 'QS ' . strtoupper($surah);
            return $ayat ? "{$s} {$ayat}" : $s;
        };

        $avgNilai = function (array $items): ?float {
            $scores = array_filter(
                array_map(fn ($i) => $i['nilai_penyetoran'] ?? null, $items),
                fn ($v) => $v !== null && $v !== ''
            );
            if (empty($scores)) return null;
            return round(array_sum($scores) / count($scores), 2);
        };

        $buildRange = function (array $items) use ($getSurahNumber): ?array {
            if (empty($items)) return null;

            $surahList = [];
            foreach ($items as $item) {
                $surah = $item['surah'] ?? null;
                if ($surah && ! isset($surahList[$surah])) {
                    $nomor = $getSurahNumber($surah);
                    if ($nomor) $surahList[$surah] = $nomor;
                }
            }
            if (empty($surahList)) return null;

            asort($surahList);
            $surahNames = array_keys($surahList);
            $firstSurah = reset($surahNames);
            $lastSurah  = end($surahNames);
            $firstAyat = null;
            $lastAyat  = null;

            foreach ($items as $item) {
                $surah = $item['surah'] ?? null;
                $ayat  = $item['ayat'] ?? null;

                if ($surah === $firstSurah) {
                    if ($ayat !== null) {
                        $ayatNum = (int) explode('-', (string) $ayat)[0];
                        if ($firstAyat === null || $ayatNum < (int) explode('-', (string) $firstAyat)[0]) {
                            $firstAyat = $ayat;
                        }
                    }
                }
                if ($surah === $lastSurah) {
                    if ($ayat !== null) {
                        $parts = explode('-', (string) $ayat);
                        $ayatNum = (int) end($parts);
                        if ($lastAyat === null || $ayatNum > (int) end(explode('-', (string) $lastAyat))) {
                            $lastAyat = $ayat;
                        }
                    }
                }
            }
            return [
                'first_surah' => $firstSurah, 'first_ayat'  => $firstAyat,
                'last_surah'  => $lastSurah,  'last_ayat'   => $lastAyat, 'nilai' => null,
            ];
        };

        $sortBySurah = function (array $items) use ($getSurahNumber): array {
            usort($items, function ($a, $b) use ($getSurahNumber) {
                $nomorA = $getSurahNumber($a['surah'] ?? null) ?? 999;
                $nomorB = $getSurahNumber($b['surah'] ?? null) ?? 999;
                return $nomorA <=> $nomorB;
            });
            return $items;
        };

        $normalize = function (?string $s): string {
            if (! $s) return '';
            $s = strtolower(trim($s));
            $s = str_replace(["'", "'", "`", "’", "‘", "′"], '', $s);
            $s = preg_replace('/[^a-z0-9]+/i', ' ', $s);
            $s = preg_replace('/\s+/', ' ', $s);
            return trim($s);
        };

        $getMinAyat = function (?string $ayatInput): ?int {
            if (! $ayatInput) return null;
            $ayatInput = trim((string) $ayatInput);
            if (preg_match('/\d+/', $ayatInput, $m)) return (int) $m[0];
            return null;
        };

        $parseEvaluationSurah = function (?string $surahName): array {
            if (! $surahName) return ['surah' => '', 'ayat' => ''];
            $parts = preg_split('/\s*:\s*/', $surahName, 2);
            if (count($parts) === 2) {
                return ['surah' => trim($parts[0]), 'ayat' => trim($parts[1])];
            }
            return ['surah' => trim($surahName), 'ayat' => ''];
        };

        $maqraWajibList = [
            ['surah' => "Ali 'Imran", 'ayat_start' => 144, 'label' => "QS Ali 'Imran 144"],
            ['surah' => 'Qaf',        'ayat_start' => 16,  'label' => "QS Qaf 16"],
            ['surah' => 'At-Taubah',  'ayat_start' => 81,  'label' => "QS At-Taubah 81"],
            ['surah' => "Al-Ma'idah", 'ayat_start' => 27,  'label' => "QS Al-Ma'idah 27"],
            ['surah' => 'Al-Isra',    'ayat_start' => 1,   'label' => "QS Al-Isra 1"],
            ['surah' => "Al-A'raf",   'ayat_start' => 31,  'label' => "QS Al-A'raf 31"],
        ];

        $evaluationList = \App\Features\Grades\Models\Evaluation::query()
            ->where('user_id', $student->id)
            ->where('class_group_id', $classGroup?->id)
            ->get();

        $tilawahRows = [];

        foreach ($maqraWajibList as $maqra) {
            $maqraSurahNorm = $normalize($maqra['surah']);
            $setoranMatches = [];
            
            foreach ($grouped['tilawah'] as $item) {
                $surahItem = $item['surah'] ?? '';
                $ayatItem  = $item['ayat'] ?? '';
                if ($normalize($surahItem) !== $maqraSurahNorm) continue;
                $ayatNum = $getMinAyat($ayatItem);
                if ($ayatNum === null) continue;
                if ($ayatNum < $maqra['ayat_start']) continue;
                $setoranMatches[] = $item;
            }

            $evaluasiMatches = [];
            foreach ($evaluationList as $eval) {
                if (($eval->jenis ?? '') !== 'tilawah') continue;
                $parsed = $parseEvaluationSurah($eval->surah_name ?? '');
                if ($normalize($parsed['surah']) !== $maqraSurahNorm) continue;
                $evalAyatNum = $getMinAyat($parsed['ayat']);
                if ($evalAyatNum === null) continue;
                if ($evalAyatNum !== $maqra['ayat_start']) continue;
                $evaluasiMatches[] = $eval;
            }

            $nilaiSetoran = null;
            if (! empty($setoranMatches)) {
                $scores = array_filter(array_map(fn ($i) => $i['nilai_penyetoran'] ?? null, $setoranMatches), fn ($v) => $v !== null && $v !== '');
                if (! empty($scores)) $nilaiSetoran = round(array_sum($scores) / count($scores), 2);
            }

            $nilaiEvaluasi = null;
            if (! empty($evaluasiMatches)) {
                $evalScores = [];
                foreach ($evaluasiMatches as $eval) {
                    if (! empty($eval->items)) {
                        foreach ($eval->items as $item) {
                            $s = $item['score'] ?? null;
                            if ($s !== null && $s !== '') $evalScores[] = (float) $s;
                        }
                    }
                }
                if (! empty($evalScores)) $nilaiEvaluasi = round(array_sum($evalScores) / count($evalScores), 2);
            }

            if ($nilaiSetoran === null || $nilaiEvaluasi === null) continue;
            $finalScore = round(($nilaiSetoran + $nilaiEvaluasi) / 2, 2);

            $tilawahRows[] = [
                'nama'     => $maqra['label'],
                'kktp'     => 80,
                'nilai'    => $finalScore,
                'predikat' => $hitungPredikat($finalScore),
            ];
        }

        $tahfizhRange = null;
        if (! empty($grouped['tahfizh'])) {
            $tahfizhRange = $buildRange($grouped['tahfizh']);
            if ($tahfizhRange) $tahfizhRange['nilai'] = $avgNilai($grouped['tahfizh']);
        }

        $tahsinRange = null;
        if (! empty($grouped['tahsin'])) {
            $tahsinRange = $buildRange($grouped['tahsin']);
            if ($tahsinRange) $tahsinRange['nilai'] = $avgNilai($grouped['tahsin']);
        }

        if (! empty($grouped['murottal'])) $grouped['murottal'] = $sortBySurah($grouped['murottal']);

        $mapelBlocks = [];

        if (! empty($tilawahRows)) {
            $mapelBlocks[] = ['label' => 'Tilawah', 'rows' => $tilawahRows];
        }

        if ($tahfizhRange) {
            $nilaiTahfizh = $tahfizhRange['nilai'];
            $rangeText = 'QS ' . strtoupper($tahfizhRange['first_surah']) . ($tahfizhRange['first_ayat'] ? ' ' . $tahfizhRange['first_ayat'] : '')
                . ' — QS ' . strtoupper($tahfizhRange['last_surah']) . ($tahfizhRange['last_ayat'] ? ' ' . $tahfizhRange['last_ayat'] : '');
            $mapelBlocks[] = [
                'label' => 'Tahfizh',
                'rows'  => [['nama' => $rangeText, 'kktp' => 80, 'nilai' => $nilaiTahfizh, 'predikat' => $hitungPredikat($nilaiTahfizh)]],
            ];
        }

        if ($tahsinRange) {
            $nilaiTahsin = $tahsinRange['nilai'];
            $rangeText = 'QS ' . strtoupper($tahsinRange['first_surah']) . ($tahsinRange['first_ayat'] ? ' ' . $tahsinRange['first_ayat'] : '')
                . ' — QS ' . strtoupper($tahsinRange['last_surah']) . ($tahsinRange['last_ayat'] ? ' ' . $tahsinRange['last_ayat'] : '');
            $mapelBlocks[] = [
                'label' => 'Tahsin',
                'rows'  => [['nama' => $rangeText, 'kktp' => 80, 'nilai' => $nilaiTahsin, 'predikat' => $hitungPredikat($nilaiTahsin)]],
            ];
        }

        if (! empty($grouped['murottal'])) {
            $mapelBlocks[] = [
                'label' => 'Murottal',
                'rows'  => array_map(fn ($item) => [
                    'nama'     => $formatSurahAyat($item),
                    'kktp'     => 80,
                    'nilai'    => $item['nilai_penyetoran'] ?? null,
                    'predikat' => $hitungPredikat($item['nilai_penyetoran'] ?? null),
                ], $grouped['murottal']),
            ];
        }

        $avgPerMapel = [];
        foreach ($mapelBlocks as $block) {
            $scores = array_filter(array_column($block['rows'], 'nilai'), fn ($v) => $v !== null && $v !== '');
            if (! empty($scores)) $avgPerMapel[] = array_sum($scores) / count($scores);
        }

        $rataKeseluruhan = ! empty($avgPerMapel) ? round(array_sum($avgPerMapel) / count($avgPerMapel), 2) : null;
    @endphp


    {{-- ═══════════════════════════════════════════════════════
         A. NILAI PENINGKATAN SANTRI (STRUKTUR RAPI PER MAPEL)
         ═══════════════════════════════════════════════════════ --}}
    <div>
        <h3 class="font-bold text-sm mb-2" style="color: #166534;"> A. Nilai Peningkatan Santri</h3>

        @if(empty($mapelBlocks))
            <p class="text-sm text-gray-500 italic">Belum ada setoran tercatat untuk semester ini.</p>
        @else
            {{-- Tabel per mapel --}}
            @foreach($mapelBlocks as $block)
                <table class="w-full border-collapse text-xs mb-2" style="table-layout: fixed;">
                    <thead>
                        <tr>
                            <th colspan="4" class="border p-2 text-left font-bold uppercase" style="background-color: #ffffff; color: #166534; border-color: #c5d6d1;">
                                MAPEL : {{ strtoupper($block['label']) }}
                            </th>
                        </tr>
                        <tr style="background-color: #e8f3f0; color: #1a3c34;">
                            <th class="border p-2 text-center font-bold" style="border-color: #c5d6d1; width: 45%;">Nama Surah / Maqra'</th>
                            <th class="border p-2 text-center font-bold" style="border-color: #c5d6d1; width: 13%;">KKTP</th>
                            <th class="border p-2 text-center font-bold" style="border-color: #c5d6d1; width: 17%;">Nilai</th>
                            <th class="border p-2 text-center font-bold" style="border-color: #c5d6d1; width: 25%;">Predikat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($block['rows'] as $row)
                            <tr>
                                <td class="border p-2" style="border-color: #c5d6d1;">{{ $row['nama'] }}</td>
                                <td class="border p-2 text-center" style="background-color: #e8f3f0; border-color: #c5d6d1;">{{ $row['kktp'] }}</td>
                                <td class="border p-2 text-center font-bold" style="border-color: #c5d6d1;">
                                    {{ $row['nilai'] !== null ? number_format((float) $row['nilai'], 2) : '—' }}
                                </td>
                                <td class="border p-2 text-center" style="border-color: #c5d6d1;">{{ $row['predikat'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach

            {{-- Tabel Rata-rata Terpisah --}}
            <table class="w-full border-collapse text-xs mt-3" style="table-layout: fixed;">
                <tbody>
                    <tr style="background-color: #1a3c34; color: #ffffff; font-weight: bold;">
                        <td class="border p-2 text-center" style="border-color: #1a3c34; letter-spacing: 0.5px; width: 45%;">RATA - RATA</td>
                        <td colspan="3" class="border p-2 text-center" style="border-color: #1a3c34;">
                            {{ $rataKeseluruhan !== null ? number_format($rataKeseluruhan, 2) : '—' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif
    </div>

    <hr class="border-gray-200 mt-3">

    {{-- ═══════════════════════════════════════════════════════
         B. ASPEK KARAKTER & SISTEM PENILAIAN
         ═══════════════════════════════════════════════════════ --}}
    @php
        $characterAssessment = \App\Features\Grades\Models\CharacterAssessment::query()
            ->where('user_id', $student->id)
            ->where('class_group_id', $classGroup?->id)
            ->first();

        $karakter = $characterAssessment ? $characterAssessment->aspect_rows : [
            ['label' => 'Adab Islam',     'score' => 0, 'stars' => 0],
            ['label' => 'Kerjasama',      'score' => 0, 'stars' => 0],
            ['label' => 'Tanggung Jawab', 'score' => 0, 'stars' => 0],
            ['label' => 'Kedisiplinan',   'score' => 0, 'stars' => 0],
            ['label' => 'Kejujuran',      'score' => 0, 'stars' => 0],
        ];
    @endphp

    <div>
        <h3 class="font-bold text-sm mb-2" style="color: #166534;"> B. Aspek Karakter & Sistem Penilaian</h3>

        <div class="grid grid-cols-2 gap-4">

            {{-- KIRI: Tabel Karakter --}}
            <div>
                <table class="w-full border-collapse text-xs" style="table-layout: fixed;">
                    <thead>
                        <tr style="background-color: #e8f3f0; color: #1a3c34;">
                            <th class="border p-2 text-left font-bold" style="border-color: #c5d6d1; width: 45%;">Aspek Karakter</th>
                            <th class="border p-2 text-center font-bold" style="border-color: #c5d6d1; width: 20%;">Nilai</th>
                            <th class="border p-2 text-center font-bold" style="border-color: #c5d6d1; width: 35%;">Bintang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($karakter as $row)
                            <tr>
                                <td class="border p-2" style="border-color: #c5d6d1;">{{ $row['label'] }}</td>
                                <td class="border p-2 text-center font-bold" style="background-color: #e8f3f0; border-color: #c5d6d1;">
                                    {{ $row['score'] > 0 ? $row['score'] : '—' }}
                                </td>
                                <td class="border p-2 text-center" style="border-color: #c5d6d1; font-size: 12px; color: #eab308;">
                                    @if($row['stars'] > 0)
                                        {!! str_repeat('★', $row['stars']) . '<span style="color:#d1d5db;">' . str_repeat('☆', 5 - $row['stars']) . '</span>' !!}
                                    @else
                                        <span style="color:#d1d5db;">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        @if($characterAssessment)
                            <tr style="background-color: #1a3c34; color: #ffffff; font-weight: bold;">
                                <td class="border p-2 text-center" style="border-color: #1a3c34;">Rata - Rata</td>
                                <td class="border p-2 text-center" style="border-color: #1a3c34;">{{ number_format($characterAssessment->average_score, 1) }}</td>
                                <td class="border p-2 text-center" style="border-color: #1a3c34; color: #facc15; font-size: 12px;">
                                    {!! str_repeat('★', $characterAssessment->average_stars) . '<span style="color:rgba(255,255,255,0.3);">' . str_repeat('☆', 5 - $characterAssessment->average_stars) . '</span>' !!}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- KANAN: Sistem Bintang & Nilai --}}
            <div class="space-y-3">
                <table class="w-full border-collapse text-xs" style="table-layout: fixed;">
                    <thead>
                        <tr style="background-color: #e8f3f0; color: #1a3c34;">
                            <th colspan="2" class="border p-2 text-center font-bold" style="border-color: #c5d6d1;">Sistem Bintang</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="border p-2" style="border-color: #c5d6d1;">Sangat Baik (90-100)</td><td class="border p-2 text-center" style="background-color: #e8f3f0; border-color: #c5d6d1; color: #eab308; font-size: 11px;">★★★★★</td></tr>
                        <tr><td class="border p-2" style="border-color: #c5d6d1;">Baik (80-89)</td><td class="border p-2 text-center" style="background-color: #e8f3f0; border-color: #c5d6d1; color: #eab308; font-size: 11px;">★★★★</td></tr>
                        <tr><td class="border p-2" style="border-color: #c5d6d1;">Cukup (70-79)</td><td class="border p-2 text-center" style="background-color: #e8f3f0; border-color: #c5d6d1; color: #eab308; font-size: 11px;">★★★</td></tr>
                        <tr><td class="border p-2" style="border-color: #c5d6d1;">Kurang (60-69)</td><td class="border p-2 text-center" style="background-color: #e8f3f0; border-color: #c5d6d1; color: #eab308; font-size: 11px;">★★</td></tr>
                        <tr><td class="border p-2" style="border-color: #c5d6d1;">Kurang Sekali (&lt; 60)</td><td class="border p-2 text-center" style="background-color: #e8f3f0; border-color: #c5d6d1; color: #eab308; font-size: 11px;">★</td></tr>
                    </tbody>
                </table>

                <table class="w-full border-collapse text-xs" style="table-layout: fixed;">
                    <thead>
                        <tr style="background-color: #e8f3f0; color: #1a3c34;">
                            <th colspan="2" class="border p-2 text-center font-bold" style="border-color: #c5d6d1;">Sistem Penilaian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="border p-2 text-center" style="border-color: #c5d6d1; width: 60%;">91 - 100</td><td class="border p-2 text-center font-bold" style="background-color: #e8f3f0; border-color: #c5d6d1;">A</td></tr>
                        <tr><td class="border p-2 text-center" style="border-color: #c5d6d1;">81 - 90</td><td class="border p-2 text-center font-bold" style="background-color: #e8f3f0; border-color: #c5d6d1;">B</td></tr>
                        <tr><td class="border p-2 text-center" style="border-color: #c5d6d1;">71 - 80</td><td class="border p-2 text-center font-bold" style="background-color: #e8f3f0; border-color: #c5d6d1;">C</td></tr>
                        <tr><td class="border p-2 text-center" style="border-color: #c5d6d1;">0 - 70</td><td class="border p-2 text-center font-bold" style="background-color: #e8f3f0; border-color: #c5d6d1;">D</td></tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <hr class="border-gray-200 mt-3">
    <p class="text-xs text-gray-500 italic text-center pt-2">
        Dokumen ini dicetak secara otomatis dari Sistem Informasi YPTQ Asy-Syams.
    </p>

</div>