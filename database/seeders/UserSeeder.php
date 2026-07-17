<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Tamus Tahir',
                'email' => 'tamuspustaka@gmail.com',
                'role' => 'Superadmin',
            ],
            [
                'name' => 'Superadmin User',
                'email' => 'tamus@gmail.com',
                'role' => 'Superadmin',
            ],
            [
                'name' => 'Joh Doe',
                'email' => 'admin@gmail.com',
                'role' => 'Admin',
            ],
            [
                'name' => 'Dosen Default',
                'email' => 'dosen@gmail.com',
                'role' => 'Dosen',
            ],
            [
                'name' => 'Mahasiswa Default',
                'email' => 'mahasiswa@gmail.com',
                'role' => 'Mahasiswa',
            ],
        ];

        foreach ($users as $user) {
            if (User::where('email', $user['email'])->exists()) {
                continue;
            }

            User::factory()->create([
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ]);
        }
    }
}
