@extends('root.app')

@section('title', 'SPMB 2026/2027 — YPTQ Asy-Syams')
@section('meta_description', 'Sistem Penerimaan Mahasiswa/Peserta Didik Baru (SPMB) YPTQ Asy-Syams tahun ajaran 2026/2027. Daftarkan putra-putri Anda sekarang.')

@section('content')

<style>
    html {
        scroll-behavior: smooth;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* ⭐ Gradient via CSS — bypass Tailwind purge */
    .spmb-hero-bg {
        background:
            radial-gradient(circle at 20% 0%, rgba(52, 211, 153, 0.2) 0%, transparent 45%),
            radial-gradient(circle at 80% 100%, rgba(245, 158, 11, 0.12) 0%, transparent 45%),
            linear-gradient(135deg, #064e3b 0%, #065f46 45%, #022c22 100%);
    }

    .spmb-cta-bg {
        background: linear-gradient(135deg, #fffbeb 0%, #f0fdf4 100%);
    }

    .spmb-grid-pattern {
        background-image:
            linear-gradient(rgba(255, 255, 255, 0.06) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.06) 1px, transparent 1px);
        background-size: 48px 48px;
        mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 30%, transparent 80%);
        -webkit-mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 30%, transparent 80%);
    }

    /* ⭐ Button base — clean, no overflow */
    .btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 1.75rem;
        background: #f59e0b;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.875rem;
        border-radius: 9999px;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);
        transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
        white-space: nowrap;
        line-height: 1;
        text-decoration: none;
    }

    .btn-primary:hover {
        background: #d97706;
        box-shadow: 0 6px 20px rgba(245, 158, 11, 0.35);
        transform: translateY(-1px);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .btn-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 1.75rem;
        background: #ffffff;
        color: #047857;
        font-weight: 700;
        font-size: 0.875rem;
        border-radius: 9999px;
        border: 2px solid #a7f3d0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
        white-space: nowrap;
        line-height: 1;
        text-decoration: none;
    }

    .btn-secondary:hover {
        border-color: #10b981;
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.15);
        transform: translateY(-1px);
    }

    .btn-secondary:active {
        transform: translateY(0);
    }

    .btn-ghost {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 1.75rem;
        background: rgba(255, 255, 255, 0.1);
        color: #ffffff;
        font-weight: 700;
        font-size: 0.875rem;
        border-radius: 9999px;
        border: 1.5px solid rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(8px);
        transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
        white-space: nowrap;
        line-height: 1;
        text-decoration: none;
    }

    .btn-ghost:hover {
        background: rgba(255, 255, 255, 0.18);
        border-color: rgba(255, 255, 255, 0.5);
        transform: translateY(-1px);
    }
</style>


