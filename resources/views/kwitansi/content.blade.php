@php
    $methodLabel = match($payment->payment_type) {
        'bank_transfer'       => 'Transfer Bank',
        'qris'                => 'QRIS',
        'manual', 'cash'      => 'Tunai',
        'ewallet', 'e_wallet' => 'E-Wallet',
        'credit_card'         => 'Kartu Kredit',
        default => $payment->payment_type
            ? ucfirst(str_replace('_', ' ', $payment->payment_type))
            : '—',
    };

    $kelas = $student?->classGroups->first()?->name ?? '—';
@endphp

<div class="struk">

    {{-- ══════════════ HEADER ══════════════ --}}
    <div class="struk-center struk-bold struk-title">
        RUMAH QUR'AN ASY-SYAMS
    </div>
    <div class="struk-center struk-subtitle">
        TILAWAH CENTER PEKANBARU
    </div>
    <div class="struk-center struk-small">
        Jl. Tegal Sari Gg. Kemuning No. 9
    </div>
    <div class="struk-center struk-small">
        Telp: +62 853-7637-4040
    </div>

    <div class="struk-divider struk-double"></div>

    <div class="struk-center struk-bold struk-heading">
        KWITANSI PEMBAYARAN
    </div>

    <div class="struk-divider struk-double"></div>

    {{-- ══════════════ INFO TRANSAKSI ══════════════ --}}
    <div class="struk-row">
        <span class="struk-label">No.</span>
        <span class="struk-sep">:</span>
        <span class="struk-value struk-mono-small">{{ $payment->order_id }}</span>
    </div>
    <div class="struk-row">
        <span class="struk-label">Tanggal</span>
        <span class="struk-sep">:</span>
        <span class="struk-value">{{ $payment->created_at?->locale('id')->translatedFormat('d/m/Y H:i') }} WIB</span>
    </div>

    <div class="struk-divider"></div>

    {{-- ══════════════ DATA SANTRI ══════════════ --}}
    <div class="struk-row">
        <span class="struk-label">Nama</span>
        <span class="struk-sep">:</span>
        <span class="struk-value">{{ $student?->name ?? '—' }}</span>
    </div>
    <div class="struk-row">
        <span class="struk-label">NISN</span>
        <span class="struk-sep">:</span>
        <span class="struk-value">{{ $student?->nisn ?? '—' }}</span>
    </div>
    <div class="struk-row">
        <span class="struk-label">Kelas</span>
        <span class="struk-sep">:</span>
        <span class="struk-value">{{ $kelas }}</span>
    </div>

    <div class="struk-divider"></div>

    {{-- ══════════════ DETAIL PEMBAYARAN ══════════════ --}}
    <div class="struk-row">
        <span class="struk-label">Untuk</span>
        <span class="struk-sep">:</span>
        <span class="struk-value">SPP {{ $semester?->name ?? '—' }}</span>
    </div>
    <div class="struk-row">
        <span class="struk-label">Metode</span>
        <span class="struk-sep">:</span>
        <span class="struk-value">{{ $methodLabel }}</span>
    </div>

    <div class="struk-divider"></div>

    {{-- ══════════════ JUMLAH ══════════════ --}}
    <div class="struk-row struk-row-total">
        <span class="struk-label-total">JUMLAH</span>
        <span class="struk-value-total">
            Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}
        </span>
    </div>

    <div class="struk-terbilang">
        ({{ ucfirst($terbilang) }} rupiah)
    </div>

    <div class="struk-divider"></div>

    {{-- ══════════════ STATUS ══════════════ --}}
    <div class="struk-center struk-status">
        ✓ LUNAS ✓
    </div>

    <div class="struk-divider"></div>

    {{-- ══════════════ TERIMA KASIH ══════════════ --}}
    <div class="struk-center struk-thanks">
        Terima kasih atas<br>
        pembayaran Anda
    </div>

    {{-- ══════════════ TTD ══════════════ --}}
    <div class="struk-ttd">
        <div class="struk-ttd-date">
            Pekanbaru, {{ now()->locale('id')->translatedFormat('d/m/Y') }}
        </div>

        <div class="struk-ttd-label">
            Penerima,
        </div>

        <div class="struk-ttd-space"></div>

        <div class="struk-ttd-name">
            Admin Keuangan
        </div>
    </div>

    <div class="struk-divider struk-double"></div>

    {{-- ══════════════ FOOTER ══════════════ --}}
    <div class="struk-center struk-small">
        Dicetak: {{ $printedAt->locale('id')->translatedFormat('d/m/Y H:i') }} WIB
    </div>
    <div class="struk-center struk-small">
        Sistem Informasi YPTQ Asy-Syams
    </div>

    <div class="struk-divider struk-double"></div>

</div>

<style>
    .struk {
        width: 100%;
        max-width: 76mm;
        margin: 0 auto;
        font-family: 'DejaVu Sans Mono', 'Courier New', monospace;
        font-size: 9.5pt;
        color: #000;
        line-height: 1.35;
        background: #fff;
        padding: 2mm 1mm;
    }

    .struk-center { text-align: center; }
    .struk-bold { font-weight: bold; }
    .struk-small { font-size: 8pt; }

    .struk-title {
        font-size: 11pt;
        letter-spacing: 0.5px;
        margin-bottom: 1px;
    }

    .struk-subtitle {
        font-size: 9.5pt;
        font-weight: bold;
        margin-bottom: 3px;
    }

    .struk-heading {
        font-size: 10pt;
        letter-spacing: 1.5px;
        padding: 2px 0;
    }

    .struk-divider {
        border-top: 1px dashed #000;
        margin: 4px 0;
    }

    .struk-divider.struk-double {
        border-top: 2px solid #000;
    }

    .struk-row {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1px;
        font-size: 9pt;
    }

    .struk-row .struk-label {
        width: 52px;
        flex-shrink: 0;
    }

    .struk-row .struk-sep {
        width: 8px;
        flex-shrink: 0;
        text-align: center;
    }

    .struk-row .struk-value {
        flex: 1;
        word-break: break-word;
    }

    .struk-mono-small {
        font-size: 7.5pt;
        letter-spacing: -0.2px;
    }

    .struk-row-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 4px 0 2px;
    }

    .struk-label-total {
        font-size: 10pt;
        font-weight: bold;
    }

    .struk-value-total {
        font-size: 12pt;
        font-weight: bold;
        letter-spacing: 0.5px;
    }

    .struk-terbilang {
        font-size: 7.5pt;
        font-style: italic;
        text-align: right;
        margin-bottom: 2px;
        line-height: 1.3;
    }

    .struk-status {
        font-size: 13pt;
        font-weight: bold;
        letter-spacing: 2px;
        padding: 4px 0;
    }

    .struk-thanks {
        font-size: 9.5pt;
        padding: 3px 0;
        line-height: 1.3;
    }

    .struk-ttd {
        margin-top: 8px;
        padding: 0 4px;
    }

    .struk-ttd-date {
        text-align: right;
        font-size: 8.5pt;
        margin-bottom: 6px;
    }

    .struk-ttd-label {
        text-align: center;
        font-size: 8.5pt;
    }

    .struk-ttd-space {
        height: 38px;
    }

    .struk-ttd-name {
        text-align: center;
        font-size: 8.5pt;
        font-weight: bold;
        border-top: 1px solid #000;
        padding-top: 2px;
        width: 60%;
        margin: 0 auto;
    }
</style>