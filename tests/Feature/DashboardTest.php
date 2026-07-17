<?php

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\AcademicYear;
use App\Models\AcademicClass;
use App\Models\Enrollment;
use App\Models\Grade;

test('mahasiswa dashboard displays SKS, IPK, and IPS metrics', function () {
    $studentUser = User::factory()->create(['role' => 'Mahasiswa']);
    $student = Student::create(['user_id' => $studentUser->id, 'nim' => '2201010001', 'angkatan' => '2022']);

    $response = $this->actingAs($studentUser)->get('/dashboard');
    $response->assertStatus(200);
    $response->assertSee('SKS KUMULATIF');
    $response->assertSee('IPK (INDEKS PRESTASI KUMULATIF)');
    $response->assertSee('IPS SEMESTER TERAKHIR');
    $response->assertSee('Tren IPS (Indeks Prestasi Semester)');
});

test('dosen dashboard displays classes, enrolled students, advisees, and schedules', function () {
    $dosenUser = User::factory()->create(['role' => 'Dosen']);
    $teacher = Teacher::create(['user_id' => $dosenUser->id, 'nip' => '111', 'nidn' => '222']);

    $response = $this->actingAs($dosenUser)->get('/dashboard');
    $response->assertStatus(200);
    $response->assertSee('Kelas Yang Diampu');
    $response->assertSee('Total Mahasiswa Terdaftar');
    $response->assertSee('Mahasiswa Bimbingan');
    $response->assertSee('Jadwal / Kelas Aktif Semester Ini');
    $response->assertSee('Kelas Memerlukan Input Nilai');
});

test('admin dashboard displays institutional stats and active period', function () {
    $adminUser = User::factory()->create(['role' => 'Admin']);

    $response = $this->actingAs($adminUser)->get('/dashboard');
    $response->assertStatus(200);
    $response->assertSee('Mahasiswa Aktif');
    $response->assertSee('Dosen Aktif');
    $response->assertSee('Rata-rata IPK');
    $response->assertSee('PERIODE AKTIF');
});

test('switch user works correctly with session safety', function () {
    $superadmin = User::factory()->create(['role' => 'Superadmin']);
    $studentUser = User::factory()->create(['role' => 'Mahasiswa']);
    $student = Student::create(['user_id' => $studentUser->id, 'nim' => '2201010001', 'angkatan' => '2022']);

    // 1. Superadmin switches to Student
    $response = $this->actingAs($superadmin)->post('/switch-user', [
        'user_id' => $studentUser->id,
    ]);

    $response->assertRedirect();
    $this->assertEquals($studentUser->id, auth()->id());
    $this->assertTrue(session()->has('original_user_id'));
    $this->assertEquals($superadmin->id, session('original_user_id'));

    // 2. Switched Student switches back to Superadmin
    $response2 = $this->actingAs($studentUser)
        ->withSession(['original_user_id' => $superadmin->id])
        ->post('/switch-user', [
            'user_id' => $superadmin->id,
        ]);

    $response2->assertRedirect();
    $this->assertEquals($superadmin->id, auth()->id());
    $this->assertFalse(session()->has('original_user_id'));

    // 3. Regular student trying to switch user receives 403
    $otherStudent = User::factory()->create(['role' => 'Mahasiswa']);
    $otherStudentObj = Student::create(['user_id' => $otherStudent->id, 'nim' => '2201010002', 'angkatan' => '2022']);

    $response3 = $this->actingAs($otherStudent)->post('/switch-user', [
        'user_id' => $superadmin->id,
    ]);
    $response3->assertStatus(403);
});
