# Task 9: Keamanan dan Log Audit (Audit Trail)

## 1. Deskripsi & Tujuan
Meningkatkan integritas sistem dengan mencatat aktivitas sensitif, khususnya yang berkaitan dengan pengubahan data nilai mahasiswa oleh dosen atau admin. Log ini merekam siapa yang melakukan perubahan, kapan, perubahan nilai dari berapa menjadi berapa, alamat IP, dan user agent.

---

## 2. Persyaratan Fungsional & Kepatuhan Arsitektur
- **Log Perubahan Nilai**: Menyimpan log secara otomatis saat nilai dibuat, diperbarui, atau difinalisasi.
- **Log Keamanan**: Mencatat aktivitas login gagal, perubahan password user, dan penghapusan data penting.
- **UI Log Audit**: Halaman khusus yang hanya bisa diakses oleh `Superadmin` untuk memantau aktivitas sistem dengan fitur pencarian dan filter berdasarkan pelaku aktivitas (user).

---

## 3. Detail Implementasi Teknis (Untuk Tahap Berikutnya)

### A. Migrasi Database
`create_audit_logs_table`:
```php
Schema::create('audit_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
    $table->string('action'); // e.g., 'UPDATE_GRADE', 'LOGIN_FAILED', 'DELETE_USER'
    $table->text('description'); // Narasi aktivitas
    $table->json('payload_before')->nullable(); // Keadaan data sebelum diubah
    $table->json('payload_after')->nullable();  // Keadaan data setelah diubah
    $table->string('ip_address')->nullable();
    $table->string('user_agent')->nullable();
    $table->timestamps();
});
```

### B. Implementasi Model Observer (`GradeObserver.php`)
Memanfaatkan Eloquent Observers untuk mengotomatisasi perekaman log saat event saving/updating di model `Grade`:
```php
namespace App\Observers;

use App\Models\Grade;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class GradeObserver
{
    public function updating(Grade $grade): void {
        if ($grade->isDirty()) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'UPDATE_GRADE',
                'description' => 'Mengubah nilai mahasiswa ID: ' . $grade->enrollment->student->nim,
                'payload_before' => json_encode($grade->getOriginal()),
                'payload_after' => json_encode($grade->getDirty()),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }
}
```

---

## 4. Rencana Seeder & Data Dummy
- **`AuditLogSeeder`**: Membuat data histori log aktivitas palsu (misalnya: riwayat perubahan nilai oleh Dosen A, riwayat penghapusan user oleh Admin) agar halaman Audit Log pada Dashboard Superadmin langsung terisi informasi aktivitas kronologis sejak awal.
