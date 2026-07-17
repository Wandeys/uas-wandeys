<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KhsController extends Controller
{
    public function index()
    {
        return response('KHS Index (Placeholder)', 200);
    }

    public function cetak()
    {
        return response('KHS Cetak (Placeholder)', 200);
    }
}
