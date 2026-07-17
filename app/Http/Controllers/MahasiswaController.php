<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    public function index()
    {
        return response('Mahasiswa Index (Placeholder)', 200);
    }
}
