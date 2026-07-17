<x-app>

    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="card shadow-lg p-3">

        <form action="{{ route('dosen.store') }}" method="post" enctype="multipart/form-data" class="form">
            @csrf

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label for="avatar" class="form-label">Avatar</label>
                    <input class="form-control @error('avatar') is-invalid @enderror" type="file" id="upload" name="avatar">
                    @error('avatar')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    <img src="{{ asset('niceadmin/img/noprofil.png') }}" alt="Avatar" class="w-100 rounded mt-2" id="preview">
                </div>

                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label required">Nama Lengkap</label>
                            <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name" required value="{{ old('name') }}">
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="gelar" class="form-label">Gelar Akademik</label>
                            <input class="form-control @error('gelar') is-invalid @enderror" type="text" id="gelar" name="gelar" value="{{ old('gelar') }}" placeholder="e.g. M.T., Ph.D.">
                            @error('gelar')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nip" class="form-label required">NIP</label>
                            <input class="form-control @error('nip') is-invalid @enderror" type="text" id="nip" name="nip" required value="{{ old('nip') }}">
                            @error('nip')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nidn" class="form-label required">NIDN</label>
                            <input class="form-control @error('nidn') is-invalid @enderror" type="text" id="nidn" name="nidn" required value="{{ old('nidn') }}">
                            @error('nidn')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label required">Email</label>
                        <input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email" required value="{{ old('email') }}">
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label required">Password</label>
                            <input class="form-control @error('password') is-invalid @enderror" type="password" id="password" name="password" required minlength="8">
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="passwordconfirm" class="form-label required">Konfirmasi Password</label>
                            <input class="form-control @error('passwordconfirm') is-invalid @enderror" type="password" id="passwordconfirm" name="passwordconfirm" required data-parsley-equalto="#password">
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
                <a href="{{ route('dosen.index') }}" class="btn btn-warning me-1">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>

        </form>

    </div>

</x-app>
