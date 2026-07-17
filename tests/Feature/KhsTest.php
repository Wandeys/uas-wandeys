<?php

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\AcademicYear;
use App\Models\AcademicClass;
use App\Models\Enrollment;
use App\Models\Grade;

test('mahasiswa can view their KHS and it displays correct statistics', function () {
    // 1. Create a Teacher (required for class creation)
    $dosenUser = User::factory()->create(['role' => 'Dosen']);
    $teacher = Teacher::create(['user_id' => $dosenUser->id, 'nip' => '111', 'nidn' => '222']);

    // 2. Create a Student
    $studentUser = User::factory()->create(['role' => 'Mahasiswa']);
    $student = Student::create([
        'user_id' => $studentUser->id,
        'nim' => '2201010001',
        'angkatan' => '2022',
        'status' => 'active',
    ]);

    // 3. Create courses and classes for two semesters
    $year1 = AcademicYear::create(['year' => '2024/2025', 'semester' => 'Ganjil', 'is_active' => false]);
    $year2 = AcademicYear::create(['year' => '2024/2025', 'semester' => 'Genap', 'is_active' => true]);

    $course1 = Course::create(['code' => 'IF101', 'name' => 'Pemrograman Dasar', 'credits' => 3, 'semester' => 1]);
    $course2 = Course::create(['code' => 'IF102', 'name' => 'Matematika Diskrit', 'credits' => 4, 'semester' => 1]);

    $class1 = AcademicClass::create([
        'course_id' => $course1->id,
        'teacher_id' => $teacher->id,
        'academic_year_id' => $year1->id,
        'name' => 'Kelas A',
        'weight_attendance' => 10.00,
        'weight_task' => 20.00,
        'weight_uts' => 30.00,
        'weight_uas' => 40.00,
    ]);
    $class2 = AcademicClass::create([
        'course_id' => $course2->id,
        'teacher_id' => $teacher->id,
        'academic_year_id' => $year1->id,
        'name' => 'Kelas B',
        'weight_attendance' => 10.00,
        'weight_task' => 20.00,
        'weight_uts' => 30.00,
        'weight_uas' => 40.00,
    ]);

    // Enrollments
    $enrollment1 = Enrollment::create(['student_id' => $student->id, 'class_id' => $class1->id]);
    $enrollment2 = Enrollment::create(['student_id' => $student->id, 'class_id' => $class2->id]);

    // Grades (locked)
    Grade::create([
        'enrollment_id' => $enrollment1->id,
        'score_attendance' => 100,
        'score_task' => 100,
        'score_uts' => 100,
        'score_uas' => 100,
        'score_final' => 100.00,
        'grade_letter' => 'A',
        'quality_point' => 4.0,
        'is_locked' => true,
    ]);

    Grade::create([
        'enrollment_id' => $enrollment2->id,
        'score_attendance' => 80,
        'score_task' => 80,
        'score_uts' => 80,
        'score_uas' => 80,
        'score_final' => 80.00,
        'grade_letter' => 'A-',
        'quality_point' => 3.7,
        'is_locked' => true,
    ]);

    // Calculate expected statistics
    // Year 1 (Ganjil):
    // Total SKS: 3 + 4 = 7
    // IPS: (3 * 4.0 + 4 * 3.7) / 7 = (12 + 14.8) / 7 = 26.8 / 7 = 3.83
    
    // Access KHS index for Year 1
    $response = $this->actingAs($studentUser)->get("/khs?academic_year_id={$year1->id}");

    $response->assertStatus(200);
    $response->assertSee('Pemrograman Dasar');
    $response->assertSee('Matematika Diskrit');
    $response->assertSee('3.83'); // IPS
    $response->assertSee('3.83'); // IPK
    $response->assertSee('7 SKS'); // SKS Lulus
});

test('mahasiswa can print their KHS', function () {
    $studentUser = User::factory()->create(['role' => 'Mahasiswa']);
    $student = Student::create([
        'user_id' => $studentUser->id,
        'nim' => '2201010001',
        'angkatan' => '2022',
        'status' => 'active',
    ]);

    $year = AcademicYear::create(['year' => '2024/2025', 'semester' => 'Ganjil', 'is_active' => true]);

    $response = $this->actingAs($studentUser)->get("/khs/cetak?academic_year_id={$year->id}");
    $response->assertStatus(200);
    $response->assertSee('KARTU HASIL STUDI (KHS)');
    $response->assertSee(e($studentUser->name));
    $response->assertSee($student->nim);
});