{{-- ============================================================ --}}
{{-- 1. HERO SPMB                                                  --}}
{{-- ============================================================ --}}
<section class="spmb-hero-bg relative text-white overflow-hidden">

    <div class="absolute inset-0 spmb-grid-pattern pointer-events-none"></div>

    <div class="absolute -top-32 -right-32 w-[500px] h-[500px] rounded-full pointer-events-none"
        style="background: radial-gradient(circle, rgba(251,191,36,0.18) 0%, transparent 70%);"></div>
    <div class="absolute -bottom-40 -left-40 w-[500px] h-[500px] rounded-full pointer-events-none"
        style="background: radial-gradient(circle, rgba(52,211,153,0.15) 0%, transparent 70%);"></div>

    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
        <div class="text-center max-w-3xl mx-auto">

            {{-- Badge --}}
            <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full mb-6"
                style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(8px);">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" style="background: #fbbf24;"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2" style="background: #fbbf24;"></span>
                </span>
                <span class="text-[10px] md:text-xs font-bold uppercase tracking-widest text-white/95">
                    Penerimaan Dibuka — TA 2026/2027
                </span>
            </div>

            {{-- Title --}}
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold mb-4 leading-tight text-white">
                SPMB
                <span class="block mt-1" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;">
                    YPTQ Asy-Syams
                </span>
            </h1>

            {{-- Divider --}}
            <div class="flex items-center justify-center gap-3 mb-5">
                <span class="h-px w-12" style="background: linear-gradient(90deg, transparent, rgba(251,191,36,0.6));"></span>
                <svg class="w-3.5 h-3.5" style="color: #fbbf24;" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 0l1.5 6.5L18 8l-6.5 1.5L10 16l-1.5-6.5L2 8l6.5-1.5L10 0z" />
                </svg>
                <span class="h-px w-12" style="background: linear-gradient(90deg, rgba(251,191,36,0.6), transparent);"></span>
            </div>

            {{-- Subtitle --}}
            <p class="text-sm md:text-base text-white/90 max-w-2xl mx-auto leading-relaxed mb-8">
                Bergabunglah bersama kami dalam mencetak generasi Qur'ani
                yang berakhlak mulia, berilmu, dan berprestasi.
            </p>

            {{-- CTA Buttons --}}
            <div class="flex flex-col sm:flex-row gap-3 justify-center items-center">
                <a href="{{ route('register') }}" class="btn-primary group">
                    <span>Daftar Sekarang</span>
                    <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>

                <a href="#alur" class="btn-ghost">
                    <span>Lihat Alur</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                </a>
            </div>

        </div>
    </div>
</section>


