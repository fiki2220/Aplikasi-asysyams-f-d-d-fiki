@extends('root.app')

@section('title', 'Menunggu Verifikasi - YPTQ Asy-Syams')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-100 via-green-50 to-gray-100 py-12 px-4 sm:px-6 lg:px-8">

    <div class="max-w-lg w-full">

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- CARD UTAMA                                                --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">

            {{-- Header hijau dengan icon centang --}}
            <div class="bg-green-800 px-6 py-10 text-center relative overflow-hidden">
                {{-- Dekorasi blur --}}
                <div aria-hidden="true" class="absolute top-0 right-0 -mt-8 -mr-8 w-40 h-40 bg-green-700 rounded-full opacity-50 blur-2xl"></div>
                <div aria-hidden="true" class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-green-900 rounded-full opacity-50 blur-2xl"></div>

                <div class="relative z-10">
                    {{-- Icon centang besar --}}
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-white/15 backdrop-blur border border-white/30 mb-5">
                        <svg aria-hidden="true" class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                        Pendaftaran Berhasil!
                    </h1>

                    <p class="mt-2 text-green-100 text-sm sm:text-base font-medium">
                        Formulir PPDB Anda telah kami terima.
                    </p>
                </div>
            </div>

            {{-- Body --}}
            <div class="p-6 sm:p-8">

                {{-- Flash message dari controller (opsional) --}}
                @if(session('status'))
                <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                    {{ session('status') }}
                </div>
                @endif

                {{-- Sapaan ke user --}}
                @auth
                <p class="text-base text-gray-700 mb-4">
                    Halo, <span class="font-bold text-gray-900">{{ Auth::user()->name }}</span> 👋
                </p>
                @endauth

                <p class="text-gray-600 text-sm leading-relaxed mb-6">
                    Akun Anda sedang dalam proses <strong>verifikasi oleh admin</strong> Rumah Qur'an Asy-Syams.
                    Silakan tunggu informasi aktivasi akun melalui email atau WhatsApp yang terdaftar.
                </p>

                {{-- Badge status --}}
                <div class="flex items-center gap-2 mb-6 p-3 rounded-xl bg-amber-50 border border-amber-200">
                    <svg aria-hidden="true" class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-sm">
                        <span class="text-amber-700">Status:</span>
                        <strong class="text-amber-900 uppercase tracking-wide">Menunggu Verifikasi</strong>
                    </div>
                </div>

                {{-- ═══════════════════════════════════════════════════ --}}
                {{-- LANGKAH SELANJUTNYA                                  --}}
                {{-- ═══════════════════════════════════════════════════ --}}
                <div class="border-t border-gray-100 pt-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg aria-hidden="true" class="w-4 h-4 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        Langkah Selanjutnya
                    </h3>

                    <ol class="space-y-3">
                        {{-- Step 1 --}}
                        <li class="flex items-start gap-3">
                            <span class="shrink-0 w-6 h-6 rounded-full bg-green-100 text-green-800 text-xs font-bold flex items-center justify-center">1</span>
                            <span class="text-sm text-gray-600 leading-relaxed">
                                Admin memverifikasi data pendaftaran Anda
                                <span class="block text-xs text-gray-400 mt-0.5">Estimasi 1×24 jam pada hari kerja</span>
                            </span>
                        </li>

                        {{-- Step 2 --}}
                        <li class="flex items-start gap-3">
                            <span class="shrink-0 w-6 h-6 rounded-full bg-green-100 text-green-800 text-xs font-bold flex items-center justify-center">2</span>
                            <span class="text-sm text-gray-600 leading-relaxed">
                                Anda menerima notifikasi via email / WhatsApp
                            </span>
                        </li>

                        {{-- Step 3 --}}
                        <li class="flex items-start gap-3">
                            <span class="shrink-0 w-6 h-6 rounded-full bg-green-100 text-green-800 text-xs font-bold flex items-center justify-center">3</span>
                            <span class="text-sm text-gray-600 leading-relaxed">
                                Login & masuk ke dashboard santri
                            </span>
                        </li>
                    </ol>
                </div>

                {{-- ═══════════════════════════════════════════════════ --}}
                {{-- TOMBOL AKSI                                          --}}
                {{-- ═══════════════════════════════════════════════════ --}}
                <div class="mt-8 pt-6 border-t border-gray-100 space-y-3">

                    {{-- Tombol kembali ke beranda --}}
                    <a href="{{ url('/') }}"
                        class="w-full inline-flex items-center justify-center gap-2 py-3 px-6
                              bg-green-700 hover:bg-green-800
                              text-white text-sm font-bold rounded-xl
                              shadow-md hover:shadow-lg
                              transition-all duration-300 ease-out
                              focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-700">
                        <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Kembali ke Beranda
                    </a>

                    {{-- Tombol logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 py-3 px-6
                                       bg-white hover:bg-gray-50
                                       border border-gray-200 hover:border-gray-300
                                       text-gray-600 hover:text-gray-800
                                       text-sm font-semibold rounded-xl
                                       transition-colors duration-200 ease-out
                                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300">
                            <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Keluar dari Akun
                        </button>
                    </form>

                </div>

            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- FOOTER — Info kontak                                     --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        <p class="mt-6 text-center text-xs text-gray-500">
            Ada pertanyaan? Hubungi admin di
            <a href="https://wa.me/6285376374040"
                target="_blank"
                rel="noopener noreferrer"
                class="font-semibold text-green-700 hover:text-green-800 transition-colors">
                WhatsApp 0853 7637 4040
            </a>
        </p>

    </div>
</div>
@endsection