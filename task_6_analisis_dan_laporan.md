# Task 6: Analisis Akademik dan Laporan

## 1. Deskripsi & Tujuan
Membangun dasbor visual analisis akademik untuk dosen dan administrator, serta menyediakan laporan akademik siap cetak dalam format PDF. Modul ini bertujuan memberikan wawasan (insight) cepat mengenai performa belajar mengajar di institusi.

---

## 2. Persyaratan Fungsional & Kepatuhan Arsitektur
- **Grafik Distribusi Nilai**: Menampilkan grafik sebaran nilai (Grade A, B, C, D, E) untuk kelas tertentu yang diampu oleh Dosen menggunakan **Chart.js** (diintegrasikan dengan aset NiceAdmin).
- **Statistik Global (Admin)**: Menampilkan metrik agregat seperti:
  - Distribusi IPK mahasiswa per angkatan.
  - Mata kuliah dengan tingkat kelulusan terendah/tertinggi.
- **Export PDF Terintegrasi**: Menggunakan library **dompdf/laravel-dompdf** untuk menghasilkan file PDF laporan nilai kolektif per kelas, yang dapat diunduh oleh Dosen atau Admin untuk keperluan arsip fisik.

---

## 3. Detail Implementasi Teknis (Untuk Tahap Berikutnya)

### A. Integrasi Dompdf
- Instalasi package `barryvdh/laravel-dompdf` (direncanakan pada tahap coding).
- Metode di `GradeReportController`:
  ```php
  public function downloadClassPdf($classId) {
      $class = ClassModel::with(['course', 'teacher', 'enrollments.student', 'enrollments.grade'])->findOrFail($classId);
      $pdf = Pdf::loadView('reports.class_grades', compact('class'));
      return $pdf->download("rekap_nilai_{$class->course->code}_{$class->name}.pdf");
  }
  ```

### B. Chart.js Data Parsing
DosenController mengumpulkan agregat huruf mutu dan mengirimkannya sebagai format JSON ke view dashboard untuk dirender sebagai bar/pie chart:
```php
$gradeCounts = Grade::whereIn('enrollment_id', $enrollmentIds)
    ->select('grade_letter', DB::raw('count(*) as total'))
    ->groupBy('grade_letter')
    ->pluck('total', 'grade_letter')
    ->toArray();
```

---

## 4. Rencana Seeder & Data Dummy
- Menyediakan data kelas dummy yang terisi nilai penuh dengan persebaran nilai yang variatif (misal: 10 mahasiswa mendapat A, 15 mahasiswa mendapat B, 5 mendapat C, dst.) agar chart distribusi nilai langsung menyajikan visualisasi grafik yang representatif setelah seeder dijalankan.
