<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $years = [
            [
                'year' => '2025/2026',
                'semester' => 'Ganjil',
                'is_active' => true,
            ],
            [
                'year' => '2025/2026',
                'semester' => 'Genap',
                'is_active' => false,
            ],
        ];

        foreach ($years as $year) {
            if (AcademicYear::where('year', $year['year'])->where('semester', $year['semester'])->exists()) {
                continue;
            }

            AcademicYear::create($year);
        }
    }
}
