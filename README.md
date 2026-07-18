# 🎓 Sistem Manajemen Nilai Akademik (SIMANA)

SIMANA adalah platform manajemen nilai akademik terintegrasi yang dibangun menggunakan **Laravel 13.0**, **Tailwind CSS v4.0**, dan **Bootstrap 5 (NiceAdmin Template)** dengan database **SQLite**. Sistem ini dirancang untuk mengotomatisasi seluruh siklus manajemen nilai—mulai dari pengelolaan data master, plotting mata kuliah, pengisian komponen nilai oleh dosen dengan bobot dinamis, perhitungan otomatis nilai akhir, hingga penerbitan Kartu Hasil Studi (KHS) dan Transkrip Akademik Sementara untuk mahasiswa.

---

## 🚀 Fitur Utama & Modul Sistem

Sistem ini menerapkan pembatasan hak akses berbasis peran (**Role-Based Access Control - RBAC**) untuk 4 jenis aktor:

### 1. 🔑 Autentikasi & Switch User (Uji Coba)
*   **Keamanan Ketat**: Form login aman menggunakan enkripsi kata sandi Bcrypt dan proteksi CSRF.
*   **Switch User**: Fitur penukar peran langsung untuk Admin & Superadmin agar dapat beralih ke akun mahasiswa atau dosen secara instan tanpa perlu bolak-balik logout-login demi kemudahan uji coba.
*   **Pengaturan Profil**: Edit profil dasar, ubah kata sandi, dan unggah foto profil (avatar).

### 2. 📁 Modul Data Master (Superadmin & Admin)
*   **Pengelolaan Pengguna**: CRUD akun pengguna beserta alokasi perannya.
*   **Data Akademik**: CRUD data dosen, mahasiswa, mata kuliah, dan tahun akademik.
*   **Manajemen Kelas**: Pembuatan kelas perkuliahan yang menghubungkan mata kuliah, dosen pengampu, tahun akademik aktif, serta konfigurasi bobot komponen nilai.

### 3. 📝 Pengisian & Pengelolaan Nilai (Dosen)
*   **Bobot Nilai Dinamis**: Mengatur bobot penilaian per kelas secara dinamis (Tugas, UTS, UAS, Presensi) dengan total bobot harus tepat 100%.
*   **Input Nilai Kolektif**: Penginputan nilai mentah mahasiswa per kelas perkuliahan secara kolektif.
*   **Kalkulasi Otomatis**: Sistem secara otomatis menghitung Nilai Akhir (skala 0-100) dan mengonversinya menjadi Nilai Huruf (A, A-, B+, B, dst.) dan Angka Mutu (4.0, 3.7, 3.3, 3.0, dst.).
*   **Finalisasi & Kunci Nilai**: Mengunci nilai setelah selesai diinput agar permanen dan tidak dapat diubah kembali demi menjaga integritas data.

### 4. 📊 Dashboard Analytics Per Peran
*   **Superadmin**: Log Audit transaksi penting secara detail (perubahan nilai, update password, hapus user, dll.) dan statistik global.
*   **Admin**: Statistik jumlah mahasiswa/dosen aktif, visualisasi ringkasan, dan status periode akademik.
*   **Dosen**: Ringkasan kelas yang diampu, daftar mahasiswa bimbingan, dan bagan histogram distribusi nilai mahasiswa (A, B, C, D, E) untuk kelas terkait.
*   **Mahasiswa**: Informasi metrik akademik (IPK, SKS Kumulatif, IPS Semester Berjalan) serta line chart tren perkembangan IPS antar semester.

### 5. 📄 Rapor Akademik & KHS (Mahasiswa)
*   **Kartu Hasil Studi (KHS)**: Melihat KHS per semester berjalan secara real-time.
*   **Riwayat Transkrip**: Informasi lengkap transkrip akademik sementara.
*   **Cetak & Ekspor PDF**: Cetak KHS atau unduh dalam format PDF resmi menggunakan `laravel-dompdf`.

### 6. 📅 Presensi Kehadiran & Notifikasi
*   **Presensi Kelas**: Input kehadiran mahasiswa per kelas oleh dosen. Kehadiran ini otomatis terintegrasi dan menyinkronkan nilai presensi ke dalam kalkulasi nilai akhir.
*   **Notifikasi Real-time**: Mahasiswa secara otomatis menerima pemberitahuan/notifikasi sistem saat nilai suatu kelas perkuliahan telah difinalisasi (rilis) oleh dosen.

