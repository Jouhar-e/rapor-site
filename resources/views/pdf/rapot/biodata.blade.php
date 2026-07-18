@extends('pdf.rapot.master')

@section('content')

<style>
    .student-title {
        text-align: center;
        font-size: 18pt;
        font-weight: bold;
        margin-bottom: 15mm;
    }

    .student-table {
        width: 100%;
        margin-left: 8mm;
        border-collapse: collapse;
    }

    .student-table td {
        border: none;
        font-size: 10pt;
        padding: 3px 0;
        vertical-align: top;
    }

    .student-table .no {
        width: 8mm;
    }

    .student-table .label {
        width: 62mm;
    }

    .student-table .colon {
        width: 5mm;
        text-align: center;
    }

    .student-table .indent {
        padding-left: 3mm;
    }

    .photo-sign {
        width: 100%;
        margin-top: 18mm;
    }

    .photo-sign td {
        border: none;
        vertical-align: top;
    }

    .photo-box {
        width: 30mm;
        height: 40mm;
        margin-left: 28mm;
        border: 1px solid #000;
    }

    .sign {
        padding-left: 18mm;
        font-size: 10pt;
    }

    .sign .name {
        margin-top: 28mm;
        font-weight: bold;
        text-decoration: underline;
    }

    .sign .nip {
        margin-top: 2mm;
    }
</style>

<div class="page">

    <div class="student-title">

        KETERANGAN DIRI TENTANG PESERTA DIDIK

    </div>

    <table class="student-table">

        <tr>
            <td class="no">1</td>
            <td class="label">Nama Peserta Didik</td>
            <td>:</td>
            <td><strong>{{ strtoupper($learner->name) }}</strong></td>
        </tr>

        <tr>
            <td>2</td>
            <td>NISN/NIS</td>
            <td>:</td>
            <td>{{ $learner->nis }} / {{ $learner->nisn }}</td>
        </tr>

        <tr>
            <td>3</td>
            <td>Tempat, Tanggal Lahir</td>
            <td>:</td>
            <td>
                {{ $learner->birth_place }},
                {{ optional($learner->birth_date)->translatedFormat('d F Y') }}
            </td>
        </tr>

        <tr>
            <td>4</td>
            <td>Jenis Kelamin</td>
            <td>:</td>
            <td>{{ $learner->gender=='L'?'Laki-laki':'Perempuan' }}</td>
        </tr>

        <tr>
            <td>5</td>
            <td>Agama</td>
            <td>:</td>
            <td>{{ $learner->religion ?: '-' }}</td>
        </tr>

        <tr>
            <td>6</td>
            <td>Anak ke</td>
            <td>:</td>
            <td>{{ $learner->child_order ?: '-' }}</td>
        </tr>

        <tr>
            <td>7</td>
            <td>Telepon</td>
            <td>:</td>
            <td>{{ $learner->phone ?: '-' }}</td>
        </tr>

        <tr>
            <td>8</td>
            <td>Alamat Peserta Didik</td>
            <td>:</td>
            <td>{{ $learner->address ?: '-' }}</td>
        </tr>

        <tr>
            <td>9</td>
            <td>Nomor Gawai</td>
            <td>:</td>
            <td>{{ $learner->phone ?: '-' }}</td>
        </tr>

        <tr>
            <td>10</td>
            <td>Diterima di sekolah ini</td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td></td>
            <td class="indent">di Kelas</td>
            <td>:</td>
            <td>{{ $learner->admission_grade ?: '-' }}</td>
        </tr>

        <tr>
            <td></td>
            <td class="indent">pada tanggal</td>
            <td>:</td>
            <td>{{ optional($learner->admission_date)->translatedFormat('d F Y') ?: '-' }}</td>
        </tr>

        <tr>
            <td></td>
            <td class="indent">sebagai</td>
            <td>:</td>
            <td>{{ $learner->admission_type ?: '-' }}</td>
        </tr>

        <tr>
            <td>11</td>
            <td>Nama Orang Tua</td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td></td>
            <td class="indent">a. Ayah</td>
            <td>:</td>
            <td>{{ $learner->father_name ?: '-' }}</td>
        </tr>

        <tr>
            <td></td>
            <td class="indent">b. Ibu</td>
            <td>:</td>
            <td>{{ $learner->mother_name ?: '-' }}</td>
        </tr>

        <tr>
            <td>12</td>
            <td>Pekerjaan Orang Tua</td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td></td>
            <td class="indent">a. Ayah</td>
            <td>:</td>
            <td>{{ $learner->father_job ?: '-' }}</td>
        </tr>

        <tr>
            <td></td>
            <td class="indent">b. Ibu</td>
            <td>:</td>
            <td>{{ $learner->mother_job ?: '-' }}</td>
        </tr>

        <tr>
            <td>13</td>
            <td>Nama Wali Peserta Didik</td>
            <td>:</td>
            <td>{{ $learner->guardian_name ?: '-' }}</td>
        </tr>

        <tr>
            <td>14</td>
            <td>Pekerjaan Wali Peserta Didik</td>
            <td>:</td>
            <td>{{ $learner->guardian_job ?: '-' }}</td>
        </tr>

    </table>

    <table class="photo-sign">

        <tr>

            <td width="30%">
                <div class="photo-box"></div>
            </td>

            <td class="sign">

                {{ $school->city }}, {{ \Carbon\Carbon::parse($printDate)->translatedFormat('d F Y') }}

                <br><br>

                Kepala Sekolah,

                <div class="name">

                    {{ strtoupper($school->headmaster_name) }}

                </div>

                <div class="nip">

                    NIP. {{ $school->headmaster_nip ?: '-' }}

                </div>

            </td>

        </tr>

    </table>

</div>

@endsection