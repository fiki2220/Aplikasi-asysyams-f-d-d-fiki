<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Struk Pembayaran - {{ $payment->order_id }}</title>
    <style>
        /* ⭐ Ukuran thermal 80mm × auto */
        @page {
            size: 80mm 220mm;
            margin: 2mm 3mm;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #fff;
        }
    </style>
</head>

<body>
    @include('kwitansi.content')
</body>

</html>