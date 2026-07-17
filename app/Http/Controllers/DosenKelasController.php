<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DosenKelasController extends Controller
{
    public function index()
    {
        return response('Dosen Kelas Index (Placeholder)', 200);
    }

    public function inputNilai($class)
    {
        return response('Dosen Input Nilai (Placeholder) for class: ' . $class, 200);
    }

    public function simpanNilai(Request $request, $class)
    {
        return response('Dosen Simpan Nilai (Placeholder) for class: ' . $class, 200);
    }

    public function lockNilai(Request $request, $class)
    {
        return response('Dosen Lock Nilai (Placeholder) for class: ' . $class, 200);
    }
}
