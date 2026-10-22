<style>
    .student-control-sheet {
        background: #ffffff;
        border: 1px solid #111111;
        margin: 0 auto;
        max-width: 980px;
        padding: 12px 14px 10px;
    }

    .sheet-header {
        align-items: center;
        display: flex;
        gap: 12px;
        justify-content: center;
        min-height: 70px;
        text-align: left;
    }

    .logo-cell {
        flex: 0 0 70px;
        text-align: center;
    }

    .logo-cell img {
        display: inline-block;
        height: 62px;
        object-fit: contain;
        width: 62px;
    }

    .logo-fallback {
        align-items: center;
        border: 2px solid #111111;
        border-radius: 50%;
        display: inline-flex;
        font-family: Arial, sans-serif;
        font-size: 10px;
        font-weight: 800;
        height: 60px;
        justify-content: center;
        line-height: 1.1;
        text-align: center;
        width: 60px;
    }

    .brand-cell { flex: 0 1 auto; }

    .brand-title {
        font-family: Arial, sans-serif;
        font-size: 18px;
        font-weight: 800;
        line-height: 1.05;
        letter-spacing: 0;
        text-transform: uppercase;
    }

    .brand-subtitle {
        font-family: Arial, sans-serif;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0;
        margin-top: 2px;
        text-transform: uppercase;
    }

    .brand-contact {
        font-family: Arial, sans-serif;
        font-size: 9px;
        line-height: 1.3;
        margin-top: 3px;
    }

    .top-rule {
        border-top: 2px solid #111111;
        margin: 6px 0 8px;
    }

    .identity-row {
        display: flex;
        gap: 24px;
        margin-bottom: 6px;
    }

    .identity-field {
        align-items: baseline;
        display: grid;
        flex: 1;
        grid-template-columns: 46px 10px 1fr;
    }

    .identity-label,
    .identity-colon,
    .identity-value { font-size: 11px; }

    .identity-value {
        border-bottom: 1px solid #111111;
        min-height: 16px;
        padding: 0 4px 1px;
    }

    /* ⭐ CONTROL TABLE */
    .control-table {
        border-collapse: collapse;
        table-layout: fixed;
        width: 100%;
    }

    .control-table .col-no { width: 32px; }
    .control-table .col-date { width: 66px; }
    .control-table .col-reading { width: 22%; }
    .control-table .col-mark { width: 24px; }
    .control-table .col-sign { width: 52px; }

    .control-table th,
    .control-table td {
        border: 1px solid #111111;
        font-size: 10px;
        line-height: 1.15;
        padding: 2px 4px;
        vertical-align: top;
        word-break: break-word;
    }

    .control-table th {
        background: #f3f4f6;
        font-family: Arial, sans-serif;
        font-size: 9.5px;
        font-weight: 700;
        text-align: center;
        vertical-align: middle;
    }

    .control-table tbody tr { height: 22px; }
    .blank-row td { height: 20px; }

    .text-center { text-align: center; }

    .mark-cell {
        font-family: Arial, sans-serif;
        font-weight: 700;
    }

    .empty-state {
        color: #4b5563;
        padding: 10px;
        text-align: center;
    }

    /* ⭐ TTD CELL — untuk nama pengajar vertikal (jika dipakai) */
    .ttd-cell {
        vertical-align: middle !important;
        text-align: center !important;
        padding: 4px 2px !important;
        background-color: #ffffff;
    }

    .ttd-vertical {
        display: inline-block;
        writing-mode: vertical-rl;
        transform: rotate(180deg);
        font-family: Arial, sans-serif;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 1.5px;
        line-height: 1.1;
        color: #111111;
        text-transform: uppercase;
        white-space: nowrap;
        padding: 4px 0;
    }

    /* ⭐ TTD INITIALS — untuk inisial pengajar per baris */
    .ttd-initials {
        font-family: Arial, sans-serif;
        font-size: 11px;
        font-weight: 700;
        color: #111111;
        letter-spacing: 0.5px;
        vertical-align: middle !important;
        text-align: center !important;
        padding: 2px 4px !important;
    }

    /* ⭐ KETERANGAN TTD — legend di bawah tabel */
    .creator-legend {
        margin-top: 6px;
        font-size: 9.5px;
        line-height: 1.4;
        color: #111111;
        padding: 4px 8px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        page-break-inside: avoid;
        break-inside: avoid;
    }

    .creator-legend strong {
        font-weight: 700;
        margin-right: 4px;
    }

    .creator-legend ul {
        display: inline;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .creator-legend li {
        display: inline-block;
        margin-right: 16px;
    }

    .notes {
        font-size: 10px;
        margin-top: 8px;
        line-height: 1.3;
    }

    .notes ol {
        margin: 2px 0 0 16px;
        padding: 0;
    }

    .notes li { margin-bottom: 1px; }

    .summary-section { margin-top: 8px; }

    .summary-title {
        font-family: Arial, sans-serif;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0;
        margin-bottom: 3px;
        text-transform: uppercase;
    }

    .summary-table {
        border-collapse: collapse;
        table-layout: fixed;
        width: 100%;
    }

    .summary-table th,
    .summary-table td {
        border: 1px solid #111111;
        font-size: 10px;
        line-height: 1.2;
        padding: 3px 5px;
        vertical-align: top;
        word-break: break-word;
    }

    .summary-table th {
        background: #f3f4f6;
        font-family: Arial, sans-serif;
        font-size: 9.5px;
        font-weight: 700;
        text-align: center;
    }

    .evaluation-table th:nth-child(1) { width: 22%; }
    .evaluation-table th:nth-child(2) { width: 22%; }
    .evaluation-table th:nth-child(3) { width: 30%; }
    .evaluation-table th:nth-child(4) { width: 10%; }
    .evaluation-table th:nth-child(5) { width: 16%; }

    .note-table th:nth-child(1) { width: 100px; }

    /* ═══════════════════════════════════════════════════════════
       ⭐ PAGE BREAK RULES
       ═══════════════════════════════════════════════════════════ */

    /* 1. Tabel BOLEH pecah di tengah — page break alami */
    .control-table {
        page-break-inside: auto;
        break-inside: auto;
    }

    /* 2. Baris TIDAK BOLEH terpotong */
    .control-table tr,
    .summary-table tr {
        page-break-inside: avoid;
        break-inside: avoid;
    }

    /* 3. ⭐ HEADER TABEL DIULANG di setiap halaman baru */
    .control-table thead,
    .summary-table thead {
        display: table-header-group;
    }

    /* 4. Footer tabel diulang di akhir */
    .control-table tfoot,
    .summary-table tfoot {
        display: table-footer-group;
    }

    /* 5. Section summary TIDAK boleh terpotong */
    .summary-section {
        page-break-inside: avoid;
        break-inside: avoid;
    }

    /* 6. Keterangan tetap satu blok */
    .notes {
        page-break-inside: avoid;
        break-inside: avoid;
    }

    /* 7. Legenda TTD tidak terpotong */
    .creator-legend {
        page-break-inside: avoid;
        break-inside: avoid;
    }

    /* 8. Jarak antar section */
    .summary-section + .summary-section {
        margin-top: 6px;
    }
</style>