<?php

use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Course;
use App\Models\AcademicYear;
use App\Models\AcademicClass;

test('admin can create dosen with a transactional user account', function () {
    $admin = User::factory()->create(['role' => 'Admin']);

    $response = $this->actingAs($admin)->post('/dosen', [
        'name' => 'Dosen Baru',
        'email' => 'dosenbaru@gmail.com',
        'password' => 'password123',
        'passwordconfirm' => 'password123',
        'nip' => '999888777666',
        'nidn' => '88877766',
        'gelar' => 'M.Kom.',
    ]);

    $response->assertRedirect('/dosen');

    // Assert User was created
    $this->assertDatabaseHas('users', [
        'email' => 'dosenbaru@gmail.com',
        'role' => 'Dosen',
    ]);

    // Assert Teacher was created linked to User
    $user = User::where('email', 'dosenbaru@gmail.com')->first();
    $this->assertDatabaseHas('teachers', [
        'user_id' => $user->id,
        'nip' => '999888777666',
    ]);
});

test('deleting a dosen deletes the associated user and teacher record', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    
    $user = User::factory()->create(['role' => 'Dosen']);
    $teacher = Teacher::create([
        'user_id' => $user->id,
        'nip' => '1111111111',
        'nidn' => '2222222',
        'gelar' => 'Dr.',
    ]);

    $response = $this->actingAs($admin)->delete("/dosen/{$teacher->id}");

    $response->assertRedirect('/dosen');

    // Assert both are gone
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
    $this->assertDatabaseMissing('teachers', ['id' => $teacher->id]);
});

test('admin can create mahasiswa with a transactional user account', function () {
    $admin = User::factory()->create(['role' => 'Admin']);

    $response = $this->actingAs($admin)->post('/mahasiswa', [
        'name' => 'Mhs Baru',
        'email' => 'mhsbaru@gmail.com',
        'password' => 'password123',
        'passwordconfirm' => 'password123',
        'nim' => '2401019999',
        'angkatan' => '2024',
        'status' => 'active',
    ]);

    $response->assertRedirect('/mahasiswa');

    // Assert User was created
    $this->assertDatabaseHas('users', [
        'email' => 'mhsbaru@gmail.com',
        'role' => 'Mahasiswa',
    ]);

    // Assert Student was created linked to User
    $user = User::where('email', 'mhsbaru@gmail.com')->first();
    $this->assertDatabaseHas('students', [
        'user_id' => $user->id,
        'nim' => '2401019999',
    ]);
});

test('deleting a mahasiswa deletes the associated user and student record', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    
    $user = User::factory()->create(['role' => 'Mahasiswa']);
    $student = Student::create([
        'user_id' => $user->id,
        'nim' => '2401018888',
        'angkatan' => '2024',
        'status' => 'active',
    ]);

    $response = $this->actingAs($admin)->delete("/mahasiswa/{$student->id}");

    $response->assertRedirect('/mahasiswa');

    // Assert both are gone
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
    $this->assertDatabaseMissing('students', ['id' => $student->id]);
});

test('academic year active status constraint: only one active at a time', function () {
    $admin = User::factory()->create(['role' => 'Admin']);

    $year1 = AcademicYear::create([
        'year' => '2025/2026',
        'semester' => 'Ganjil',
        'is_active' => true,
    ]);

    $year2 = AcademicYear::create([
        'year' => '2025/2026',
        'semester' => 'Genap',
        'is_active' => false,
    ]);

    // Activating year2 should deactivate year1
    $response = $this->actingAs($admin)->put("/tahun-akademik/{$year2->id}", [
        'year' => '2025/2026',
        'semester' => 'Genap',
        'is_active' => '1',
    ]);

    $response->assertRedirect('/tahun-akademik');

    // Assert statuses
    $this->assertTrue($year2->fresh()->is_active);
    $this->assertFalse($year1->fresh()->is_active);
});

test('class total weight validation sum must be exactly 100', function () {
    $admin = User::factory()->create(['role' => 'Admin']);

    $course = Course::create(['code' => 'IF999', 'name' => 'Test Course', 'credits' => 3, 'semester' => 1]);
    $user = User::factory()->create(['role' => 'Dosen']);
    $teacher = Teacher::create(['user_id' => $user->id, 'nip' => '111', 'nidn' => '222']);
    $year = AcademicYear::create(['year' => '2025/2026', 'semester' => 'Ganjil', 'is_active' => true]);

    // Try creating class with total weight 90% (should fail)
    $response = $this->actingAs($admin)->post('/kelas', [
        'course_id' => $course->id,
        'teacher_id' => $teacher->id,
        'academic_year_id' => $year->id,
        'name' => 'Kelas X',
        'weight_attendance' => 10,
        'weight_task' => 20,
        'weight_uts' => 30,
        'weight_uas' => 30, // Total = 90%
    ]);

    $response->assertSessionHasErrors(['total_weight']);
    $this->assertDatabaseMissing('classes', ['name' => 'Kelas X']);

    // Try creating class with total weight 100% (should pass)
    $response2 = $this->actingAs($admin)->post('/kelas', [
        'course_id' => $course->id,
        'teacher_id' => $teacher->id,
        'academic_year_id' => $year->id,
        'name' => 'Kelas Y',
        'weight_attendance' => 10,
        'weight_task' => 20,
        'weight_uts' => 30,
        'weight_uas' => 40, // Total = 100%
    ]);

    $response2->assertRedirect('/kelas');
    $this->assertDatabaseHas('classes', ['name' => 'Kelas Y']);
});
