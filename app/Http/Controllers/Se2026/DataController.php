<?php

namespace App\Http\Controllers\Se2026;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DataController extends Controller
{
    //
    public function index() 
    {
        return Inertia::render('Se2026/Dashboard');
    }
}