{{-- ============================================================ --}}
{{-- 2. JENJANG PENDIDIKAN (SYARAT & BIAYA DIGABUNG)              --}}
{{-- ============================================================ --}}
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl inline-block relative">
                Pilihan <span class="text-green-800">Jenjang Pendidikan</span>
                <span class="block mt-4 h-1 w-20 bg-yellow-400 mx-auto rounded-full"></span>
            </h2>
            <p class="mt-4 text-gray-500">
                Pilih program yang sesuai. Syarat dan rincian biaya masing-masing program tercantum di bawah ini.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">

            {{-- Rumah Qur'an --}}
            <div class="group bg-white rounded-2xl p-7 border border-gray-100 shadow-sm
                        hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 flex flex-col">
                <div class="mb-5 flex items-center gap-4">
                    <div class="w-14 h-14 bg-green-50 text-green-600 rounded-xl flex items-center justify-center
                                group-hover:bg-green-600 group-hover:text-white transition-all duration-500 shadow-inner shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-[#183F3B] group-hover:text-green-700 transition-colors">
                            Rumah Qur'an (RQ)
                        </h3>
                        <p class="text-gray-500 text-sm mt-1">
                            Tahsin, Tahfizh, & Seni Baca Al-Qur'an.
                        </p>
                    </div>
                </div>

                {{-- Syarat & Biaya RQ --}}
                <div class="mt-4 pt-5 border-t border-gray-100 space-y-6 flex-1">
                    <div>
                        <h4 class="font-bold text-[#183F3B] mb-3 text-sm">Syarat Pendaftaran:</h4>
                        <ul class="space-y-2">
                            <li class="flex items-start gap-2 text-sm text-gray-600">
                                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Fotokopi Akta Kelahiran (2 lembar)</span>
                            </li>
                            <li class="flex items-start gap-2 text-sm text-gray-600">
                                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Fotokopi Kartu Keluarga (KK)</span>
                            </li>
                            <li class="flex items-start gap-2 text-sm text-gray-600">
                                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Fotokopi KTP Orang Tua/Wali</span>
                            </li>
                            <li class="flex items-start gap-2 text-sm text-gray-600">
                                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Mengisi formulir pendaftaran online</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-emerald-50/50 p-4 rounded-xl border border-emerald-100">
                        <h4 class="font-bold text-[#183F3B] mb-2 text-sm">Biaya RQ:</h4>
                        <div class="space-y-1.5 text-sm text-gray-700">
                            <div class="flex justify-between"><span>Pendaftaran</span> <span class="font-bold">Rp 100.000</span></div>
                            <div class="flex justify-between"><span>Uang Pangkal</span> <span class="font-bold">Rp 1.000.000</span></div>
                            <div class="flex justify-between"><span>SPP Bulanan</span> <span class="font-bold">Rp 200.000</span></div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100">
                    <a href="{{ url('/register?jenjang=RQ') }}"
                        class="w-full flex justify-center items-center gap-1.5 text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-4 py-3 rounded-lg font-bold text-sm transition-all duration-300">
                        <span>Daftar RQ</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>

            {{-- TK Asy-Syams --}}
            <div class="group bg-white rounded-2xl p-7 border border-gray-100 shadow-sm
                        hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 flex flex-col">
                <div class="mb-5 flex items-center gap-4">
                    <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center
                                group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 shadow-inner shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-[#183F3B] group-hover:text-blue-700 transition-colors">
                            TK Asy-Syams
                        </h3>
                        <p class="text-gray-500 text-sm mt-1">
                            Playgroup, TK A, & TK B.
                        </p>
                    </div>
                </div>

                {{-- Syarat & Biaya TK --}}
                <div class="mt-4 pt-5 border-t border-gray-100 space-y-6 flex-1">
                    <div>
                        <h4 class="font-bold text-[#183F3B] mb-3 text-sm">Syarat Pendaftaran:</h4>
                        <ul class="space-y-2">
                            <li class="flex items-start gap-2 text-sm text-gray-600">
                                <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Fotokopi Akta Kelahiran (2 lembar)</span>
                            </li>
                            <li class="flex items-start gap-2 text-sm text-gray-600">
                                <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Fotokopi Kartu Keluarga (KK)</span>
                            </li>
                            <li class="flex items-start gap-2 text-sm text-gray-600">
                                <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Fotokopi KTP Orang Tua/Wali</span>
                            </li>
                            <li class="flex items-start gap-2 text-sm text-gray-600">
                                <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Mengisi formulir pendaftaran online</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                        <h4 class="font-bold text-[#183F3B] mb-2 text-sm">Biaya TK:</h4>
                        <div class="space-y-1.5 text-sm text-gray-700">
                            <div class="flex justify-between"><span>Pendaftaran</span> <span class="font-bold">Rp 150.000</span></div>
                            <div class="flex justify-between"><span>Uang Pangkal</span> <span class="font-bold">Rp 1.500.000</span></div>
                            <div class="flex justify-between"><span>SPP Bulanan</span> <span class="font-bold">Rp 300.000</span></div>
                            <div class="flex justify-between"><span>Seragam & Perlengkapan</span> <span class="font-bold">Rp 750.000</span></div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100">
                    <a href="{{ url('/register?jenjang=TKIT') }}"
                        class="w-full flex justify-center items-center gap-1.5 text-blue-700 bg-blue-50 hover:bg-blue-100 px-4 py-3 rounded-lg font-bold text-sm transition-all duration-300">
                        <span>Daftar TK Asy-Syams</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>


{{-- ============================================================ --}}
{{-- 3. ALUR PENDAFTARAN                                          --}}
{{-- ============================================================ --}}
<section id="alur" class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl inline-block relative">
                Alur <span class="text-green-800">Pendaftaran</span>
                <span class="block mt-4 h-1 w-20 bg-yellow-400 mx-auto rounded-full"></span>
            </h2>
            <p class="mt-4 text-gray-500">
                Ikuti langkah-langkah berikut untuk mendaftar sebagai peserta didik baru.
            </p>
        </div>

        @php
        $alur = [
        ['no' => '01', 'title' => 'Registrasi Online', 'desc' => 'Daftar melalui website resmi kami dengan mengisi formulir pendaftaran online.'],
        ['no' => '02', 'title' => 'Pembayaran Biaya Pendaftaran', 'desc' => 'Lakukan pembayaran biaya pendaftaran sesuai instruksi yang diberikan.'],
        ['no' => '03', 'title' => 'Kelengkapan Data', 'desc' => 'Login ke portal SPMB untuk melengkapi data diri, orang tua, dan upload dokumen.'],
        ['no' => '04', 'title' => 'Seleksi & Tes', 'desc' => 'Ikuti rangkaian tes (baca Al-Qur\'an, wawancara) sesuai jadwal yang ditentukan sekolah.'],
        ['no' => '05', 'title' => 'Pengumuman', 'desc' => 'Hasil seleksi diumumkan melalui website resmi dan/atau WhatsApp pendaftar.'],
        ['no' => '06', 'title' => 'Daftar Ulang', 'desc' => 'Peserta yang diterima melakukan daftar ulang & pembayaran biaya pendidikan.'],
        ];
        @endphp

        <div class="relative max-w-4xl mx-auto">
            <div class="hidden md:block absolute left-1/2 top-0 bottom-0 w-px -translate-x-1/2"
                style="background: linear-gradient(to bottom, transparent 0%, #a7f3d0 10%, #10b981 50%, #a7f3d0 90%, transparent 100%);"></div>

            <div class="space-y-5 md:space-y-8">
                @foreach($alur as $i => $step)
                <div class="relative md:grid md:grid-cols-2 md:gap-12 items-center">

                    @if($i % 2 === 0)
                    <div class="md:text-right md:pr-10">
                        <div class="group inline-block w-full md:w-auto bg-white rounded-2xl p-6 border border-gray-100
                                            hover:border-emerald-200 transition-all duration-300 hover:-translate-y-1
                                            hover:shadow-2xl">
                            <div class="flex items-start gap-4 md:flex-row-reverse">
                                <div class="flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center
                                                    transition-transform duration-300 group-hover:scale-110"
                                    style="background: linear-gradient(135deg, #10b981 0%, #047857 100%); box-shadow: 0 6px 16px -4px rgba(16,185,129,0.4);">
                                    <span class="text-white font-extrabold text-sm">{{ $step['no'] }}</span>
                                </div>
                                <div class="flex-1 md:text-right pt-1">
                                    <h3 class="text-base font-bold text-[#183F3B] mb-1.5">
                                        {{ $step['title'] }}
                                    </h3>
                                    <p class="text-xs text-gray-500 leading-relaxed">
                                        {{ $step['desc'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="hidden md:flex absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2
                                        w-3 h-3 bg-white rounded-full z-10"
                        style="box-shadow: 0 0 0 4px #d1fae5, 0 0 0 5px #10b981;"></div>
                    <div class="hidden md:block"></div>
                    @else
                    <div class="hidden md:block"></div>
                    <div class="hidden md:flex absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2
                                        w-3 h-3 bg-white rounded-full z-10"
                        style="box-shadow: 0 0 0 4px #d1fae5, 0 0 0 5px #10b981;"></div>

                    <div class="md:pl-10">
                        <div class="group inline-block w-full md:w-auto bg-white rounded-2xl p-6 border border-gray-100
                                            hover:border-emerald-200 transition-all duration-300 hover:-translate-y-1
                                            hover:shadow-2xl">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center
                                                    transition-transform duration-300 group-hover:scale-110"
                                    style="background: linear-gradient(135deg, #10b981 0%, #047857 100%); box-shadow: 0 6px 16px -4px rgba(16,185,129,0.4);">
                                    <span class="text-white font-extrabold text-sm">{{ $step['no'] }}</span>
                                </div>
                                <div class="flex-1 pt-1">
                                    <h3 class="text-base font-bold text-[#183F3B] mb-1.5">
                                        {{ $step['title'] }}
                                    </h3>
                                    <p class="text-xs text-gray-500 leading-relaxed">
                                        {{ $step['desc'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
                @endforeach
            </div>
        </div>

    </div>
</section>


{{-- ============================================================ --}}
{{-- 4. JADWAL PENTING                                            --}}
{{-- ============================================================ --}}
<section class="py-16 bg-gray-50 border-t border-gray-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl inline-block relative">
                Jadwal <span class="text-green-800">Penting</span>
                <span class="block mt-4 h-1 w-20 bg-yellow-400 mx-auto rounded-full"></span>
            </h2>
            <p class="mt-4 text-gray-500">
                Catat tanggal penting berikut agar tidak terlewat.
            </p>
        </div>

        @php
        $jadwal = [
        ['date' => '1 Mei 2026', 'event' => 'Pembukaan Pendaftaran Online', 'dot' => '#10b981', 'text' => '#047857', 'ring' => 'rgba(16,185,129,0.18)'],
        ['date' => '30 Juni 2026', 'event' => 'Batas Akhir Pendaftaran', 'dot' => '#ef4444', 'text' => '#b91c1c', 'ring' => 'rgba(239,68,68,0.18)'],
        ['date' => '5 Juli 2026', 'event' => 'Pelaksanaan Tes Seleksi', 'dot' => '#f59e0b', 'text' => '#b45309', 'ring' => 'rgba(245,158,11,0.18)'],
        ['date' => '10 Juli 2026', 'event' => 'Pengumuman Hasil Seleksi', 'dot' => '#3b82f6', 'text' => '#1d4ed8', 'ring' => 'rgba(59,130,246,0.18)'],
        ['date' => '15 Juli 2026', 'event' => 'Daftar Ulang Peserta Diterima', 'dot' => '#a855f7', 'text' => '#7e22ce', 'ring' => 'rgba(168,85,247,0.18)'],
        ['date' => '20 Juli 2026', 'event' => 'Awal Tahun Ajaran Baru', 'dot' => '#10b981', 'text' => '#047857', 'ring' => 'rgba(16,185,129,0.18)'],
        ];
        @endphp

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @foreach($jadwal as $i => $item)
            <div class="flex items-center gap-4 md:gap-6 px-6 py-4
                            {{ $i < count($jadwal) - 1 ? 'border-b border-gray-100' : '' }}
                            hover:bg-gray-50 transition-colors duration-200">

                <div class="flex-shrink-0">
                    <div class="w-2.5 h-2.5 rounded-full" style="background: {{ $item['dot'] }}; box-shadow: 0 0 0 4px {{ $item['ring'] }};"></div>
                </div>

                <div class="flex-shrink-0 w-28 md:w-32">
                    <span class="text-xs font-bold uppercase tracking-wider" style="color: {{ $item['text'] }};">
                        {{ $item['date'] }}
                    </span>
                </div>

                <div class="flex-1">
                    <p class="text-sm text-gray-700">
                        {{ $item['event'] }}
                    </p>
                </div>

            </div>
            @endforeach
        </div>

    </div>
</section>


{{-- ============================================================ --}}
{{-- 5. FAQ                                                       --}}
{{-- ============================================================ --}}
<section class="py-16 bg-gray-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl inline-block relative">
                Pertanyaan <span class="text-green-800">Umum</span>
                <span class="block mt-4 h-1 w-20 bg-yellow-400 mx-auto rounded-full"></span>
            </h2>
            <p class="mt-4 text-gray-500">
                Beberapa pertanyaan yang sering ditanyakan calon peserta didik.
            </p>
        </div>

        @php
        $faqs = [
        ['q' => 'Siapa saja yang bisa mendaftar di YPTQ Asy-Syams?', 'a' => 'Pendaftaran terbuka untuk semua kalangan, baik anak-anak, remaja, maupun dewasa yang ingin belajar membaca dan menghafal Al-Qur\'an. Untuk TK Asy-Syams, khusus anak usia 3-6 tahun.'],
        ['q' => 'Apakah ada tes masuk?', 'a' => 'Ya, ada tes sederhana berupa tes baca Al-Qur\'an dan wawancara untuk menempatkan peserta pada kelas yang sesuai. Tidak ada tes yang memberatkan, hanya untuk pemetaan.'],
        ['q' => 'Bagaimana cara pembayaran biaya pendaftaran?', 'a' => 'Pembayaran bisa dilakukan melalui transfer bank atau langsung ke kantor yayasan. Detail rekening akan dikirim via WhatsApp setelah registrasi online.'],
        ['q' => 'Apakah ada beasiswa?', 'a' => 'Ya, YPTQ Asy-Syams menyediakan beasiswa untuk peserta didik berprestasi dan dari keluarga kurang mampu. Silakan hubungi admin untuk informasi lebih lanjut.'],
        ['q' => 'Berapa lama proses pendaftaran?', 'a' => 'Proses pendaftaran hingga pengumuman biasanya memakan waktu 2-3 minggu, tergantung pada kelengkapan dokumen dan jadwal tes seleksi.'],
        ['q' => 'Apakah bisa daftar langsung ke kantor?', 'a' => 'Bisa. Namun kami sangat menyarankan untuk mendaftar online terlebih dahulu agar proses lebih cepat. Jika ada kendala, silakan datang ke kantor untuk dibantu.'],
        ];
        @endphp

        <div class="space-y-3" x-data="{ openFaq: null }">
            @foreach($faqs as $i => $faq)
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden
                            hover:border-emerald-200 transition-colors duration-200">

                <button type="button"
                    @click="openFaq = openFaq === {{ $i }} ? null : {{ $i }}"
                    class="w-full flex items-center justify-between gap-4 px-5 md:px-6 py-5 text-left">
                    <span class="font-bold text-[#183F3B] text-sm md:text-base pr-4">
                        {{ $faq['q'] }}
                    </span>
                    <span class="flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center
                                     transition-all duration-300"
                        :class="{ 'rotate-180': openFaq === {{ $i }} }"
                        :style="openFaq === {{ $i }} ? 'background:#10b981;' : 'background:#f3f4f6;'">
                        <svg class="w-3.5 h-3.5 transition-colors duration-300"
                            :class="openFaq === {{ $i }} ? 'text-white' : 'text-emerald-600'"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </span>
                </button>

                <div x-show="openFaq === {{ $i }}" x-collapse
                    class="px-5 md:px-6 pb-5 text-sm text-gray-500 leading-relaxed border-t border-gray-100 pt-4">
                    {{ $faq['a'] }}
                </div>
            </div>
            @endforeach
        </div>

    </div>
</section>


{{-- ============================================================ --}}
{{-- 6. CTA FINAL — Rapi & Professional                            --}}
{{-- ============================================================ --}}
<section class="spmb-cta-bg py-16 md:py-20 border-t border-gray-100">
    <div class="max-w-3xl mx-auto px-6 sm:px-8 lg:px-12 text-center">

        {{-- Icon --}}
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-full mb-5"
            style="background: #f59e0b; box-shadow: 0 8px 20px rgba(245,158,11,0.25);">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        {{-- Heading --}}
        <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl mb-4">
            Siap Bergabung <span class="text-green-800">Bersama Kami?</span>
        </h2>

        {{-- Subtitle --}}
        <p class="text-gray-500 max-w-xl mx-auto mb-8 leading-relaxed">
            Daftarkan putra-putri Anda sekarang dan jadilah bagian dari generasi
            Qur'ani yang berakhlak mulia dan berprestasi.
        </p>

        {{-- Buttons --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-center items-center">
            <a href="{{ route('register') }}" class="btn-primary group">
                <span>Daftar Sekarang</span>
                <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>

            <a href="https://wa.me/6285376374040?text={{ urlencode('Assalamu\'alaikum, saya ingin bertanya tentang SPMB YPTQ Asy-Syams.') }}"
                target="_blank" rel="noopener noreferrer"
                class="btn-secondary">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12.04 2C6.57 2 2.13 6.44 2.13 11.91c0 1.75.46 3.46 1.34 4.97L2 22l5.25-1.38a9.86 9.86 0 004.79 1.22h.01c5.47 0 9.91-4.44 9.91-9.91C21.96 6.44 17.52 2 12.04 2z" />
                </svg>
                <span>Hubungi Admin</span>
            </a>
        </div>

    </div>
</section>

@endsection