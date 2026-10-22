@extends('root.app')

@section('title', 'Transkrip Nilai')

@section('content')
<div class="bg-gray-50 min-h-screen pb-12">

    {{-- HEADER --}}
    <div class="bg-gradient-to-br from-green-700 to-green-800 pb-20">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                        📚 Transkrip Nilai
                    </h1>
                    <p class="text-green-100 mt-2">
                        Rekap nilai akademik per semester
                    </p>
                </div>
                <a href="{{ route('dashboard') }}"
                   class="hidden sm:inline-flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur text-white text-sm font-semibold rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12 space-y-6">

        {{-- ⭐ SUMMARY STATISTIK --}}
        @php
            $allGrades = $gradesGrouped->flatten();
            $totalMapel = $allGrades->count();
            $nilaiValid = $allGrades->filter(fn ($g) => $g->score !== null)->pluck('score');
            $rataRata = $nilaiValid->count() > 0 ? round($nilaiValid->avg(), 1) : null;
            $nilaiTertinggi = $nilaiValid->count() > 0 ? $nilaiValid->max() : null;
            $totalSemester = $gradesGrouped->count();
        @endphp

        @if($totalMapel > 0)
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl shadow p-5 border-l-4 border-emerald-500">
                    <div class="text-xs font-semibold text-gray-500 uppercase">Rata-rata</div>
                    <div class="text-2xl font-bold text-gray-900 mt-1">{{ $rataRata ?? '—' }}</div>
                </div>
                <div class="bg-white rounded-xl shadow p-5 border-l-4 border-blue-500">
                    <div class="text-xs font-semibold text-gray-500 uppercase">Tertinggi</div>
                    <div class="text-2xl font-bold text-gray-900 mt-1">{{ $nilaiTertinggi ?? '—' }}</div>
                </div>
                <div class="bg-white rounded-xl shadow p-5 border-l-4 border-amber-500">
                    <div class="text-xs font-semibold text-gray-500 uppercase">Total Mapel</div>
                    <div class="text-2xl font-bold text-gray-900 mt-1">{{ $totalMapel }}</div>
                </div>
                <div class="bg-white rounded-xl shadow p-5 border-l-4 border-purple-500">
                    <div class="text-xs font-semibold text-gray-500 uppercase">Semester</div>
                    <div class="text-2xl font-bold text-gray-900 mt-1">{{ $totalSemester }}</div>
                </div>
            </div>
        @endif

        {{-- ⭐ LIST SEMESTER --}}
        @forelse($gradesGrouped as $semesterName => $grades)
            @php
                $nilaiSemester = $grades->filter(fn ($g) => $g->score !== null)->pluck('score');
                $avgSemester = $nilaiSemester->count() > 0 ? round($nilaiSemester->avg(), 1) : null;
            @endphp

            <div class="bg-white rounded-xl shadow-lg overflow-hidden">

                {{-- Header Semester --}}
                <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-200 flex items-center justify-between flex-wrap gap-3">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-green-100 text-green-700 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $semesterName }}</h3>
                            <p class="text-xs text-gray-500">{{ $grades->count() }} mata pelajaran</p>
                        </div>
                    </div>

                    @if($avgSemester)
                        <div class="text-right">
                            <div class="text-xs text-gray-500 uppercase font-semibold">Rata-rata</div>
                            <div class="text-xl font-bold text-emerald-600">{{ $avgSemester }}</div>
                        </div>
                    @endif
                </div>

                {{-- Table Header --}}
                <div class="hidden md:grid grid-cols-12 gap-4 px-6 py-3 bg-gray-50 border-b border-gray-200 text-xs font-bold text-gray-600 uppercase tracking-wide">
                    <div class="col-span-5">Mata Pelajaran</div>
                    <div class="col-span-2 text-center">Nilai</div>
                    <div class="col-span-2 text-center">Predikat</div>
                    <div class="col-span-3">Catatan</div>
                </div>

                {{-- Table Body --}}
                <div class="divide-y divide-gray-100">
                    @foreach($grades as $grade)
                        @php
                            $score = $grade->score;
                            $predikat = $score === null
                                ? ['—', 'gray']
                                : ($score >= 91 ? ['A', 'emerald']
                                : ($score >= 81 ? ['B', 'blue']
                                : ($score >= 71 ? ['C', 'amber']
                                : ['D', 'red'])));
                        $predikatLabel = $predikat[0];
                        $predikatColor = $predikat[1];
                    @endphp

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-2 md:gap-4 px-6 py-4 items-center hover:bg-gray-50 transition">

                            {{-- Mapel --}}
                            <div class="md:col-span-5 flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-green-500 flex-shrink-0"></div>
                                <span class="font-medium text-gray-900">{{ $grade->subject->name }}</span>
                            </div>

                            {{-- Nilai --}}
                            <div class="md:col-span-2 flex md:justify-center items-center gap-2">
                                <span class="md:hidden text-xs text-gray-500 font-semibold w-20">Nilai:</span>
                                @if($score !== null)
                                    <span class="inline-flex items-center justify-center min-w-[3rem] px-3 py-1 rounded-lg text-sm font-bold
                                        {{ $score >= 90 ? 'bg-emerald-100 text-emerald-800'
                                            : ($score >= 75 ? 'bg-blue-100 text-blue-800'
                                            : ($score >= 60 ? 'bg-amber-100 text-amber-800'
                                            : 'bg-red-100 text-red-800')) }}">
                                        {{ $score }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </div>

                            {{-- Predikat --}}
                            <div class="md:col-span-2 flex md:justify-center items-center gap-2">
                                <span class="md:hidden text-xs text-gray-500 font-semibold w-20">Predikat:</span>
                                <span class="inline-flex items-center justify-center min-w-[2rem] px-3 py-1 rounded-lg text-sm font-bold
                                    bg-{{ $predikatColor }}-100 text-{{ $predikatColor }}-800">
                                    {{ $predikatLabel }}
                                </span>
                            </div>

                            {{-- Catatan --}}
                            <div class="md:col-span-3 flex items-start gap-2">
                                <span class="md:hidden text-xs text-gray-500 font-semibold w-20">Catatan:</span>
                                <span class="text-sm text-gray-600 italic">
                                    {{ $grade->notes ?? '—' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                <div class="inline-block p-4 rounded-full bg-gray-100 text-gray-400 mb-4">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Belum Ada Nilai</h3>
                <p class="text-gray-500 mt-1">Nilai akan muncul setelah Ustad melakukan input.</p>
            </div>
        @endforelse

        {{-- Tombol Kembali (mobile) --}}
        <div class="sm:hidden">
            <a href="{{ route('dashboard') }}"
               class="flex items-center justify-center gap-2 w-full py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Dashboard
            </a>
        </div>

    </div>
</div>
@endsection