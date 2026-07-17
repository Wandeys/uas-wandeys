<x-app>

    <x-slot:title>{{ $title }}</x-slot:title>

    <!-- Overview Cards -->
    <div class="row g-3 mb-4">
        <!-- IPS Card -->
        <div class="col-md-4">
            <div class="card shadow-lg border-start border-primary border-4 p-3 h-100 mb-0">
                <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary-light text-primary fs-3 p-3">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div class="ps-3">
                        <span class="text-muted small uppercase fw-bold">IPS (Semester Ini)</span>
                        <h3 class="fw-bold text-dark m-0">{{ number_format($ips, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- IPK Card -->
        <div class="col-md-4">
            <div class="card shadow-lg border-start border-success border-4 p-3 h-100 mb-0">
                <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success-light text-success fs-3 p-3">
                        <i class="bi bi-award-fill"></i>
                    </div>
                    <div class="ps-3">
                        <span class="text-muted small uppercase fw-bold">IPK (Kumulatif)</span>
                        <h3 class="fw-bold text-dark m-0">{{ number_format($ipk, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total SKS Card -->
        <div class="col-md-4">
            <div class="card shadow-lg border-start border-info border-4 p-3 h-100 mb-0">
                <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-info-light text-info fs-3 p-3">
                        <i class="bi bi-journal-bookmark-fill"></i>
                    </div>
                    <div class="ps-3">
                        <span class="text-muted small uppercase fw-bold">SKS Lulus</span>
                        <h3 class="fw-bold text-dark m-0">{{ $totalSksLulus }} SKS</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Print Card -->
    <div class="card shadow-lg p-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
            <!-- Filter Form -->
            <form action="{{ route('khs.index') }}" method="get" class="d-flex align-items-center gap-2">
                <label for="academic_year_id" class="form-label text-nowrap fw-bold mb-0">Tahun Akademik :</label>
                <select name="academic_year_id" id="academic_year_id" class="form-select select2-default" onchange="this.form.submit()">
                    @foreach ($academicYears as $year)
                        <option value="{{ $year->id }}" @selected($selectedYearId == $year->id)>
                            {{ $year->year }} - {{ $year->semester }} @if($year->is_active)(Aktif)@endif
                        </option>
                    @endforeach
                </select>
            </form>

            <!-- Print Button -->
            @if ($enrollments->isNotEmpty())
                <a href="{{ route('khs.cetak', ['academic_year_id' => $selectedYearId]) }}" 
                   target="_blank" 
                   class="btn btn-secondary">
                    <i class="bi bi-printer-fill me-1"></i> Cetak KHS
                </a>
            @endif
        </div>

        <!-- Grades Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle w-100">
                <thead>
                    <tr class="text-center bg-light">
                        <th scope="col" style="width: 5%;">#</th>
                        <th scope="col" style="width: 15%;">Kode MK</th>
                        <th scope="col">Nama Mata Kuliah</th>
                        <th scope="col" style="width: 10%;">SKS</th>
                        <th scope="col" style="width: 10%;">Presensi</th>
                        <th scope="col" style="width: 10%;">Tugas</th>
                        <th scope="col" style="width: 10%;">UTS</th>
                        <th scope="col" style="width: 10%;">UAS</th>
                        <th scope="col" style="width: 10%;">Akhir</th>
                        <th scope="col" style="width: 10%;">Huruf</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($enrollments as $enrollment)
                        @php
                            $grade = $enrollment->grade;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center font-monospace"><span class="badge bg-secondary">{{ $enrollment->class?->course?->code }}</span></td>
                            <td>{{ $enrollment->class?->course?->name }}</td>
                            <td class="text-center">{{ $enrollment->class?->course?->credits }} SKS</td>
                            <td class="text-center">{{ $grade ? (float)$grade->score_attendance : '-' }}</td>
                            <td class="text-center">{{ $grade ? (float)$grade->score_task : '-' }}</td>
                            <td class="text-center">{{ $grade ? (float)$grade->score_uts : '-' }}</td>
                            <td class="text-center">{{ $grade ? (float)$grade->score_uas : '-' }}</td>
                            <td class="text-center fw-bold text-primary">{{ $grade ? number_format($grade->score_final, 2) : '-' }}</td>
                            <td class="text-center fw-bold">
                                <span class="badge bg-success fs-6">{{ $grade ? $grade->grade_letter : '-' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">Belum ada nilai yang dipublikasikan untuk semester ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-app>
