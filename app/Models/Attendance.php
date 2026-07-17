<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['enrollment_id', 'meeting_number', 'status', 'date'];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    protected static function booted()
    {
        static::saved(function ($attendance) {
            self::syncAttendanceScore($attendance->enrollment_id);
        });

        static::deleted(function ($attendance) {
            self::syncAttendanceScore($attendance->enrollment_id);
        });
    }

    public static function syncAttendanceScore($enrollmentId)
    {
        $totalMeetings = self::where('enrollment_id', $enrollmentId)->count();
        if ($totalMeetings === 0) {
            $grade = Grade::where('enrollment_id', $enrollmentId)->first();
            if ($grade && !$grade->is_locked) {
                $grade->score_attendance = 0.00;
                $grade->calculateGrade($grade->enrollment->class);
                $grade->save();
            }
            return;
        }

        $presentCount = self::where('enrollment_id', $enrollmentId)
            ->where('status', 'H')
            ->count();

        $score = ($presentCount / $totalMeetings) * 100;

        $enrollment = Enrollment::with('class')->findOrFail($enrollmentId);
        $grade = Grade::firstOrNew(['enrollment_id' => $enrollmentId]);
        
        if (!$grade->is_locked) {
            $grade->score_attendance = $score;
            $grade->calculateGrade($enrollment->class);
            $grade->save();
        }
    }
}
