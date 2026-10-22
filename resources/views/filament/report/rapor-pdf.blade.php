<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapor - {{ $student->name }}</title>
    <style>
        @page { margin: 5mm 9mm 5mm 9mm; }
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10.5px; color: #222; line-height: 1.4; margin: 0; }

        .header-table { width: 100%; border-collapse: collapse; border-bottom: 2px solid #1a3c34; margin-bottom: 6px; }
        .header-table td { border: none; vertical-align: middle; padding: 3px 0; }
        .header-logo { width: 82px; text-align: center; }
        .header-logo img { width: 70px; height: 70px; }
        .header-title { text-align: center; padding: 0 12px; }
        .header-title h1 { color: #1a3c34; font-size: 18px; font-weight: bold; margin: 0; letter-spacing: 1.2px; }
        .header-title h2 { color: #1a3c34; font-size: 13px; font-weight: normal; margin: 2px 0 0; letter-spacing: 1px; }
        .header-title .address { color: #666; font-size: 8.5px; margin-top: 3px; font-style: italic; }
        .header-badge { width: 82px; text-align: center; }
        .header-badge .badge-inner { background-color: #1a3c34; color: #fff; padding: 7px 0; font-size: 13px; font-weight: bold; letter-spacing: 3px; text-align: center; }

        .info-wrapper { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        .info-wrapper > tbody > tr > td { vertical-align: middle; padding: 0; border: none; }
        .info-col { width: 38%; padding-right: 10px !important; }
        .info-col-right { width: 38%; padding-right: 10px !important; }
        .qr-col { width: 24%; text-align: center; vertical-align: middle; }

        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 4px 6px; font-size: 10.5px; vertical-align: middle; }
        .info-table .label { width: 74px; font-weight: bold; color: #444; }
        .info-table .colon { width: 9px; text-align: center; }
        .info-table .value { border-bottom: 1px dotted #999; padding-left: 7px; font-weight: 600; }

        .qr-box { display: inline-block; padding: 3px; border: 1px solid #c5d6d1; background: #fff; }
        .qr-caption { font-size: 7px; color: #666; margin-top: 3px; line-height: 1.1; font-weight: normal; }

        .section-title { background-color: #1a3c34; color: #fff; padding: 4px 12px; font-size: 10.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 6px; margin-bottom: 0; }

        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 3px; }
        table.data-table th { background-color: #e8f3f0; color: #1a3c34; padding: 3px 6px; border: 1px solid #c5d6d1; text-align: center; font-size: 10px; font-weight: bold; }
        table.data-table td { border: 1px solid #c5d6d1; padding: 2.5px 6px; font-size: 10px; vertical-align: middle; }

        .mapel-table { margin-bottom: 0 !important; page-break-inside: avoid; }

        .mapel-table thead tr.mapel-header-row th {
            background-color: #fff;
            color: #1a3c34;
            border: 1px solid #c5d6d1;
            padding: 3.5px 8px;
            text-align: left;
            font-size: 10.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .mapel-table thead tr.col-header-row th {
            background-color: #e8f3f0;
            color: #1a3c34;
            border: 1px solid #c5d6d1;
            padding: 3px 6px;
            font-size: 10px;
            font-weight: bold;
        }

        .mapel-table tbody td {
            border: 1px solid #c5d6d1;
            padding: 2.5px 6px;
            font-size: 10px;
            vertical-align: middle;
        }
        .mapel-table tbody tr:nth-child(odd) { background-color: #f7fbfa; }

        .rata-table { margin-top: 3px !important; }
        .rata-table td { background-color: #1a3c34; color: #fff; font-weight: bold; padding: 3.5px 8px; border: 1px solid #1a3c34; }

        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .bg-green { background-color: #e8f3f0; }
        .bg-dark { background-color: #1a3c34; color: #fff; font-weight: bold; }
        .star { color: #e6a000; font-size: 12px; letter-spacing: 1.5px; }
        .star-empty { color: #ccc; font-size: 12px; letter-spacing: 1.5px; }

        .two-col-table { width: 100%; border-collapse: separate; border-spacing: 8px 0; margin-top: 3px; }
        .two-col-table > tbody > tr > td { width: 50%; vertical-align: top; padding: 0; }

        .signature-wrapper { margin-top: 10px; page-break-inside: avoid; }
        .signature-date { margin-left: 66.67%; width: 33.33%; text-align: center; font-size: 10.5px; margin-bottom: 8px; }
        .signature-table { width: 100%; border-collapse: collapse; }
        .signature-table > tbody > tr > td { border: none; text-align: center; vertical-align: top; width: 33.33%; padding: 0 10px; }

        .signature-label { font-size: 10.5px; line-height: 1.4; }
        .signature-sublabel { font-size: 10.5px; color: #444; line-height: 1.4; margin-bottom: 0; }

        .signature-space { height: 50px; }
        .signature-name { font-weight: bold; font-size: 10.5px; padding-top: 4px; display: inline-block; min-width: 150px; }
        .signature-name-empty { padding-top: 4px; display: inline-block; min-width: 150px; height: 1px; }

        .empty-state { padding: 9px; text-align: center; color: #888; font-style: italic; border: 1px solid #ddd; background-color: #fafafa; font-size: 10px; }
        .footer-note { margin-top: 6px; font-size: 8px; color: #666; font-style: italic; text-align: center; }
    </style>
</head>
<body>

    @php
        \Carbon\Carbon::setLocale('id');

        $filterMonth = $filterMonth ?? null;
        $filterYear  = $filterYear  ?? null;

        // ── Logo ────────────────────────────────────────
        $base64Logo = '';
        try {
            $logoPath = public_path('images/logo.PNG');
            if (file_exists($logoPath)) {
                $base64Logo = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
            }
        } catch (\Exception $e) { $base64Logo = ''; }

        $className    = $classGroup?->name ?? '-';
        $semesterName = $classGroup?->semester?->name ?? '-';
        $teacherName  = $classGroup?->teacher?->name ?? '_______________________';

        $kepalaNama = \App\Features\SiteSettings\Models\SiteSetting::query()
            ->where('key', 'kepala_rq_nama')->value('value') ?? '_______________________';
        $kepalaJabatan = \App\Features\SiteSettings\Models\SiteSetting::query()
            ->where('key', 'kepala_rq_jabatan')->value('value') ?? 'Kepala RQ Asy-Syams';

        // ── Generate QR Code ────────────────────────────
        $qrSvg = '';
        if ($student && $classGroup?->semester) {
            $qrSvg = app(\App\Features\Reports\Services\RaporVerificationService::class)
                ->buildQrSvg($student, $classGroup->semester, 80);
        }

        // ── MAPEL DINAMIS PER KELAS ─────────────────────
        $classLower = strtolower($className);

        if (str_contains($classLower, 'murottal') || str_contains($classLower, 'murotal')) {
            $targetSubjects = ['Murottal', 'Tahsin', 'Tahfizh'];
        } elseif (str_contains($classLower, 'tahsin')) {
            $targetSubjects = ['Tahsin', 'Tahfizh'];
        } elseif (str_contains($classLower, 'tilawah')) {
            $targetSubjects = ['Tilawah', 'Tahsin', 'Tahfizh'];
        } else {
            $targetSubjects = ['Tilawah', 'Tahsin', 'Tahfizh'];
        }

        // ── Karakter ────────────────────────────────────
        $characterAssessment = \App\Features\Grades\Models\CharacterAssessment::query()
            ->where('user_id', $student->id)
            ->where('class_group_id', $classGroup?->id)
            ->first();

        $karakter = $characterAssessment
            ? $characterAssessment->aspect_rows
            : [
                ['label' => 'Adab Islam',     'score' => 0, 'stars' => 0],
                ['label' => 'Kerjasama',      'score' => 0, 'stars' => 0],
                ['label' => 'Tanggung Jawab', 'score' => 0, 'stars' => 0],
                ['label' => 'Kedisiplinan',   'score' => 0, 'stars' => 0],
                ['label' => 'Kejujuran',      'score' => 0, 'stars' => 0],
            ];

        // ═══════════════════════════════════════════════════
        // Assessment sesuai periode
        // ═══════════════════════════════════════════════════
        $assessmentQuery = \App\Features\Grades\Models\Assessment::query()
            ->where('user_id', $student->id)
            ->where('class_group_id', $classGroup?->id);

        if (! empty($filterMonth) && ! empty($filterYear)) {
            $assessmentQuery->where('month', $filterMonth)
                            ->where('year', $filterYear);
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
                'first_surah' => $firstSurah,
                'first_ayat'  => $firstAyat,
                'last_surah'  => $lastSurah,
                'last_ayat'   => $lastAyat,
                'nilai'       => null,
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

        // ⭐ Normalize string
        $normalize = function (?string $s): string {
            if (! $s) return '';
            $s = strtolower(trim($s));
            $s = str_replace(["'", "'", "`", "’", "‘", "′"], '', $s);
            $s = preg_replace('/[^a-z0-9]+/i', ' ', $s);
            $s = preg_replace('/\s+/', ' ', $s);
            return trim($s);
        };

        // ⭐ Ambil angka ayat terkecil dari string
        $getMinAyat = function (?string $ayatInput): ?int {
            if (! $ayatInput) return null;
            $ayatInput = trim((string) $ayatInput);
            if (preg_match('/\d+/', $ayatInput, $m)) {
                return (int) $m[0];
            }
            return null;
        };

        // ⭐ Parse evaluasi "Ali 'Imran : 144" → ['surah', 'ayat']
        $parseEvaluationSurah = function (?string $surahName): array {
            if (! $surahName) return ['surah' => '', 'ayat' => ''];
            $parts = preg_split('/\s*:\s*/', $surahName, 2);
            if (count($parts) === 2) {
                return ['surah' => trim($parts[0]), 'ayat' => trim($parts[1])];
            }
            return ['surah' => trim($surahName), 'ayat' => ''];
        };

        // ═══════════════════════════════════════════════════════════
        // MAQRA' WAJIB — Konsep RANGE
        // Aturan:
        //   1. Surah harus sama (Ali 'Imran = Ali 'Imran)
        //   2. Ayat harus >= ayat_start (mis. >= 144)
        //   3. HANYA jenis 'tilawah' yang dihitung
        //   4. WAJIB LENGKAP: harus ada setoran + evaluasi
        // ═══════════════════════════════════════════════════════════
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

            // ─── MATCHING SETORAN (RANGE-BASED, hanya jenis 'tilawah') ───
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

            // ─── MATCHING EVALUASI (exact surah + ayat_start) ───
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

            // ─── Hitung rata-rata nilai setoran ───
            $nilaiSetoran = null;
            if (! empty($setoranMatches)) {
                $scores = array_filter(
                    array_map(fn ($i) => $i['nilai_penyetoran'] ?? null, $setoranMatches),
                    fn ($v) => $v !== null && $v !== ''
                );
                if (! empty($scores)) {
                    $nilaiSetoran = round(array_sum($scores) / count($scores), 2);
                }
            }

            // ─── Hitung rata-rata nilai evaluasi ───
            $nilaiEvaluasi = null;
            if (! empty($evaluasiMatches)) {
                $evalScores = [];
                foreach ($evaluasiMatches as $eval) {
                    if (! empty($eval->items)) {
                        foreach ($eval->items as $item) {
                            $s = $item['score'] ?? null;
                            if ($s !== null && $s !== '') {
                                $evalScores[] = (float) $s;
                            }
                        }
                    }
                }
                if (! empty($evalScores)) {
                    $nilaiEvaluasi = round(array_sum($evalScores) / count($evalScores), 2);
                }
            }

            // ⭐ WAJIB LENGKAP — harus ada setoran DAN evaluasi
            if ($nilaiSetoran === null || $nilaiEvaluasi === null) {
                continue;
            }

            // ─── Nilai Akhir — gabungan setoran + evaluasi ───
            $finalScore = round(($nilaiSetoran + $nilaiEvaluasi) / 2, 2);

            $tilawahRows[] = [
                'nama'     => $maqra['label'],
                'kktp'     => 80,
                'nilai'    => $finalScore,
                'predikat' => $hitungPredikat($finalScore),
            ];
        }

        // TAHFIZH RANGE
        $tahfizhRange = null;
        if (! empty($grouped['tahfizh'])) {
            $tahfizhRange = $buildRange($grouped['tahfizh']);
            if ($tahfizhRange) {
                $tahfizhRange['nilai'] = $avgNilai($grouped['tahfizh']);
            }
        }

        // TAHSIN RANGE
        $tahsinRange = null;
        if (! empty($grouped['tahsin'])) {
            $tahsinRange = $buildRange($grouped['tahsin']);
            if ($tahsinRange) {
                $tahsinRange['nilai'] = $avgNilai($grouped['tahsin']);
            }
        }

        if (! empty($grouped['murottal'])) {
            $grouped['murottal'] = $sortBySurah($grouped['murottal']);
        }

        // ═══════════════════════════════════════════════════
        // SUSUN MAPEL BLOCKS
        // ═══════════════════════════════════════════════════
        $mapelBlocks = [];

        if (! empty($tilawahRows)) {
            $mapelBlocks[] = [
                'label' => 'Tilawah',
                'rows'  => $tilawahRows,
            ];
        }

        if ($tahfizhRange) {
            $nilaiTahfizh = $tahfizhRange['nilai'];

            $rangeText = 'QS ' . strtoupper($tahfizhRange['first_surah'])
                . ($tahfizhRange['first_ayat'] ? ' ' . $tahfizhRange['first_ayat'] : '')
                . ' — QS ' . strtoupper($tahfizhRange['last_surah'])
                . ($tahfizhRange['last_ayat'] ? ' ' . $tahfizhRange['last_ayat'] : '');

            $mapelBlocks[] = [
                'label' => 'Tahfizh',
                'rows'  => [[
                    'nama'     => $rangeText,
                    'kktp'     => 80,
                    'nilai'    => $nilaiTahfizh,
                    'predikat' => $hitungPredikat($nilaiTahfizh),
                ]],
            ];
        }

        if ($tahsinRange) {
            $nilaiTahsin = $tahsinRange['nilai'];

            $rangeText = 'QS ' . strtoupper($tahsinRange['first_surah'])
                . ($tahsinRange['first_ayat'] ? ' ' . $tahsinRange['first_ayat'] : '')
                . ' — QS ' . strtoupper($tahsinRange['last_surah'])
                . ($tahsinRange['last_ayat'] ? ' ' . $tahsinRange['last_ayat'] : '');

            $mapelBlocks[] = [
                'label' => 'Tahsin',
                'rows'  => [[
                    'nama'     => $rangeText,
                    'kktp'     => 80,
                    'nilai'    => $nilaiTahsin,
                    'predikat' => $hitungPredikat($nilaiTahsin),
                ]],
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

        // RATA-RATA
        $avgPerMapel = [];
        foreach ($mapelBlocks as $block) {
            $scores = array_filter(
                array_column($block['rows'], 'nilai'),
                fn ($v) => $v !== null && $v !== ''
            );
            if (! empty($scores)) {
                $avgPerMapel[] = array_sum($scores) / count($scores);
            }
        }

        $rataKeseluruhan = ! empty($avgPerMapel)
            ? round(array_sum($avgPerMapel) / count($avgPerMapel), 2)
            : null;
    @endphp

    {{-- HEADER --}}
    <table class="header-table">
        <tr>
            <td class="header-logo">
                @if($base64Logo)<img src="{{ $base64Logo }}" alt="Logo">@endif
            </td>
            <td class="header-title">
                <h1>RUMAH QUR'AN ASY-SYAMS</h1>
                <h2>TILAWAH CENTER PEKANBARU</h2>
                <div class="address">Jl. Tegal Sari Gg. Kemuning No. 9 • Telp: +62 853-7637-4040 • Email: rqasysyams23@gmail.com</div>
            </td>
            <td class="header-badge"><div class="badge-inner">RAPOR</div></td>
        </tr>
    </table>

    {{-- STUDENT INFO + QR CODE --}}
    <table class="info-wrapper">
        <tr>
            <td class="info-col">
                <table class="info-table">
                    <tr><td class="label">Nama</td><td class="colon">:</td><td class="value">{{ $student->name }}</td></tr>
                    <tr><td class="label">NISN</td><td class="colon">:</td><td class="value">{{ $student->nisn ?? '-' }}</td></tr>
                </table>
            </td>
            <td class="info-col-right">
                <table class="info-table">
                    <tr><td class="label">Kelas</td><td class="colon">:</td><td class="value">{{ $className }}</td></tr>
                    <tr><td class="label">Semester</td><td class="colon">:</td><td class="value">{{ $semesterName }}</td></tr>
                </table>
            </td>
            <td class="qr-col">
                @if(!empty($qrSvg))
                    <div class="qr-box">
                        <img src="{{ $qrSvg }}" width="80" height="80" alt="QR">
                    </div>
                    <div class="qr-caption">Scan untuk<br>verifikasi keaslian</div>
                @endif
            </td>
        </tr>
    </table>

    {{-- A. NILAI PENINGKATAN --}}
    <div class="section-title">A. Nilai Peningkatan Santri</div>

    @if(empty($mapelBlocks))
        <div class="empty-state" style="margin-top: 5px;">Belum ada setoran tercatat untuk semester ini.</div>
    @else
        @foreach($mapelBlocks as $block)
            <table class="data-table mapel-table">
                <thead>
                    <tr class="mapel-header-row">
                        <th colspan="4">MAPEL : {{ strtoupper($block['label']) }}</th>
                    </tr>
                    <tr class="col-header-row">
                        <th width="45%">Nama Surah / Maqra'</th>
                        <th width="13%">KKTP</th>
                        <th width="17%">Nilai</th>
                        <th width="25%">Predikat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($block['rows'] as $row)
                        <tr>
                            <td>{{ $row['nama'] }}</td>
                            <td class="text-center bg-green">{{ $row['kktp'] }}</td>
                            <td class="text-center font-bold">{{ $row['nilai'] !== null ? number_format((float) $row['nilai'], 2) : '—' }}</td>
                            <td class="text-center">{{ $row['predikat'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach

        {{-- RATA-RATA --}}
        <table class="data-table rata-table">
            <tbody>
                <tr>
                    <td width="45%" class="text-center" style="font-size: 11px; letter-spacing: 0.5px;">RATA - RATA</td>
                    <td colspan="3" class="text-center" style="font-size: 11px;">
                        {{ $rataKeseluruhan !== null ? number_format($rataKeseluruhan, 2) : '—' }}
                    </td>
                </tr>
            </tbody>
        </table>
    @endif

    {{-- B. ASPEK KARAKTER --}}
    <div class="section-title">B. Aspek Karakter & Sistem Penilaian</div>
    <table class="two-col-table">
        <tr>
            <td>
                <table class="data-table" style="margin-top: 3px;">
                    <thead>
                        <tr>
                            <th width="45%">Aspek Karakter</th>
                            <th width="20%">Nilai</th>
                            <th width="35%">Bintang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($karakter as $k)
                            <tr>
                                <td>{{ $k['label'] }}</td>
                                <td class="text-center bg-green font-bold">{{ $k['score'] > 0 ? $k['score'] : '—' }}</td>
                                <td class="text-center">
                                    @if($k['stars'] > 0)
                                        @for($i = 0; $i < $k['stars']; $i++)<span class="star">&#9733;</span>@endfor
                                        @for($i = $k['stars']; $i < 5; $i++)<span class="star-empty">&#9734;</span>@endfor
                                    @else
                                        <span style="color: #ccc;">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        @if($characterAssessment)
                            <tr class="bg-dark">
                                <td class="text-center">Rata - Rata</td>
                                <td class="text-center">{{ number_format($characterAssessment->average_score, 1) }}</td>
                                <td class="text-center">
                                    @for($i = 0; $i < $characterAssessment->average_stars; $i++)<span class="star">&#9733;</span>@endfor
                                    @for($i = $characterAssessment->average_stars; $i < 5; $i++)<span style="color: rgba(255,255,255,0.3);">&#9734;</span>@endfor
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </td>
            <td>
                <table class="data-table" style="margin-top: 3px;">
                    <thead><tr><th colspan="2">Sistem Bintang</th></tr></thead>
                    <tbody>
                        <tr><td width="60%">Sangat Baik (90-100)</td><td class="text-center bg-green"><span class="star">&#9733;&#9733;&#9733;&#9733;&#9733;</span></td></tr>
                        <tr><td>Baik (80-89)</td><td class="text-center bg-green"><span class="star">&#9733;&#9733;&#9733;&#9733;</span></td></tr>
                        <tr><td>Cukup (70-79)</td><td class="text-center bg-green"><span class="star">&#9733;&#9733;&#9733;</span></td></tr>
                        <tr><td>Kurang (60-69)</td><td class="text-center bg-green"><span class="star">&#9733;&#9733;</span></td></tr>
                        <tr><td>Kurang Sekali (&lt; 60)</td><td class="text-center bg-green"><span class="star">&#9733;</span></td></tr>
                    </tbody>
                </table>
                <table class="data-table">
                    <thead><tr><th colspan="2">Sistem Penilaian</th></tr></thead>
                    <tbody>
                        <tr><td width="60%" class="text-center">91 - 100</td><td class="text-center bg-green font-bold">A</td></tr>
                        <tr><td class="text-center">81 - 90</td><td class="text-center bg-green font-bold">B</td></tr>
                        <tr><td class="text-center">71 - 80</td><td class="text-center bg-green font-bold">C</td></tr>
                        <tr><td class="text-center">0 - 70</td><td class="text-center bg-green font-bold">D</td></tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    {{-- TANDA TANGAN --}}
    <div class="signature-wrapper">
        <div class="signature-date">
            Pekanbaru, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}
        </div>

        <table class="signature-table">
            <tr>
                <td>
                    <div class="signature-label">Orang Tua / Wali,</div>
                    <div class="signature-sublabel">&nbsp;</div>
                </td>
                <td>
                    <div class="signature-label">Mengetahui,</div>
                    <div class="signature-sublabel">{{ $kepalaJabatan }},</div>
                </td>
                <td>
                    <div class="signature-label">Pengajar,</div>
                    <div class="signature-sublabel">&nbsp;</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="signature-space"></div>
                    <div class="signature-name-empty">&nbsp;</div>
                </td>
                <td>
                    <div class="signature-space"></div>
                    <div class="signature-name">{{ $kepalaNama }}</div>
                </td>
                <td>
                    <div class="signature-space"></div>
                    <div class="signature-name">{{ $teacherName }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer-note">
        Dokumen ini dicetak secara otomatis dari Sistem Informasi YPTQ Asy-Syams.
    </div>

</body>
</html>