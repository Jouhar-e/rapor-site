@extends('pdf.rapot.master')

@section('content')

<style>
    /* ==========================================
   HEADER RAPOR
========================================== */

    .header {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 8mm;
    }

    .header td {
        border: none;
        vertical-align: top;
    }

    .header-left {
        width: 68%;
        padding-right: 10mm;
    }

    .header-right {
        width: 32%;
    }

    .header-table {
        width: 100%;
        border-collapse: collapse;
    }

    .header-table td {
        border: none;
        padding: 2px 0;
        font-size: 10pt;
        vertical-align: top;
    }

    .header-table .label {
        width: 40mm;
    }

    .header-table .colon {
        width: 4mm;
        text-align: center;
    }

    .header-table .value {
        width: auto;
    }

    .section-title {
        font-size: 11pt;
        font-weight: bold;
        margin: 5mm 0 3mm;
    }

    /* ==========================================
   TABEL NILAI
========================================== */

    .nilai {
        width: 100%;
        border-collapse: collapse;
        font-size: 9pt;
    }

    .nilai th,
    .nilai td {
        border: 1px solid #000;
        padding: 5px;
        vertical-align: top;
    }

    .nilai th {
        text-align: center;
        font-weight: bold;
    }

    .nilai .no {
        width: 8mm;
        text-align: center;
        vertical-align: middle;
        font-weight: bold;
    }

    .nilai .mapel {
        width: 70mm;
        vertical-align: middle;
        text-align: left;
    }

    .nilai .nilai-akhir {
        width: 20mm;
        text-align: center;
        vertical-align: middle;
        font-weight: bold;
        font-size: 11pt;
    }

    .nilai .deskripsi {
        width: auto;
    }

    .group-title td {
        font-weight: bold;
        background: #f5f5f5;
        text-align: left;
    }

    /* ==========================================
   EKSTRAKURIKULER
========================================== */

    .ekskul-title {
        margin-top: 8mm;
        margin-bottom: 3mm;
        font-size: 11pt;
        font-weight: bold;
    }

    .ekskul {
        width: 100%;
        border-collapse: collapse;
        font-size: 9pt;
    }

    .ekskul th,
    .ekskul td {
        border: 1px solid #000;
        padding: 5px;
        vertical-align: top;
    }

    .ekskul th {
        text-align: center;
        font-weight: bold;
    }

    .ekskul .no {
        width: 8mm;
        text-align: center;
        vertical-align: middle;
        font-weight: bold;
    }

    .ekskul .nama {
        width: 70mm;
        vertical-align: middle;
    }

    .ekskul .predikat {
        width: 22mm;
        text-align: center;
        vertical-align: middle;
        font-weight: bold;
        font-size: 11pt;
    }

    .ekskul .keterangan {
        width: auto;
    }

    /* ==========================================
   ABSENSI
========================================== */

    .absensi-title {
        margin-top: 8mm;
        margin-bottom: 3mm;
        font-size: 11pt;
        font-weight: bold;
    }

    .absensi {
        width: 85mm;
        border-collapse: collapse;
        font-size: 9pt;
    }

    .absensi th,
    .absensi td {
        border: 1px solid #000;
        padding: 5px;
    }

    .absensi th {
        text-align: center;
        font-weight: bold;
    }

    .absensi .label {
        width: 60mm;
    }

    .absensi .jumlah {
        width: 25mm;
        text-align: center;
    }

    /* ==========================================
   CATATAN WALI KELAS
========================================== */

    .catatan-title {
        margin-top: 8mm;
        margin-bottom: 3mm;
        font-size: 11pt;
        font-weight: bold;
    }

    .catatan-box {
        width: 93%;
        min-height: 27mm;
        border: 1px solid #000;
        padding: 5mm;
        font-size: 10pt;
        line-height: 1.5;
        text-align: justify;
    }

    /* ==========================================
   TANDA TANGAN
========================================== */


    .signature {
        width: 100%;
        margin-top: 12mm;
        border-collapse: collapse;
    }

    .signature td {
        border: none;
        vertical-align: top;
        font-size: 10pt;
    }

    .signature-space {
        height: 25mm;
    }

    .signature-name {
        display: inline-block;
        font-weight: bold;
        text-decoration: underline;
    }

    .signature-nip {
        display: block;
        margin-top: 2px;
        font-size: 9pt;
        text-align: left;
    }
</style>

