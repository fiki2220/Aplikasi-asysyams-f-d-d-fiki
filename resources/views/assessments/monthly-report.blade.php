@extends('root.app')

@section('title', 'Rekapan Penilaian Bulanan')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Rekapan Penilaian Bulanan</h1>
        <p class="text-gray-600">Statistik penilaian santri per bulan</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Bulan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                <select name="month" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                            {{ Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <!-- Tahun -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                <input type="number" name="year" value="{{ $year }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
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
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-emerald-600 text-white font-semibold py-2 rounded-lg hover:bg-emerald-700 transition">
                    Filter
                </button>
                <a href="{{ route('reports.monthly.pdf', ['month' => $month, 'year' => $year, 'class_group_id' => $classGroupId]) }}" 
                   class="flex-1 bg-orange-700 text-white font-semibold py-2 rounded-lg hover:bg-orange-800 transition text-center">
                    📥 PDF
                </a>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        @php
            $tahsinStats = $stats['tahsin'] ?? [];
            $tahfizhStats = $stats['tahfizh'] ?? [];
            $tajwidStats = $stats['tajwid'] ?? [];
        @endphp

        <!-- Tahsin -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-emerald-700">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Tahsin</h3>
            <div class="space-y-2 text-sm">
                <p class="text-gray-600">Total: <span class="font-bold">{{ $tahsinStats['total'] ?? 0 }}</span></p>
                <p class="text-green-600">Lancar: <span class="font-bold">{{ $tahsinStats['lancar'] ?? 0 }}</span></p>
                <p class="text-yellow-600">Cukup: <span class="font-bold">{{ $tahsinStats['cukup'] ?? 0 }}</span></p>
                <p class="text-red-600">Tidak Lancar: <span class="font-bold">{{ $tahsinStats['tidak_lancar'] ?? 0 }}</span></p>
            </div>
        </div>

        <!-- Tahfizh -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Tahfizh</h3>
            <div class="space-y-2 text-sm">
                <p class="text-gray-600">Total: <span class="font-bold">{{ $tahfizhStats['total'] ?? 0 }}</span></p>
                <p class="text-green-600">Lancar: <span class="font-bold">{{ $tahfizhStats['lancar'] ?? 0 }}</span></p>
                <p class="text-yellow-600">Cukup: <span class="font-bold">{{ $tahfizhStats['cukup'] ?? 0 }}</span></p>
                <p class="text-red-600">Tidak Lancar: <span class="font-bold">{{ $tahfizhStats['tidak_lancar'] ?? 0 }}</span></p>
            </div>
        </div>

        <!-- Tajwid -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-700">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Tajwid</h3>
            <div class="space-y-2 text-sm">
                <p class="text-gray-600">Total: <span class="font-bold">{{ $tajwidStats['total'] ?? 0 }}</span></p>
                <p class="text-green-600">Lancar: <span class="font-bold">{{ $tajwidStats['lancar'] ?? 0 }}</span></p>
                <p class="text-yellow-600">Cukup: <span class="font-bold">{{ $tajwidStats['cukup'] ?? 0 }}</span></p>
                <p class="text-red-600">Tidak Lancar: <span class="font-bold">{{ $tajwidStats['tidak_lancar'] ?? 0 }}</span></p>
            </div>
        </div>

        <!-- Total Keseluruhan -->
        <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-lg shadow-md p-6 text-white">
            <h3 class="text-lg font-bold mb-4">Total Keseluruhan</h3>
            <div class="space-y-2 text-sm">
                <p class="text-emerald-100">Total Data: <span class="font-bold text-2xl">{{ collect($stats)->sum('total') }}</span></p>
                <p class="text-emerald-100 mt-4">{{ Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Detail Table -->
    @foreach (['tahsin' => 'Tahsin', 'tahfizh' => 'Tahfizh', 'tajwid' => 'Tajwid'] as $key => $label)
        @if (isset($assessments[$key]) && $assessments[$key]->count() > 0)
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <div class="bg-emerald-700 text-white px-6 py-4">
                    <h2 class="text-xl font-bold">{{ $label }}</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 border-b border-gray-300">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Nama Santri</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Kelas</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Surah</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Ayat</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Nilai</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Nilai Penyetoran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($assessments[$key] as $assessment)
                                @if ($assessment->data)
                                    @foreach ($assessment->data as $item)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $item['nama'] ?? $assessment->student->name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $assessment->classGroup->name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $item['surah'] ?? '-' }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $item['ayat'] ?? '-' }}</td>
                                            <td class="px-6 py-4 text-sm">
                                                @if ($item['nilai'] === 'L')
                                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">L (Lancar)</span>
                                                @elseif ($item['nilai'] === 'C')
                                                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">C (Cukup)</span>
                                                @else
                                                    <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-semibold">TL (Tidak Lancar)</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $item['nilai_penyetoran'] ?? '-' }}</td>
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
    @if (collect($assessments)->count() === 0)
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <p class="text-gray-600 text-lg">Tidak ada data penilaian untuk bulan ini.</p>
        </div>
    @endif

</div>

@endsection
