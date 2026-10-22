@extends('root.app')

@section('title', 'Riwayat Absensi')

@section('content')
<div class="bg-gray-50 min-h-screen pb-12">

    {{-- HEADER --}}
    <div class="bg-gradient-to-br from-purple-700 to-purple-800 pb-20">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                        📅 Riwayat Kehadiran
                    </h1>
                    <p class="text-purple-100 mt-2">
                        Rekap absensi Anda per pertemuan
                    </p>
                </div>
                <a href="{{ route('dashboard') }}"
                    class="hidden sm:inline-flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur text-white text-sm font-semibold rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12 space-y-6">

        {{-- ⭐ KARTU STATISTIK --}}
        @php
        $countHadir = $attendances->where('status', 'present')->count();
        $countSakit = $attendances->where('status', 'sick')->count();
        $countIzin = $attendances->where('status', 'permission')->count();
        $countAlpha = $attendances->where('status', 'alpha')->count();
        $total = $attendances->total();
        @endphp

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">

                {{-- Persentase --}}
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2 bg-purple-100 text-purple-700 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-gray-500 uppercase">Persentase Kehadiran</div>
                            <div class="text-3xl font-bold text-purple-700">{{ $percentage }}%</div>
                        </div>
                    </div>

                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-500 to-purple-700 h-3 rounded-full transition-all duration-500"
                            style="width: {{ $percentage }}%"></div>
                    </div>
                </div>

                {{-- Breakdown --}}
                <div class="grid grid-cols-4 gap-3 md:gap-4">
                    <div class="text-center px-3 py-2 bg-green-50 rounded-lg border border-green-200">
                        <div class="text-2xl font-bold text-green-700">{{ $countHadir }}</div>
                        <div class="text-xs font-semibold text-green-800 uppercase mt-1">Hadir</div>
                    </div>
                    <div class="text-center px-3 py-2 bg-yellow-50 rounded-lg border border-yellow-200">
                        <div class="text-2xl font-bold text-yellow-700">{{ $countSakit }}</div>
                        <div class="text-xs font-semibold text-yellow-800 uppercase mt-1">Sakit</div>
                    </div>
                    <div class="text-center px-3 py-2 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="text-2xl font-bold text-blue-700">{{ $countIzin }}</div>
                        <div class="text-xs font-semibold text-blue-800 uppercase mt-1">Izin</div>
                    </div>
                    <div class="text-center px-3 py-2 bg-red-50 rounded-lg border border-red-200">
                        <div class="text-2xl font-bold text-red-700">{{ $countAlpha }}</div>
                        <div class="text-xs font-semibold text-red-800 uppercase mt-1">Alpha</div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ⭐ LIST ABSENSI --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">

            <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    📋 Detail Riwayat
                </h2>
                <p class="text-sm text-gray-500 mt-1">Total {{ $total }} data absensi tercatat</p>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($attendances as $attendance)
                @php
                $statusConfig = match ($attendance->status) {
                'present' => ['Hadir', 'green', '✓'],
                'sick' => ['Sakit', 'yellow', '🤒'],
                'permission' => ['Izin', 'blue', '📝'],
                'alpha' => ['Alpha', 'red', '✕'],
                default => ['—', 'gray', '•'],
                };
                [$statusLabel, $statusColor, $statusIcon] = $statusConfig;
                @endphp

                <div class="px-6 py-4 hover:bg-gray-50 transition">
                    <div class="flex items-start justify-between gap-4">

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-semibold text-gray-900 truncate">
                                    {{ $attendance->meeting->classGroup->subject->name ?? '—' }}
                                </span>
                                <span class="text-gray-300">·</span>
                                <span class="text-sm text-gray-600 truncate">
                                    {{ $attendance->meeting->title ?? '—' }}
                                </span>
                            </div>
                            <div class="flex items-center gap-3 text-xs text-gray-500">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $attendance->meeting->date->format('d M Y') }}
                                </span>
                                @if($attendance->meeting->classGroup->name ?? null)
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    {{ $attendance->meeting->classGroup->name }}
                                </span>
                                @endif
                            </div>
                        </div>

                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-bold
                                bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800 flex-shrink-0">
                            <span>{{ $statusIcon }}</span>
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="px-6 py-16 text-center">
                    <div class="inline-block p-4 rounded-full bg-gray-100 text-gray-400 mb-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Belum Ada Absensi</h3>
                    <p class="text-gray-500 mt-1">Data absensi akan muncul setelah Ustad menginput pertemuan.</p>
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($attendances->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $attendances->links() }}
            </div>
            @endif
        </div>

        {{-- Tombol Kembali (mobile) --}}
        <div class="sm:hidden">
            <a href="{{ route('dashboard') }}"
                class="flex items-center justify-center gap-2 w-full py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Dashboard
            </a>
        </div>

    </div>
</div>
@endsection