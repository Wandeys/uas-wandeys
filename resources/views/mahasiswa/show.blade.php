<div class="row align-items-center mb-3">
    <div class="col-md-4 text-center">
        <img src="{{ $mahasiswa->user?->avatar ? asset('storage/' . $mahasiswa->user->avatar) : asset('niceadmin/img/noprofil.png') }}"
            alt="Avatar" class="img-fluid rounded-circle border border-3 border-primary" style="max-width: 120px; max-height: 120px; object-fit: cover;">
    </div>
    <div class="col-md-8">
        <h4 class="fw-bold mb-1 text-primary">{{ $mahasiswa->user?->name }}</h4>
        <span class="badge bg-info text-dark">Mahasiswa Aktif</span>
    </div>
</div>

<table class="table table-bordered table-striped m-0">
    <tbody>
        <tr>
            <th scope="row" style="width: 30%;">NIM</th>
            <td>{{ $mahasiswa->nim }}</td>
        </tr>
        <tr>
            <th scope="row">Angkatan</th>
            <td>{{ $mahasiswa->angkatan }}</td>
        </tr>
        <tr>
            <th scope="row">Email</th>
            <td>{{ $mahasiswa->user?->email }}</td>
        </tr>
        <tr>
            <th scope="row">Status</th>
            <td>
                @if($mahasiswa->status == 'active')
                    <span class="badge bg-success">Aktif</span>
                @elseif($mahasiswa->status == 'inactive')
                    <span class="badge bg-warning text-dark">Cuti</span>
                @elseif($mahasiswa->status == 'graduated')
                    <span class="badge bg-secondary">Lulus</span>
                @else
                    <span class="badge bg-secondary">{{ $mahasiswa->status }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <th scope="row">Akun Dibuat</th>
            <td>{{ $mahasiswa->created_at->format('d M Y - H:i') }} WIB</td>
        </tr>
    </tbody>
</table>
