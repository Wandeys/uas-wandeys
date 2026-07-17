<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            [
                'code' => 'IF101',
                'name' => 'Pemrograman Web',
                'credits' => 3,
                'semester' => 3,
            ],
            [
                'code' => 'IF102',
                'name' => 'Pemrograman Mobile',
                'credits' => 3,
                'semester' => 4,
            ],
            [
                'code' => 'IF103',
                'name' => 'Basis Data',
                'credits' => 3,
                'semester' => 2,
            ],
            [
                'code' => 'IF104',
                'name' => 'Jaringan Komputer',
                'credits' => 3,
                'semester' => 3,
            ],
            [
                'code' => 'IF105',
                'name' => 'Kecerdasan Buatan',
                'credits' => 4,
                'semester' => 5,
            ],
            [
                'code' => 'IF106',
                'name' => 'Algoritma & Struktur Data',
                'credits' => 4,
                'semester' => 1,
            ],
            [
                'code' => 'IF107',
                'name' => 'Interaksi Manusia & Komputer',
                'credits' => 2,
                'semester' => 2,
            ],
            [
                'code' => 'IF108',
                'name' => 'Rekayasa Perangkat Lunak',
                'credits' => 3,
                'semester' => 4,
            ],
        ];

        foreach ($courses as $course) {
            if (Course::where('code', $course['code'])->exists()) {
                continue;
            }

            Course::create($course);
        }
    }
}
