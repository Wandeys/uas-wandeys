<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AcademicClass;
use App\Notifications\GradeReleasedNotification;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = User::where('role', 'Mahasiswa')->get();
        $class = AcademicClass::with('course')->first();

        if ($students->isEmpty() || !$class) {
            return;
        }

        // Send a mock GradeReleasedNotification to all student users
        foreach ($students as $student) {
            $student->notify(new GradeReleasedNotification($class));
        }
    }
}
