<x-guest-layout>
    <div class="flex flex-col items-center mb-8">
        <div class="p-2 bg-white rounded-2xl shadow-xl border border-gray-50 mb-4 transform hover:scale-105 transition-transform duration-300">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Asy-Syams" class="h-20 w-auto rounded-xl">
        </div>

        <div class="flex flex-col items-center">
            <h1 class="text-2xl font-extrabold text-[#2D4746] tracking-tight leading-tight">YPTQ Asy-Syams</h1>

            <div class="flex gap-1 mt-2">
                <div class="h-1.5 w-10 bg-[#FF4D00] rounded-full"></div>
                <div class="h-1.5 w-2 bg-[#FF4D00] rounded-full opacity-60"></div>
            </div>
        </div>
    </div>

    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-800">Verifikasi Email Anda</h2>
        <p class="text-sm text-gray-500 mt-2 leading-relaxed">
            Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan ke email Anda.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 p-4 rounded-xl border border-green-200 bg-green-50 flex items-start gap-3">
            <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-sm font-medium text-green-800">
                Tautan verifikasi baru telah dikirim ke alamat email yang Anda daftarkan.
            </p>
        </div>
    @endif

    <div class="mt-8 space-y-4">
        {{-- Form Kirim Ulang Email --}}
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit"
                class="w-full flex justify-center items-center py-3.5 px-4 bg-[#FF4D00] hover:bg-[#e64500] text-white text-sm font-bold uppercase tracking-widest rounded-xl shadow-lg shadow-orange-200 active:scale-[0.98] transition-all duration-200">
                {{ __('Kirim Ulang Email Verifikasi') }}
            </button>
        </form>

        {{-- Form Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-center text-sm font-semibold text-gray-500 hover:text-gray-800 transition-colors">
                {{ __('Keluar (Logout)') }}
            </button>
        </form>
    </div>
</x-guest-layout>