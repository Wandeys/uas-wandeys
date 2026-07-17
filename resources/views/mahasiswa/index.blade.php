<x-app>

    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="card shadow-lg p-3">

        <div class="mb-3">
            <a class="btn btn-primary" href="{{ route('mahasiswa.create') }}" role="button">
                <i class="bi bi-plus-lg me-1"></i> Tambah Mahasiswa
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped w-100" id="data-table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nama</th>
                        <th scope="col">NIM</th>
                        <th scope="col">Angkatan</th>
                        <th scope="col">Status</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $student)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $student->user?->name }}</td>
                            <td>{{ $student->nim }}</td>
                            <td>{{ $student->angkatan }}</td>
                            <td>
                                @if($student->status == 'active')
                                    <span class="badge bg-success">Aktif</span>
                                @elseif($student->status == 'inactive')
                                    <span class="badge bg-warning">Cuti</span>
                                @elseif($student->status == 'graduated')
                                    <span class="badge bg-secondary">Lulus</span>
                                @else
                                    <span class="badge bg-info">{{ $student->status }}</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm btn-detail"
                                    data-route="{{ route('mahasiswa.show', $student) }}">
                                    <i class='bx bx-show'></i>
                                </button>
                                <a href="{{ route('mahasiswa.edit', $student) }}" class="btn btn-warning btn-sm">
                                    <i class='bx bx-edit-alt'></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-sm btn-delete" data-bs-toggle="modal"
                                    data-bs-target="#deleteModal" data-route="{{ route('mahasiswa.destroy', $student) }}">
                                    <i class='bx bx-trash'></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    @push('modals')
        <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Detail Mahasiswa</h1>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modal-detail">
                        ...
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endpush

    @push('scripts')
        <script>
            $('#data-table').on('click', '.btn-delete', function() {
                $('#form-delete').attr('action', $(this).data('route'))
            })

            $('#data-table').on('click', '.btn-detail', function() {
                Swal.fire({
                    title: 'Memuat...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $('#modal-detail').load($(this).data('route'), function(response, status, xhr) {
                    if (status == "success") {
                        setTimeout(() => {
                            Swal.close();
                            $('#detailModal').modal('show');
                        }, 500);
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: "Gagal memuat data",
                            icon: "error"
                        });
                    }
                });
            })
        </script>
    @endpush

</x-app>
