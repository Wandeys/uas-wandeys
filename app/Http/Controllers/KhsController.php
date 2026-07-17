<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AcademicYear;
use App\Models\Enrollment;

class KhsController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(404, 'Data Mahasiswa tidak ditemukan.');
        }

        $selectedYearId = $request->query('academic_year_id');

        $academicYears = AcademicYear::orderBy('year', 'desc')->orderBy('semester', 'desc')->get();

        if ($selectedYearId) {
            $selectedYear = AcademicYear::find($selectedYearId);
        } else {
            $selectedYear = AcademicYear::where('is_active', true)->first() 
                ?? AcademicYear::orderBy('year', 'desc')->orderBy('semester', 'desc')->first();
        }

        $selectedYearId = $selectedYear?->id;

        $enrollments = Enrollment::with(['class.course', 'grade'])
            ->where('student_id', $student->id)
            ->whereHas('class', function ($query) use ($selectedYearId) {
                $query->where('academic_year_id', $selectedYearId);
            })
            ->get();

        $ips = $student->calculateIPS($selectedYearId);
        $ipk = $student->calculateIPK();
        $totalSksLulus = $student->calculateTotalSksLulus();

        return view('khs.index', [
            'title' => 'Kartu Hasil Studi (KHS)',
            'ips' => $ips,
            'ipk' => $ipk,
            'totalSksLulus' => $totalSksLulus,
            'enrollments' => $enrollments,
            'academicYears' => $academicYears,
            'selectedYearId' => $selectedYearId,
        ]);
    }

    public function cetak(Request $request)
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(404, 'Data Mahasiswa tidak ditemukan.');
        }

        $selectedYearId = $request->query('academic_year_id');

        if ($selectedYearId) {
            $selectedYear = AcademicYear::find($selectedYearId);
        } else {
            $selectedYear = AcademicYear::where('is_active', true)->first() 
                ?? AcademicYear::orderBy('year', 'desc')->orderBy('semester', 'desc')->first();
        }

        if (!$selectedYear) {
            abort(404, 'Tahun Akademik tidak ditemukan.');
        }

        $selectedYearId = $selectedYear->id;

        $enrollments = Enrollment::with(['class.course', 'grade'])
            ->where('student_id', $student->id)
            ->whereHas('class', function ($query) use ($selectedYearId) {
                $query->where('academic_year_id', $selectedYearId);
            })
            ->get();

        $ips = $student->calculateIPS($selectedYearId);
        $ipk = $student->calculateIPK();
        $totalSksLulus = $student->calculateTotalSksLulus();

        return view('khs.cetak', [
            'student' => $student,
            'selectedYear' => $selectedYear,
            'enrollments' => $enrollments,
            'ips' => $ips,
            'ipk' => $ipk,
            'totalSksLulus' => $totalSksLulus,
        ]);
    }
}
