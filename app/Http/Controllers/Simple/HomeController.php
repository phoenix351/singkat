<?php

namespace App\Http\Controllers\Simple;

use App\Http\Controllers\Controller;
use App\Models\ManManagement\Pegawai;
use App\Models\ManManagement\TimKerja;
use App\Models\Simple\LemburPegawai;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class HomeController extends Controller
{
    //
    public function index(Request $request)
    {
        $filters = $request->input('filters', null);
        $tahun = null;
        $bulan = null;

        $query = LemburPegawai::query();
        if ($filters) {
            $tahun = $filters['tahun'] ?? null;
            $bulan = $filters['bulan'] ?? null;
            if ($bulan != 0)
                $query->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun);
            else
                $query->whereYear('tanggal', $tahun);
        } else
            $query->whereYear('tanggal', date('Y'));

        $fivePegawaiMost = (clone $query)
            ->where('status', '4')
            ->with('pegawai')->get()->groupBy('pegawai.name')
            ->map(function ($group) {
                return $group->count();
            })
            ->sortByDesc(fn($count) => $count)
            ->take(5);
        $fiveTeamMost = (clone $query)
            ->where('status', '4')
            ->with('lembur.tim')->get()->groupBy('lembur.tim.label')
            ->map(function ($group) {
                return $group->count();
            })
            ->sortByDesc(fn($count) => $count)
            ->take(5);
        $lp = $query->get();
        $pending = $lp->where('status', 1)->count();
        $setuju_katim = $lp->where('status', 2)->count();
        $tolak_katim = $lp->where('status', 3)->count();
        $setuju_kabag = $lp->where('status', 4)->count();
        $tolak_kabag = $lp->where('status', 5)->count();

        $result = [
            'pending' => $pending,
            'setuju_katim' => $setuju_katim,
            'tolak_katim' => $tolak_katim,
            'setuju_kabag' => $setuju_kabag,
            'tolak_kabag' => $tolak_kabag
        ];
        if ($filters) {
            return response()->json([
                'result' => $result,
                'pegawais' => $fivePegawaiMost,
                'tims' => $fiveTeamMost
            ]);
        }

        return Inertia::render('Simple/Home', [
            'result' => $result,
            'pegawais' => $fivePegawaiMost,
            'tims' => $fiveTeamMost
        ]);
    }

    public function summary(Request $request)
    {
        if ($request->paginated)
            $paginated = $request->paginated;
        else
            $paginated = 10;
        if ($request->currentPage)
            $currentPage = $request->currentPage;
        else
            $currentPage = 1;
        $filters = $request->input('filters', null);
        $range_waktu = null;
        $data_type = null;
        $pegawai_tim = null;

        $query = LemburPegawai::query()->from('sulutweb_simple.lembur_pegawai as lp');
        $query->where('status', '4');
        if ($filters) {
            $range_waktu = $filters['range_waktu'] ?? null;
            $data_type = $filters['data_type'] ?? null;
            $pegawai_tim = $filters['pegawai_tim'] ?? null;

            if ($range_waktu) {
                $query->whereBetween(
                    'tanggal',
                    [
                        Carbon::parse($range_waktu[0]),
                        Carbon::parse($range_waktu[1])
                    ]
                );
            } else {
                $query->whereBetween(
                    'tanggal', 
                    [
                        Carbon::now()->startOfYear()->format('Y-m-d'),
                        Carbon::now()->format('Y-m-d')
                    ]
                );
            }
            if ($data_type && $data_type == 'tim') {
                $query
                    ->join('sulutweb_simple.lembur as l', 'lp.lembur_id', '=', 'l.id')
                    ->join('sulutweb_man_management.timkerja as tk', 'l.tim_id', '=', 'tk.id')
                    ->select('tk.label as label', DB::raw('count(lp.id) as total_lembur'))
                    ->groupBy('tk.id', 'tk.label')
                    ->orderByDesc('total_lembur');
            } else {
                $query->join('sulutweb_man_management.pegawai as p', 'lp.pegawai_id', '=', 'p.id')
                    ->select('p.name as label', DB::raw('count(lp.id) as total_lembur'))
                    ->groupBy('p.id', 'p.name')
                    ->orderByDesc('total_lembur');
            }
            if ($pegawai_tim) {
                if ($data_type == 'tim') {
                    $query->where('tk.label', 'like', '%' . $pegawai_tim . '%');
                } else {
                    $query->where('p.name', 'like', '%' . $pegawai_tim . '%');
                }
            }
        } else {
            $query->whereBetween(
                    'tanggal', 
                    [
                        Carbon::now()->startOfYear()->format('Y-m-d'),
                        Carbon::now()->format('Y-m-d')
                    ]
                );
            $query->join('sulutweb_man_management.pegawai as p', 'lp.pegawai_id', '=', 'p.id')
                ->select('p.name as label', DB::raw('count(lp.id) as total_lembur'))
                ->groupBy('p.id', 'p.name')
                ->orderByDesc('total_lembur');
        }
        $data = $query
            ->paginate($paginated, ['*'], 'page', $currentPage);
        foreach ($data as $item) {
            $queryLp = LemburPegawai::query();
            if (!$data_type || $data_type == 'pegawai') {
                $pegawaiId = Pegawai::where('name', $item->label)->value('id');
                $queryLp->where('pegawai_id', $pegawaiId);
            } else if ($data_type && $data_type == 'tim') {
                $timId = TimKerja::where('label', $item->label)->value('id');
                $queryLp->whereHas('lembur', function ($q) use ($timId) {
                    $q->where('tim_id', $timId);
                });
            }
            // $item->pegawai_id = $pegawaiId;
            $queryLp
                ->where('status', '4')
                ->whereNotNull('jam_berangkat')
                ->whereNotNull('jam_pulang');
            if ($filters) {
                $tahun = $filters['tahun'] ?? null;
                $bulan = $filters['bulan'] ?? null;
                if ($bulan != 0)
                    $queryLp->whereMonth('tanggal', $bulan)
                        ->whereYear('tanggal', $tahun);
                else
                    $queryLp->whereYear('tanggal', $tahun);
            }
            $data_lp_pegawai = $queryLp->get();
            $durasi_pegawai = 0;
            foreach ($data_lp_pegawai as $lp) {
                $tgl = Carbon::parse($lp->tanggal);
                $dayOfWeek = $tgl->dayOfWeekIso;
                $durasi = $lp->jumlah_jam;
                $durasi_final = 0;

                if ($lp->jam_berangkat && $lp->jam_pulang) {
                    $masuk = Carbon::parse($lp->jam_berangkat);
                    $pulang = Carbon::parse($lp->jam_pulang);

                    if ($pulang->lessThan($masuk)) {
                        $pulang->addDay();
                    }

                    if ($dayOfWeek <= 5) {
                        $batas_pulang_str = ($dayOfWeek <= 4) ? '16:00:00' : '16:30:00';
                        $batas_pulang = Carbon::parse($batas_pulang_str);

                        $mulai_lembur = $masuk->greaterThan($batas_pulang) ? $masuk : $batas_pulang;

                        if ($pulang->greaterThan($mulai_lembur)) {
                            $selisih = floor($mulai_lembur->diffInMinutes($pulang) / 60);
                        } else {
                            $selisih = 0;
                        }
                    } else {
                        $selisih = floor($masuk->diffInMinutes($pulang) / 60);
                    }
                    $durasi_final = ($selisih > $durasi) ? $durasi : $selisih;
                }
                $durasi_pegawai += $durasi_final;
            }

            $item->durasi_lembur = $durasi_pegawai;
        }
        if ($request->paginated) {
            return response()->json($data);
        }
        return Inertia::render('Simple/Summary', [
            'data' => $data
        ]);
    }
}
