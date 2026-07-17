<x-app>

    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="card shadow-lg p-3">

        <div class="mb-3">
            <a class="btn btn-primary" href="{{ route('matakuliah.create') }}" role="button">
                <i class="bi bi-plus-lg me-1"></i> Tambah Mata Kuliah
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped w-100" id="data-table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Kode</th>
                        <th scope="col">Nama Mata Kuliah</th>
                        <th scope="col">SKS</th>
                        <th scope="col">Semester</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($courses as $course)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="badge bg-secondary font-monospace">{{ $course->code }}</span></td>
                            <td>{{ $course->name }}</td>
                            <td>{{ $course->credits }} SKS</td>
                            <td>Semester {{ $course->semester }}</td>
                            <td>
                                <a href="{{ route('matakuliah.edit', $course) }}" class="btn btn-warning btn-sm">
                                    <i class='bx bx-edit-alt'></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-sm btn-delete" data-bs-toggle="modal"
                                    data-bs-target="#deleteModal" data-route="{{ route('matakuliah.destroy', $course) }}">
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
