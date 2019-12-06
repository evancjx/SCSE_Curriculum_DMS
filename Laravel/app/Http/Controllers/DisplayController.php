<?php

namespace App\Http\Controllers;

use App\GraduateAttributes;
use Illuminate\Http\Request;

class DisplayController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function gradattr(){
        return response()
            ->json(GraduateAttributes::all())
            ->header('Content-Type', 'application/json');
    }
}
