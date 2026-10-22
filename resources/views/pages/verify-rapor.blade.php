@extends('root.app')

@section('title', 'Verifikasi Rapor — YPTQ Asy-Syams')

@section('content')
<div class="bg-gray-50 min-h-screen py-10">

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        @if($valid)
            {{-- ══════════════ HASIL VALID ══════════════ --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

                <div class="bg-gradient-to-br from-emerald-500 to-green-700 p-8 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur rounded-full mb-4">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-white">Rapor Valid</h1>
                    <p class="text-emerald-100 text-sm mt-2">
                        Rapor ini terdaftar di sistem YPTQ Asy-Syams
                    </p>
                </div>

                <div class="p-8 space-y-6">

                    {{-- Info santri --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                            Informasi Santri
                        </p>
                        <div class="space-y-3">
                            <div class="flex justify-between border-b border-gray-100 pb-3">
                                <span class="text-sm text-gray-600">Nama Lengkap</span>
                                <span class="text-sm font-bold text-gray-900">{{ $student->name }}</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-100 pb-3">
                                <span class="text-sm text-gray-600">NISN</span>
                                <span class="text-sm font-semibold text-gray-900">{{ $student->nisn ?? '—' }}</span>
                            </div>
                            @if(!empty($classGroup))
                                <div class="flex justify-between border-b border-gray-100 pb-3">
                                    <span class="text-sm text-gray-600">Kelas</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ $classGroup->name }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between border-b border-gray-100 pb-3">
                                <span class="text-sm text-gray-600">Semester</span>
                                <span class="text-sm font-semibold text-gray-900">{{ $semester->name }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Info verifikasi --}}
                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <div class="text-sm text-emerald-800">
                                <p class="font-semibold">Rapor ini asli</p>
                                <p class="mt-1 text-emerald-700">
                                    Diverifikasi pada
                                    {{ $verifiedAt->locale('id')->translatedFormat('d F Y, H:i') }} WIB.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Token (technical) --}}
                    <div class="text-xs text-gray-400 font-mono break-all text-center pt-2">
                        Token: {{ \Illuminate\Support\Str::limit($token, 40) }}...
                    </div>

                </div>
            </div>

        @else
            {{-- ══════════════ HASIL INVALID ══════════════ --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

                <div class="bg-gradient-to-br from-red-500 to-red-700 p-8 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur rounded-full mb-4">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-white">Rapor Tidak Valid</h1>
                    <p class="text-red-100 text-sm mt-2">
                        Rapor ini tidak terdaftar atau sudah tidak berlaku
                    </p>
                </div>

                <div class="p-8">
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div class="text-sm text-red-800">
                                <p class="font-semibold">Verifikasi gagal</p>
                                <p class="mt-1 text-red-700">{{ $reason }}</p>
                            </div>
                        </div>
                    </div>

                    <p class="text-xs text-gray-500 mt-4 text-center">
                        Jika Anda yakin rapor ini asli, hubungi admin YPTQ Asy-Syams untuk verifikasi manual.
                    </p>
                </div>
            </div>
        @endif

        {{-- Tombol kembali --}}
        <div class="mt-6 text-center">
            <a href="{{ url('/') }}"
               class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Beranda
            </a>
        </div>

    </div>
</div>
@endsection