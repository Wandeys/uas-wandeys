# Task 1: Autentikasi dan Manajemen Pengguna (User Management)

## 1. Deskripsi & Tujuan
Menyelaraskan sistem autentikasi dan manajemen pengguna Laravel yang sudah ada dengan peran pengguna (user role) baru sesuai dengan PRD.md. Tugas ini melibatkan penyesuaian migrasi database, model User, controller, view, dan seeder data dummy tanpa merusak modul `User` yang sudah ada, melainkan memperluasnya agar mengenali role `Dosen` dan `Mahasiswa`.

---

## 2. Persyaratan Fungsional & Kepatuhan Arsitektur
- **Penyesuaian Skema**: Mengubah struktur kolom `role` pada tabel `users` untuk mendukung pilihan: `Superadmin`, `Admin`, `Dosen`, dan `Mahasiswa`.
- **CRUD Pengguna**: Memperbarui form Tambah/Edit User agar Admin/Superadmin dapat memilih role baru tersebut.
- **Validasi Data**: Memperbarui validasi input pada `UserController` (`store` dan `update`) untuk memastikan input role valid.
- **Konsistensi Gaya Koding**:
  - Tetap menggunakan transaksi database (`DB::beginTransaction()`, `DB::commit()`, `DB::rollBack()`).
  - Menggunakan attribute-based fillable/hidden pada model `User`.
  - Pertahankan struktur handling upload file avatar ke storage public `img/`.

---

## 3. Detail Implementasi Teknis (Untuk Tahap Berikutnya)

### A. Migrasi Database
Membuat file migrasi baru untuk memodifikasi kolom `role` pada tabel `users`. Karena SQLite tidak mendukung perubahan kolom enum secara langsung dengan mudah, alternatif terbaik adalah mengubah kolom `role` menjadi `string` dengan nilai default `Mahasiswa`.
- **Nama File**: `database/migrations/xxxx_xx_xx_xxxxxx_modify_role_column_in_users_table.php`
- **Isi Perubahan**:
  ```php
  Schema::table('users', function (Blueprint $table) {
      $table->string('role')->default('Mahasiswa')->change();
  });
  ```

### B. Pembaruan Controller & Model
- **Model `User` (`app/Models/User.php`)**: Memastikan attribute `role` tetap fillable.
- **Controller `UserController` (`app/Http/Controllers/UserController.php`)**:
  - Validasi role: `'role' => 'required|in:Superadmin,Admin,Dosen,Mahasiswa'`
  - Menghindari hardcode pilihan role di view dengan mempassing array role dari controller atau mendefinisikannya di model.

### C. Pembaruan View (`resources/views/user/`)
- **`create.blade.php` & `edit.blade.php`**: Perbarui dropdown role:
  ```html
  <select name="role" class="form-select">
      <option value="Superadmin" {{ old('role', $user->role ?? '') == 'Superadmin' ? 'selected' : '' }}>Superadmin</option>
      <option value="Admin" {{ old('role', $user->role ?? '') == 'Admin' ? 'selected' : '' }}>Admin</option>
      <option value="Dosen" {{ old('role', $user->role ?? '') == 'Dosen' ? 'selected' : '' }}>Dosen</option>
      <option value="Mahasiswa" {{ old('role', $user->role ?? '') == 'Mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
  </select>
  ```
- **`index.blade.php`**: Tampilkan badge warna-warni berdasarkan role pada tabel pengguna (misal: Superadmin = Danger, Admin = Success, Dosen = Primary, Mahasiswa = Info).

---

## 4. Rencana Seeder & Data Dummy
Memperbarui `database/seeders/UserSeeder.php` untuk menghasilkan setidaknya 1 pengguna untuk masing-masing role agar mempermudah pengujian visualisasi dashboard dan fungsionalitas.
- **Superadmin**: `tamus@gmail.com`
- **Admin**: `admin@gmail.com`
- **Dosen**: `dosen@gmail.com` (Sebagai representasi Dosen Default)
- **Mahasiswa**: `mahasiswa@gmail.com` (Sebagai representasi Mahasiswa Default)

Semua password default: `password` (atau disesuaikan dengan pattern factory `User::factory()`).
