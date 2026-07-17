<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Link default dosen@gmail.com if exists
        $defaultUser = User::where('email', 'dosen@gmail.com')->first();
        if ($defaultUser) {
            if (!Teacher::where('user_id', $defaultUser->id)->exists()) {
                Teacher::create([
                    'user_id' => $defaultUser->id,
                    'nip' => '198001012005011001',
                    'nidn' => '0401018001',
                    'gelar' => 'M.T.',
                ]);
            }
        }

        // 2. Create additional dummy teachers
        $additionalDosen = [
            [
                'name' => 'Dr. Budi Santoso',
                'email' => 'budi@gmail.com',
                'nip' => '197503122001121002',
                'nidn' => '0412037501',
                'gelar' => 'M.Kom., Ph.D.',
            ],
            [
                'name' => 'Siti Aminah, M.Sc.',
                'email' => 'siti@gmail.com',
                'nip' => '198808152015042003',
                'nidn' => '0415088802',
                'gelar' => 'M.Sc.',
            ],
            [
                'name' => 'Andi Wijaya, M.T.',
                'email' => 'andi@gmail.com',
                'nip' => '198211302008011004',
                'nidn' => '0430118201',
                'gelar' => 'M.T.',
            ],
        ];

        foreach ($additionalDosen as $dosen) {
            if (User::where('email', $dosen['email'])->exists()) {
                continue;
            }

            $user = User::factory()->create([
                'name' => $dosen['name'],
                'email' => $dosen['email'],
                'role' => 'Dosen',
            ]);

            Teacher::create([
                'user_id' => $user->id,
                'nip' => $dosen['nip'],
                'nidn' => $dosen['nidn'],
                'gelar' => $dosen['gelar'],
            ]);
        }
    }
}
