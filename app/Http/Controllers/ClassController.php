<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        return response('Class Index (Placeholder)', 200);
    }
}
