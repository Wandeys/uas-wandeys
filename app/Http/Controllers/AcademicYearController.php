<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('tahun-akademik.index', [
            'title' => 'Tahun Akademik',
            'years' => AcademicYear::latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tahun-akademik.create', [
            'title' => 'Tambah Tahun Akademik',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'year' => 'required',
            'semester' => 'required|in:Ganjil,Genap',
            'is_active' => 'nullable|in:0,1',
        ], [
            'year.required' => 'Tahun Akademik wajib diisi (e.g. 2025/2026)',
            'semester.required' => 'Semester wajib diisi',
            'semester.in' => 'Semester harus berupa Ganjil atau Genap',
        ]);

        DB::beginTransaction();

        try {
            $is_active = $request->has('is_active') && $request->is_active == '1';

            $academicYear = AcademicYear::create([
                'year' => $validate['year'],
                'semester' => $validate['semester'],
                'is_active' => $is_active,
            ]);

            // Enforce single active academic year constraint
            if ($is_active) {
                AcademicYear::where('id', '!=', $academicYear->id)->update(['is_active' => false]);
            }

            DB::commit();
            return to_route('tahun-akademik.index')->withSuccess('Tahun Akademik berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return to_route('tahun-akademik.create')->withError('Gagal menambahkan data: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcademicYear $tahunAkademik)
    {
        return view('tahun-akademik.edit', [
            'title' => 'Edit Tahun Akademik',
            'academicYear' => $tahunAkademik,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AcademicYear $tahunAkademik)
    {
        $validate = $request->validate([
            'year' => 'required',
            'semester' => 'required|in:Ganjil,Genap',
            'is_active' => 'nullable|in:0,1',
        ], [
            'year.required' => 'Tahun Akademik wajib diisi (e.g. 2025/2026)',
            'semester.required' => 'Semester wajib diisi',
            'semester.in' => 'Semester harus berupa Ganjil atau Genap',
        ]);

        DB::beginTransaction();

        try {
            $is_active = $request->has('is_active') && $request->is_active == '1';

            $tahunAkademik->update([
                'year' => $validate['year'],
                'semester' => $validate['semester'],
                'is_active' => $is_active,
            ]);

            // Enforce single active academic year constraint
            if ($is_active) {
                AcademicYear::where('id', '!=', $tahunAkademik->id)->update(['is_active' => false]);
            }

            DB::commit();
            return to_route('tahun-akademik.index')->withSuccess('Tahun Akademik berhasil diubah');
        } catch (\Exception $e) {
            DB::rollBack();
            return to_route('tahun-akademik.edit', $tahunAkademik)->withError('Gagal mengubah data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicYear $tahunAkademik)
    {
        DB::beginTransaction();

        try {
            $tahunAkademik->delete();
            DB::commit();
            return to_route('tahun-akademik.index')->withSuccess('Tahun Akademik berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return to_route('tahun-akademik.index')->withError('Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
