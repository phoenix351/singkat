<?php

namespace App\Http\Controllers\ManManagement;

use App\Http\Controllers\Controller;
use App\Models\ManManagement\Pegawai;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    //
    public function index() {
        $pegawai = Pegawai::get();
        return Inertia::render('ManManagement/Index', ['pegawai' => $pegawai]);
    }
}
