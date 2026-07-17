<x-app>

    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="card shadow-lg p-3">

        <div class="table-responsive">
            <table class="table table-bordered table-striped w-100" id="data-table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nama Kelas</th>
                        <th scope="col">Mata Kuliah</th>
                        <th scope="col">Tahun Akademik</th>
                        <th scope="col">Mahasiswa Terdaftar</th>
                        <th scope="col">Status Nilai</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($classes as $class)
                        @php
                            $studentCount = $class->enrollments->count();
                            
                            // Check if grades are locked
                            $isLocked = $class->enrollments->contains(function ($enrollment) {
                                return $enrollment->grade?->is_locked;
                            });
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $class->name }}</strong></td>
                            <td>{{ $class->course?->name }} ({{ $class->course?->code }})</td>
                            <td>{{ $class->academicYear?->year }} - {{ $class->academicYear?->semester }}</td>
                            <td>{{ $studentCount }} Mahasiswa</td>
                            <td>
                                @if($isLocked)
                                    <span class="badge bg-success"><i class="bi bi-lock-fill me-1"></i> Final (Terkunci)</span>
                                @else
                                    <span class="badge bg-warning text-dark"><i class="bi bi-pencil-fill me-1"></i> Draft</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('dosen.kelas.input_nilai', $class->id) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-pencil-square me-1"></i> Input / Lihat Nilai
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

</x-app>
