<x-app>

    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="card shadow-lg p-3">

        <div class="mb-3">
            <a class="btn btn-primary" href="{{ route('kelas.create') }}" role="button">
                <i class="bi bi-plus-lg me-1"></i> Tambah Kelas
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped w-100" id="data-table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nama Kelas</th>
                        <th scope="col">Mata Kuliah</th>
                        <th scope="col">Dosen Pengampu</th>
                        <th scope="col">Tahun Akademik</th>
                        <th scope="col">Bobot (Absen / Tugas / UTS / UAS)</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($classes as $class)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $class->name }}</td>
                            <td>{{ $class->course?->name }} ({{ $class->course?->code }})</td>
                            <td>{{ $class->teacher?->user?->name }}{{ $class->teacher?->gelar ? ', ' . $class->teacher->gelar : '' }}</td>
                            <td>{{ $class->academicYear?->year }} - {{ $class->academicYear?->semester }}</td>
                            <td>
                                <span class="badge bg-secondary">H: {{ (float) $class->weight_attendance }}%</span>
                                <span class="badge bg-secondary">T: {{ (float) $class->weight_task }}%</span>
                                <span class="badge bg-secondary">UTS: {{ (float) $class->weight_uts }}%</span>
                                <span class="badge bg-secondary">UAS: {{ (float) $class->weight_uas }}%</span>
                            </td>
                            <td>
                                <a href="{{ route('kelas.edit', $class) }}" class="btn btn-warning btn-sm">
                                    <i class='bx bx-edit-alt'></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-sm btn-delete" data-bs-toggle="modal"
                                    data-bs-target="#deleteModal" data-route="{{ route('kelas.destroy', $class) }}">
                                    <i class='bx bx-trash'></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    @push('scripts')
        <script>
            $('#data-table').on('click', '.btn-delete', function() {
                $('#form-delete').attr('action', $(this).data('route'))
            })
        </script>
    @endpush

</x-app>
