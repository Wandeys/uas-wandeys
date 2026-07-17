<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\Attendance;
use App\Models\AcademicClass;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = AcademicClass::all();
        if ($classes->isEmpty()) {
            return;
        }

        $startDate = Carbon::now()->subWeeks(16);

        foreach ($classes as $class) {
            $classEnrollments = Enrollment::where('class_id', $class->id)->get();
            if ($classEnrollments->isEmpty()) {
                continue;
            }

            for ($meeting = 1; $meeting <= 16; $meeting++) {
                $meetingDate = $startDate->copy()->addWeeks($meeting)->toDateString();

                foreach ($classEnrollments as $enrollment) {
                    // Randomly assign attendance with high probability of 'H' (Hadir)
                    $statuses = ['H', 'H', 'H', 'H', 'H', 'H', 'S', 'I', 'A'];
                    $status = $statuses[array_rand($statuses)];

                    Attendance::create([
                        'enrollment_id' => $enrollment->id,
                        'meeting_number' => $meeting,
                        'status' => $status,
                        'date' => $meetingDate,
                    ]);
                }
            }
        }
    }
}
