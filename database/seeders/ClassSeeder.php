<?php

namespace Database\Seeders;

use App\Models\AcademicClass;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            return;
        }

        $teachers = Teacher::all();
        $courses = Course::limit(5)->get();

        if ($teachers->isEmpty() || $courses->isEmpty()) {
            return;
        }

        $classNames = ['Kelas A', 'Kelas B', 'Kelas C', 'Kelas A', 'Kelas B'];

        foreach ($courses as $index => $course) {
            $teacher = $teachers[$index % $teachers->count()];
            
            // Check if class already exists
            if (AcademicClass::where('course_id', $course->id)
                ->where('teacher_id', $teacher->id)
                ->where('academic_year_id', $activeYear->id)
                ->where('name', $classNames[$index])
                ->exists()) {
                continue;
            }

            AcademicClass::create([
                'course_id' => $course->id,
                'teacher_id' => $teacher->id,
                'academic_year_id' => $activeYear->id,
                'name' => $classNames[$index],
                'weight_attendance' => 10.00,
                'weight_task' => 20.00,
                'weight_uts' => 30.00,
                'weight_uas' => 40.00,
            ]);
        }
    }
}
