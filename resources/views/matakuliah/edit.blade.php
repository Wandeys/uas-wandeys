<x-app>

    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="card shadow-lg p-3">

        <form action="{{ route('matakuliah.update', $course) }}" method="post" class="form">
            @csrf
            @method('put')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="code" class="form-label required">Kode Mata Kuliah</label>
                    <input class="form-control @error('code') is-invalid @enderror" type="text" id="code" name="code" required value="{{ old('code', $course->code) }}">
                    @error('code')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label required">Nama Mata Kuliah</label>
                    <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name" required value="{{ old('name', $course->name) }}">
                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="credits" class="form-label required">SKS (Credits)</label>
                    <input class="form-control @error('credits') is-invalid @enderror" type="number" id="credits" name="credits" required min="1" max="10" value="{{ old('credits', $course->credits) }}">
                    @error('credits')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="semester" class="form-label required">Semester</label>
                    <input class="form-control @error('semester') is-invalid @enderror" type="number" id="semester" name="semester" required min="1" max="8" value="{{ old('semester', $course->semester) }}">
                    @error('semester')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('matakuliah.index') }}" class="btn btn-warning me-1">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>

        </form>

    </div>

</x-app>
