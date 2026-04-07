<?php

namespace App\Http\Controllers\ManManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    //
    public function index() {
        return Inertia::render('ManManagement/Index');
    }
}
