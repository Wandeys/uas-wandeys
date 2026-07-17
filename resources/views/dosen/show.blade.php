<div class="row align-items-center mb-3">
    <div class="col-md-4 text-center">
        <img src="{{ $dosen->user?->avatar ? asset('storage/' . $dosen->user->avatar) : asset('niceadmin/img/noprofil.png') }}"
            alt="Avatar" class="img-fluid rounded-circle border border-3 border-primary" style="max-width: 120px; max-height: 120px; object-fit: cover;">
    </div>
    <div class="col-md-8">
        <h4 class="fw-bold mb-1 text-primary">{{ $dosen->user?->name }}{{ $dosen->gelar ? ', ' . $dosen->gelar : '' }}</h4>
        <span class="badge bg-primary">Dosen Pengampu</span>
    </div>
</div>

<table class="table table-bordered table-striped m-0">
    <tbody>
        <tr>
            <th scope="row" style="width: 30%;">NIP</th>
            <td>{{ $dosen->nip }}</td>
        </tr>
        <tr>
            <th scope="row">NIDN</th>
            <td>{{ $dosen->nidn }}</td>
        </tr>
        <tr>
            <th scope="row">Email</th>
            <td>{{ $dosen->user?->email }}</td>
        </tr>
        <tr>
            <th scope="row">Gelar</th>
            <td>{{ $dosen->gelar ?? '-' }}</td>
        </tr>
        <tr>
            <th scope="row">Akun Dibuat</th>
            <td>{{ $dosen->created_at->format('d M Y - H:i') }} WIB</td>
        </tr>
    </tbody>
</table>
