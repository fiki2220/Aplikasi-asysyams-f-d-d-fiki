@php
    $className = $classGroup?->name ?? '-';
    $semesterName = $classGroup?->semester?->name;
    $classDisplay = collect([$className, $semesterName])->filter()->implode(' / ');

    $displayRows = collect($rows);
    $count = $displayRows->count();

    // ⭐ LOGIKA BARIS KOSONG
    if ($count >= 15) {
        $emptyRows = 0;
        $isDataEmpty = false;
    } elseif ($count === 0) {
        $emptyRows = 15;
        $isDataEmpty = true;
    } else {
        $emptyRows = 15 - $count;
        $isDataEmpty = false;
    }

    $colWidths = $colWidths ?? [
        'no'      => '4%',
        'date'    => '9%',
        'reading' => '21%',
        'mark'    => '3%',
        'sign'    => '6%',
    ];

    $markNum = (int) filter_var($colWidths['mark'], FILTER_SANITIZE_NUMBER_INT);
    $unit    = preg_replace('/[0-9.]/', '', $colWidths['mark']) ?: '%';
    $markTotal3 = ($markNum * 3) . $unit;
    $markTotal2 = ($markNum * 2) . $unit;
@endphp

<div class="student-control-sheet">
    <div class="sheet-header">
        <div class="logo-cell">
            @if (! empty($logoSrc))
                <img src="{{ $logoSrc }}" alt="Logo Asy-Syams">
            @else
                <div class="logo-fallback">ASY<br>SYAMS</div>
            @endif
        </div>
        <div class="brand-cell">
            <div class="brand-title">{{ $institution['name'] }}</div>
            <div class="brand-subtitle">{{ $institution['subtitle'] }}</div>
            @if (! empty($institution['address']) || ! empty($institution['phone']) || ! empty($institution['email']))
                <div class="brand-contact">
                    {{ collect([$institution['address'], $institution['phone'], $institution['email']])->filter()->implode(' | ') }}
                </div>
            @endif
        </div>
    </div>

    <div class="top-rule"></div>

    <div class="identity-row">
        <div class="identity-field">
            <span class="identity-label">Nama</span>
            <span class="identity-colon">:</span>
            <span class="identity-value">{{ $student->name }}</span>
        </div>
        <div class="identity-field identity-class">
            <span class="identity-label">Kelas</span>
            <span class="identity-colon">:</span>
            <span class="identity-value">{{ $classDisplay }}</span>
        </div>
    </div>

    <table class="control-table">
        <colgroup>
            <col style="width: {{ $colWidths['no'] }}">
            <col style="width: {{ $colWidths['date'] }}">
            <col style="width: {{ $colWidths['reading'] }}">
            <col style="width: {{ $colWidths['mark'] }}">
            <col style="width: {{ $colWidths['mark'] }}">
            <col style="width: {{ $colWidths['mark'] }}">
            <col style="width: {{ $colWidths['reading'] }}">
            <col style="width: {{ $colWidths['mark'] }}">
            <col style="width: {{ $colWidths['mark'] }}">
            <col style="width: {{ $colWidths['reading'] }}">
            <col style="width: {{ $colWidths['sign'] }}">
        </colgroup>

        <thead>
            <tr>
                <th rowspan="2" style="width: {{ $colWidths['no'] }};">NO</th>
                <th rowspan="2" style="width: {{ $colWidths['date'] }};">TANGGAL</th>
                <th rowspan="2" style="width: {{ $colWidths['reading'] }};">ZIYADAH</th>
                <th colspan="3" style="width: {{ $markTotal3 }};">KET</th>
                <th rowspan="2" style="width: {{ $colWidths['reading'] }};">MUROJAAH</th>
                <th colspan="2" style="width: {{ $markTotal2 }};">KET</th>
                <th rowspan="2" style="width: {{ $colWidths['reading'] }};">TAHSIN</th>
                <th rowspan="2" style="width: {{ $colWidths['sign'] }};">TTD</th>
            </tr>
            <tr>
                <th style="width: {{ $colWidths['mark'] }};">L</th>
                <th style="width: {{ $colWidths['mark'] }};">C</th>
                <th style="width: {{ $colWidths['mark'] }};">TL</th>
                <th style="width: {{ $colWidths['mark'] }};">B</th>
                <th style="width: {{ $colWidths['mark'] }};">K</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($displayRows as $row)
                <tr>
                    <td class="text-center">{{ $row['number'] }}</td>
                    <td class="text-center">
                        {{ $row['date'] ? $row['date']->format('d/m/Y') : '' }}
                    </td>
                    <td>{{ $row['ziyadah'] ?: '' }}</td>
                    <td class="text-center mark-cell">{{ $row['ziyadah_score'] === 'L' ? '✓' : '' }}</td>
                    <td class="text-center mark-cell">{{ $row['ziyadah_score'] === 'C' ? '✓' : '' }}</td>
                    <td class="text-center mark-cell">{{ $row['ziyadah_score'] === 'TL' ? '✓' : '' }}</td>
                    <td>{{ $row['murojaah'] ?: '' }}</td>
                    <td class="text-center mark-cell">{{ $row['murojaah_score'] === 'B' ? '✓' : '' }}</td>
                    <td class="text-center mark-cell">{{ $row['murojaah_score'] === 'K' ? '✓' : '' }}</td>
                    <td>{{ $row['tahsin'] ?: '' }}</td>

                    {{-- ⭐ Kolom TTD — inisial pengajar per baris --}}
                    <td class="text-center ttd-initials" title="{{ $row['creator_name'] ?? '' }}">
                        {{ $row['creator_initials'] ?? '—' }}
                    </td>
                </tr>
            @endforeach

            @if ($isDataEmpty)
                <tr>
                    <td colspan="11" class="empty-state">Belum ada data assessment untuk santri ini.</td>
                </tr>
            @endif

            @for ($i = 0; $i < $emptyRows; $i++)
                <tr class="blank-row">
                    <td>&nbsp;</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
        </tbody>
    </table>

    {{-- ⭐ KETERANGAN TTD --}}
    @if (! empty($creatorLegend))
        <div class="creator-legend">
            <strong>Keterangan TTD:</strong>
            <ul>
                @foreach ($creatorLegend as $initials => $name)
                    <li><strong>{{ $initials }}</strong> = {{ $name }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ⭐ CATATAN PEMBELAJARAN --}}
    <div class="summary-section note-section">
        <div class="summary-title">CATATAN PEMBELAJARAN</div>
        <table class="summary-table note-table">
            <thead>
                <tr>
                    <th style="width: 12%;">Tanggal</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($learningNotes as $learningNote)
                    @php $notesList = $learningNote['notes'] ?? []; @endphp
                    <tr>
                        <td class="text-center" style="vertical-align: top;">
                            {{ $learningNote['date']?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td style="vertical-align: top;">
                            @if (count($notesList) > 1)
                                @foreach ($notesList as $note)
                                    <div style="margin-bottom: 2px;">
                                        &bull; {{ $note }}
                                    </div>
                                @endforeach
                            @elseif (count($notesList) === 1)
                                {{ $notesList[0] }}
                            @else
                                {{ $learningNote['note'] ?? '—' }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="empty-state">Belum ada catatan pembelajaran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="notes">
        <div>Keterangan:</div>
        <ol>
            <li>Beri tanda centang (✓) pada kolom keterangan.</li>
            <li>Kolom Ziyadah: Lancar (L), Cukup (C), Tidak Lancar (TL).</li>
            <li>Kolom Murojaah: Baik (B), Kurang (K).</li>
        </ol>
    </div>

    {{-- ⭐ EVALUASI --}}
    <div class="summary-section">
        <div class="summary-title">EVALUASI</div>
        <table class="summary-table evaluation-table">
            <thead>
                <tr>
                    <th style="width: 22%;">Surah / Ayat</th>
                    <th style="width: 22%;">Lagu / Irama</th>
                    <th style="width: 30%;">Catatan / Item</th>
                    <th style="width: 10%;">Nilai</th>
                    <th style="width: 16%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($evaluationSummary as $evaluation)
                    <tr>
                        <td>{{ $evaluation['surah_name'] ?? '—' }}</td>
                        <td>{{ $evaluation['song_name'] ?? '—' }}</td>
                        <td>{{ $evaluation['item'] ?? '—' }}</td>
                        <td class="text-center">{{ $evaluation['score'] ?? '—' }}</td>
                        <td class="text-center">{{ $evaluation['status'] ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-state">Belum ada data evaluasi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ⭐ REKAP ABSENSI --}}
    <div class="summary-section">
        <div class="summary-title">REKAP ABSENSI</div>
        <table class="summary-table attendance-table">
            <thead>
                <tr>
                    <th>Hadir</th>
                    <th>Sakit</th>
                    <th>Izin</th>
                    <th>Alpha</th>
                    <th>Total</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">{{ $attendanceSummary['present'] }}</td>
                    <td class="text-center">{{ $attendanceSummary['sick'] }}</td>
                    <td class="text-center">{{ $attendanceSummary['permission'] }}</td>
                    <td class="text-center">{{ $attendanceSummary['alpha'] }}</td>
                    <td class="text-center">{{ $attendanceSummary['total'] }}</td>
                    <td class="text-center">{{ $attendanceSummary['percentage'] }}%</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>