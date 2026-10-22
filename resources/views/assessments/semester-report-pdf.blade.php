<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rapor Semester {{ $semester->name ?? 'N/A' }} {{ now()->year }}</title>
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
        .semester-info {
            background-color: #f0fdf4;
            border: 2px solid #059669;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .semester-info h2 {
            margin: 0 0 10px 0;
            color: #059669;
            font-size: 14px;
        }
        .semester-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
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
            page-break-inside: avoid;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            font-size: 10px;
            color: #666;
        }
        @media print {
            .section-title {
                page-break-after: avoid;
            }
            table {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <h1>LAPORAN RAPOR SEMESTER</h1>
        <p><strong>Pondok As-Syams Pekanbaru</strong></p>
        <p>Semester: {{ $semester->name ?? 'Tidak Diketahui' }} - Tahun Ajaran {{ now()->year }}/{{ now()->year + 1 }}</p>
        @if ($classGroup)
            <p>Kelas: {{ $classGroup->name }}</p>
        @endif
    </div>

    <!-- Filter Info -->
    <div class="filter-info">
        <strong>Periode:</strong> Semester {{ $semester->name }} {{ now()->year }}/{{ now()->year + 1 }}
        @if ($classGroup)
            | <strong>Kelas:</strong> {{ $classGroup->name }}
        @else
            | <strong>Kelas:</strong> Semua Kelas
        @endif
        | <strong>Tanggal Cetak:</strong> {{ now()->translatedFormat('d F Y H:i') }}
    </div>

    <!-- Semester Information -->
    <div class="semester-info">
        <h2>INFORMASI SEMESTER</h2>
        <div class="semester-info-grid">
            <div>
                <strong>Jenis Semester:</strong><br>
                {{ $semester->name }}
            </div>
            <div>
                <strong>Tahun Ajaran:</strong><br>
                {{ now()->year }}/{{ now()->year + 1 }}
            </div>
            <div>
                <strong>Total Penilaian:</strong><br>
                {{ $assessments->count() }}
            </div>
        </div>
    </div>

    <!-- Tahsin Section -->
    @if ($assessments->where('assessment_type', 'tahsin')->count() > 0)
        <div class="section-title">📖 PENILAIAN TAHSIN</div>
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
                    <th>Bulan</th>
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
                                <td>{{ Carbon\Carbon::create()->month($item->month)->translatedFormat('F') }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Tahfizh Section -->
    @if ($assessments->where('assessment_type', 'tahfizh')->count() > 0)
        <div class="section-title">💚 PENILAIAN TAHFIZH</div>
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
                    <th>Bulan</th>
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
                                <td>{{ Carbon\Carbon::create()->month($item->month)->translatedFormat('F') }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Tajwid Section -->
    @if ($assessments->where('assessment_type', 'tajwid')->count() > 0)
        <div class="section-title">✨ PENILAIAN TAJWID</div>
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
                    <th>Bulan</th>
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
                                <td>{{ Carbon\Carbon::create()->month($item->month)->translatedFormat('F') }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini adalah bagian dari Laporan Penilaian Santri Pondok As-Syams</p>
        <p>Dicetak secara otomatis oleh Sistem Manajemen Pondok As-Syams</p>
        <p>© 2025 - Pondok As-Syams Pekanbaru. All Rights Reserved.</p>
    </div>

</body>
</html>
