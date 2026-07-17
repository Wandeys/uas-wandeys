<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AuditLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dosen = User::where('role', 'Dosen')->first();
        $admin = User::where('role', 'Admin')->first();

        // 1. LOGIN_FAILED log
        AuditLog::create([
            'user_id' => null,
            'action' => 'LOGIN_FAILED',
            'description' => 'Gagal login menggunakan email: admin_typo@simana.com',
            'payload_before' => null,
            'payload_after' => ['email' => 'admin_typo@simana.com'],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'created_at' => Carbon::now()->subDays(5),
        ]);

        // 2. CHANGE_PASSWORD log
        if ($dosen) {
            AuditLog::create([
                'user_id' => $dosen->id,
                'action' => 'CHANGE_PASSWORD',
                'description' => 'Mengubah password user: ' . $dosen->email,
                'payload_before' => null,
                'payload_after' => null,
                'ip_address' => '192.168.1.5',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
                'created_at' => Carbon::now()->subDays(3),
            ]);
        }

        // 3. LOCK_GRADE log
        if ($dosen) {
            AuditLog::create([
                'user_id' => $dosen->id,
                'action' => 'LOCK_GRADE',
                'description' => 'Finalisasi & Kunci nilai mahasiswa NIM: 2201010001 di kelas: Kelas A',
                'payload_before' => ['is_locked' => false, 'score_final' => 85.00],
                'payload_after' => ['is_locked' => true],
                'ip_address' => '192.168.1.5',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
                'created_at' => Carbon::now()->subDays(2),
            ]);
        }

        // 4. DELETE_USER log
        if ($admin) {
            AuditLog::create([
                'user_id' => $admin->id,
                'action' => 'DELETE_USER',
                'description' => 'Menghapus user: dummy_student@simana.com (Nama: Dummy Student, Role: Mahasiswa)',
                'payload_before' => ['id' => 99, 'name' => 'Dummy Student', 'email' => 'dummy_student@simana.com', 'role' => 'Mahasiswa'],
                'payload_after' => null,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subDays(1),
            ]);
        }
    }
}
