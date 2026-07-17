<x-app>

    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="row g-3 mb-3">
        <!-- Class Meta info -->
        <div class="col-md-6">
            <div class="card shadow-lg p-3 h-100 mb-0">
                <h5 class="fw-bold border-bottom pb-2 text-primary">Informasi Kelas</h5>
                <table class="table table-sm table-borderless m-0">
                    <tbody>
                        <tr>
                            <th style="width: 30%;">Mata Kuliah</th>
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

        <!-- Meeting Selector -->
        <div class="col-md-6">
            <div class="card shadow-lg p-3 h-100 mb-0 d-flex flex-column justify-content-center">
                <h5 class="fw-bold border-bottom pb-2 text-primary">Pilih Pertemuan</h5>
                <form action="{{ route('dosen.kelas.presensi', $class->id) }}" method="get" class="d-flex align-items-center gap-2 mt-2">
                    <label for="meeting_number" class="form-label text-nowrap fw-bold mb-0">Pertemuan Ke :</label>
                    <select name="meeting_number" id="meeting_number" class="form-select select2-default" onchange="this.form.submit()">
                        @for ($i = 1; $i <= 16; $i++)
                            <option value="{{ $i }}" @selected($meetingNumber == $i)>
                                Pertemuan {{ $i }}
                            </option>
                        @endfor
                    </select>
                </form>
            </div>
        </div>
    </div>

    @if ($isLocked)
        <div class="alert alert-success d-flex align-items-center" role="alert">
            <i class="bi bi-shield-lock-fill fs-4 me-2"></i>
            <div>
                <strong>Nilai Kelas Telah Final & Terkunci.</strong> Presensi tidak dapat diubah kembali.
            </div>
        </div>
    @endif

    <div class="card shadow-lg p-3">
        <h5 class="fw-bold border-bottom pb-2 mb-3 text-primary">
            Lembar Presensi - Pertemuan {{ $meetingNumber }}
        </h5>

        <form action="{{ route('dosen.kelas.simpan_presensi', $class->id) }}" method="post" id="form-attendance">
            @csrf
            <input type="hidden" name="meeting_number" value="{{ $meetingNumber }}">

            <div class="row mb-4">
                <div class="col-md-4">
                    <label for="date" class="form-label fw-bold">Tanggal Pertemuan</label>
                    <input type="date" name="date" id="date" class="form-control" value="{{ $meetingDate }}" @disabled($isLocked) required>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle w-100">
                    <thead>
                        <tr class="text-center">
                            <th scope="col" style="width: 5%;">#</th>
                            <th scope="col" style="width: 30%;">Nama Mahasiswa</th>
                            <th scope="col" style="width: 15%;">NIM</th>
                            <th scope="col">Status Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($enrollments as $enrollment)
                            @php
                                $statusVal = $attendances->get($enrollment->id)?->status ?? 'H';
                            @endphp
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td><strong>{{ $enrollment->student?->user?->name }}</strong></td>
                                <td class="text-center font-monospace">{{ $enrollment->student?->nim }}</td>
                                <td>
                                    <div class="d-flex gap-3 justify-content-center">
                                        <div class="form-check form-check-inline m-0">
                                            <input class="form-check-input" type="radio" 
                                                name="attendances[{{ $enrollment->id }}]" 
                                                id="h_{{ $enrollment->id }}" 
                                                value="H" 
                                                @checked($statusVal == 'H') 
                                                @disabled($isLocked)>
                                            <label class="form-check-label text-success fw-bold" for="h_{{ $enrollment->id }}">H (Hadir)</label>
                                        </div>
                                        <div class="form-check form-check-inline m-0">
                                            <input class="form-check-input" type="radio" 
                                                name="attendances[{{ $enrollment->id }}]" 
                                                id="s_{{ $enrollment->id }}" 
                                                value="S" 
                                                @checked($statusVal == 'S') 
                                                @disabled($isLocked)>
                                            <label class="form-check-label text-primary fw-bold" for="s_{{ $enrollment->id }}">S (Sakit)</label>
                                        </div>
                                        <div class="form-check form-check-inline m-0">
                                            <input class="form-check-input" type="radio" 
                                                name="attendances[{{ $enrollment->id }}]" 
                                                id="i_{{ $enrollment->id }}" 
                                                value="I" 
                                                @checked($statusVal == 'I') 
                                                @disabled($isLocked)>
                                            <label class="form-check-label text-warning fw-bold" for="i_{{ $enrollment->id }}">I (Izin)</label>
                                        </div>
                                        <div class="form-check form-check-inline m-0">
                                            <input class="form-check-input" type="radio" 
                                                name="attendances[{{ $enrollment->id }}]" 
                                                id="a_{{ $enrollment->id }}" 
                                                value="A" 
                                                @checked($statusVal == 'A') 
                                                @disabled($isLocked)>
                                            <label class="form-check-label text-danger fw-bold" for="a_{{ $enrollment->id }}">A (Alpa)</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Belum ada mahasiswa terdaftar di kelas ini.</td>
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
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan Presensi
                    </button>
                @endif
            </div>

        </form>
    </div>

</x-app>
