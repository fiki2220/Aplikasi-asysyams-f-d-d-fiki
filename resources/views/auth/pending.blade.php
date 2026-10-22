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

    <div class="text-center">
        {{-- Icon sukses --}}
        <div class="mx-auto w-20 h-20 rounded-full bg-green-50 border border-green-100 flex items-center justify-center mb-5 shadow-sm">
            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        {{-- Judul --}}
        <h2 class="text-2xl font-bold text-[#2D4746] mb-2">
            Pendaftaran Berhasil!
        </h2>

        {{-- Pesan --}}
        <p class="text-gray-500 text-sm mb-6 max-w-sm mx-auto leading-relaxed">
            {{ session('status', 'Akun Anda sedang menunggu verifikasi dari admin.') }}
        </p>

        {{-- Info tambahan --}}
        <div class="bg-gray-50 border border-gray-200 rounded-2xl p-5 mb-8 text-left shadow-sm">
            <p class="text-sm text-[#2D4746] font-bold mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-[#FF4D00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Langkah Selanjutnya:
            </p>
            <ul class="text-sm text-gray-600 space-y-2">
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Admin akan memverifikasi data Anda</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Estimasi 1×24 jam pada hari kerja</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Anda akan dihubungi via email / WhatsApp</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Setelah disetujui, Anda bisa login & masuk ke dashboard</span>
                </li>
            </ul>
        </div>

        {{-- Tombol logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex justify-center items-center gap-2 py-3.5 px-4 bg-white border-2 border-gray-200 hover:border-gray-300 text-gray-600 hover:text-gray-900 text-sm font-bold uppercase tracking-widest rounded-xl transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Keluar dari Akun
            </button>
        </form>

    </div>
</x-guest-layout>