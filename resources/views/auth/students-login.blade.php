<x-guest-layout>
    <div class="flex flex-col items-center mb-10">
        <div class="p-2 bg-white rounded-2xl shadow-xl border border-gray-50 mb-6 transform hover:scale-105 transition-transform duration-300">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Asy-Syams" class="h-24 w-auto rounded-xl">
        </div>

        <div class="flex flex-col items-center">
            <h1 class="text-2xl font-extrabold text-[#2D4746] tracking-tight leading-tight">YPTQ Asy-Syams</h1>

            <div class="flex gap-1 mt-2">
                <div class="h-1.5 w-10 bg-[#FF4D00] rounded-full"></div>
                <div class="h-1.5 w-2 bg-[#FF4D00] rounded-full opacity-60"></div>
            </div>
        </div>
    </div>

    <div class="mb-8">
        <a href="{{ url('/') }}"
            class="group inline-flex items-center gap-2 text-xs font-semibold text-gray-400 uppercase tracking-wider hover:text-[#2D4746] transition-all">
            <svg class="h-4 w-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Kembali ke Beranda
        </a>
    </div>

    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800">Selamat Datang</h2>
        <p class="text-sm text-gray-500">Silahkan masuk menggunakan akun ananda</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" x-data="{ showPassword: false }">
        @csrf

        {{-- INPUT EMAIL --}}
        <div class="space-y-1">
            <x-input-label for="email" :value="__('Email Address')" class="text-xs font-bold uppercase text-gray-600 ml-1" />
            <x-text-input id="email"
                class="block w-full bg-gray-50 border-gray-200 focus:bg-white focus:ring-[#FF4D00] focus:border-[#FF4D00] rounded-xl py-3 transition-all shadow-sm"
                type="email" name="email" :value="old('email')" required autofocus placeholder="Masukkan email terdaftar" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        {{-- INPUT PASSWORD --}}
        <div class="mt-5 space-y-1">
            <div class="flex justify-between items-center ml-1">
                <x-input-label for="password" :value="__('Password')" class="text-xs font-bold uppercase text-gray-600" />
                @if (Route::has('password.request'))
                <a class="text-xs font-bold text-[#FF4D00] hover:text-[#e64500] transition-colors" href="{{ route('password.request') }}">
                    Lupa?
                </a>
                @endif
            </div>
            
            <div class="relative">
                <input id="password"
                    :type="showPassword ? 'text' : 'password'"
                    name="password" required autocomplete="current-password"
                    placeholder="Masukkan password Anda"
                    class="block w-full bg-gray-50 border-gray-200 focus:bg-white focus:ring-[#FF4D00] focus:border-[#FF4D00] rounded-xl py-3 pl-4 pr-12 transition-all shadow-sm" />

                {{-- TOMBOL LIHAT PASSWORD (IKON MATA) --}}
                <button type="button" @click="showPassword = !showPassword" 
                        class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-[#FF4D00] transition-colors focus:outline-none">
                    {{-- Ikon Mata Tertutup (Sembunyikan) --}}
                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0l-3.29-3.29" />
                    </svg>
                    {{-- Ikon Mata Terbuka (Tampilkan) --}}
                    <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox"
                    class="rounded-md border-gray-300 text-[#FF4D00] shadow-sm focus:ring-[#FF4D00] transition-colors"
                    name="remember">
                <span class="ms-2 text-sm text-gray-500 group-hover:text-gray-700">{{ __('Ingat Saya') }}</span>
            </label>
        </div>

        <div class="mt-8">
            <button type="submit"
                class="w-full flex justify-center items-center py-3.5 px-4 bg-[#FF4D00] hover:bg-[#e64500] text-white text-sm font-bold uppercase tracking-widest rounded-xl shadow-lg shadow-orange-200 active:scale-[0.98] transition-all duration-200">
                {{ __('Masuk Sekarang') }}
            </button>
        </div>
    </form>
</x-guest-layout>