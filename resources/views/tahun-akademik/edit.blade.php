<x-app>

    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="card shadow-lg p-3">

        <form action="{{ route('tahun-akademik.update', $academicYear) }}" method="post" class="form">
            @csrf
            @method('put')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="year" class="form-label required">Tahun Ajaran</label>
                    <input class="form-control @error('year') is-invalid @enderror" type="text" id="year" name="year" required value="{{ old('year', $academicYear->year) }}">
                    @error('year')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="semester" class="form-label required">Semester</label>
                    <select class="form-select select2-default @error('semester') is-invalid @enderror" id="semester" name="semester" required>
                        <option value="Ganjil" @selected(old('semester', $academicYear->semester) == 'Ganjil')>Ganjil</option>
                        <option value="Genap" @selected(old('semester', $academicYear->semester) == 'Genap')>Genap</option>
                    </select>
                    @error('semester')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $academicYear->is_active) == '1' || $academicYear->is_active)>
                    <label class="form-check-label" for="is_active">Jadikan Semester Aktif</label>
                </div>
                <small class="text-muted">Menjadikan semester ini aktif otomatis akan menonaktifkan semester aktif lainnya.</small>
            </div>

            <div class="text-end">
                <a href="{{ route('tahun-akademik.index') }}" class="btn btn-warning me-1">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>

        </form>

    </div>

</x-app>
