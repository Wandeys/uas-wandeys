<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('matakuliah.index', [
            'title' => 'Mata Kuliah',
            'courses' => Course::latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('matakuliah.create', [
            'title' => 'Tambah Mata Kuliah',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'code' => 'required|unique:courses,code',
            'name' => 'required',
            'credits' => 'required|integer|min:1',
            'semester' => 'required|integer|min:1|max:8',
        ], [
            'code.required' => 'Kode wajib diisi',
            'code.unique' => 'Kode sudah terdaftar',
            'name.required' => 'Nama Mata Kuliah wajib diisi',
            'credits.required' => 'SKS wajib diisi',
            'credits.integer' => 'SKS harus berupa angka',
            'credits.min' => 'SKS minimal 1',
            'semester.required' => 'Semester wajib diisi',
            'semester.integer' => 'Semester harus berupa angka',
            'semester.min' => 'Semester minimal 1',
            'semester.max' => 'Semester maksimal 8',
        ]);

        DB::beginTransaction();

        try {
            Course::create($validate);
            DB::commit();
            return to_route('matakuliah.index')->withSuccess('Data Mata Kuliah berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return to_route('matakuliah.create')->withError('Gagal menambahkan data: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $matakuliah)
    {
        return view('matakuliah.edit', [
            'title' => 'Edit Mata Kuliah',
            'course' => $matakuliah,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $matakuliah)
    {
        $validate = $request->validate([
            'code' => 'required|unique:courses,code,' . $matakuliah->id,
            'name' => 'required',
            'credits' => 'required|integer|min:1',
            'semester' => 'required|integer|min:1|max:8',
        ], [
            'code.required' => 'Kode wajib diisi',
            'code.unique' => 'Kode sudah terdaftar',
            'name.required' => 'Nama Mata Kuliah wajib diisi',
            'credits.required' => 'SKS wajib diisi',
            'credits.integer' => 'SKS harus berupa angka',
            'credits.min' => 'SKS minimal 1',
            'semester.required' => 'Semester wajib diisi',
            'semester.integer' => 'Semester harus berupa angka',
            'semester.min' => 'Semester minimal 1',
            'semester.max' => 'Semester maksimal 8',
        ]);

        DB::beginTransaction();

        try {
            $matakuliah->update($validate);
            DB::commit();
            return to_route('matakuliah.index')->withSuccess('Data Mata Kuliah berhasil diubah');
        } catch (\Exception $e) {
            DB::rollBack();
            return to_route('matakuliah.edit', $matakuliah)->withError('Gagal mengubah data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $matakuliah)
    {
        DB::beginTransaction();

        try {
            $matakuliah->delete();
            DB::commit();
            return to_route('matakuliah.index')->withSuccess('Data Mata Kuliah berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return to_route('matakuliah.index')->withError('Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
