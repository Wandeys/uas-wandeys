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
                $grade = Grade::firstOrNew(['enrollment_id' => $enrollment->id]);
                if (!$grade->exists || $grade->score_attendance == 0) {
                    $grade->score_attendance = rand(80, 100);
                }
                $grade->score_task = rand(70, 95);
                $grade->score_uts = rand(65, 90);
                $grade->score_uas = rand(70, 95);
                $grade->is_locked = false;
                $grade->calculateGrade($draftClass);
                $grade->save();
            }
        }

        // 2. Seed Locked Class
        if ($lockedClass) {
            $enrollments = Enrollment::where('class_id', $lockedClass->id)->get();
            foreach ($enrollments as $enrollment) {
                $grade = Grade::firstOrNew(['enrollment_id' => $enrollment->id]);
                if (!$grade->exists || $grade->score_attendance == 0) {
                    $grade->score_attendance = rand(85, 100);
                }
                $grade->score_task = rand(75, 95);
                $grade->score_uts = rand(70, 90);
                $grade->score_uas = rand(75, 95);
                $grade->is_locked = true;
                $grade->calculateGrade($lockedClass);
                $grade->save();
            }
        }
    }
}
