<?php

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\AcademicYear;
use App\Models\AcademicClass;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Attendance;

test('dosen can access their class attendance page', function () {
    $dosenUser = User::factory()->create(['role' => 'Dosen']);
    $teacher = Teacher::create(['user_id' => $dosenUser->id, 'nip' => '111', 'nidn' => '222']);

    $course = Course::create(['code' => 'IF101', 'name' => 'Pemrograman Dasar', 'credits' => 3, 'semester' => 1]);
    $year = AcademicYear::create(['year' => '2024/2025', 'semester' => 'Ganjil', 'is_active' => true]);

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

    $response = $this->actingAs($dosenUser)->get("/dosen/kelas/{$class->id}/presensi");
    $response->assertStatus(200);
    $response->assertSee('Lembar Presensi - Pertemuan 1');
});

test('dosen cannot access another dosen class attendance page', function () {
    $dosenUser1 = User::factory()->create(['role' => 'Dosen']);
    $teacher1 = Teacher::create(['user_id' => $dosenUser1->id, 'nip' => '111', 'nidn' => '222']);

    $dosenUser2 = User::factory()->create(['role' => 'Dosen']);
    $teacher2 = Teacher::create(['user_id' => $dosenUser2->id, 'nip' => '333', 'nidn' => '444']);

    $course = Course::create(['code' => 'IF101', 'name' => 'Pemrograman Dasar', 'credits' => 3, 'semester' => 1]);
    $year = AcademicYear::create(['year' => '2024/2025', 'semester' => 'Ganjil', 'is_active' => true]);

    $class = AcademicClass::create([
        'course_id' => $course->id,
        'teacher_id' => $teacher1->id,
        'academic_year_id' => $year->id,
        'name' => 'Kelas A',
        'weight_attendance' => 10.00,
        'weight_task' => 20.00,
        'weight_uts' => 30.00,
        'weight_uas' => 40.00,
    ]);

    $response = $this->actingAs($dosenUser2)->get("/dosen/kelas/{$class->id}/presensi");
    $response->assertStatus(403);
});

test('dosen can submit attendance and it syncs to Grade', function () {
    $dosenUser = User::factory()->create(['role' => 'Dosen']);
    $teacher = Teacher::create(['user_id' => $dosenUser->id, 'nip' => '111', 'nidn' => '222']);

    $course = Course::create(['code' => 'IF101', 'name' => 'Pemrograman Dasar', 'credits' => 3, 'semester' => 1]);
    $year = AcademicYear::create(['year' => '2024/2025', 'semester' => 'Ganjil', 'is_active' => true]);

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

    $studentUser = User::factory()->create(['role' => 'Mahasiswa']);
    $student = Student::create(['user_id' => $studentUser->id, 'nim' => '2201010001', 'angkatan' => '2022']);

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'class_id' => $class->id,
        'status' => 'approved',
    ]);

    // Submit meeting 1 as Present (H)
    $response = $this->actingAs($dosenUser)->post("/dosen/kelas/{$class->id}/presensi/simpan", [
        'meeting_number' => 1,
        'date' => '2026-07-17',
        'attendances' => [
            $enrollment->id => 'H',
        ],
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('attendances', [
        'enrollment_id' => $enrollment->id,
        'meeting_number' => 1,
        'status' => 'H',
    ]);

    // Verify Grade is automatically created/updated
    $grade = Grade::where('enrollment_id', $enrollment->id)->first();
    expect($grade)->not->toBeNull();
    expect((float)$grade->score_attendance)->toBe(100.00);

    // Submit meeting 2 as Absent (A)
    $response2 = $this->actingAs($dosenUser)->post("/dosen/kelas/{$class->id}/presensi/simpan", [
        'meeting_number' => 2,
        'date' => '2026-07-24',
        'attendances' => [
            $enrollment->id => 'A',
        ],
    ]);

    $response2->assertRedirect();
    $grade->refresh();
    expect((float)$grade->score_attendance)->toBe(50.00);
});

test('dosen cannot submit attendance if class grades are locked', function () {
    $dosenUser = User::factory()->create(['role' => 'Dosen']);
    $teacher = Teacher::create(['user_id' => $dosenUser->id, 'nip' => '111', 'nidn' => '222']);

    $course = Course::create(['code' => 'IF101', 'name' => 'Pemrograman Dasar', 'credits' => 3, 'semester' => 1]);
    $year = AcademicYear::create(['year' => '2024/2025', 'semester' => 'Ganjil', 'is_active' => true]);

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

    $studentUser = User::factory()->create(['role' => 'Mahasiswa']);
    $student = Student::create(['user_id' => $studentUser->id, 'nim' => '2201010001', 'angkatan' => '2022']);

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'class_id' => $class->id,
        'status' => 'approved',
    ]);

    // Lock the grades
    Grade::create([
        'enrollment_id' => $enrollment->id,
        'score_attendance' => 100.00,
        'is_locked' => true,
    ]);

    // Attempt to submit attendance
    $response = $this->actingAs($dosenUser)->post("/dosen/kelas/{$class->id}/presensi/simpan", [
        'meeting_number' => 1,
        'date' => '2026-07-17',
        'attendances' => [
            $enrollment->id => 'H',
        ],
    ]);

    $response->assertSessionHas('error');
    $this->assertDatabaseMissing('attendances', [
        'enrollment_id' => $enrollment->id,
        'meeting_number' => 1,
    ]);
});
