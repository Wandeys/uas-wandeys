<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'nim', 'angkatan', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Calculate Semester GPA (IPS)
     */
    public function calculateIPS($academicYearId): float
    {
        $enrollments = $this->enrollments()
            ->whereHas('class', function($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            })
            ->whereHas('grade', function($q) {
                $q->where('is_locked', true);
            })->get();

        $totalSks = 0;
        $weightedPoints = 0;

        foreach ($enrollments as $enrollment) {
            $sks = $enrollment->class->course->credits;
            $qualityPoint = $enrollment->grade->quality_point;
            $weightedPoints += ($sks * $qualityPoint);
            $totalSks += $sks;
        }

        return $totalSks > 0 ? round($weightedPoints / $totalSks, 2) : 0.00;
    }

    /**
     * Calculate Cumulative GPA (IPK), filtering out duplicate/repeated courses and selecting the highest grade.
     */
    public function calculateIPK(): float
    {
        $enrollments = $this->enrollments()
            ->whereHas('grade', function($q) {
                $q->where('is_locked', true);
            })->get();

        $bestGrades = [];

        foreach ($enrollments as $enrollment) {
            $course = $enrollment->class->course;
            $courseId = $course->id;
            $sks = $course->credits;
            $qualityPoint = $enrollment->grade->quality_point;

            if (!isset($bestGrades[$courseId]) || $qualityPoint > $bestGrades[$courseId]['quality_point']) {
                $bestGrades[$courseId] = [
                    'credits' => $sks,
                    'quality_point' => $qualityPoint,
                ];
            }
        }

        $totalSks = 0;
        $weightedPoints = 0;

        foreach ($bestGrades as $gradeInfo) {
            $weightedPoints += ($gradeInfo['credits'] * $gradeInfo['quality_point']);
            $totalSks += $gradeInfo['credits'];
        }

        return $totalSks > 0 ? round($weightedPoints / $totalSks, 2) : 0.00;
    }

    /**
     * Calculate Total Passed SKS (SKS Lulus)
     */
    public function calculateTotalSksLulus(): int
    {
        $enrollments = $this->enrollments()
            ->whereHas('grade', function($q) {
                $q->where('is_locked', true);
            })->get();

        $bestGrades = [];

        foreach ($enrollments as $enrollment) {
            $course = $enrollment->class->course;
            $courseId = $course->id;
            $sks = $course->credits;
            $qualityPoint = $enrollment->grade->quality_point;

            if (!isset($bestGrades[$courseId]) || $qualityPoint > $bestGrades[$courseId]['quality_point']) {
                $bestGrades[$courseId] = [
                    'credits' => $sks,
                    'quality_point' => $qualityPoint,
                ];
            }
        }

        $totalSksLulus = 0;
        foreach ($bestGrades as $gradeInfo) {
            if ($gradeInfo['quality_point'] > 0) {
                $totalSksLulus += $gradeInfo['credits'];
            }
        }

        return $totalSksLulus;
    }
}
