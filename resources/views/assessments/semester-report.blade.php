@extends('root.app')

@section('title', 'Laporan Rapor Semester')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Laporan Rapor Semester</h1>
        <p class="text-gray-600">Rekapan penilaian untuk laporan rapor santri per 6 bulan</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Semester -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Semester</label>
                <select name="semester_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Pilih Semester --</option>
                    @foreach ($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ $semester->id == $sem->id ? 'selected' : '' }}>
                            {{ $sem->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Kelas -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                <select name="class_group_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Semua Kelas --</option>
                    @foreach ($classGroups as $group)
                        <option value="{{ $group->id }}" {{ $classGroupId == $group->id ? 'selected' : '' }}>
                            {{ $group->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-end gap-2 md:col-span-2">
                <button type="submit" class="flex-1 bg-emerald-600 text-white font-semibold py-2 rounded-lg hover:bg-emerald-700 transition">
                    Filter
                </button>
                <a href="{{ route('reports.semester.pdf', ['semester_id' => $semester->id, 'class_group_id' => $classGroupId]) }}" 
                   class="flex-1 bg-orange-700 text-white font-semibold py-2 rounded-lg hover:bg-orange-800 transition text-center">
                    📥 PDF
                </a>
            </div>
        </form>
    </div>

    <!-- Semester Info -->
    <div class="bg-gradient-to-r from-emerald-700 to-emerald-600 text-white rounded-lg shadow-md p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-emerald-100 text-sm">Semester</p>
                <p class="text-2xl font-bold">{{ $semester->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-emerald-100 text-sm">Tahun Ajaran</p>
                <p class="text-2xl font-bold">{{ now()->year }}/{{ now()->year + 1 }}</p>
            </div>
            <div>
                <p class="text-emerald-100 text-sm">Total Data Penilaian</p>
                <p class="text-2xl font-bold">{{ $assessments->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Summary by Assessment Type -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @php
            $tahsinCount = $assessments->where('assessment_type', 'tahsin')->count();
            $tahfizhCount = $assessments->where('assessment_type', 'tahfizh')->count();
            $tajwidCount = $assessments->where('assessment_type', 'tajwid')->count();
        @endphp

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-emerald-700">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Tahsin</h3>
            <p class="text-3xl font-bold text-emerald-700">{{ $tahsinCount }}</p>
            <p class="text-sm text-gray-600 mt-2">Data Penilaian</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Tahfizh</h3>
            <p class="text-3xl font-bold text-yellow-600">{{ $tahfizhCount }}</p>
            <p class="text-sm text-gray-600 mt-2">Data Penilaian</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-700">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Tajwid</h3>
            <p class="text-3xl font-bold text-orange-700">{{ $tajwidCount }}</p>
            <p class="text-sm text-gray-600 mt-2">Data Penilaian</p>
        </div>
    </div>

    <!-- Detail Tables by Assessment Type -->
    @foreach (['tahsin' => 'Tahsin', 'tahfizh' => 'Tahfizh', 'tajwid' => 'Tajwid'] as $key => $label)
        @php
            $typeAssessments = $assessments->where('assessment_type', $key);
        @endphp
        
        @if ($typeAssessments->count() > 0)
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <div class="bg-emerald-700 text-white px-6 py-4">
                    <h2 class="text-xl font-bold">{{ $label }}</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 border-b border-gray-300">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">No</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Nama Santri</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Kelas</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Surah</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Ayat</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Nilai</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Nilai Penyetoran</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Bulan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @php $no = 1; @endphp
                            @foreach ($typeAssessments as $assessment)
                                @if ($assessment->data)
                                    @foreach ($assessment->data as $item)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $no++ }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $item['nama'] ?? $assessment->student->name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $assessment->classGroup->name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $item['surah'] ?? '-' }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $item['ayat'] ?? '-' }}</td>
                                            <td class="px-6 py-4 text-sm">
                                                @if ($item['nilai'] === 'L')
                                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">L</span>
                                                @elseif ($item['nilai'] === 'C')
                                                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">C</span>
                                                @else
                                                    <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-semibold">TL</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $item['nilai_penyetoran'] ?? '-' }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                {{ Carbon\Carbon::create()->month($assessment->month)->translatedFormat('F') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endforeach

    <!-- Empty State -->
    @if ($assessments->count() === 0)
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <p class="text-gray-600 text-lg">Tidak ada data penilaian untuk semester ini.</p>
        </div>
    @endif

</div>

@endsection
