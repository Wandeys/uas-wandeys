# Task 8: Notifikasi dan Komunikasi Akademik

## 1. Deskripsi & Tujuan
Membangun sistem notifikasi internal dalam aplikasi (in-app notifications) untuk memberi tahu mahasiswa ketika nilai mata kuliah mereka telah difinalisasi dan dirilis oleh Dosen, serta mengirimkan email pemberitahuan.

---

## 2. Persyaratan Fungsional & Kepatuhan Arsitektur
- **In-App Notification**: Notifikasi real-time yang muncul di pojok kanan atas (header) NiceAdmin. Tanda lonceng (badge count) menunjukkan jumlah notifikasi belum dibaca (unread).
- **Notifikasi Rilis Nilai**: Saat dosen mengubah status kelas/nilai menjadi `is_locked = true`, sistem otomatis mengirim notifikasi ke semua mahasiswa terdaftar di kelas tersebut.
- **Pemberitahuan Email**: Integrasi opsional dengan Mailtrap / local mail driver Laravel untuk simulasi pengiriman email pemberitahuan rilis nilai.

---

## 3. Detail Implementasi Teknis (Untuk Tahap Berikutnya)

### A. Setup Notifikasi Laravel (Database Channel)
Menjalankan perintah `php artisan notifications:table` untuk membuat tabel notifikasi terpadu bawaan Laravel:
- **Nama File**: `database/migrations/xxxx_xx_xx_xxxxxx_create_notifications_table.php`

### B. Notification Class (`GradeReleasedNotification.php`)
```php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class GradeReleasedNotification extends Notification
{
    use Queueable;

    protected $class;

    public function __construct($class) {
        $this->class = $class;
    }

    public function via($notifiable): array {
        return ['database', 'mail']; // Disimpan di db dan dikirim via mail
    }

    public function toArray($notifiable): array {
        return [
            'title' => 'Nilai Baru Dirilis',
            'message' => "Nilai untuk mata kuliah {$this->class->course->name} ({$this->class->name}) telah difinalisasi.",
            'action_url' => route('khs.index'),
        ];
    }
}
```

---

## 4. Rencana Seeder & Data Dummy
- **`NotificationSeeder`**: Memasukkan data notifikasi mentah ke tabel `notifications` untuk akun mahasiswa dummy, sehingga saat pengguna masuk (login) pertama kali sebagai mahasiswa, badge lonceng di layout NiceAdmin akan langsung menyala dan menampilkan pesan notifikasi dummy.
