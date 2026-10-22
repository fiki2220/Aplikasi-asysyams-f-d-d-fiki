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

    <div class="text-center mb-6">
        {{-- Ikon Peringatan yang Lebih Elegan --}}
        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-orange-50 mb-5 shadow-sm border border-orange-100">
            <svg class="h-10 w-10 text-[#FF4D00]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <h2 class="text-2xl font-bold text-gray-900 mb-2">Pendaftaran Telah Ditutup</h2>
        <p class="text-gray-500 text-sm leading-relaxed max-w-sm mx-auto">
            Mohon maaf, periode pendaftaran Santri Baru (SPMB) untuk saat ini telah berakhir. Terima kasih atas antusiasme Anda.
        </p>
    </div>

    {{-- Info Batas Waktu (Jika ada) --}}
    @if(isset($deadline) && $deadline)
        <div class="mb-8 p-4 rounded-2xl bg-gray-50 border border-gray-200 text-center">
            <span class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Batas Waktu Pendaftaran</span>
            <span class="block text-sm font-semibold text-red-600">{{ $deadline->translatedFormat('d F Y, H:i') }} WIB</span>
        </div>
    @endif

    {{-- Tombol Aksi --}}
    <div class="space-y-3">
        <a href="{{ url('/') }}" 
           class="w-full flex justify-center items-center py-3.5 px-4 bg-[#FF4D00] hover:bg-[#e64500] text-white text-sm font-bold uppercase tracking-widest rounded-xl shadow-lg shadow-orange-200 active:scale-[0.98] transition-all duration-200">
            Kembali ke Beranda
        </a>
        
        <a href="{{ route('login') }}" 
           class="w-full flex justify-center items-center py-3.5 px-4 bg-white border-2 border-gray-200 hover:border-gray-300 text-gray-700 text-sm font-bold uppercase tracking-widest rounded-xl transition-all duration-200">
            Login Santri
        </a>
    </div>
</x-guest-layout>