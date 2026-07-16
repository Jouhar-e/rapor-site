<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>Rapor {{ $learner->name }}</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        html {
            margin: 0;
            padding: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #000;
        }

        .page {
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: auto;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        img {
            max-width: 100%;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .fw-bold {
            font-weight: bold;
        }
    </style>

</head>

<body>

    @yield('content')

</body>

</html>