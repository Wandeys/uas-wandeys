<?php

namespace App\Http\Controllers;

use App\Models\AcademicClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class GradeReportController extends Controller
{
    public function downloadClassPdf($classId)
    {
        $user = Auth::user();
        
        $class = AcademicClass::with([
            'course',
            'teacher.user',
            'academicYear',
            'enrollments.student.user',
            'enrollments.grade'
        ])->findOrFail($classId);

        // Authorization checks
        if ($user->role === 'Dosen') {
            if ($class->teacher_id !== $user->teacher?->id) {
                abort(403, 'Anda tidak berhak mengunduh laporan kelas ini.');
            }
        } elseif ($user->role !== 'Superadmin' && $user->role !== 'Admin') {
            abort(403, 'Akses ditolak.');
        }

        // Load the view and parse into PDF
        $pdf = Pdf::loadView('reports.class_grades', compact('class'));

        // Return download response
        return $pdf->download("rekap_nilai_{$class->course->code}_{$class->name}.pdf");
    }
}
