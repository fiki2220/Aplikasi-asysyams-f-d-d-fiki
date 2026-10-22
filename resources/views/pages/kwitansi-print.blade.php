<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Struk Pembayaran - {{ $payment->order_id }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * { box-sizing: border-box; }

        body {
            background: #e5e7eb;
            margin: 0;
            padding: 0;
            font-family: 'DejaVu Sans Mono', 'Courier New', monospace;
        }

        /* ══════════════ TOOLBAR ══════════════ */
        .print-toolbar {
            background: #1a3c34;
            color: #fff;
            padding: 14px 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,.15);
            position: sticky;
            top: 0;
            z-index: 100;
            gap: 12px;
            font-family: Arial, sans-serif;
        }

        .print-toolbar-title {
            font-size: 14px;
            font-weight: 600;
            letter-spacing: .5px;
        }

        .print-toolbar-actions {
            display: flex;
            gap: 10px;
        }

        .print-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            font-family: inherit;
        }

        .print-btn-primary {
            background: #f59e0b;
            color: #fff;
        }
        .print-btn-primary:hover { background: #d97706; }

        .print-btn-secondary {
            background: rgba(255,255,255,.15);
            color: #fff;
            border: 1px solid rgba(255,255,255,.3);
        }
        .print-btn-secondary:hover { background: rgba(255,255,255,.25); }

        /* ══════════════ PREVIEW STRUK ══════════════ */
        .struk-wrapper {
            max-width: 80mm;
            margin: 24px auto;
            padding: 0;
            background: #fff;
            box-shadow: 0 4px 20px rgba(0,0,0,.15);
            border-radius: 4px;
            overflow: hidden;
        }

        .struk-hint {
            max-width: 80mm;
            margin: 8px auto 24px;
            text-align: center;
            font-size: 11px;
            color: #666;
            font-family: Arial, sans-serif;
            line-height: 1.5;
        }

        /* ══════════════ PRINT MODE ══════════════ */
        @media print {
            body { background: #fff; }
            .print-toolbar { display: none !important; }
            .struk-hint { display: none !important; }
            .struk-wrapper {
                box-shadow: none;
                margin: 0;
                padding: 0;
                max-width: 100%;
                border-radius: 0;
            }
            @page {
                size: 80mm 220mm;
                margin: 2mm 3mm;
            }
        }

        @media (max-width: 640px) {
            .print-toolbar { flex-direction: column; align-items: flex-start; }
            .print-toolbar-title { font-size: 13px; }
            .struk-wrapper { margin: 12px auto; }
        }
    </style>
</head>
<body>

    {{-- TOOLBAR --}}
    <div class="print-toolbar">
        <div class="print-toolbar-title">
            🧾 Struk — {{ $payment->order_id }}
        </div>
        <div class="print-toolbar-actions">
            <a href="{{ route('payment.kwitansi.pdf', $payment) }}"
               class="print-btn print-btn-secondary"
               target="_blank">
                📄 Download PDF
            </a>
            <button type="button"
                    class="print-btn print-btn-primary"
                    onclick="window.print()">
                🖨️ Print Sekarang
            </button>
        </div>
    </div>

    {{-- STRUK --}}
    <div class="struk-wrapper">
        @include('kwitansi.content')
    </div>

    {{-- HINT --}}
    <div class="struk-hint">
        💡 Ukuran struk: 80mm<br>
        Cocok untuk printer thermal. Untuk printer biasa, pilih "Fit to page" atau "Scale to 80%".
    </div>

</body>
</html>