<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'score_attendance',
        'score_task',
        'score_uts',
        'score_uas',
        'score_final',
        'grade_letter',
        'quality_point',
        'is_locked',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
    ];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Calculate and set the final score, letter grade, and quality point based on class weights.
     */
    public function calculateGrade(AcademicClass $class): void
    {
        $this->score_final = ($this->score_attendance * $class->weight_attendance / 100) +
                             ($this->score_task * $class->weight_task / 100) +
                             ($this->score_uts * $class->weight_uts / 100) +
                             ($this->score_uas * $class->weight_uas / 100);

        if ($this->score_final >= 85) {
            $this->grade_letter = 'A';
            $this->quality_point = 4.0;
        } elseif ($this->score_final >= 80) {
            $this->grade_letter = 'A-';
            $this->quality_point = 3.7;
        } elseif ($this->score_final >= 75) {
            $this->grade_letter = 'B+';
            $this->quality_point = 3.3;
        } elseif ($this->score_final >= 70) {
            $this->grade_letter = 'B';
            $this->quality_point = 3.0;
        } elseif ($this->score_final >= 65) {
            $this->grade_letter = 'B-';
            $this->quality_point = 2.7;
        } elseif ($this->score_final >= 60) {
            $this->grade_letter = 'C+';
            $this->quality_point = 2.3;
        } elseif ($this->score_final >= 55) {
            $this->grade_letter = 'C';
            $this->quality_point = 2.0;
        } elseif ($this->score_final >= 40) {
            $this->grade_letter = 'D';
            $this->quality_point = 1.0;
        } else {
            $this->grade_letter = 'E';
            $this->quality_point = 0.0;
        }
    }
}
