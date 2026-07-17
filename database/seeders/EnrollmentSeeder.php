<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\AcademicClass;
use App\Models\Enrollment;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::all();
        $classes = AcademicClass::all();

        if ($students->isEmpty() || $classes->isEmpty()) {
            return;
        }

        foreach ($students as $student) {
            // Enroll every student in at least 3-4 classes
            foreach ($classes as $index => $class) {
                // Let's enroll in first 4 classes
                if ($index >= 4) {
                    continue;
                }

                if (Enrollment::where('student_id', $student->id)->where('class_id', $class->id)->exists()) {
                    continue;
                }

                Enrollment::create([
                    'student_id' => $student->id,
                    'class_id' => $class->id,
                    'status' => 'approved',
                ]);
            }
        }
    }
}
