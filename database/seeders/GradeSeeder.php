<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\AcademicClass;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = AcademicClass::all();
        if ($classes->isEmpty()) {
            return;
        }

        // We will seed grades for Class 1 (Draft) and Class 2 (Locked/Finalized)
        $draftClass = $classes->get(0);
        $lockedClass = $classes->get(1);

        // 1. Seed Draft Class
        if ($draftClass) {
            $enrollments = Enrollment::where('class_id', $draftClass->id)->get();
            foreach ($enrollments as $enrollment) {
                if (Grade::where('enrollment_id', $enrollment->id)->exists()) {
                    continue;
                }

                $grade = new Grade([
                    'enrollment_id' => $enrollment->id,
                    'score_attendance' => rand(80, 100),
                    'score_task' => rand(70, 95),
                    'score_uts' => rand(65, 90),
                    'score_uas' => rand(70, 95),
                    'is_locked' => false,
                ]);
                $grade->calculateGrade($draftClass);
                $grade->save();
            }
        }

        // 2. Seed Locked Class
        if ($lockedClass) {
            $enrollments = Enrollment::where('class_id', $lockedClass->id)->get();
            foreach ($enrollments as $enrollment) {
                if (Grade::where('enrollment_id', $enrollment->id)->exists()) {
                    continue;
                }

                $grade = new Grade([
                    'enrollment_id' => $enrollment->id,
                    'score_attendance' => rand(85, 100),
                    'score_task' => rand(75, 95),
                    'score_uts' => rand(70, 90),
                    'score_uas' => rand(75, 95),
                    'is_locked' => true,
                ]);
                $grade->calculateGrade($lockedClass);
                $grade->save();
            }
        }
    }
}
