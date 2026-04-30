<?php

namespace App\Http\Controllers\Simple;

use App\Http\Controllers\Controller;
use App\Models\ManManagement\AnggotaTimKerja;
use App\Models\Simple\Lembur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LemburController extends Controller
{
    //
    public function index(Request $request)
    {
        if ($request->paginated)
            $paginated = $request->paginated;
        else
            $paginated = 10;
        if ($request->currentPage)
            $currentPage = $request->currentPage;
        else
            $currentPage = 1;

        $query = Lembur::query();
        $lembur = $query->paginate($paginated, ['*'], 'page', $currentPage);

        $myTeam = AnggotaTimKerja::from('sulutweb_man_management.keanggotaan_timkerja as mkt')
            ->join('sulutweb_man_management.timkerja as ttk', 'mkt.tim_id', 'ttk.id')
            ->select(['mkt.*', 'ttk.label as tim_kerja'])
            ->where('mkt.pegawai_id', Auth::user()->id)->get();
        $keanggotaan = $myTeam->pluck('keanggotaan')->toArray();

        return Inertia::render('Simple/Lembur', [
            'lembur' => $lembur,
            'tim' => $myTeam,
            'keanggotaan' => $keanggotaan
        ]);
    }
}