<div class="page">

    <table class="header">

        <tr>

            <td class="header-left" width="70%">

                <table class="header-table">

                    <tr>
                        <td class="label">Nama Satuan Pendidikan</td>
                        <td class="colon">:</td>
                        <td class="value"><strong>{{ strtoupper($school->name) }}</strong></td>
                    </tr>

                    <tr>
                        <td class="label">Alamat</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $school->address }}, Kec. {{ $school->district }}, Kab. {{ $school->city }}</td>
                    </tr>

                    <tr>
                        <td class="label">Nama Peserta Didik</td>
                        <td class="colon">:</td>
                        <td class="value"><strong>{{ strtoupper($learner->name) }}</strong></td>
                    </tr>

                    <tr>
                        <td class="label">Nomor Induk / NISN</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $learner->nis }} / {{ $learner->nisn }}</td>
                    </tr>

                </table>

            </td>

            <td class="header-right">

                <table class="header-table">

                    <tr>
                        <td class="label">Kelas</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $class->name }}</td>
                    </tr>

                    <tr>
                        <td class="label">Semester</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $semester->name }}</td>
                    </tr>

                    <tr>
                        <td class="label">Fase</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $phaseName }}</td>
                    </tr>

                    <tr>
                        <td class="label">Tahun Pelajaran</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $academicYear->name }}</td>
                    </tr>

                </table>

            </td>

        </tr>

    </table>

    <div class="section-title">
        A. Nilai Akademik
    </div>

    {{-- ===========================
        TABEL NILAI
    =========================== --}}

    <table class="nilai">

        <thead>

            <tr>

                <th class="no">
                    No
                </th>

                <th class="mapel">
                    Mata Pelajaran/Muatan Pemberdayaan dan Keterampilan
                </th>

                <th class="nilai-akhir">
                    Nilai
                </th>

                <th class="deskripsi">
                    Capaian Kompetensi
                </th>

            </tr>

        </thead>

        <tbody>

            @php
            $no = 1;
            @endphp

            @forelse($groupedGrades as $groupName => $grades)

            @if($groupName !== '_ungrouped')

            <tr class="group-title">

                <td colspan="4">

                    {{ $groupName }}

                </td>

            </tr>

            @endif

            @foreach($grades as $grade)

            <tr>

                <td class="no">

                    {{ $no++ }}

                </td>

                <td class="mapel">

                    {{ $grade->subject?->name }}

                </td>

                <td class="nilai-akhir">

                    {{ $grade->final_score !== null ? number_format($grade->final_score,0) : '-' }}

                </td>

                <td>

                    {{ $grade->competency_description ?: '-' }}

                </td>

            </tr>

            @endforeach

            @empty

            <tr>

                <td colspan="4" style="text-align:center">

                    Belum ada data nilai

                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

    {{-- ==========================================
        B. EKSTRAKURIKULER
    ========================================== --}}
    <div class="ekskul-title" style="page-break-before: always;">

        B. Ekstrakurikuler

    </div>

    <table class="ekskul">

        <thead>

            <tr>

                <th class="no">
                    No
                </th>

                <th class="nama">
                    Kegiatan Ekstrakurikuler
                </th>

                <th class="predikat">
                    Predikat
                </th>

                <th class="keterangan">
                    Keterangan
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse($extracurriculars as $ekskul)

            <tr>

                <td class="no">

                    {{ $loop->iteration }}

                </td>

                <td class="nama">

                    {{ $ekskul->extracurricular?->name ?? '-' }}

                </td>

                <td class="predikat">

                    {{ $ekskul->predicate ?? '-' }}

                </td>

                <td>

                    {{ $ekskul->description ?? '-' }}

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="4" style="text-align:center">

                    Tidak ada data ekstrakurikuler

                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

    {{-- ==========================================
     C. KETIDAKHADIRAN
========================================== --}}

    <div class="absensi-title">

        C. Ketidakhadiran

    </div>

    <table class="absensi">

        <tr>

            <th class="label">
                Keterangan
            </th>

            <th class="jumlah">
                Hari
            </th>

        </tr>

        <tr>

            <td>Sakit</td>

            <td class="jumlah">

                {{ $attendance?->total_sick ?? 0 }}

            </td>

        </tr>

        <tr>

            <td>Izin</td>

            <td class="jumlah">

                {{ $attendance?->total_permission ?? 0 }}

            </td>

        </tr>

        <tr>

            <td>Tanpa Keterangan</td>

            <td class="jumlah">

                {{ $attendance?->total_absent ?? 0 }}

            </td>

        </tr>

    </table>

    {{-- ==========================================
        D. CATATAN WALI KELAS
    ========================================== --}}

    <div class="catatan-title">

        D. Catatan Wali Kelas

    </div>

    <div class="catatan-box">

        @if(!empty($homeroomNote?->note))

        {{ $homeroomNote->note }}

        @else

        &nbsp;

        @endif

    </div>

    {{-- ==========================================
     TANDA TANGAN
========================================== --}}

    <table class="signature">

        <tr>

            <td class="parent">
                <div class="signature-title">
                    Mengetahui,
                    <br>
                    Orang Tua / Wali
                </div>
            </td>

            <td class="headmaster">

                <div class="signature-title">



                    <br>

                    Kepala {{ $school->name }}

                </div>

            </td>

            <td class="homeroom">
                {{ $school->city }},
                {{ \Carbon\Carbon::parse($printDate)->translatedFormat('d F Y') }}
                <br>
                Wali Kelas

            </td>

        </tr>

        <tr>

            <td class="signature-space"></td>

            <td class="signature-space"></td>

            <td class="signature-space"></td>

        </tr>

        <tr>

            <td>

                <span class="signature-name">

                    (................................)

                </span>

            </td>

            <td class="headmaster">

                <div class="signature-name">
                    {{ strtoupper($school->headmaster_name) }}
                </div>

                <div class="signature-nip">
                    NIP : {{ $school->headmaster_nip ?: '-' }}
                </div>

            </td>

            <td class="homeroom">

                <div class="signature-name">
                    {{ strtoupper($homeroomTeacher?->user?->name ?? '-') }}
                </div>

                <div class="signature-nip" style="text-align: left;">
                    NIP : {{ $homeroomTeacherNip ?: '-' }}
                </div>

            </td>

        </tr>

    </table>

</div>

@endsection