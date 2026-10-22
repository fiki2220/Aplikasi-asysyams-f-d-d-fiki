<x-guest-layout>
    <div class="py-6 text-center">

        {{-- Icon sukses --}}
        <div class="mx-auto w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mb-6">
            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        {{-- Judul --}}
        <h2 class="text-2xl font-bold text-green-800 mb-2">
            Pendaftaran Berhasil!
        </h2>

        {{-- Pesan --}}
        <p class="text-gray-600 mb-6 max-w-md mx-auto">
            {{ session('status', 'Akun Anda sedang menunggu verifikasi dari admin.') }}
        </p>

        {{-- Info tambahan --}}
        <div class="bg-green-50 border border-green-100 rounded-xl p-4 mb-6 max-w-md mx-auto text-left">
            <p class="text-sm text-green-800 font-semibold mb-2">Langkah selanjutnya:</p>
            <ul class="text-sm text-green-700 space-y-1 list-disc pl-5">
                <li>Admin akan memverifikasi data Anda</li>
                <li>Estimasi 1×24 jam pada hari kerja</li>
                <li>Anda akan dihubungi via email / WhatsApp</li>
                <li>Setelah disetujui, Anda bisa login & masuk ke dashboard</li>
            </ul>
        </div>

        {{-- Tombol logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500
                           hover:text-green-700 transition-colors duration-200 ease-out">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Keluar dari akun
            </button>
        </form>

    </div>
</x-guest-layout>