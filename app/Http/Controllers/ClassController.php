<?php

namespace App\Http\Controllers;

use App\Models\AcademicClass;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('kelas.index', [
            'title' => 'Kelas Perkuliahan',
            'classes' => AcademicClass::with(['course', 'teacher.user', 'academicYear'])->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kelas.create', [
            'title' => 'Tambah Kelas',
            'courses' => Course::orderBy('name')->get(),
            'teachers' => Teacher::with('user')->get(),
            'academicYears' => AcademicYear::orderBy('year', 'desc')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:teachers,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string',
            'weight_attendance' => 'required|numeric|min:0|max:100',
            'weight_task' => 'required|numeric|min:0|max:100',
            'weight_uts' => 'required|numeric|min:0|max:100',
            'weight_uas' => 'required|numeric|min:0|max:100',
        ], [
            'course_id.required' => 'Mata Kuliah wajib dipilih',
            'course_id.exists' => 'Mata Kuliah tidak valid',
            'teacher_id.required' => 'Dosen Pengampu wajib dipilih',
            'teacher_id.exists' => 'Dosen tidak valid',
            'academic_year_id.required' => 'Tahun Akademik wajib dipilih',
            'academic_year_id.exists' => 'Tahun Akademik tidak valid',
            'name.required' => 'Nama Kelas wajib diisi (e.g. Kelas A)',
            'weight_attendance.required' => 'Bobot Presensi wajib diisi',
            'weight_attendance.numeric' => 'Bobot Presensi harus berupa angka',
            'weight_task.required' => 'Bobot Tugas wajib diisi',
            'weight_task.numeric' => 'Bobot Tugas harus berupa angka',
            'weight_uts.required' => 'Bobot UTS wajib diisi',
            'weight_uts.numeric' => 'Bobot UTS harus berupa angka',
            'weight_uas.required' => 'Bobot UAS wajib diisi',
            'weight_uas.numeric' => 'Bobot UAS harus berupa angka',
        ]);

        // Total weight validation
        $totalWeight = $validate['weight_attendance'] + $validate['weight_task'] + $validate['weight_uts'] + $validate['weight_uas'];
        if ($totalWeight != 100) {
            return back()->withErrors(['total_weight' => 'Total bobot penilaian (Kehadiran + Tugas + UTS + UAS) harus berjumlah 100% (saat ini ' . $totalWeight . '%)'])->withInput();
        }

        DB::beginTransaction();

        try {
            AcademicClass::create($validate);
            DB::commit();
            return to_route('kelas.index')->withSuccess('Kelas Perkuliahan berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return to_route('kelas.create')->withError('Gagal menambahkan data: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcademicClass $kela)
    {
        return view('kelas.edit', [
            'title' => 'Edit Kelas',
            'class' => $kela,
            'courses' => Course::orderBy('name')->get(),
            'teachers' => Teacher::with('user')->get(),
            'academicYears' => AcademicYear::orderBy('year', 'desc')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AcademicClass $kela)
    {
        $validate = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:teachers,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string',
            'weight_attendance' => 'required|numeric|min:0|max:100',
            'weight_task' => 'required|numeric|min:0|max:100',
            'weight_uts' => 'required|numeric|min:0|max:100',
            'weight_uas' => 'required|numeric|min:0|max:100',
        ], [
            'course_id.required' => 'Mata Kuliah wajib dipilih',
            'course_id.exists' => 'Mata Kuliah tidak valid',
            'teacher_id.required' => 'Dosen Pengampu wajib dipilih',
            'teacher_id.exists' => 'Dosen tidak valid',
            'academic_year_id.required' => 'Tahun Akademik wajib dipilih',
            'academic_year_id.exists' => 'Tahun Akademik tidak valid',
            'name.required' => 'Nama Kelas wajib diisi (e.g. Kelas A)',
            'weight_attendance.required' => 'Bobot Presensi wajib diisi',
            'weight_attendance.numeric' => 'Bobot Presensi harus berupa angka',
            'weight_task.required' => 'Bobot Tugas wajib diisi',
            'weight_task.numeric' => 'Bobot Tugas harus berupa angka',
            'weight_uts.required' => 'Bobot UTS wajib diisi',
            'weight_uts.numeric' => 'Bobot UTS harus berupa angka',
            'weight_uas.required' => 'Bobot UAS wajib diisi',
            'weight_uas.numeric' => 'Bobot UAS harus berupa angka',
        ]);

        // Total weight validation
        $totalWeight = $validate['weight_attendance'] + $validate['weight_task'] + $validate['weight_uts'] + $validate['weight_uas'];
        if ($totalWeight != 100) {
            return back()->withErrors(['total_weight' => 'Total bobot penilaian (Kehadiran + Tugas + UTS + UAS) harus berjumlah 100% (saat ini ' . $totalWeight . '%)'])->withInput();
        }

        DB::beginTransaction();

        try {
            $kela->update($validate);
            DB::commit();
            return to_route('kelas.index')->withSuccess('Kelas Perkuliahan berhasil diubah');
        } catch (\Exception $e) {
            DB::rollBack();
            return to_route('kelas.edit', $kela)->withError('Gagal mengubah data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicClass $kela)
    {
        DB::beginTransaction();

        try {
            $kela->delete();
            DB::commit();
            return to_route('kelas.index')->withSuccess('Kelas Perkuliahan berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return to_route('kelas.index')->withError('Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
