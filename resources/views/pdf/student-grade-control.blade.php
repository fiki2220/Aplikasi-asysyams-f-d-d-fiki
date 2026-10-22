<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Tabel Nilai {{ $student->name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            color: #111111;
            font-family: "Arial", "DejaVu Sans", "Helvetica", sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }

        .student-control-sheet {
            background: #ffffff;
            padding: 0;
            width: 100%;
        }

        .sheet-header {
            display: table;
            margin: 0 auto;
            width: auto;
            min-height: 62px;
            text-align: left;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .logo-cell {
            display: table-cell;
            padding-right: 14px;
            text-align: center;
            vertical-align: middle;
            width: 74px;
        }

        .logo-cell img {
            display: inline-block;
            height: 60px;
            object-fit: contain;
            width: 60px;
        }

        .logo-fallback {
            border: 2px solid #111111;
            border-radius: 30px;
            display: inline-block;
            font-family: "Arial", "DejaVu Sans", sans-serif;
            font-size: 9px;
            font-weight: 800;
            height: 42px;
            line-height: 1.15;
            padding-top: 18px;
            text-align: center;
            width: 60px;
        }

        .brand-cell {
            display: table-cell;
            text-align: left;
            vertical-align: middle;
        }

        .brand-title {
            font-family: "Arial", "DejaVu Sans", sans-serif;
            font-size: 20px;
            font-weight: 900;
            line-height: 1.05;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }

        .brand-subtitle {
            font-family: "Arial", "DejaVu Sans", sans-serif;
            font-size: 12px;
            font-weight: 800;
            margin-top: 2px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .brand-contact {
            font-family: "Arial", "DejaVu Sans", sans-serif;
            font-size: 8.6px;
            line-height: 1.25;
            margin-top: 3px;
        }

        .top-rule {
            border-top: 1.8px solid #111111;
            margin: 6px 0 8px;
        }

        .identity-row {
            display: table;
            margin-bottom: 7px;
            width: 100%;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .identity-field {
            display: table-cell;
            width: 50%;
        }

        .identity-label,
        .identity-colon,
        .identity-value {
            display: inline-block;
            font-family: "Arial", "DejaVu Sans", sans-serif;
            font-size: 10.5px;
            vertical-align: bottom;
        }

        .identity-label {
            width: 44px;
            font-weight: 600;
        }

        .identity-colon {
            width: 9px;
        }

        .identity-value {
            border-bottom: 1px solid #111111;
            min-height: 13px;
            padding: 0 3px 1px;
            width: 72%;
        }

        .control-table {
            border-collapse: collapse;
            table-layout: fixed;
            width: 100%;
        }

        .control-table thead {
            display: table-header-group;
        }

        .control-table th,
        .control-table td {
            border: 0.6px solid #1f2937;
            font-family: "Arial", "DejaVu Sans", sans-serif;
            font-size: 9.5px;
            line-height: 1.25;
            padding: 3px 4px;
            vertical-align: top;
            word-wrap: break-word;
            word-break: break-word;
            overflow: hidden;
        }

        .control-table th {
            background-color: #f3f4f6;
            font-family: "Arial", "DejaVu Sans", sans-serif;
            font-size: 9px;
            font-weight: 700;
            text-align: center;
            vertical-align: middle;
            padding: 4px 2px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .control-table tbody tr {
            height: 20px;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .blank-row td {
            height: 18px;
        }

        .text-center {
            text-align: center;
        }

        .mark-cell {
            font-family: "Arial", "DejaVu Sans", sans-serif;
            font-weight: bold;
            font-size: 10px;
            text-align: center;
        }

        .empty-state {
            color: #444444;
            padding: 10px;
            text-align: center;
            font-style: italic;
        }

        .notes {
            font-family: "Arial", "DejaVu Sans", sans-serif;
            font-size: 9px;
            margin-top: 6px;
            line-height: 1.35;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .notes ol {
            margin: 2px 0 0 15px;
            padding: 0;
        }

        .notes li {
            margin-bottom: 0;
        }

        .summary-section {
            margin-top: 8px;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .summary-title {
            font-family: "Arial", "DejaVu Sans", sans-serif;
            font-size: 9.5px;
            font-weight: 800;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .summary-table {
            border-collapse: collapse;
            table-layout: fixed;
            width: 100%;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .summary-table th,
        .summary-table td {
            border: 0.6px solid #1f2937;
            font-family: "Arial", "DejaVu Sans", sans-serif;
            font-size: 9px;
            line-height: 1.25;
            padding: 2px 5px;
            vertical-align: top;
            word-wrap: break-word;
            word-break: break-word;
            overflow: hidden;
        }

        .summary-table th {
            background-color: #f3f4f6;
            font-family: "Arial", "DejaVu Sans", sans-serif;
            font-size: 8.6px;
            font-weight: 700;
            text-align: center;
            padding: 3px 3px;
            letter-spacing: 0.3px;
        }

        .evaluation-table th:nth-child(1) {
            width: 6%;
        }

        .evaluation-table th:nth-child(2) {
            width: 22%;
        }

        .evaluation-table th:nth-child(3) {
            width: 16%;
        }

        .evaluation-table th:nth-child(4) {
            width: 26%;
        }

        .evaluation-table th:nth-child(5) {
            width: 10%;
        }

        .evaluation-table th:nth-child(6) {
            width: 20%;
        }

        .note-table th:nth-child(1) {
            width: 12%;
        }

        .attendance-table th,
        .attendance-table td {
            width: 16.6%;
            text-align: center;
        }
    </style>
</head>

<body>
    @include('grades.student-control-table', [
    'logoSrc' => extension_loaded('gd') ? public_path('images/logo.PNG') : null,
    ])
</body>

</html>