# Task 10: Dashboard dan Fitur Tambahan

## 1. Deskripsi & Tujuan
Mengintegrasikan seluruh modul ke dalam dasbor interaktif yang dirancang khusus untuk masing-masing peran pengguna (Admin, Dosen, Mahasiswa). Tahap ini juga mencakup pemolesan fitur antarmuka, pengaturan profil, pengalihan peran (switch-user untuk pengujian), dan finalisasi layout NiceAdmin.

---

## 2. Persyaratan Fungsional & Kepatuhan Arsitektur
- **Dashboard Kustom Per Role**:
  - **Mahasiswa**: Widget SKS Kumulatif, IPK, IPS semester terakhir, dan grafik garis tren perkembangan Indeks Prestasi Semester (IPS) dari waktu ke waktu.
  - **Dosen**: Ringkasan jadwal/kelas aktif hari ini, jumlah mahasiswa bimbingan, dan quick links ke kelas-kelas yang memerlukan input nilai.
  - **Admin**: Statistik jumlah mahasiswa/dosen aktif, quick stats, dan konfigurasi periode akademik berjalan.
- **Switch User**: Menyesuaikan fungsionalitas switch-user yang sudah ada di login controller agar Admin dapat langsung bertukar peran menjadi mahasiswa atau dosen demi kemudahan uji coba fitur tanpa logout-login berulang kali.
- **Pengaturan Profil**: Pengguna dapat mengunggah avatar, mengubah info kontak dasar, dan mengganti sandi akun mereka.

---

## 3. Detail Implementasi Teknis (Untuk Tahap Berikutnya)

### A. Rincian Widget Dashboard View
- Dashboard Mahasiswa menggunakan card-card informatif NiceAdmin untuk menampilkan metrik kunci (IPK, SKS) disusul dengan visualisasi grafik dari Chart.js:
```javascript
// Contoh inisialisasi grafik tren IPS mahasiswa di dashboard mahasiswa
const chartData = {
    labels: ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4'],
    datasets: [{
        label: 'Indeks Prestasi Semester (IPS)',
        data: [3.20, 3.45, 3.10, 3.65],
        borderColor: 'rgb(75, 192, 192)',
        tension: 0.1
    }]
};
```

### B. Route Pengaturan & System Setting
- Route: `/setting` (Admin) memetakan ke `SettingController` untuk menyesuaikan detail instansi seperti Nama Universitas, Periode Aktif, dan Logo Instansi yang tampil di header atas aplikasi.

---

## 4. Rencana Seeder & Data Dummy
- Menyediakan seeder instansi (`SettingSeeder` yang sudah ada diperluas jika perlu) agar data nama instansi dan logo default langsung terisi.
- Mengintegrasikan seeder seluruh fasa untuk memastikan dashboard menampilkan data grafis yang dinamis (IPK, IPS, jumlah mahasiswa) sesaat setelah database di-refresh dan di-seed.
