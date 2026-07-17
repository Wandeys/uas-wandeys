<?php

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\AcademicYear;
use App\Models\AcademicClass;
use App\Models\Enrollment;
use App\Models\Grade;

test('admin dashboard displays institutional stats and cohort IPK averages', function () {
    $admin = User::factory()->create(['role' => 'Admin']);

    // Create a student in angkatan 2022
    $studentUser = User::factory()->create(['role' => 'Mahasiswa']);
    $student = Student::create(['user_id' => $studentUser->id, 'nim' => '2201010001', 'angkatan' => '2022']);

    $response = $this->actingAs($admin)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee('Rata-rata IPK per Angkatan');
    $response->assertSee('2022');
});

test('dosen dashboard displays class stats and global grade distribution', function () {
    $dosenUser = User::factory()->create(['role' => 'Dosen']);
    $teacher = Teacher::create(['user_id' => $dosenUser->id, 'nip' => '111', 'nidn' => '222']);

    $response = $this->actingAs($dosenUser)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee('Kelas Yang Diampu');
    $response->assertSee('Distribusi Nilai Mahasiswa (Semua Kelas)');
});

test('mahasiswa dashboard displays IPS trend line chart', function () {
    $studentUser = User::factory()->create(['role' => 'Mahasiswa']);
    $student = Student::create([
        'user_id' => $studentUser->id,
        'nim' => '2201010001',
        'angkatan' => '2022',
        'status' => 'active',
    ]);

    $response = $this->actingAs($studentUser)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee('Tren IPS (Indeks Prestasi Semester)');
});

test('authorized users can download class grades PDF report', function () {
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

    // 1. Dosen who teaches the class can download
    $response1 = $this->actingAs($dosenUser)->get("/kelas/{$class->id}/download-pdf");
    $response1->assertStatus(200);
    $response1->assertHeader('content-type', 'application/pdf');

    // 2. Admin can download
    $admin = User::factory()->create(['role' => 'Admin']);
    $response2 = $this->actingAs($admin)->get("/kelas/{$class->id}/download-pdf");
    $response2->assertStatus(200);
    $response2->assertHeader('content-type', 'application/pdf');
});

test('unauthorized users cannot download class grades PDF report', function () {
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

    // 1. Dosen who doesn't teach the class cannot download
    $response1 = $this->actingAs($dosenUser2)->get("/kelas/{$class->id}/download-pdf");
    $response1->assertStatus(403);

    // 2. Mahasiswa cannot download
    $studentUser = User::factory()->create(['role' => 'Mahasiswa']);
    $student = Student::create([
        'user_id' => $studentUser->id,
        'nim' => '2201010001',
        'angkatan' => '2022',
        'status' => 'active',
    ]);
    $response2 = $this->actingAs($studentUser)->get("/kelas/{$class->id}/download-pdf");
    $response2->assertStatus(403);
});
