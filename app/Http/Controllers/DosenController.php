<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DosenController extends Controller
{
    public function index()
    {
        return response('Dosen Index (Placeholder)', 200);
    }
}
