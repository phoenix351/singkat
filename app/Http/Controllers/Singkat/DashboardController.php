<?php

namespace App\Http\Controllers\Singkat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pegawai;

use App\Models\UnitKerja;
use App\Models\User;
// use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'viewer') {
            return redirect('/kelola-pak/' . $user->pegawai_id);
        }
        $jumlahPegawai = Pegawai::count();
        $totalUsers = User::count();
        $unitKerja = UnitKerja::count();

        return Inertia::render('Singkat/Dashboard/Dashboard', [
            "jumlahPegawai" => $jumlahPegawai,
            "users" => $totalUsers,
            "unitKerja" => $unitKerja,
        ]);
    }
}
