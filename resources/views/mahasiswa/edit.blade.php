<x-app>

    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="card shadow-lg p-3">

        <form action="{{ route('mahasiswa.update', $mahasiswa) }}" method="post" enctype="multipart/form-data" class="form">
            @csrf
            @method('put')

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label for="avatar" class="form-label">Avatar</label>
                    <input class="form-control @error('avatar') is-invalid @enderror" type="file" id="upload" name="avatar">
                    @error('avatar')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    <img src="{{ $mahasiswa->user?->avatar ? asset('storage/' . $mahasiswa->user->avatar) : asset('niceadmin/img/noprofil.png') }}" alt="Avatar" class="w-100 rounded mt-2" id="preview">
                </div>

                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label required">Nama Lengkap</label>
                            <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name" required value="{{ old('name', $mahasiswa->user?->name) }}">
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nim" class="form-label required">NIM</label>
                            <input class="form-control @error('nim') is-invalid @enderror" type="text" id="nim" name="nim" required value="{{ old('nim', $mahasiswa->nim) }}">
                            @error('nim')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="angkatan" class="form-label required">Angkatan</label>
                            <input class="form-control @error('angkatan') is-invalid @enderror" type="text" id="angkatan" name="angkatan" required value="{{ old('angkatan', $mahasiswa->angkatan) }}">
                            @error('angkatan')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label required">Status Akademik</label>
                            <select class="form-select select2-default @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" @selected(old('status', $mahasiswa->status) == 'active')>Aktif</option>
                                <option value="inactive" @selected(old('status', $mahasiswa->status) == 'inactive')>Cuti</option>
                                <option value="graduated" @selected(old('status', $mahasiswa->status) == 'graduated')>Lulus</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label required">Email</label>
                        <input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email" required value="{{ old('email', $mahasiswa->user?->email) }}">
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password <small class="text-muted">(Kosongkan jika tidak diubah)</small></label>
                            <input class="form-control @error('password') is-invalid @enderror" type="password" id="password" name="password" minlength="8">
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="passwordconfirm" class="form-label">Konfirmasi Password</label>
                            <input class="form-control @error('passwordconfirm') is-invalid @enderror" type="password" id="passwordconfirm" name="passwordconfirm" data-parsley-equalto="#password">
                            @error('passwordconfirm')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('mahasiswa.index') }}" class="btn btn-warning me-1">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>

        </form>

    </div>

</x-app>
