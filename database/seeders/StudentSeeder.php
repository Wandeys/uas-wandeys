<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Link default mahasiswa@gmail.com
        $defaultUser = User::where('email', 'mahasiswa@gmail.com')->first();
        if ($defaultUser) {
            if (!Student::where('user_id', $defaultUser->id)->exists()) {
                Student::create([
                    'user_id' => $defaultUser->id,
                    'nim' => '2201010001',
                    'angkatan' => '2022',
                    'status' => 'active',
                ]);
            }
        }

        // 2. Create additional dummy students
        $additionalStudents = [
            ['name' => 'Ahmad Fauzi', 'email' => 'ahmad@gmail.com', 'nim' => '2201010002', 'angkatan' => '2022'],
            ['name' => 'Bella Citra', 'email' => 'bella@gmail.com', 'nim' => '2201010003', 'angkatan' => '2022'],
            ['name' => 'Candra Wijaya', 'email' => 'candra@gmail.com', 'nim' => '2201010004', 'angkatan' => '2022'],
            ['name' => 'Dewi Lestari', 'email' => 'dewi@gmail.com', 'nim' => '2301010001', 'angkatan' => '2023'],
            ['name' => 'Eko Prasetyo', 'email' => 'eko@gmail.com', 'nim' => '2301010002', 'angkatan' => '2023'],
            ['name' => 'Fani Rahmawati', 'email' => 'fani@gmail.com', 'nim' => '2301010003', 'angkatan' => '2023'],
            ['name' => 'Gilang Ramadhan', 'email' => 'gilang@gmail.com', 'nim' => '2301010004', 'angkatan' => '2023'],
            ['name' => 'Hendra Setiawan', 'email' => 'hendra@gmail.com', 'nim' => '2401010001', 'angkatan' => '2024'],
            ['name' => 'Indah Permata', 'email' => 'indah@gmail.com', 'nim' => '2401010002', 'angkatan' => '2024'],
            ['name' => 'Joko Susilo', 'email' => 'joko@gmail.com', 'nim' => '2401010003', 'angkatan' => '2024'],
            ['name' => 'Kartika Sari', 'email' => 'kartika@gmail.com', 'nim' => '2401010004', 'angkatan' => '2024'],
            ['name' => 'Lutfi Hakim', 'email' => 'lutfi@gmail.com', 'nim' => '2501010001', 'angkatan' => '2025'],
            ['name' => 'Mega Utami', 'email' => 'mega@gmail.com', 'nim' => '2501010002', 'angkatan' => '2025'],
            ['name' => 'Naufal Abdi', 'email' => 'naufal@gmail.com', 'nim' => '2501010003', 'angkatan' => '2025'],
            ['name' => 'Olivia Putri', 'email' => 'olivia@gmail.com', 'nim' => '2501010004', 'angkatan' => '2025'],
        ];

        foreach ($additionalStudents as $student) {
            if (User::where('email', $student['email'])->exists()) {
                continue;
            }

            $user = User::factory()->create([
                'name' => $student['name'],
                'email' => $student['email'],
                'role' => 'Mahasiswa',
            ]);

            Student::create([
                'user_id' => $user->id,
                'nim' => $student['nim'],
                'angkatan' => $student['angkatan'],
                'status' => 'active',
            ]);
        }
    }
}
