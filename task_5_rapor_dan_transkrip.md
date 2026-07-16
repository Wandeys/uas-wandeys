# Task 5: Rapor (KHS) dan Transkrip Akademik

## 1. Deskripsi & Tujuan
Membangun portal informasi bagi mahasiswa untuk mengakses hasil studi mereka. Fitur ini memuat Kartu Hasil Studi (KHS) yang difilter per semester/tahun akademik serta Halaman Transkrip Akademik Sementara untuk memantau performa studi kumulatif mahasiswa.

---

## 2. Persyaratan Fungsional & Kepatuhan Arsitektur
- **Filter Semester**: Mahasiswa dapat memilih tahun akademik yang ingin ditampilkan nilainya menggunakan dropdown filter.
- **Kalkulasi IPS & IPK**:
  - IPS (Indeks Prestasi Semester) dihitung dari rata-rata tertimbang bobot mutu mata kuliah pada semester yang dipilih.
  - IPK (Indeks Prestasi Kumulatif) dihitung dari keseluruhan mata kuliah yang telah diselesaikan (mengambil nilai terbaik jika ada matakuliah yang diulang).
- **Cetak Dokumen**: Antarmuka cetak yang bersih untuk KHS. Format cetakan harus memiliki layout resmi institusi, ramah printer, tanpa memuat elemen navigasi/sidebar template NiceAdmin (menggunakan CSS print media `@media print`).

---

## 3. Detail Implementasi Teknis (Untuk Tahap Berikutnya)

### A. Formula Kalkulasi IPS & IPK (Logic Helper/Model Method)
- Di Model `Student.php` atau `AcademicService.php`:
  ```php
  // Mengambil IPS
  public function calculateIPS($academicYearId) {
      $enrollments = $this->enrollments()
          ->whereHas('class', function($q) use ($academicYearId) {
              $q->where('academic_year_id', $academicYearId);
          })
          ->whereHas('grade', function($q) {
              $q->where('is_locked', true);
          })->get();

      $totalSks = 0;
      $weightedPoints = 0;

      foreach ($enrollments as $enrollment) {
          $sks = $enrollment->class->course->credits;
          $qualityPoint = $enrollment->grade->quality_point;
          $weightedPoints += ($sks * $qualityPoint);
          $totalSks += $sks;
      }

      return $totalSks > 0 ? round($weightedPoints / $totalSks, 2) : 0.00;
  }
  ```

### B. Route & View
- Route: `/mahasiswa/khs` dipetakan ke `KhsController@index`.
- View: `resources/views/khs/index.blade.php` menampilkan ringkasan IPK, IPS, tabel nilai mata kuliah, dan tombol "Cetak KHS" yang mengaktifkan `window.print()`.

---

## 4. Rencana Seeder & Data Dummy
- Menambahkan data historis nilai mahasiswa dummy untuk semester-semester sebelumnya (misal Semester 1 dan Semester 2) agar mahasiswa tersebut memiliki tren IPK/IPS yang realistis (tidak hanya berisi data semester berjalan saja).
- Memastikan ada data nilai mahasiswa yang berstatus `is_locked = true` (tampil di KHS) dan `is_locked = false` (tidak tampil di KHS mahasiswa karena masih draft dosen).
