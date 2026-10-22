@extends('root.app')

@section('title', 'Akses Tidak Diizinkan')

@section('content')
<main class="min-h-[70vh] bg-gray-50 flex items-center justify-center px-4 py-12">
    <section class="w-full max-w-lg bg-white border border-gray-200 rounded-lg shadow-sm p-8 text-center">
        
        {{-- Ikon Peringatan --}}
        <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-red-50 text-red-600" aria-hidden="true">
            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" />
            </svg>
        </div>

        {{-- Teks --}}
        <h1 class="text-2xl font-bold text-gray-900">Akses Tidak Diizinkan</h1>
        <p class="mt-3 text-sm leading-relaxed text-gray-600">
            Hak akses Anda telah berubah atau Anda tidak memiliki izin membuka halaman ini. 
            Silakan kembali ke halaman utama atau login ulang.
        </p>

        {{-- Tombol Aksi --}}
        <div class="mt-8 flex flex-col sm:flex-row justify-center gap-3">
            {{-- Tombol Beranda --}}
            <a href="{{ url('/') }}" 
               class="inline-flex items-center justify-center px-5 py-3 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Beranda
            </a>

            {{-- Tombol Logout --}}
            <form method="POST" action="{{ route('logout') }}" class="inline-block">
                @csrf
                <button type="submit" 
                        class="inline-flex w-full sm:w-auto items-center justify-center px-5 py-3 text-sm font-semibold text-white bg-emerald-700 rounded-lg hover:bg-emerald-800 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                    Logout & Login Ulang
                </button>
            </form>
        </div>
    </section>
</main>
@endsection