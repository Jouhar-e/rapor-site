@extends('pdf.rapot.master')

@section('content')

<div class="page">

    <style>
        .identity-title {

            text-align: center;
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 20mm;

        }

        .identity-table {

            width: 90%;
            margin: auto;
            border-collapse: collapse;

        }

        .identity-table td {

            padding: 7px 4px;
            vertical-align: top;
            font-size: 11pt;

        }

        .identity-table .label {

            width: 55mm;

        }

        .identity-table .colon {

            width: 5mm;
            text-align: center;

        }

        .identity-table .line td {

            padding: 0;
            height: 3mm;
            border-bottom: 1px dotted #000;

        }

        .identity-table .value {

            text-align: left;

        }
    </style>

    <div class="identity-title">

        IDENTITAS SEKOLAH

    </div>

    <table class="identity-table">

        <tr>

            <td class="label">Nama Satuan Pendidikan</td>
            <td class="colon">:</td>
            <td class="value"><strong>{{ strtoupper($school->name) }}</strong></td>

        </tr>

        <tr class="line">
            <td colspan="3"></td>
        </tr>

        <tr>

            <td class="label">NPSN</td>
            <td class="colon">:</td>
            <td>{{ $school->npsn }}</td>

        </tr>

        <tr class="line">
            <td colspan="3"></td>
        </tr>

        <tr>

            <td class="label">Alamat</td>
            <td class="colon">:</td>

            <td>

                {{ $school->address }}

                @if($school->district)

                <br>

                Kec. {{ $school->district }}

                @endif

                @if($school->city)

                ,

                Kab. {{ $school->city }}

                @endif

                @if($school->province)

                ,

                Prov. {{ $school->province }}

                @endif

            </td>

        </tr>

        <tr class="line">
            <td colspan="3"></td>
        </tr>

        <tr>

            <td class="label">Kode Pos</td>
            <td class="colon">:</td>
            <td>{{ $school->postal_code ?: '-' }}</td>

        </tr>

        <tr class="line">
            <td colspan="3"></td>
        </tr>

        <tr>

            <td class="label">Website</td>
            <td class="colon">:</td>
            <td>{{ $school->website ?: '-' }}</td>

        </tr>

        <tr class="line">
            <td colspan="3"></td>
        </tr>

        <tr>

            <td class="label">Email</td>
            <td class="colon">:</td>
            <td>{{ $school->email ?: '-' }}</td>

        </tr>

        <tr class="line">
            <td colspan="3"></td>
        </tr>

        <tr>

            <td class="label">Telepon</td>
            <td class="colon">:</td>
            <td>{{ $school->phone ?: '-' }}</td>

        </tr>

        <tr class="line">
            <td colspan="3"></td>
        </tr>

    </table>

</div>

@endsection