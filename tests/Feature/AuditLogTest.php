<?php

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\AcademicYear;
use App\Models\AcademicClass;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\AuditLog;

test('grade operations (create, update, lock) trigger audit logging', function () {
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

    // 1. Create Grade
    $grade = Grade::create([
        'enrollment_id' => $enrollment->id,
        'score_attendance' => 90.00,
        'score_task' => 80.00,
        'score_uts' => 85.00,
        'score_uas' => 90.00,
        'score_final' => 86.50,
        'grade_letter' => 'A',
        'is_locked' => false,
    ]);

    $this->assertDatabaseHas('audit_logs', [
        'action' => 'CREATE_GRADE',
        'description' => 'Membuat nilai mahasiswa NIM: 2201010001 di kelas: Kelas A',
    ]);

    // 2. Update Grade (Act as Dosen, submit scores)
    $this->actingAs($dosenUser)->post("/dosen/kelas/{$class->id}/simpan-nilai", [
        'scores' => [
            $enrollment->id => [
                'attendance' => 95.00,
                'task' => 85.00,
                'uts' => 90.00,
                'uas' => 95.00,
            ]
        ]
    ]);

    $this->assertDatabaseHas('audit_logs', [
        'action' => 'UPDATE_GRADE',
        'description' => 'Mengubah nilai mahasiswa NIM: 2201010001 di kelas: Kelas A',
    ]);

    // 3. Lock Grade (Act as Dosen, lock/finalisasi)
    $this->actingAs($dosenUser)->post("/dosen/kelas/{$class->id}/lock-nilai");

    $this->assertDatabaseHas('audit_logs', [
        'action' => 'LOCK_GRADE',
        'description' => 'Finalisasi & Kunci nilai mahasiswa NIM: 2201010001 di kelas: Kelas A',
    ]);
});

test('password updates and user deletions trigger audit logging', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $studentUser = User::factory()->create(['role' => 'Mahasiswa', 'email' => 'student_test@simana.com']);
    $student = Student::create(['user_id' => $studentUser->id, 'nim' => '2201010002', 'angkatan' => '2022']);

    // 1. Password update (admin updating student)
    $this->actingAs($admin)->put("/mahasiswa/{$student->id}", [
        'name' => $studentUser->name,
        'email' => 'student_test@simana.com',
        'nim' => '2201010002',
        'angkatan' => '2022',
        'status' => 'active',
        'password' => 'newpassword123',
        'passwordconfirm' => 'newpassword123',
    ]);

    $this->assertDatabaseHas('audit_logs', [
        'action' => 'CHANGE_PASSWORD',
        'description' => 'Mengubah password user: student_test@simana.com',
    ]);

    // 2. User deletion
    $this->actingAs($admin)->delete("/mahasiswa/{$student->id}");

    $this->assertDatabaseHas('audit_logs', [
        'action' => 'DELETE_USER',
        'description' => 'Menghapus user: student_test@simana.com (Nama: ' . $studentUser->name . ', Role: Mahasiswa)',
    ]);
});

test('failed login attempts trigger audit logging', function () {
    $response = $this->post('/authenticate', [
        'email' => 'nonexistent@simana.com',
        'password' => 'wrongpassword',
    ]);

    $this->assertDatabaseHas('audit_logs', [
        'action' => 'LOGIN_FAILED',
        'description' => 'Gagal login menggunakan email: nonexistent@simana.com',
    ]);
});

test('only Superadmin can access Audit Logs view', function () {
    $superadmin = User::factory()->create(['role' => 'Superadmin']);
    $admin = User::factory()->create(['role' => 'Admin']);
    $dosen = User::factory()->create(['role' => 'Dosen']);
    $mahasiswa = User::factory()->create(['role' => 'Mahasiswa']);

    // 1. Superadmin can access
    $response1 = $this->actingAs($superadmin)->get('/audit-logs');
    $response1->assertStatus(200);
    $response1->assertSee('Audit Trail / Log Aktivitas Sistem');

    // 2. Others cannot access
    $response2 = $this->actingAs($admin)->get('/audit-logs');
    $response2->assertStatus(401);

    $response3 = $this->actingAs($dosen)->get('/audit-logs');
    $response3->assertStatus(401);

    $response4 = $this->actingAs($mahasiswa)->get('/audit-logs');
    $response4->assertStatus(401);
});
