# Task 2: Role-Based Access Control (RBAC)

## 1. Deskripsi & Tujuan
Membatasi akses pengguna ke halaman dan fitur tertentu berdasarkan peran (role) yang mereka miliki. Otorisasi ini diimplementasikan di tingkat router (middleware) dan tingkat UI (sidebar navigation & tombol aksi) agar Dosen tidak bisa mengakses menu Admin, Mahasiswa tidak bisa mengakses menu Dosen, dan seterusnya.

---

## 2. Persyaratan Fungsional & Kepatuhan Arsitektur
- **Middleware Proteksi**: Menggunakan middleware `EnsureHasRole` (aliased as `role`) yang sudah ada di proyek untuk memproteksi routes di `routes/web.php`.
- **Dinamisasi Menu Sidebar**: Menyesuaikan file template sidebar (`resources/views/layouts/sidebar.blade.php` atau sejenisnya) menggunakan direktif Blade `@if` atau `@can` untuk menampilkan/menyembunyikan menu secara kondisional.
- **Handling Unauthorized Access**: Mengarahkan pengguna ke halaman error `401 Unauthorized` atau `403 Forbidden` jika mencoba mengakses route yang tidak diizinkan.

---

## 3. Detail Implementasi Teknis (Untuk Tahap Berikutnya)

### A. Konfigurasi Routes (`routes/web.php`)
Mengelompokkan rute menggunakan grup middleware `role` seperti berikut:
```php
Route::middleware(['auth'])->group(function () {
    // Route umum (bisa diakses semua role yang login)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Khusus Superadmin & Admin
    Route::middleware(['role:Superadmin,Admin'])->group(function () {
        Route::resource('/user', UserController::class);
        Route::resource('/dosen', DosenController::class);
        Route::resource('/mahasiswa', MahasiswaController::class);
        Route::resource('/matakuliah', CourseController::class);
        Route::resource('/tahun-akademik', AcademicYearController::class);
        Route::resource('/kelas', ClassController::class);
    });

    // Khusus Dosen
    Route::middleware(['role:Dosen'])->group(function () {
        Route::get('/dosen/kelas', [DosenKelasController::class, 'index'])->name('dosen.kelas.index');
        Route::get('/dosen/kelas/{class}/input-nilai', [DosenKelasController::class, 'inputNilai'])->name('dosen.kelas.input_nilai');
        Route::post('/dosen/kelas/{class}/simpan-nilai', [DosenKelasController::class, 'simpanNilai'])->name('dosen.kelas.simpan_nilai');
        Route::post('/dosen/kelas/{class}/lock-nilai', [DosenKelasController::class, 'lockNilai'])->name('dosen.kelas.lock_nilai');
    });

    // Khusus Mahasiswa
    Route::middleware(['role:Mahasiswa'])->group(function () {
        Route::get('/khs', [KhsController::class, 'index'])->name('khs.index');
        Route::get('/khs/cetak', [KhsController::class, 'cetak'])->name('khs.cetak');
    });
});
```

### B. Dinamisasi Sidebar View (NiceAdmin Layout)
Mengatur menu agar terlihat hanya oleh role yang bersangkutan:
```html
@if(auth()->user()->role == 'Superadmin' || auth()->user()->role == 'Admin')
  <li class="nav-item">
    <a class="nav-link" href="{{ route('user.index') }}">
      <i class="bi bi-people"></i><span>Manajemen User</span>
    </a>
  </li>
@endif
```

---

## 4. Rencana Seeder & Data Dummy
Membuat pengujian otorisasi dengan menggunakan akun dummy dari **Task 1**:
1. Login sebagai `mahasiswa@gmail.com` dan coba akses `/user`. Sistem harus mengembalikan kode HTTP `401/403`.
2. Login sebagai `dosen@gmail.com` dan coba akses `/user`. Sistem harus mengembalikan kode HTTP `401/403`.
3. Login sebagai `admin@gmail.com` dan coba akses `/dosen/kelas`. Sistem harus mengembalikan kode HTTP `401/403`.
Semua skenario ini diverifikasi secara manual menggunakan akun dummy yang terbuat saat menjalankan seeder.
