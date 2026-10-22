<x-guest-layout>
    <div class="flex flex-col items-center mb-10">
        <div class="p-2 bg-white rounded-2xl shadow-xl border border-gray-50 mb-6 transform hover:scale-105 transition-transform duration-300">
            <img src="{{ asset('images/logo.PNG') }}" alt="Logo Asy-Syams" class="h-24 w-auto rounded-xl">
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

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="space-y-1">
            <x-input-label for="email" :value="__('Email Address')" class="text-xs font-bold uppercase text-gray-600 ml-1" />
            <x-text-input id="email"
                class="block w-full bg-gray-50 border-gray-200 focus:bg-white focus:ring-[#FF4D00] focus:border-[#FF4D00] rounded-xl py-3 transition-all shadow-sm"
                type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div class="mt-5 space-y-1">
            <div class="flex justify-between items-center ml-1">
                <x-input-label for="password" :value="__('Password')" class="text-xs font-bold uppercase text-gray-600" />
                @if (Route::has('password.request'))
                <a class="text-xs font-bold text-[#FF4D00] hover:text-[#e64500] transition-colors" href="{{ route('password.request') }}">
                    Lupa?
                </a>
                @endif
            </div>
            <x-text-input id="password"
                class="block w-full bg-gray-50 border-gray-200 focus:bg-white focus:ring-[#FF4D00] focus:border-[#FF4D00] rounded-xl py-3 transition-all shadow-sm"
                type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox"
                    class="rounded-md border-gray-300 text-[#FF4D00] shadow-sm focus:ring-[#FF4D00] transition-colors"
                    name="remember">
                <span class="ms-2 text-sm text-gray-500 group-hover:text-gray-700">{{ __('Remember me') }}</span>
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