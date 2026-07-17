<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KHS_{{ $student->nim }}_{{ str_replace('/', '_', $selectedYear->year) }}_{{ $selectedYear->semester }}</title>
    <!-- Core Bootstrap CSS -->
    <link href="{{ asset('niceadmin/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: white;
            color: black;
            font-size: 14px;
        }
        .header-title {
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 25px;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
        }
        .student-info table th, .student-info table td {
            padding: 3px 5px !important;
        }
        .table-khs th {
            background-color: #f2f2f2 !important;
            text-align: center;
            vertical-align: middle;
            border: 1px solid #000 !important;
        }
        .table-khs td {
            border: 1px solid #000 !important;
        }
        .signature-section {
            margin-top: 50px;
        }
        .signature-box {
            text-align: center;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
                margin: 0;
            }
            @page {
                size: A4;
                margin: 2cm;
            }
        }
    </style>
</head>
<body>

    <div class="container py-4">
        
        <!-- Action Buttons (Hidden on Print) -->
        <div class="no-print mb-4 d-flex justify-content-between">
            <button onclick="window.close()" class="btn btn-warning">
                <i class="bi bi-x-lg"></i> Tutup Halaman
            </button>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer-fill"></i> Cetak Sekarang
            </button>
        </div>

        <!-- Institutional Header -->
        <div class="header-title">
            <h3 class="fw-bold m-0">KEMENTERIAN PENDIDIKAN DAN KEBUDAYAAN</h3>
            <h4 class="fw-bold m-0">UNIVERSITAS TEKNOLOGI SIMANA</h4>
            <p class="m-0 small">Jl. Raya Kampus No. 1, Kota Akademik. Telp: (021) 12345678</p>
        </div>

        <h4 class="text-center fw-bold text-uppercase mb-4">KARTU HASIL STUDI (KHS)</h4>

        <!-- Student & Semester Metadata -->
        <div class="row student-info mb-4">
            <div class="col-6">
                <table class="table table-sm table-borderless m-0">
                    <tr>
                        <th style="width: 30%;">NAMA</th>
                        <td>: {{ $student->user?->name }}</td>
                    </tr>
                    <tr>
                        <th>NIM</th>
                        <td>: {{ $student->nim }}</td>
                    </tr>
                    <tr>
                        <th>ANGKATAN</th>
                        <td>: {{ $student->angkatan }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-6">
                <table class="table table-sm table-borderless m-0">
                    <tr>
                        <th style="width: 40%;">TAHUN AKADEMIK</th>
                        <td>: {{ $selectedYear->year }}</td>
                    </tr>
                    <tr>
                        <th>SEMESTER</th>
                        <td>: {{ $selectedYear->semester }}</td>
                    </tr>
                    <tr>
                        <th>PROGRAM STUDI</th>
                        <td>: Teknik Informatika</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Grades Table -->
        <table class="table table-bordered table-khs mb-4">
            <thead>
                <tr>
                    <th scope="col" style="width: 5%;">NO</th>
                    <th scope="col" style="width: 15%;">KODE MK</th>
                    <th scope="col">NAMA MATA KULIAH</th>
                    <th scope="col" style="width: 10%;">SKS (K)</th>
                    <th scope="col" style="width: 10%;">NILAI</th>
                    <th scope="col" style="width: 10%;">MUTU (M)</th>
                    <th scope="col" style="width: 12%;">K x M</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalK = 0;
                    $totalKM = 0;
                @endphp
                @forelse ($enrollments as $enrollment)
                    @php
                        $grade = $enrollment->grade;
                        $sks = $enrollment->class?->course?->credits ?? 0;
                        $quality = $grade ? (float)$grade->quality_point : 0.00;
                        $km = $sks * $quality;
                        $totalK += $sks;
                        $totalKM += $km;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center font-monospace">{{ $enrollment->class?->course?->code }}</td>
                        <td>{{ $enrollment->class?->course?->name }}</td>
                        <td class="text-center">{{ $sks }}</td>
                        <td class="text-center fw-bold">{{ $grade ? $grade->grade_letter : 'E' }}</td>
                        <td class="text-center">{{ number_format($quality, 2) }}</td>
                        <td class="text-center">{{ number_format($km, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Belum ada nilai yang dipublikasikan untuk semester ini.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="fw-bold">
                    <td colspan="3" class="text-end">JUMLAH</td>
                    <td class="text-center">{{ $totalK }}</td>
                    <td></td>
                    <td></td>
                    <td class="text-center">{{ number_format($totalKM, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Summary Statistics -->
        <div class="row mb-5">
            <div class="col-6">
                <div class="p-3 border rounded bg-light">
                    <table class="table table-sm table-borderless m-0">
                        <tr>
                            <th style="width: 70%;">Indeks Prestasi Semester (IPS)</th>
                            <td>: <strong>{{ number_format($ips, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <th>Indeks Prestasi Kumulatif (IPK)</th>
                            <td>: <strong>{{ number_format($ipk, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <th>Total Kredit Diselesaikan</th>
                            <td>: <strong>{{ $totalSksLulus }} SKS</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="row signature-section">
            <div class="col-4 signature-box">
                <p class="mb-5">Mengetahui,<br>Dosen Wali</p>
                <p class="fw-bold text-decoration-underline m-0">................................................</p>
                <p class="text-muted small m-0">NIP. ....................................</p>
            </div>
            <div class="col-4"></div>
            <div class="col-4 signature-box">
                <p class="mb-5">Kota Akademik, {{ now()->translatedFormat('d F Y') }}<br>Ketua Program Studi</p>
                <p class="fw-bold text-decoration-underline m-0">Dr. Ir. H. Suparman, M.T.</p>
                <p class="text-muted small m-0">NIP. 197005121995031002</p>
            </div>
        </div>

    </div>

    <!-- Automatically trigger printing on load -->
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            // Delay slightly to ensure page assets are rendered before opening print dialog
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
