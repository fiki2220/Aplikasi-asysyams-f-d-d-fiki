@extends('root.app')

@section('title', 'Pembayaran - YPTQ Asy-Syams')

@section('content')
<div class="bg-gray-50 min-h-screen py-10">

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- HEADER --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pembayaran</h1>
                <p class="text-sm text-gray-500 mt-1">Detail tagihan dan pembayaran</p>
            </div>
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:border-gray-300 text-gray-700 text-sm font-semibold rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>

        @if(! $activeSemester)
            <div class="bg-white rounded-xl shadow p-12 text-center">
                <div class="inline-block p-4 rounded-full bg-gray-100 text-gray-400 mb-4">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Tidak Ada Semester Aktif</h3>
                <p class="text-gray-500 mt-1">Pembayaran akan muncul setelah semester aktif diatur.</p>
            </div>
        @else
            @php $isPaid = in_array($paymentStatus, ['success', 'paid']); @endphp

            {{-- KARTU TAGIHAN --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">

                <div class="px-6 py-5 bg-gradient-to-r {{ $isPaid ? 'from-emerald-500 to-emerald-700' : 'from-red-500 to-red-700' }}">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-white/80">Tagihan Semester</p>
                            <h2 class="text-xl font-bold text-white mt-1">{{ $activeSemester->name }}</h2>
                        </div>
                        <div class="p-2.5 bg-white/20 backdrop-blur rounded-xl">
                            @if($isPaid)
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @else
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            @endif
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-3xl font-extrabold text-white">Rp {{ number_format($billAmount, 0, ',', '.') }}</p>
                        <p class="text-sm text-white/80 mt-1">
                            {{ $isPaid ? 'Sudah dibayar' : 'Belum dibayar' }}
                        </p>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Nama Santri</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ $user->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">NISN</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ $user->nisn ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Semester</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ $activeSemester->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Status</p>
                            <div class="mt-1">
                                @if($isPaid)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                        LUNAS
                                    </span>
                                @elseif($paymentStatus === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                        <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
                                        MENUNGGU
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span>
                                        BELUM BAYAR
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($hasUnpaidBill && $canCheckoutPayment)
                        <div class="pt-2">
                            <button id="pay-button"
                                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                Bayar Sekarang
                            </button>
                        </div>
                    @elseif($isPaid)
                        <div class="pt-2">
                            <div class="w-full flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 font-semibold text-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Terima kasih, pembayaran Anda sudah lunas
                            </div>
                        </div>
                    @else
                        <div class="pt-2">
                            <div class="w-full flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-gray-100 text-gray-500 font-semibold text-sm">
                                Pembayaran tidak tersedia. Hubungi admin.
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- INFO --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-sm text-blue-800">
                    <p class="font-semibold">Metode Pembayaran</p>
                    <p class="mt-1">Pembayaran menggunakan Midtrans. Tersedia transfer bank, e-wallet (GoPay, OVO, DANA), QRIS, dan lainnya.</p>
                </div>
            </div>
        @endif

    </div>
</div>

{{-- SCRIPT MIDTRANS --}}
@if(isset($hasUnpaidBill) && $hasUnpaidBill && isset($canCheckoutPayment) && $canCheckoutPayment)
    <script src="{{ $midtransSnapUrl }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <script type="text/javascript">
        var payButton = document.getElementById('pay-button');
        if (payButton) {
            payButton.onclick = function () {
                payButton.innerHTML = 'Memproses...';
                payButton.disabled = true;

                fetch('{{ route("payment.checkout") }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                })
                    .then(async response => {
                        const data = await response.json().catch(() => ({}));
                        if (! response.ok) throw new Error(data.message || 'Checkout gagal.');
                        return data;
                    })
                    .then(data => {
                        if (!window.snap) throw new Error('Midtrans belum termuat.');
                        if (!data.snap_token) throw new Error('Token tidak tersedia.');

                        window.snap.pay(data.snap_token, {
                            onSuccess: function(result){
                                const params = new URLSearchParams({
                                    order_id: result.order_id || '',
                                    transaction_status: result.transaction_status || 'settlement',
                                    status_code: result.status_code || '',
                                    payment_type: result.payment_type || '',
                                });
                                window.location.href = "{{ route('payment.success') }}?" + params.toString();
                            },
                            onPending: function(){ alert("Menunggu pembayaran!"); location.reload(); },
                            onError: function(){ alert("Pembayaran gagal!"); location.reload(); },
                            onClose: function(){ alert('Anda menutup popup.'); location.reload(); }
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(error.message || 'Terjadi kesalahan.');
                        payButton.innerHTML = 'Bayar Sekarang';
                        payButton.disabled = false;
                    });
            };
        }
    </script>
@endif
@endsection