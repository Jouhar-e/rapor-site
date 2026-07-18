@extends('pdf.rapot.master')

@section('content')

@php
$logo = null;

$paths = [
storage_path('app/private/'.$school->logo),
storage_path('app/public/'.$school->logo),
storage_path('app/school-profiles/'.$school->logo),
];

foreach ($paths as $path) {
if ($school->logo && file_exists($path)) {
$logo = $path;
break;
}
}
@endphp

<style>
    .cover-header {
        margin-bottom: 20px;
    }

    .cover-header table {
        width: 170px;
        margin-left: auto;
    }

    .cover-header td {
        border: 1px solid #000;
        padding: 5px;
        text-align: center;
        font-size: 9pt;
    }

    .cover-logo {
        text-align: center;
        margin-top: 30px;
        margin-bottom: 30px;
    }

    .cover-logo img {
        width: 110px;
    }

    .cover-title {
        text-align: center;
        margin-top: 15px;
        margin-bottom: 100px;
    }

    .cover-title h1 {
        font-size: 18pt;
        margin: 0;
    }

    .cover-title h2 {
        margin-top: 8px;
        font-size: 15pt;
    }

    .cover-label {
        text-align: center;
        margin-bottom: 5px;
        font-size: 10pt;
    }

    .cover-box {
        width: 82%;
        margin: 8px auto 0;
        border: 2px solid #000;
        padding: 10px;
        text-align: center;
        font-size: 17pt;
        font-weight: bold;
    }

    .cover-box-small {
        width: 42%;
        margin: 8px auto 0;
        border: 2px solid #000;
        padding: 8px;
        text-align: center;
        font-size: 11pt;
        font-weight: bold;

    }

    .cover-space {
        height: 55px;
    }

    .cover-footer {
        margin-top: 200px;
    }

    .cover-footer table {
        width: 100%;
    }

    .cover-footer td {
        padding: 3px 4px;
        text-align: center;
        font-size: 10pt;
    }

    .cover-line {
        border-top: 1.3px dotted #000;
    }

    .school-name {
        font-size: 22pt;
        font-weight: bold;
        letter-spacing: 0.5px;
        padding-bottom: 4px;
    }
</style>

<div class="page">

    {{-- HEADER --}}
    <div class="cover-header">

        <table>

            <tr>
                <td>No Rapor</td>
                <td>Nomor Pokok Sekolah</td>
            </tr>

            <tr>
                <td>{{ $reportNumber ?? $learner->report_number ?? '-' }}</td>
                <td>NPSN : {{ $school->npsn }}</td>
            </tr>

        </table>

    </div>

    {{-- LOGO --}}
    <div class="cover-logo">

        @if($logo)
        <img src="{{ $logo }}">
        @endif

    </div>

    {{-- JUDUL --}}
    <div class="cover-title">

        <h1>LAPORAN HASIL BELAJAR PESERTA DIDIK</h1>

        <h2>PROGRAM {{ strtoupper($programName) }}</h2>

    </div>

    {{-- NAMA --}}
    <div class="cover-label">

        NAMA PESERTA DIDIK

    </div>

    <div class="cover-box">

        {{ strtoupper($learner->name) }}

    </div>

    <div class="cover-space"></div>

    {{-- NISN --}}
    <div class="cover-label">

        NIPD / NISN

    </div>

    <div class="cover-box-small">

        {{ $learner->nis }} / {{ $learner->nisn }}

    </div>

    {{-- FOOTER --}}
    <div class="cover-footer">

        <table>

            <tr>
                <td class="school-name">
                    {{ strtoupper($school->name) }}
                </td>
            </tr>

            <tr>
                <td class="cover-line"></td>
            </tr>

            <tr>
                <td>
                    <strong>NPSN : {{ $school->npsn }}</strong>
                </td>
            </tr>

            <tr>
                <td class="cover-line"></td>
            </tr>

            <tr>
                <td>
                    {{ $school->address }}
                </td>
            </tr>

            <tr>
                <td class="cover-line"></td>
            </tr>

            <tr>
                <td>
                    Kec. {{ $school->district }}
                    Kab. {{ $school->city }}
                    Prov. {{ $school->province }}
                </td>
            </tr>

            <tr>
                <td class="cover-line"></td>
            </tr>

            <tr>
                <td>

                    Telepon :
                    {{ $school->phone }}

                    &nbsp;-&nbsp;

                    Kode Pos :
                    {{ $school->postal_code }}

                </td>
            </tr>

            <tr>
                <td class="cover-line"></td>
            </tr>

            <tr>
                <td>

                    Email :
                    {{ $school->email }}

                    &nbsp;|&nbsp;

                    Website :
                    {{ $school->website ?: '-' }}

                </td>
            </tr>

        </table>

    </div>

</div>

@endsection