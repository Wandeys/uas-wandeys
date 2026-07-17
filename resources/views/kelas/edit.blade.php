<x-app>

    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="card shadow-lg p-3">

        @if ($errors->has('total_weight'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-octagon me-1"></i>
                {{ $errors->first('total_weight') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('kelas.update', $class) }}" method="post" class="form">
            @csrf
            @method('put')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label required">Nama Kelas</label>
                    <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name" required value="{{ old('name', $class->name) }}">
                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="course_id" class="form-label required">Mata Kuliah</label>
                    <select class="form-select select2-default @error('course_id') is-invalid @enderror" id="course_id" name="course_id" required>
                        <option value="">Pilih Mata Kuliah</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" @selected(old('course_id', $class->course_id) == $course->id)>
                                {{ $course->name }} ({{ $course->code }}) - {{ $course->credits }} SKS
                            </option>
                        @endforeach
                    </select>
                    @error('course_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="teacher_id" class="form-label required">Dosen Pengampu</label>
                    <select class="form-select select2-default @error('teacher_id') is-invalid @enderror" id="teacher_id" name="teacher_id" required>
                        <option value="">Pilih Dosen</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" @selected(old('teacher_id', $class->teacher_id) == $teacher->id)>
                                {{ $teacher->user?->name }}{{ $teacher->gelar ? ', ' . $teacher->gelar : '' }} (NIP: {{ $teacher->nip }})
                            </option>
                        @endforeach
                    </select>
                    @error('teacher_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="academic_year_id" class="form-label required">Tahun Akademik</label>
                    <select class="form-select select2-default @error('academic_year_id') is-invalid @enderror" id="academic_year_id" name="academic_year_id" required>
                        <option value="">Pilih Tahun Akademik</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" @selected(old('academic_year_id', $class->academic_year_id) == $year->id)>
                                {{ $year->year }} - {{ $year->semester }} @if($year->is_active)(Aktif)@endif
                            </option>
                        @endforeach
                    </select>
                    @error('academic_year_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <h5 class="fw-bold border-bottom pb-2 mt-4 text-primary">Bobot Penilaian (Harus Berjumlah 100%)</h5>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="weight_attendance" class="form-label required">Presensi / Kehadiran (%)</label>
                    <input class="form-control weight-input @error('weight_attendance') is-invalid @enderror" type="number" step="0.01" id="weight_attendance" name="weight_attendance" required min="0" max="100" value="{{ old('weight_attendance', (float) $class->weight_attendance) }}">
                    @error('weight_attendance')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="weight_task" class="form-label required">Tugas (%)</label>
                    <input class="form-control weight-input @error('weight_task') is-invalid @enderror" type="number" step="0.01" id="weight_task" name="weight_task" required min="0" max="100" value="{{ old('weight_task', (float) $class->weight_task) }}">
                    @error('weight_task')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="weight_uts" class="form-label required">UTS (%)</label>
                    <input class="form-control weight-input @error('weight_uts') is-invalid @enderror" type="number" step="0.01" id="weight_uts" name="weight_uts" required min="0" max="100" value="{{ old('weight_uts', (float) $class->weight_uts) }}">
                    @error('weight_uts')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="weight_uas" class="form-label required">UAS (%)</label>
                    <input class="form-control weight-input @error('weight_uas') is-invalid @enderror" type="number" step="0.01" id="weight_uas" name="weight_uas" required min="0" max="100" value="{{ old('weight_uas', (float) $class->weight_uas) }}">
                    @error('weight_uas')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="alert alert-info py-2 d-flex justify-content-between align-items-center">
                <span>Total akumulasi bobot:</span>
                <strong id="total-weight-display">100%</strong>
            </div>

            <div class="text-end mt-3">
                <a href="{{ route('kelas.index') }}" class="btn btn-warning me-1">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>

        </form>

    </div>

    @push('scripts')
        <script>
            function calculateTotal() {
                let total = 0;
                $('.weight-input').each(function() {
                    let val = parseFloat($(this).val()) || 0;
                    total += val;
                });
                $('#total-weight-display').text(total.toFixed(2) + '%');
                if (total === 100) {
                    $('#total-weight-display').parent().removeClass('alert-danger').addClass('alert-info');
                } else {
                    $('#total-weight-display').parent().removeClass('alert-info').addClass('alert-danger');
                }
            }

            $('.weight-input').on('input change', calculateTotal);
            calculateTotal();
        </script>
    @endpush

</x-app>
