# Task 3: Manajemen Data Master

## 1. Deskripsi & Tujuan
Membangun fitur manajemen (CRUD) data master akademik yang menjadi fondasi sistem. Data master ini dikelola sepenuhnya oleh Admin/Superadmin dan mencakup: Dosen, Mahasiswa, Mata Kuliah, Tahun Akademik, dan Kelas.

---

## 2. Persyaratan Fungsional & Kepatuhan Arsitektur
- **CRUD Entitas Terkait**:
  - Dosen (`teachers`): NIDN, NIP, Nama, Gelar, Relasi ke `users` (sebagai login account).
  - Mahasiswa (`students`): NIM, Nama, Angkatan, Status (Aktif/Cuti/Lulus), Relasi ke `users`.
  - Mata Kuliah (`courses`): Kode MK, Nama MK, SKS, Semester.
  - Tahun Akademik (`academic_years`): Tahun (contoh: 2025/2026), Semester (Ganjil/Genap), Status Aktif (Hanya boleh 1 semester aktif).
  - Kelas Perkuliahan (`classes`): Relasi ke `courses`, `teachers`, `academic_years`, Nama Kelas, Bobot Penilaian dinamis (Kehadiran, Tugas, UTS, UAS).
- **Kepatuhan Arsitektur**:
  - Validasi ketat pada input (unique NIM/NIDN, total bobot kelas harus 100%, dll).
  - Menggunakan Bootstrap NiceAdmin untuk layout tabel data master (menggunakan DataTables jika tersedia di template).
  - Penghapusan data master dosen/mahasiswa harus menghapus data user terkait menggunakan database transaction (`DB::beginTransaction`).

---

## 3. Detail Implementasi Teknis (Untuk Tahap Berikutnya)

### A. Migrasi Database
1. `create_teachers_table`:
   ```php
   Schema::create('teachers', function (Blueprint $table) {
       $table->id();
       $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
       $table->string('nip')->unique();
       $table->string('nidn')->unique();
       $table->string('gelar')->nullable();
       $table->timestamps();
   });
   ```
2. `create_students_table`:
   ```php
   Schema::create('students', function (Blueprint $table) {
       $table->id();
       $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
       $table->string('nim')->unique();
       $table->string('angkatan');
       $table->string('status')->default('active'); // active, inactive, graduated
       $table->timestamps();
   });
   ```
3. `create_courses_table` & `create_academic_years_table` & `create_classes_table`.

### B. Controller & Routing
Masing-masing entitas didefinisikan sebagai Resource Controller pada namespace `App\Http\Controllers`:
- `DosenController`, `StudentController`, `CourseController`, `AcademicYearController`, `ClassController`.

---

## 4. Rencana Seeder & Data Dummy
Membuat seeder untuk menyuplai data master siap pakai ke dalam database agar sistem langsung dapat diuji secara visual:
- **`TeacherSeeder`**: Menambahkan 3-5 data dosen dummy.
- **`StudentSeeder`**: Menambahkan minimal 15-20 data mahasiswa dummy yang terbagi ke dalam angkatan yang berbeda.
- **`CourseSeeder`**: Menambahkan 5-8 mata kuliah (e.g., Pemrograman Web, Pemrograman Mobile, Basis Data, Jaringan Komputer).
- **`AcademicYearSeeder`**: Menambahkan tahun akademik 2025/2026 Ganjil (Aktif) dan Genap (Nonaktif).
- **`ClassSeeder`**: Membuat 5 kelas perkuliahan aktif (misal: "Pemrograman Web - Kelas A", "Basis Data - Kelas B").
