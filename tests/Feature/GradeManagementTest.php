<?php

use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Course;
use App\Models\AcademicYear;
use App\Models\AcademicClass;
use App\Models\Enrollment;
use App\Models\Grade;

test('dosen can only see their own classes on the index page', function () {
    $dosenUser1 = User::factory()->create(['role' => 'Dosen']);
    $teacher1 = Teacher::create(['user_id' => $dosenUser1->id, 'nip' => '111', 'nidn' => '222']);

    $dosenUser2 = User::factory()->create(['role' => 'Dosen']);
    $teacher2 = Teacher::create(['user_id' => $dosenUser2->id, 'nip' => '333', 'nidn' => '444']);

    $course = Course::create(['code' => 'IF101', 'name' => 'Pemrograman Web', 'credits' => 3, 'semester' => 3]);
    $year = AcademicYear::create(['year' => '2025/2026', 'semester' => 'Ganjil', 'is_active' => true]);

    $class1 = AcademicClass::create([
        'course_id' => $course->id,
        'teacher_id' => $teacher1->id,
        'academic_year_id' => $year->id,
        'name' => 'Kelas Dosen 1',
    ]);

    $class2 = AcademicClass::create([
        'course_id' => $course->id,
        'teacher_id' => $teacher2->id,
        'academic_year_id' => $year->id,
        'name' => 'Kelas Dosen 2',
    ]);

    $response = $this->actingAs($dosenUser1)->get('/dosen/kelas');

    $response->assertStatus(200);
    $response->assertSee('Kelas Dosen 1');
    $response->assertDontSee('Kelas Dosen 2');
});

test('dosen cannot access input nilai page of a class they do not teach', function () {
    $dosenUser1 = User::factory()->create(['role' => 'Dosen']);
    $teacher1 = Teacher::create(['user_id' => $dosenUser1->id, 'nip' => '111', 'nidn' => '222']);

    $dosenUser2 = User::factory()->create(['role' => 'Dosen']);
    $teacher2 = Teacher::create(['user_id' => $dosenUser2->id, 'nip' => '333', 'nidn' => '444']);

    $course = Course::create(['code' => 'IF101', 'name' => 'Pemrograman Web', 'credits' => 3, 'semester' => 3]);
    $year = AcademicYear::create(['year' => '2025/2026', 'semester' => 'Ganjil', 'is_active' => true]);

    $classOfDosen2 = AcademicClass::create([
        'course_id' => $course->id,
        'teacher_id' => $teacher2->id,
        'academic_year_id' => $year->id,
        'name' => 'Kelas Dosen 2',
    ]);

    $response = $this->actingAs($dosenUser1)->get("/dosen/kelas/{$classOfDosen2->id}/input-nilai");

    $response->assertStatus(403);
});

test('dosen can bulk save scores and final grade is calculated correctly', function () {
    $dosenUser = User::factory()->create(['role' => 'Dosen']);
    $teacher = Teacher::create(['user_id' => $dosenUser->id, 'nip' => '111', 'nidn' => '222']);

    $studentUser = User::factory()->create(['role' => 'Mahasiswa']);
    $student = Student::create(['user_id' => $studentUser->id, 'nim' => '2201010001', 'angkatan' => '2022']);

    $course = Course::create(['code' => 'IF101', 'name' => 'Pemrograman Web', 'credits' => 3, 'semester' => 3]);
    $year = AcademicYear::create(['year' => '2025/2026', 'semester' => 'Ganjil', 'is_active' => true]);

    $class = AcademicClass::create([
        'course_id' => $course->id,
        'teacher_id' => $teacher->id,
        'academic_year_id' => $year->id,
        'name' => 'Kelas A',
        'weight_attendance' => 10.00,
        'weight_task' => 20.00,
        'weight_uts' => 30.00,
        'weight_uas' => 40.00,
    ]);

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'class_id' => $class->id,
    ]);

    $response = $this->actingAs($dosenUser)->post("/dosen/kelas/{$class->id}/simpan-nilai", [
        'scores' => [
            $enrollment->id => [
                'attendance' => 90,
                'task' => 85,
                'uts' => 80,
                'uas' => 88,
            ]
        ]
    ]);

    $response->assertRedirect();
    
    // Weighted Final Score:
    // (90 * 10%) + (85 * 20%) + (80 * 30%) + (88 * 40%) = 9.0 + 17.0 + 24.0 + 35.2 = 85.2
    // 85.2 >= 85 -> Grade Letter: A, Quality Point: 4.0
    $this->assertDatabaseHas('grades', [
        'enrollment_id' => $enrollment->id,
        'score_attendance' => 90.00,
        'score_task' => 85.00,
        'score_uts' => 80.00,
        'score_uas' => 88.00,
        'score_final' => 85.20,
        'grade_letter' => 'A',
        'quality_point' => 4.00,
        'is_locked' => false,
    ]);
});

test('dosen can finalize class grades and lock them from editing', function () {
    $dosenUser = User::factory()->create(['role' => 'Dosen']);
    $teacher = Teacher::create(['user_id' => $dosenUser->id, 'nip' => '111', 'nidn' => '222']);

    $studentUser = User::factory()->create(['role' => 'Mahasiswa']);
    $student = Student::create(['user_id' => $studentUser->id, 'nim' => '2201010001', 'angkatan' => '2022']);

    $course = Course::create(['code' => 'IF101', 'name' => 'Pemrograman Web', 'credits' => 3, 'semester' => 3]);
    $year = AcademicYear::create(['year' => '2025/2026', 'semester' => 'Ganjil', 'is_active' => true]);

    $class = AcademicClass::create([
        'course_id' => $course->id,
        'teacher_id' => $teacher->id,
        'academic_year_id' => $year->id,
        'name' => 'Kelas A',
    ]);

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'class_id' => $class->id,
    ]);

    $grade = Grade::create([
        'enrollment_id' => $enrollment->id,
        'score_attendance' => 90,
        'score_task' => 90,
        'score_uts' => 90,
        'score_uas' => 90,
        'score_final' => 90.00,
        'grade_letter' => 'A',
        'quality_point' => 4.0,
        'is_locked' => false,
    ]);

    // Lock grades
    $response = $this->actingAs($dosenUser)->post("/dosen/kelas/{$class->id}/lock-nilai");

    $response->assertRedirect();
    $this->assertTrue($grade->fresh()->is_locked);

    // Try modifying locked grade (should fail)
    $response2 = $this->actingAs($dosenUser)->post("/dosen/kelas/{$class->id}/simpan-nilai", [
        'scores' => [
            $enrollment->id => [
                'attendance' => 50,
                'task' => 50,
                'uts' => 50,
                'uas' => 50,
            ]
        ]
    ]);

    // Grade values should not change
    $this->assertEquals(90.00, (float) $grade->fresh()->score_final);
});
