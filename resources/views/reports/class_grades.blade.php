<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap_Nilai_{{ $class->course->code }}_{{ $class->name }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            text-transform: uppercase;
            font-size: 16px;
        }
        .header h3 {
            margin: 5px 0 0 0;
            text-transform: uppercase;
            font-size: 14px;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 10px;
            color: #666;
        }
        .title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .meta-label {
            font-weight: bold;
            width: 18%;
        }
        .meta-separator {
            width: 2%;
        }
        .meta-value {
            width: 30%;
        }
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .grades-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
            border: 1px solid #000;
            padding: 6px;
            font-size: 11px;
        }
        .grades-table td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 11px;
            vertical-align: middle;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .font-bold {
            font-weight: bold;
        }
        .signature-table {
            width: 100%;
            margin-top: 40px;
        }
        .signature-box {
            width: 40%;
            text-align: center;
        }
        .signature-space {
            width: 60%;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <h2>Kementerian Pendidikan dan Kebudayaan</h2>
        <h3>Universitas Teknologi Simana</h3>
        <p>Jl. Raya Kampus No. 1, Kota Akademik. Telp: (021) 12345678</p>
    </div>

    <div class="title">Rekapitulasi Nilai Akademik Mahasiswa</div>

    <!-- Metadata -->
    <table class="meta-table">
        <tr>
            <td class="meta-label">Mata Kuliah</td>
            <td class="meta-separator">:</td>
            <td class="meta-value">{{ $class->course?->name }} ({{ $class->course?->code }})</td>
            
            <td class="meta-label">Tahun Ajaran</td>
            <td class="meta-separator">:</td>
            <td class="meta-value">{{ $class->academicYear?->year }} - {{ $class->academicYear?->semester }}</td>
        </tr>
        <tr>
            <td class="meta-label">Kelas / SKS</td>
            <td class="meta-separator">:</td>
            <td class="meta-value">{{ $class->name }} / {{ $class->course?->credits }} SKS</td>

            <td class="meta-label">Dosen Pengampu</td>
            <td class="meta-separator">:</td>
            <td class="meta-value">{{ $class->teacher?->user?->name }}{{ $class->teacher?->gelar ? ', ' . $class->teacher->gelar : '' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Bobot Nilai</td>
            <td class="meta-separator">:</td>
            <td class="meta-value" colspan="4">
                Presensi: {{ (float) $class->weight_attendance }}% | 
                Tugas: {{ (float) $class->weight_task }}% | 
                UTS: {{ (float) $class->weight_uts }}% | 
                UAS: {{ (float) $class->weight_uas }}%
            </td>
        </tr>
    </table>

    <!-- Grades Table -->
    <table class="grades-table">
        <thead>
            <tr>
                <th style="width: 5%;">NO</th>
                <th style="width: 15%;">NIM</th>
                <th>NAMA MAHASISWA</th>
                <th style="width: 10%;">PRESENSI</th>
                <th style="width: 10%;">TUGAS</th>
                <th style="width: 10%;">UTS</th>
                <th style="width: 10%;">UAS</th>
                <th style="width: 12%;">NILAI AKHIR</th>
                <th style="width: 8%;">HURUF</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($class->enrollments as $enrollment)
                @php
                    $grade = $enrollment->grade;
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ $enrollment->student?->nim }}</td>
                    <td>{{ $enrollment->student?->user?->name }}</td>
                    <td class="text-center">{{ $grade ? number_format($grade->score_attendance, 2) : '0.00' }}</td>
                    <td class="text-center">{{ $grade ? number_format($grade->score_task, 2) : '0.00' }}</td>
                    <td class="text-center">{{ $grade ? number_format($grade->score_uts, 2) : '0.00' }}</td>
                    <td class="text-center">{{ $grade ? number_format($grade->score_uas, 2) : '0.00' }}</td>
                    <td class="text-center font-bold">{{ $grade ? number_format($grade->score_final, 2) : '0.00' }}</td>
                    <td class="text-center font-bold">{{ $grade ? $grade->grade_letter : 'E' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="color: #666; font-style: italic;">
                        Belum ada mahasiswa terdaftar di kelas ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signatures -->
    <table class="signature-table">
        <tr>
            <td class="signature-space"></td>
            <td class="signature-box">
                <p>Kota Akademik, {{ now()->translatedFormat('d F Y') }}</p>
                <p style="margin-bottom: 60px;">Dosen Pengampu,</p>
                <p class="font-bold" style="text-decoration: underline; margin-bottom: 2px;">
                    {{ $class->teacher?->user?->name }}{{ $class->teacher?->gelar ? ', ' . $class->teacher->gelar : '' }}
                </p>
                <p style="margin: 0; font-size: 10px; color: #666;">
                    NIP. {{ $class->teacher?->nip ?? '........................' }}
                </p>
            </td>
        </tr>
    </table>

</body>
</html>
