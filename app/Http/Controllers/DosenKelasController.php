<?php

namespace App\Http\Controllers;

use App\Models\AcademicClass;
use App\Models\Enrollment;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DosenKelasController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        if (!$teacher) {
            abort(403, 'Anda tidak terdaftar sebagai Dosen');
        }

        $classes = AcademicClass::with(['course', 'academicYear', 'enrollments.grade'])
            ->where('teacher_id', $teacher->id)
            ->latest()
            ->get();

        return view('dosen.kelas.index', [
            'title' => 'Kelas Saya',
            'classes' => $classes,
        ]);
    }

    public function inputNilai($class)
    {
        $classModel = AcademicClass::with(['course', 'academicYear'])->findOrFail($class);
        $teacher = Auth::user()->teacher;

        if (!$teacher || $classModel->teacher_id !== $teacher->id) {
            abort(403, 'Anda tidak berhak mengelola kelas ini');
        }

        $enrollments = Enrollment::with(['student.user', 'grade'])
            ->where('class_id', $classModel->id)
            ->get();

        // A class is considered locked if at least one grade in the class is locked
        $isLocked = $enrollments->contains(function ($enrollment) {
            return $enrollment->grade?->is_locked;
        });

        return view('dosen.kelas.input_nilai', [
            'title' => 'Input Nilai - ' . $classModel->name,
            'class' => $classModel,
            'enrollments' => $enrollments,
            'isLocked' => $isLocked,
        ]);
    }

    public function simpanNilai(Request $request, $class)
    {
        $classModel = AcademicClass::findOrFail($class);
        $teacher = Auth::user()->teacher;

        if (!$teacher || $classModel->teacher_id !== $teacher->id) {
            abort(403, 'Anda tidak berhak mengelola kelas ini');
        }

        // Check if grades are locked
        $hasLocked = Grade::whereIn('enrollment_id', Enrollment::where('class_id', $classModel->id)->pluck('id'))
            ->where('is_locked', true)
            ->exists();

        if ($hasLocked) {
            return back()->withError('Nilai kelas ini telah dikunci dan tidak bisa diubah');
        }

        $request->validate([
            'scores' => 'required|array',
            'scores.*.attendance' => 'required|numeric|min:0|max:100',
            'scores.*.task' => 'required|numeric|min:0|max:100',
            'scores.*.uts' => 'required|numeric|min:0|max:100',
            'scores.*.uas' => 'required|numeric|min:0|max:100',
        ], [
            'scores.*.attendance.required' => 'Nilai kehadiran wajib diisi',
            'scores.*.attendance.numeric' => 'Nilai kehadiran harus berupa angka',
            'scores.*.attendance.min' => 'Nilai kehadiran minimal 0',
            'scores.*.attendance.max' => 'Nilai kehadiran maksimal 100',
            'scores.*.task.required' => 'Nilai tugas wajib diisi',
            'scores.*.task.numeric' => 'Nilai tugas harus berupa angka',
            'scores.*.task.min' => 'Nilai tugas minimal 0',
            'scores.*.task.max' => 'Nilai tugas maksimal 100',
            'scores.*.uts.required' => 'Nilai UTS wajib diisi',
            'scores.*.uts.numeric' => 'Nilai UTS harus berupa angka',
            'scores.*.uts.min' => 'Nilai UTS minimal 0',
            'scores.*.uts.max' => 'Nilai UTS maksimal 100',
            'scores.*.uas.required' => 'Nilai UAS wajib diisi',
            'scores.*.uas.numeric' => 'Nilai UAS harus berupa angka',
            'scores.*.uas.min' => 'Nilai UAS minimal 0',
            'scores.*.uas.max' => 'Nilai UAS maksimal 100',
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->scores as $enrollmentId => $scoreData) {
                // Ensure enrollment belongs to this class
                $enrollment = Enrollment::where('id', $enrollmentId)
                    ->where('class_id', $classModel->id)
                    ->firstOrFail();

                $grade = Grade::firstOrNew(['enrollment_id' => $enrollment->id]);

                if ($grade->is_locked) {
                    continue;
                }

                $grade->score_attendance = $scoreData['attendance'];
                $grade->score_task = $scoreData['task'];
                $grade->score_uts = $scoreData['uts'];
                $grade->score_uas = $scoreData['uas'];

                $grade->calculateGrade($classModel);
                $grade->save();
            }

            DB::commit();
            return back()->withSuccess('Nilai berhasil disimpan sebagai draft');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withError('Gagal menyimpan nilai: ' . $e->getMessage());
        }
    }

    public function lockNilai(Request $request, $class)
    {
        $classModel = AcademicClass::findOrFail($class);
        $teacher = Auth::user()->teacher;

        if (!$teacher || $classModel->teacher_id !== $teacher->id) {
            abort(403, 'Anda tidak berhak mengelola kelas ini');
        }

        DB::beginTransaction();

        try {
            $enrollments = Enrollment::where('class_id', $classModel->id)->get();
            foreach ($enrollments as $enrollment) {
                $grade = Grade::firstOrNew(['enrollment_id' => $enrollment->id]);

                if (!$grade->exists) {
                    $grade->score_attendance = 0;
                    $grade->score_task = 0;
                    $grade->score_uts = 0;
                    $grade->score_uas = 0;
                    $grade->calculateGrade($classModel);
                }

                $grade->is_locked = true;
                $grade->save();
            }

            DB::commit();
            return back()->withSuccess('Nilai kelas berhasil difinalisasi dan dikunci');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withError('Gagal memfinalisasi nilai: ' . $e->getMessage());
        }
    }
}
