<x-app>

    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="row g-3 mb-3">
        <!-- Class Meta info -->
        <div class="col-md-4">
            <div class="card shadow-lg p-3 h-100">
                <h5 class="fw-bold border-bottom pb-2 text-primary">Informasi Kelas</h5>
                <table class="table table-sm table-borderless m-0">
                    <tbody>
                        <tr>
                            <th>Mata Kuliah</th>
                            <td>: {{ $class->course?->name }} ({{ $class->course?->code }})</td>
                        </tr>
                        <tr>
                            <th>Kelas</th>
                            <td>: {{ $class->name }}</td>
                        </tr>
                        <tr>
                            <th>Semester</th>
                            <td>: Semester {{ $class->course?->semester }}</td>
                        </tr>
                        <tr>
                            <th>Tahun Ajaran</th>
                            <td>: {{ $class->academicYear?->year }} - {{ $class->academicYear?->semester }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Class Weights info -->
        <div class="col-md-8">
            <div class="card shadow-lg p-3 h-100">
                <h5 class="fw-bold border-bottom pb-2 text-primary">Bobot Penilaian</h5>
                <div class="row text-center mt-2">
                    <div class="col-3">
                        <div class="p-2 border rounded bg-light">
                            <div class="text-muted small">Presensi</div>
                            <h4 class="fw-bold text-dark m-0">{{ (float) $class->weight_attendance }}%</h4>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 border rounded bg-light">
                            <div class="text-muted small">Tugas</div>
                            <h4 class="fw-bold text-dark m-0">{{ (float) $class->weight_task }}%</h4>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 border rounded bg-light">
                            <div class="text-muted small">UTS</div>
                            <h4 class="fw-bold text-dark m-0">{{ (float) $class->weight_uts }}%</h4>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 border rounded bg-light">
                            <div class="text-muted small">UAS</div>
                            <h4 class="fw-bold text-dark m-0">{{ (float) $class->weight_uas }}%</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($isLocked)
        <div class="alert alert-success d-flex align-items-center" role="alert">
            <i class="bi bi-shield-lock-fill fs-4 me-2"></i>
            <div>
                <strong>Nilai Kelas Telah Final & Terkunci.</strong> Nilai tidak dapat diubah kembali dan sudah dipublikasikan ke KHS mahasiswa.
            </div>
        </div>
    @endif

    <div class="card shadow-lg p-3">

        <form action="{{ route('dosen.kelas.simpan_nilai', $class->id) }}" method="post" id="form-grades">
            @csrf

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle w-100">
                    <thead>
                        <tr class="text-center">
                            <th scope="col" style="width: 5%;">#</th>
                            <th scope="col" style="width: 25%;">Nama Mahasiswa</th>
                            <th scope="col" style="width: 15%;">NIM</th>
                            <th scope="col" style="width: 11%;">Presensi (%)</th>
                            <th scope="col" style="width: 11%;">Tugas (%)</th>
                            <th scope="col" style="width: 11%;">UTS (%)</th>
                            <th scope="col" style="width: 11%;">UAS (%)</th>
                            <th scope="col" style="width: 11%;">Nilai Akhir</th>
                            <th scope="col" style="width: 10%;">Huruf</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($enrollments as $enrollment)
                            @php
                                $grade = $enrollment->grade;
                            @endphp
                            <tr class="grade-row">
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td><strong>{{ $enrollment->student?->user?->name }}</strong></td>
                                <td class="text-center font-monospace">{{ $enrollment->student?->nim }}</td>
                                <td>
                                    <input type="number" step="0.01" min="0" max="100" 
                                        name="scores[{{ $enrollment->id }}][attendance]" 
                                        class="form-control text-center score-attendance weight-calc-input" 
                                        value="{{ old('scores.'.$enrollment->id.'.attendance', $grade ? (float)$grade->score_attendance : 0.00) }}" 
                                        @disabled($isLocked) required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" max="100" 
                                        name="scores[{{ $enrollment->id }}][task]" 
                                        class="form-control text-center score-task weight-calc-input" 
                                        value="{{ old('scores.'.$enrollment->id.'.task', $grade ? (float)$grade->score_task : 0.00) }}" 
                                        @disabled($isLocked) required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" max="100" 
                                        name="scores[{{ $enrollment->id }}][uts]" 
                                        class="form-control text-center score-uts weight-calc-input" 
                                        value="{{ old('scores.'.$enrollment->id.'.uts', $grade ? (float)$grade->score_uts : 0.00) }}" 
                                        @disabled($isLocked) required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" max="100" 
                                        name="scores[{{ $enrollment->id }}][uas]" 
                                        class="form-control text-center score-uas weight-calc-input" 
                                        value="{{ old('scores.'.$enrollment->id.'.uas', $grade ? (float)$grade->score_uas : 0.00) }}" 
                                        @disabled($isLocked) required>
                                </td>
                                <td class="text-center fw-bold text-primary">
                                    <span class="score-final">{{ $grade ? number_format($grade->score_final, 2) : '0.00' }}</span>
                                </td>
                                <td class="text-center fw-bold">
                                    <span class="badge bg-secondary fs-6 grade-letter">{{ $grade ? $grade->grade_letter : 'E' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">Belum ada mahasiswa terdaftar di kelas ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <a href="{{ route('dosen.kelas.index') }}" class="btn btn-warning">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>

                @if (!$isLocked && $enrollments->isNotEmpty())
                    <div>
                        <button type="submit" class="btn btn-secondary me-2">
                            <i class="bi bi-save me-1"></i> Simpan Draft
                        </button>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmLockModal">
                            <i class="bi bi-shield-lock-fill me-1"></i> Finalisasi & Kunci Nilai
                        </button>
                    </div>
                @endif
            </div>

        </form>

    </div>

    <!-- Lock Confirmation Modal -->
    @if (!$isLocked && $enrollments->isNotEmpty())
        @push('modals')
            <div class="modal fade" id="confirmLockModal" tabindex="-1" aria-labelledby="lockModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="lockModalLabel"><i class="bi bi-exclamation-triangle-fill me-1"></i> Finalisasi Nilai Kelas</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Apakah Anda yakin ingin memfinalisasi dan mengunci nilai kelas ini?</p>
                            <div class="alert alert-warning py-2 small">
                                <i class="bi bi-info-circle-fill me-1"></i> Nilai yang telah dikunci <strong>tidak dapat diubah kembali</strong> dengan cara apa pun dan akan langsung dipublikasikan ke halaman KHS mahasiswa.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <form action="{{ route('dosen.kelas.lock_nilai', $class->id) }}" method="post">
                                @csrf
                                <button type="submit" class="btn btn-danger">Ya, Finalisasi & Kunci</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endpush
    @endif

    @push('scripts')
        <script>
            function recalculateRowGrade(row) {
                let attendance = parseFloat(row.find('.score-attendance').val()) || 0;
                let task = parseFloat(row.find('.score-task').val()) || 0;
                let uts = parseFloat(row.find('.score-uts').val()) || 0;
                let uas = parseFloat(row.find('.score-uas').val()) || 0;

                let wAttendance = {{ (float) $class->weight_attendance }};
                let wTask = {{ (float) $class->weight_task }};
                let wUts = {{ (float) $class->weight_uts }};
                let wUas = {{ (float) $class->weight_uas }};

                let final = (attendance * wAttendance / 100) +
                            (task * wTask / 100) +
                            (uts * wUts / 100) +
                            (uas * wUas / 100);

                row.find('.score-final').text(final.toFixed(2));

                let grade = 'E';
                if (final >= 85) { grade = 'A'; }
                else if (final >= 80) { grade = 'A-'; }
                else if (final >= 75) { grade = 'B+'; }
                else if (final >= 70) { grade = 'B'; }
                else if (final >= 65) { grade = 'B-'; }
                else if (final >= 60) { grade = 'C+'; }
                else if (final >= 55) { grade = 'C'; }
                else if (final >= 40) { grade = 'D'; }

                row.find('.grade-letter').text(grade);
            }

            $('.weight-calc-input').on('input change', function() {
                recalculateRowGrade($(this).closest('.grade-row'));
            });
        </script>
    @endpush

</x-app>
