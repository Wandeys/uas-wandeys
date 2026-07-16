# Task 7: Manajemen Presensi Perkuliahan

## 1. Deskripsi & Tujuan
Membangun modul presensi (kehadiran) mahasiswa untuk setiap kelas perkuliahan. Data presensi ini diinput secara berkala oleh Dosen dan secara otomatis memengaruhi komponen nilai kehadiran (`score_attendance`) mahasiswa di akhir semester berdasarkan persentase kehadiran nyata.

---

## 2. Persyaratan Fungsional & Kepatuhan Arsitektur
- **Pencatatan Pertemuan**: Sistem mencatat riwayat kehadiran mahasiswa untuk 16 kali pertemuan (standar akademik).
- **Status Presensi**: Setiap baris data kehadiran memuat opsi status: Hadir ($H$), Sakit ($S$), Izin ($I$), atau Alpa ($A$).
- **Perhitungan Otomatis**: Kehadiran dihitung sebagai:
  $$\text{Persentase Kehadiran} = \frac{\text{Jumlah Hadir}}{\text{Total Pertemuan yang Diisi}} \times 100\%$$
  Nilai persentase ini akan disinkronkan langsung ke tabel `grades` sebagai komponen `score_attendance`.
- **UI Dosen**: Tabel presensi per pertemuan kelas yang mudah diisi dengan radio button atau checkbox.

---

## 3. Detail Implementasi Teknis (Untuk Tahap Berikutnya)

### A. Migrasi Database
`create_attendances_table`:
```php
Schema::create('attendances', function (Blueprint $table) {
    $table->id();
    $table->foreignId('enrollment_id')->constrained('enrollments')->onDelete('cascade');
    $table->integer('meeting_number'); // 1 sampai 16
    $table->enum('status', ['H', 'S', 'I', 'A'])->default('H'); // Hadir, Sakit, Izin, Alpa
    $table->date('date');
    $table->timestamps();
});
```

### B. Otomatisasi Sinkronisasi Ke Nilai (`Observer` atau `Event`)
Setiap kali ada penambahan atau pengubahan presensi di suatu kelas, sistem menghitung ulang rata-rata kehadiran mahasiswa tersebut dan meng-update kolom `score_attendance` pada record `grades` yang bersangkutan.
```php
public function syncAttendanceScore($enrollmentId) {
    $totalMeetings = Attendance::where('enrollment_id', $enrollmentId)->count();
    if ($totalMeetings === 0) return;

    $presentCount = Attendance::where('enrollment_id', $enrollmentId)
        ->where('status', 'H')
        ->count();

    // Misalkan Sakit (S) & Izin (I) dihitung hadir dengan bobot tertentu atau diabaikan,
    // di sini diasumsikan hanya status 'H' (Hadir) yang bernilai 1.0 penuh
    $score = ($presentCount / $totalMeetings) * 100;

    $grade = Grade::firstOrNew(['enrollment_id' => $enrollmentId]);
    $grade->score_attendance = $score;
    $grade->save();
}
```

---

## 4. Rencana Seeder & Data Dummy
- **`AttendanceSeeder`**: Membuat data kehadiran dummy untuk 1 kelas perkuliahan penuh (pertemuan 1 hingga 14 atau 16) bagi seluruh mahasiswa di dalamnya, dengan catatan kehadiran yang beragam untuk memicu kalkulasi persentase nilai kehadiran yang bervariasi.
