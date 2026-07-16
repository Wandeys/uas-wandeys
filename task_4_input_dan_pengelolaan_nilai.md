# Task 4: Input dan Pengelolaan Nilai

## 1. Deskripsi & Tujuan
Membangun modul utama bagi dosen untuk mengelola nilai mahasiswa di kelas yang diampu. Proses ini mencakup pengaturan bobot penilaian (dinamis per kelas), input nilai mentah mahasiswa (kehadiran, tugas, UTS, UAS), penghitungan nilai akhir otomatis berbasis bobot, konversi nilai huruf (A-E), serta mekanisme penguncian nilai (finalize).

---

## 2. Persyaratan Fungsional & Kepatuhan Arsitektur
- **Pengaturan Bobot Dinamis**: Dosen dapat mengatur persentase kontribusi nilai (Kehadiran + Tugas + UTS + UAS = 100%) di setiap kelas.
- **Form Input Kolektif (Bulk Input)**: Halaman input nilai berupa tabel baris yang memuat seluruh mahasiswa terdaftar pada kelas tersebut. Dosen dapat mengisi seluruh kolom nilai sekaligus dan menyimpannya secara massal (bulk save).
- **Kalkulasi Backend**: Nilai Akhir, Nilai Huruf, dan Angka Mutu dihitung di backend saat penyimpanan data (`store`/`update` nilai).
- **Penguncian Nilai (Locking)**: Setelah nilai difinalisasi (`is_locked = true`), tombol edit/simpan pada kelas tersebut akan dinonaktifkan, sehingga mahasiswa dapat melihat nilai di KHS mereka dan nilai tidak dapat dimanipulasi kembali.

---

## 3. Detail Implementasi Teknis (Untuk Tahap Berikutnya)

### A. Migrasi Database
1. `create_enrollments_table`:
   ```php
   Schema::create('enrollments', function (Blueprint $table) {
       $table->id();
       $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
       $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
       $table->string('status')->default('approved'); // approved, pending
       $table->timestamps();
   });
   ```
2. `create_grades_table`:
   ```php
   Schema::create('grades', function (Blueprint $table) {
       $table->id();
       $table->foreignId('enrollment_id')->constrained('enrollments')->onDelete('cascade');
       $table->decimal('score_attendance', 5, 2)->default(0);
       $table->decimal('score_task', 5, 2)->default(0);
       $table->decimal('score_uts', 5, 2)->default(0);
       $table->decimal('score_uas', 5, 2)->default(0);
       $table->decimal('score_final', 5, 2)->default(0);
       $table->string('grade_letter', 2)->nullable();
       $table->decimal('quality_point', 3, 2)->default(0);
       $table->boolean('is_locked')->default(false);
       $table->timestamps();
   });
   ```

### B. Rumus Kalkulasi di Controller / Service
Setiap kali dosen menyimpan nilai:
```php
$score_final = ($score_attendance * $weight_attendance / 100) +
               ($score_task * $weight_task / 100) +
               ($score_uts * $weight_uts / 100) +
               ($score_uas * $weight_uas / 100);

// Logika konversi nilai huruf dan bobot mutu
if ($score_final >= 85) { $grade_letter = 'A'; $quality_point = 4.0; }
elseif ($score_final >= 80) { $grade_letter = 'A-'; $quality_point = 3.7; }
elseif ($score_final >= 75) { $grade_letter = 'B+'; $quality_point = 3.3; }
elseif ($score_final >= 70) { $grade_letter = 'B'; $quality_point = 3.0; }
elseif ($score_final >= 65) { $grade_letter = 'B-'; $quality_point = 2.7; }
elseif ($score_final >= 60) { $grade_letter = 'C+'; $quality_point = 2.3; }
elseif ($score_final >= 55) { $grade_letter = 'C'; $quality_point = 2.0; }
elseif ($score_final >= 40) { $grade_letter = 'D'; $quality_point = 1.0; }
else { $grade_letter = 'E'; $quality_point = 0.0; }
```

---

## 4. Rencana Seeder & Data Dummy
- **`EnrollmentSeeder`**: Memetakan mahasiswa dummy ke kelas perkuliahan yang aktif. Setiap mahasiswa harus terdaftar di setidaknya 3-4 kelas.
- **`GradeSeeder`**: Mengisi nilai awal untuk beberapa mahasiswa untuk menyimulasikan kelas yang nilainya sudah terisi (sebagian terkunci untuk dicoba diakses mahasiswa, sebagian masih *draft* agar dosen bisa mencoba mengedit).
