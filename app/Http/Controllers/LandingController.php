<?php

namespace App\Http\Controllers;

use App\Models\ManManagement\AppManagement;
use Inertia\Inertia;

class LandingController extends Controller
{
    public function index()
    {
        $apps = AppManagement::all();
        return Inertia::render("Landing/index", ['app' => $apps]);
    }
}
