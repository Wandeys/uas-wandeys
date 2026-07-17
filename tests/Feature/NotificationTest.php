<?php

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\AcademicYear;
use App\Models\AcademicClass;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Notifications\GradeReleasedNotification;
use Illuminate\Support\Facades\Notification;

test('finalizing grades dispatches GradeReleasedNotification to students', function () {
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

    // Seed a draft grade first
    Grade::create([
        'enrollment_id' => $enrollment->id,
        'score_attendance' => 90.00,
        'score_task' => 85.00,
        'score_uts' => 80.00,
        'score_uas' => 85.00,
        'score_final' => 84.50,
        'grade_letter' => 'A-',
        'is_locked' => false,
    ]);

    // Lock grades as Dosen
    $response = $this->actingAs($dosenUser)->post("/dosen/kelas/{$class->id}/lock-nilai");

    $response->assertRedirect();
    
    // Verify notification is created in database
    $studentUser->refresh();
    expect($studentUser->unreadNotifications->count())->toBe(1);
    
    $notification = $studentUser->unreadNotifications->first();
    expect($notification->type)->toBe(GradeReleasedNotification::class);
    expect($notification->data['title'])->toBe('Nilai Baru Dirilis');
    expect($notification->data['message'])->toContain('Pemrograman Dasar');
});

test('students can mark a notification as read and redirect', function () {
    $dosenUser = User::factory()->create(['role' => 'Dosen']);
    $teacher = Teacher::create(['user_id' => $dosenUser->id, 'nip' => '111', 'nidn' => '222']);

    $studentUser = User::factory()->create(['role' => 'Mahasiswa']);
    $student = Student::create(['user_id' => $studentUser->id, 'nim' => '2201010001', 'angkatan' => '2022']);

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

    $studentUser->notify(new GradeReleasedNotification($class));

    $notification = $studentUser->unreadNotifications->first();
    expect($notification)->not->toBeNull();

    $response = $this->actingAs($studentUser)->get("/notifications/{$notification->id}/read");
    
    $response->assertRedirect(route('khs.index'));
    
    $studentUser->refresh();
    expect($studentUser->unreadNotifications->count())->toBe(0);
    expect($studentUser->notifications()->first()->read_at)->not->toBeNull();
});

test('students can mark all notifications as read', function () {
    $dosenUser = User::factory()->create(['role' => 'Dosen']);
    $teacher = Teacher::create(['user_id' => $dosenUser->id, 'nip' => '333', 'nidn' => '444']);

    $studentUser = User::factory()->create(['role' => 'Mahasiswa']);
    $student = Student::create(['user_id' => $studentUser->id, 'nim' => '2201010001', 'angkatan' => '2022']);

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

    $studentUser->notify(new GradeReleasedNotification($class));
    $studentUser->notify(new GradeReleasedNotification($class));

    expect($studentUser->unreadNotifications->count())->toBe(2);

    $response = $this->actingAs($studentUser)->post('/notifications/read-all');
    
    $response->assertRedirect();
    
    $studentUser->refresh();
    expect($studentUser->unreadNotifications->count())->toBe(0);
});
