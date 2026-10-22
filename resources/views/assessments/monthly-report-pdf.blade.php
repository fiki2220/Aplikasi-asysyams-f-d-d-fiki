<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rekapan Penilaian {{ $monthName ?? 'Bulan' }} {{ $year }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #059669;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #059669;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
        }
        .filter-info {
            background-color: #f3f4f6;
            padding: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #059669;
            font-size: 12px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            margin-bottom: 30px;
        }
        .stat-box {
            border: 1px solid #059669;
            padding: 12px;
            border-radius: 4px;
            background-color: #f0fdf4;
        }
        .stat-box h3 {
            margin: 0 0 10px 0;
            color: #059669;
            font-size: 14px;
            font-weight: bold;
        }
        .stat-item {
            font-size: 11px;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }
        table thead {
            background-color: #059669;
            color: white;
        }
        table th {
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        table td {
            padding: 6px 8px;
            border: 1px solid #ddd;
        }
        table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .nilai-l {
            background-color: #dcfce7;
            color: #166534;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
        }
        .nilai-c {
            background-color: #fef3c7;
            color: #92400e;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
        }
        .nilai-tl {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
        }
        .section-title {
            background-color: #059669;
            color: white;
            padding: 10px;
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
            font-size: 12px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <h1>REKAPAN PENILAIAN SANTRI</h1>
        <p><strong>Pondok As-Syams</strong></p>
        <p>Bulan: {{ $monthName ?? 'Tidak Diketahui' }} {{ $year }}</p>
        @if ($classGroup)
            <p>Kelas: {{ $classGroup->name }}</p>
        @endif
    </div>

    <!-- Filter Info -->
    <div class="filter-info">
        <strong>Periode:</strong> {{ $monthName }} {{ $year }} 
        @if ($classGroup)
            | <strong>Kelas:</strong> {{ $classGroup->name }}
        @else
            | <strong>Kelas:</strong> Semua Kelas
        @endif
        | <strong>Tanggal Cetak:</strong> {{ now()->translatedFormat('d F Y H:i') }}
    </div>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-box">
            <h3>Tahsin</h3>
            <div class="stat-item">📊 Total: <strong>{{ $assessments->where('assessment_type', 'tahsin')->count() }}</strong></div>
            <div class="stat-item">✓ Lancar: <strong>{{ $assessments->where('assessment_type', 'tahsin')->filter(fn($a) => $a->data && collect($a->data)->where('nilai', 'L')->count() > 0)->count() }}</strong></div>
            <div class="stat-item">~ Cukup: <strong>{{ $assessments->where('assessment_type', 'tahsin')->filter(fn($a) => $a->data && collect($a->data)->where('nilai', 'C')->count() > 0)->count() }}</strong></div>
            <div class="stat-item">✗ Tidak Lancar: <strong>{{ $assessments->where('assessment_type', 'tahsin')->filter(fn($a) => $a->data && collect($a->data)->where('nilai', 'TL')->count() > 0)->count() }}</strong></div>
        </div>

        <div class="stat-box">
            <h3>Tahfizh</h3>
            <div class="stat-item">📊 Total: <strong>{{ $assessments->where('assessment_type', 'tahfizh')->count() }}</strong></div>
            <div class="stat-item">✓ Lancar: <strong>{{ $assessments->where('assessment_type', 'tahfizh')->filter(fn($a) => $a->data && collect($a->data)->where('nilai', 'L')->count() > 0)->count() }}</strong></div>
            <div class="stat-item">~ Cukup: <strong>{{ $assessments->where('assessment_type', 'tahfizh')->filter(fn($a) => $a->data && collect($a->data)->where('nilai', 'C')->count() > 0)->count() }}</strong></div>
            <div class="stat-item">✗ Tidak Lancar: <strong>{{ $assessments->where('assessment_type', 'tahfizh')->filter(fn($a) => $a->data && collect($a->data)->where('nilai', 'TL')->count() > 0)->count() }}</strong></div>
        </div>

        <div class="stat-box">
            <h3>Tajwid</h3>
            <div class="stat-item">📊 Total: <strong>{{ $assessments->where('assessment_type', 'tajwid')->count() }}</strong></div>
            <div class="stat-item">✓ Lancar: <strong>{{ $assessments->where('assessment_type', 'tajwid')->filter(fn($a) => $a->data && collect($a->data)->where('nilai', 'L')->count() > 0)->count() }}</strong></div>
            <div class="stat-item">~ Cukup: <strong>{{ $assessments->where('assessment_type', 'tajwid')->filter(fn($a) => $a->data && collect($a->data)->where('nilai', 'C')->count() > 0)->count() }}</strong></div>
            <div class="stat-item">✗ Tidak Lancar: <strong>{{ $assessments->where('assessment_type', 'tajwid')->filter(fn($a) => $a->data && collect($a->data)->where('nilai', 'TL')->count() > 0)->count() }}</strong></div>
        </div>
    </div>

    <!-- Tahsin Table -->
    @if ($assessments->where('assessment_type', 'tahsin')->count() > 0)
        <div class="section-title">PENILAIAN TAHSIN</div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Santri</th>
                    <th>Kelas</th>
                    <th>Surah</th>
                    <th>Ayat</th>
                    <th>Nilai</th>
                    <th>Nilai Penyetoran</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($assessments->where('assessment_type', 'tahsin') as $item)
                    @if ($item->data)
                        @foreach ($item->data as $data)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $data['nama'] ?? $item->student->name }}</td>
                                <td>{{ $item->classGroup->name }}</td>
                                <td>{{ $data['surah'] ?? '-' }}</td>
                                <td>{{ $data['ayat'] ?? '-' }}</td>
                                <td>
                                    @if ($data['nilai'] === 'L')
                                        <span class="nilai-l">L</span>
                                    @elseif ($data['nilai'] === 'C')
                                        <span class="nilai-c">C</span>
                                    @else
                                        <span class="nilai-tl">TL</span>
                                    @endif
                                </td>
                                <td>{{ $data['nilai_penyetoran'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Tahfizh Table -->
    @if ($assessments->where('assessment_type', 'tahfizh')->count() > 0)
        <div class="section-title">PENILAIAN TAHFIZH</div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Santri</th>
                    <th>Kelas</th>
                    <th>Surah</th>
                    <th>Ayat</th>
                    <th>Nilai</th>
                    <th>Nilai Penyetoran</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($assessments->where('assessment_type', 'tahfizh') as $item)
                    @if ($item->data)
                        @foreach ($item->data as $data)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $data['nama'] ?? $item->student->name }}</td>
                                <td>{{ $item->classGroup->name }}</td>
                                <td>{{ $data['surah'] ?? '-' }}</td>
                                <td>{{ $data['ayat'] ?? '-' }}</td>
                                <td>
                                    @if ($data['nilai'] === 'L')
                                        <span class="nilai-l">L</span>
                                    @elseif ($data['nilai'] === 'C')
                                        <span class="nilai-c">C</span>
                                    @else
                                        <span class="nilai-tl">TL</span>
                                    @endif
                                </td>
                                <td>{{ $data['nilai_penyetoran'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Tajwid Table -->
    @if ($assessments->where('assessment_type', 'tajwid')->count() > 0)
        <div class="section-title">PENILAIAN TAJWID</div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Santri</th>
                    <th>Kelas</th>
                    <th>Surah</th>
                    <th>Ayat</th>
                    <th>Nilai</th>
                    <th>Nilai Penyetoran</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($assessments->where('assessment_type', 'tajwid') as $item)
                    @if ($item->data)
                        @foreach ($item->data as $data)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $data['nama'] ?? $item->student->name }}</td>
                                <td>{{ $item->classGroup->name }}</td>
                                <td>{{ $data['surah'] ?? '-' }}</td>
                                <td>{{ $data['ayat'] ?? '-' }}</td>
                                <td>
                                    @if ($data['nilai'] === 'L')
                                        <span class="nilai-l">L</span>
                                    @elseif ($data['nilai'] === 'C')
                                        <span class="nilai-c">C</span>
                                    @else
                                        <span class="nilai-tl">TL</span>
                                    @endif
                                </td>
                                <td>{{ $data['nilai_penyetoran'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis oleh Sistem Manajemen Pondok As-Syams</p>
        <p>© 2025 - Pondok As-Syams Pekanbaru</p>
    </div>

</body>
</html>