---

## 🎨 Kustomisasi Tema Warna

Aplikasi ini menggunakan integrasi CSS Variables sehingga warna tema utama dapat diubah dengan sangat mudah di satu file:

1. Buka file `resources/views/layouts/app.blade.php`.
2. Cari tag `<style>` di bagian `<head>` pada blok `:root`.
3. Ubah nilai variabel CSS berikut sesuai keinginan Anda:

```css
:root {
    /* ====== UBAH WARNA TEMA DI SINI ====== */
    --theme-bg: #000080;     /* Warna latar header, footer, tombol utama, sidebar aktif */
    --theme-hover: #020260;  /* Warna efek hover menu/tombol */
    --theme-text: #ffffff;   /* Warna teks yang berada di atas warna tema */
    --main-bg: #eeeeee;      /* Warna latar belakang utama halaman konten */
    /* ===================================== */
}
```

---

## 🔑 Akun & Kredensial Default

Setelah menjalankan database seeder, Anda dapat masuk ke aplikasi menggunakan akun berikut (semua kata sandi adalah `password`):

| Nama Pengguna | Surel (Email) | Peran (Role) | Keterangan |
| :--- | :--- | :--- | :--- |
| **Superadmin User** | `tamus@gmail.com` | Superadmin | Hak akses penuh termasuk Audit Logs |
| **Tamus Tahir** | `tamuspustaka@gmail.com` | Superadmin | Akun Superadmin Alternatif |
| **Joh Doe** | `admin@gmail.com` | Admin | Pengelolaan Data Master & Pengguna |
| **Dosen Default** | `dosen@gmail.com` | Dosen | Pengampu kelas, input nilai & presensi |
| **Mahasiswa Default** | `mahasiswa@gmail.com` | Mahasiswa | Mengakses KHS, riwayat IPK/IPS & cetak PDF |

---

## 🛠️ Stack Teknologi

*   **Backend**: PHP ^8.3 & Laravel ^13.0
*   **Frontend**: Tailwind CSS v4.0 & Bootstrap v5.3 (NiceAdmin Template)
*   **Basis Data (Database)**: SQLite (file-based portable database)
*   **Paket/Library Kunci**:
    *   `barryvdh/laravel-dompdf` (Generasi dokumen PDF)
    *   `pestphp/pest` & `pestphp/pest-plugin-laravel` (Testing framework)
    *   `laravel/tinker` (Interactive REPL)

---

## 💻 Instalasi & Menjalankan Proyek

Pastikan Anda telah memasang **PHP >= 8.3**, **Composer**, dan **Node.js** di perangkat Anda. Ikuti langkah-langkah di bawah ini:

### 1. Klon Repositori & Masuk ke Folder
```bash
git clone <repository-url>
cd uas-laravel
```

### 2. Jalankan Script Setup Otomatis
Kami menyediakan script perintah composer terpadu untuk menginstal dependensi PHP, membuat file konfigurasi `.env`, men-generate key enkripsi, menginstal modul javascript, serta mem-build aset frontend:
```bash
composer run setup
```

### 3. Setup Basis Data
Buat file SQLite database secara manual (atau otomatis jika menggunakan script di atas), lalu jalankan migrasi database beserta seeder data default:
```bash
# Membuat file database SQLite kosong
touch database/database.sqlite

# Menjalankan migrasi dan seeding data awal
php artisan migrate:fresh --seed
```

### 4. Jalankan Server Pengembangan
Jalankan server aplikasi secara lokal (menjalankan server Laravel, queue listener, dan Vite compiler sekaligus menggunakan utilitas Concurrently):
```bash
composer run dev
```
Buka browser Anda dan akses aplikasi di **`http://127.0.0.1:8000`** atau alamat port lokal yang tertera pada terminal.

---

## 🧪 Menjalankan Unit & Feature Testing

Aplikasi ini dilengkapi dengan pengujian menyeluruh (39 test cases & 144 assertions) untuk memvalidasi RBAC, pengelolaan nilai, presensi, audit logging, KHS, dan performa dashboard:

Jalankan pengujian menggunakan Pest PHP dengan perintah:
```bash
composer run test
```

---

## 📄 Lisensi

Proyek ini dirilis di bawah lisensi [MIT](https://opensource.org/licenses/MIT).
