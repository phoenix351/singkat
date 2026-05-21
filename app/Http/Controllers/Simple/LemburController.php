<?php

namespace App\Http\Controllers\Simple;

use App\Http\Controllers\Controller;
use App\Models\ManManagement\AnggotaTimKerja;
use App\Models\ManManagement\Pegawai;
use App\Models\ManManagement\Role;
use App\Models\Simple\Lembur;
use App\Models\Simple\LemburPegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class LemburController extends Controller
{
    //
    public function index(Request $request)
    {
        if ($request->paginated)
            $paginated = $request->paginated;
        else
            $paginated = 5;
        if ($request->currentPage)
            $currentPage = $request->currentPage;
        else
            $currentPage = 1;

        $query = Lembur::query();
        $query->join('sulutweb_man_management.timkerja as st', 'st.id', 'lembur.tim_id');
        $query->whereHas('pegawai', function ($q) {
            $q->where('created_by', Auth::id());
        });

        $query->with([
            'pegawai' => function ($q) {
                $q->where('created_by', Auth::id())->with(['pegawai', 'edited']);
            }
        ]);
        if ($request->sortOrder) {
            $fieldOrder = $request->sortField;
            if ($fieldOrder == 'tanggal') {
                $order = $request->sortOrder == 1 ? 'asc' : 'desc';
                $query->orderBy(
                    LemburPegawai::select('tanggal')
                        ->whereColumn('lembur_pegawai.lembur_id', 'lembur.id')
                        ->orderBy('tanggal', $order)
                        ->limit(1),
                    $order
                );
            } elseif ($fieldOrder == 'status_pengajuan') {
                $sub = LemburPegawai::selectRaw('COUNT(*)')
                    ->whereColumn('lembur_pegawai.lembur_id', 'lembur.id')
                    ->whereIn('status', [2, 4]);
                if ($request->sortOrder == 1) {
                    $query->orderByDesc($sub);
                } else {
                    $query->orderBy($sub);
                }
            } else {
                $order = $request->sortOrder == 1 ? 'asc' : 'desc';
                $query->orderBy($fieldOrder, $order);
            }
        } else {
            $query->orderByDesc(
                LemburPegawai::select('updated_at')
                    ->whereColumn('lembur_pegawai.lembur_id', 'lembur.id')
                    ->orderByDesc('updated_at')
                    ->limit(1)
            )->orderByDesc(
                    LemburPegawai::select('tanggal')
                        ->whereColumn('lembur_pegawai.lembur_id', 'lembur.id')
                        ->orderByDesc('tanggal')
                        ->limit(1)
                );
        }
        if ($request->searchField) {
            $search = $request->searchField;
            $pegawaiIds = Pegawai::where('name', 'like', '%' . $search . '%')
                ->orWhere('nip', 'like', '%' . $search . '%')
                ->pluck('id')
                ->toArray();
            $query->where(function ($q) use ($pegawaiIds) {
                $q->WhereHas('pegawai', function ($q2) use ($pegawaiIds) {
                    $q2->whereIn('pegawai_id', $pegawaiIds);
                });
            });
        }

        if ($request->has('filters')) {
            $filters = $request->filters;
            if (!empty($filters['tim_kerja'])) {
                $query->where('st.label', 'like', '%' . $filters['tim_kerja'] . '%');
            }
            if (!empty($filters['tanggal'])) {
                $monthMap = [
                    'januari' => '-01-',
                    'februari' => '-02-',
                    'maret' => '-03-',
                    'april' => '-04-',
                    'mei' => '-05-',
                    'juni' => '-06-',
                    'juli' => '-07-',
                    'agustus' => '-08-',
                    'september' => '-09-',
                    'oktober' => '-10-',
                    'november' => '-11-',
                    'desember' => '-12-'
                ];
                $dateSearch = str_ireplace(array_keys($monthMap), array_values($monthMap), $filters['tanggal']);
                $dateSearch = str_replace([' ', '--'], ['-', '-'], $dateSearch);
                $query->where(function ($q) use ($dateSearch) {
                    $q->WhereHas('pegawai', function ($q2) use ($dateSearch) {
                        $q2->WhereRaw("DATE_FORMAT(tanggal, '%e-%m-%Y') LIKE ?", ['%' . $dateSearch . '%']);
                    });
                });
            }
            if (!empty($filters['maksud_lembur'])) {
                $query->where('lembur.maksud_lembur', 'like', '%' . $filters['maksud_lembur'] . '%');
            }
        }
        $query->with(['pegawai.pegawai', 'pegawai.edited']);
        $query->select(['lembur.*', 'st.label as tim_kerja']);
        $lembur = $query->paginate($paginated, ['*'], 'page', $currentPage);

        $myTeam = AnggotaTimKerja::from('sulutweb_man_management.keanggotaan_timkerja as mkt')
            ->join('sulutweb_man_management.timkerja as ttk', 'mkt.tim_id', 'ttk.id')
            ->select(['mkt.*', 'ttk.label as tim_kerja'])
            ->where('mkt.pegawai_id', Auth::user()->id)->get();
        $keanggotaan = $myTeam->pluck('keanggotaan')->toArray();

        if ($request->paginated) {
            return response()->json($lembur);
        }
        return Inertia::render('Simple/Lembur', [
            'lembur' => $lembur,
            'tim' => $myTeam,
            'keanggotaan' => $keanggotaan
        ]);
    }

    public function store(Request $request)
    {
        if ($request->isMethod('patch')) {
            if ($request->input('add_pegawai') == true) {
                $validated = $request->validate([
                    'id' => ['required', 'integer'],
                    'anggotalembur' => ['required', 'array'],
                    'pegawai' => ['required', 'array']
                ], [
                    'anggotalembur.required' => 'Anggota tim harus diisi'
                ]);
            } else {
                $pegawai = $request->input('pegawai') ?? [];
                $validated = $request->validate([
                    'id' => ['required', 'integer'],
                    'pegawai.0.tanggal' => ['required', 'date'],
                    'pegawai.0.jumlah_jam' => ['required', 'numeric', 'max:24'],
                    'maksud_lembur' => ['required', 'string'],
                    'link_dokumentasi' => ['required', 'string']
                ], [
                    'pegawai.0.tanggal.required' => 'Tanggal harus diisi',
                    'pegawai.0.jumlah_jam.required' => 'Jumlah jam harus diisi',
                    'pegawai.0.jumlah_jam.max' => 'Jumlah jam tidak boleh lebih dari 24',
                    'maksud_lembur.required' => 'Maksud lembur harus diisi',
                    'link_dokumentasi.required' => 'Link dokumentasi harus diisi',
                ]);
            }
            try {
                //code...
                DB::connection('sulutweb_simple')->beginTransaction();
                if ($request->input('add_pegawai') == true) {
                    $anggota = $validated['anggotalembur'];
                    $pegawai = $request->input('pegawai')[0];
                    foreach ($anggota as $key => $p) {
                        # code...
                        LemburPegawai::firstOrCreate(
                            [
                                'lembur_id' => $validated['id'],
                                'pegawai_id' => $p,
                            ],
                            [
                                'lembur_id' => $validated['id'],
                                'pegawai_id' => $p,
                                'tanggal' => $pegawai['tanggal'],
                                'jumlah_jam' => $pegawai['jumlah_jam'],
                                'created_by' => Auth::id(),
                                'edited_by' => Auth::id(),
                            ]
                        );
                    }
                } else {
                    $lembur_to_update = Lembur::findOrFail($validated['id']);
                    $lembur_to_update->update($validated);

                    if (sizeof($pegawai) > 0) {
                        $lp = [
                            'tanggal' => \Carbon\Carbon::parse($validated['pegawai'][0]['tanggal'])->setTimezone('Asia/Makassar')->format('Y-m-d'),
                            'jumlah_jam' => $validated['pegawai'][0]['jumlah_jam'],
                            'edited_by' => Auth::id()
                        ];
                        foreach ($pegawai as $key => $p) {
                            # code...
                            $lp_to_update = LemburPegawai::findOrFail($p['id']);
                            $lp_to_update->update($lp);
                        }
                    }
                }
                DB::connection('sulutweb_simple')->commit();
                return redirect()->route('simple.lembur')->with('success', 'Berhasil mengedit data lembur');
            } catch (\Throwable $th) {
                //throw $th;
                DB::connection('sulutweb_simple')->rollback();
                return redirect()->route('simple.lembur')->with('error', 'Terdapat error : ' . $th->getMessage());
            }
        }
        $validated = $request->validate([
            'tim_id' => ['required', 'integer'],
            'anggotalembur' => ['required', 'array'],
            'tanggal' => ['required', 'date'],
            'jumlah_jam' => ['required', 'numeric', 'max:24'],
            'maksud_lembur' => ['required', 'string'],
            'link_dokumentasi' => ['required', 'string']
        ], [
            'tim_id.required' => 'Tim kerja harus diisi',
            'anggotalembur.required' => 'Anggota tim harus diisi',
            'tanggal.required' => 'Tanggal harus diisi',
            'jumlah_jam.required' => 'Jumlah jam harus diisi',
            'jumlah_jam.max' => 'Jumlah jam tidak boleh lebih dari 24',
            'maksud_lembur.required' => 'Maksud lembur harus diisi',
            'link_dokumentasi.required' => 'Link dokumentasi harus diisi'
        ]);
        try {
            //code...
            DB::connection('sulutweb_simple')->beginTransaction();
            $validated['tanggal'] = \Carbon\Carbon::parse($validated['tanggal'])->setTimezone('Asia/Makassar')->format('Y-m-d');
            $lembur = Lembur::create($validated);
            foreach ($validated['anggotalembur'] as $key => $anggota) {
                # code...
                $validated['pegawai_id'] = $anggota;
                $validated['lembur_id'] = $lembur->id;
                $validated['created_by'] = Auth::id();
                $validated['edited_by'] = Auth::id();
                LemburPegawai::firstOrCreate(
                    [
                        'lembur_id' => $lembur->id,
                        'pegawai_id' => $anggota,
                    ],
                    $validated
                );
            }
            DB::connection('sulutweb_simple')->commit();
            return redirect()->route('simple.lembur')->with('success', 'Berhasil menambahkan data');
        } catch (\Throwable $th) {
            //throw $th;
            DB::connection('sulutweb_simple')->rollBack();
            return redirect()->route('simple.lembur')->with('error', 'Gagal menambahkan data, error: ' . $th->getMessage());
        }
    }

    public function destroyPegawai($id)
    {
        try {
            //code...
            DB::connection('sulutweb_simple')->beginTransaction();
            $lp_to_delete = LemburPegawai::findOrFail($id);
            if ($lp_to_delete)
                $lp_to_delete->delete();
            DB::connection('sulutweb_simple')->commit();
            return redirect()->route('simple.lembur')->with('success', 'Berhasil hapus data');
        } catch (\Throwable $th) {
            //throw $th;
            DB::connection('sulutweb_simple')->rollBack();
            return redirect()->route('simple.lembur')->with('error', 'Gagal hapus data, error : ' . $th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            //code...
            DB::connection('sulutweb_simple')->beginTransaction();
            $lembur_to_delete = Lembur::findOrFail($id);
            if ($lembur_to_delete)
                $lembur_to_delete->delete();
            DB::connection('sulutweb_simple')->commit();
            return redirect()->route('simple.lembur')->with('success', 'Berhasil hapus data');
        } catch (\Throwable $th) {
            //throw $th;
            DB::connection('sulutweb_simple')->rollBack();
            return redirect()->route('simple.lembur')->with('error', 'Gagal hapus data, error : ' . $th->getMessage());
        }

    }

    public function verify(Request $request)
    {
        if ($request->paginated)
            $paginated = $request->paginated;
        else
            $paginated = 5;
        if ($request->currentPage)
            $currentPage = $request->currentPage;
        else
            $currentPage = 1;
        $my_role = Role::currentRole();

        $query_myTeam = AnggotaTimKerja::query();
        $query_myTeam->from('sulutweb_man_management.keanggotaan_timkerja as mkt')
            ->join('sulutweb_man_management.timkerja as ttk', 'mkt.tim_id', 'ttk.id');
        if ($my_role != 'admin') {
            $query_myTeam->where('mkt.keanggotaan', 'ketua');
        }

        $myTeam = $query_myTeam->select(['mkt.*', 'ttk.label as tim_kerja'])
            ->where('mkt.pegawai_id', Auth::user()->id)->get();
        $keanggotaan = $myTeam->pluck('keanggotaan')->toArray();

        $query = Lembur::query();
        $query->join('sulutweb_man_management.timkerja as st', 'st.id', 'lembur.tim_id');
        if ($my_role != 'admin') {
            $query->whereIn('lembur.tim_id', $myTeam->pluck('tim_id')->toArray());
        }

        //search
        if ($request->sortOrder) {
            $fieldOrder = $request->sortField;
            if ($fieldOrder == 'tanggal') {
                $order = $request->sortOrder == 1 ? 'asc' : 'desc';
                $query->orderBy(
                    LemburPegawai::select('tanggal')
                        ->whereColumn('lembur_pegawai.lembur_id', 'lembur.id')
                        ->orderBy('tanggal', $order)
                        ->limit(1),
                    $order
                );
            } elseif ($fieldOrder == 'status_pengajuan') {
                $sub = LemburPegawai::selectRaw('COUNT(*)')
                    ->whereColumn('lembur_pegawai.lembur_id', 'lembur.id')
                    ->whereIn('status', [2, 4]);
                if ($request->sortOrder == 1) {
                    $query->orderByDesc($sub);
                } else {
                    $query->orderBy($sub);
                }
            } else {
                $order = $request->sortOrder == 1 ? 'asc' : 'desc';
                $query->orderBy($fieldOrder, $order);
            }
        } else {
            $query->orderByDesc(
                LemburPegawai::select('updated_at')
                    ->whereColumn('lembur_pegawai.lembur_id', 'lembur.id')
                    ->orderByDesc('updated_at')
                    ->limit(1)
            )->orderByDesc(
                    LemburPegawai::select('tanggal')
                        ->whereColumn('lembur_pegawai.lembur_id', 'lembur.id')
                        ->orderByDesc('tanggal')
                        ->limit(1)
                );
        }
        if ($request->searchField) {
            $search = $request->searchField;
            $pegawaiIds = Pegawai::where('name', 'like', '%' . $search . '%')
                ->orWhere('nip', 'like', '%' . $search . '%')
                ->pluck('id')
                ->toArray();
            $query->where(function ($q) use ($pegawaiIds) {
                $q->WhereHas('pegawai', function ($q2) use ($pegawaiIds) {
                    $q2->whereIn('pegawai_id', $pegawaiIds);
                });
            });
        }

        if ($request->has('filters')) {
            $filters = $request->filters;
            if (!empty($filters['tim_kerja'])) {
                $query->where('st.label', 'like', '%' . $filters['tim_kerja'] . '%');
            }
            if (!empty($filters['tanggal'])) {
                $monthMap = [
                    'januari' => '-01-',
                    'februari' => '-02-',
                    'maret' => '-03-',
                    'april' => '-04-',
                    'mei' => '-05-',
                    'juni' => '-06-',
                    'juli' => '-07-',
                    'agustus' => '-08-',
                    'september' => '-09-',
                    'oktober' => '-10-',
                    'november' => '-11-',
                    'desember' => '-12-'
                ];
                $dateSearch = str_ireplace(array_keys($monthMap), array_values($monthMap), $filters['tanggal']);
                $dateSearch = str_replace([' ', '--'], ['-', '-'], $dateSearch);
                $query->where(function ($q) use ($dateSearch) {
                    $q->WhereHas('pegawai', function ($q2) use ($dateSearch) {
                        $q2->WhereRaw("DATE_FORMAT(tanggal, '%e-%m-%Y') LIKE ?", ['%' . $dateSearch . '%']);
                    });
                });
            }
            if (!empty($filters['maksud_lembur'])) {
                $query->where('lembur.maksud_lembur', 'like', '%' . $filters['maksud_lembur'] . '%');
            }
        }
        $query->with(['pegawai.pegawai', 'pegawai.edited']);
        $query->select(['lembur.*', 'st.label as tim_kerja']);
        $lembur = $query->paginate($paginated, ['*'], 'page', $currentPage);

        if ($request->paginated)
            return response()->json($lembur);
        return Inertia::render('Simple/Verify', [
            'lembur' => $lembur,
            'tim' => $myTeam,
            'keanggotaan' => $keanggotaan
        ]);
    }

    public function verifyPatch(Request $request)
    {
        $validated = $request->validate([
            'individual' => ['required', 'boolean'],
            'status' => ['required', 'string', 'in:setuju,ditolak'],
            'catatan' => ['required_if:status,ditolak', 'nullable', 'string'],
            'lembur_id' => ['required_if:individual,false', 'integer'],
            'lembur_pegawai' => ['required_if:individual,true', 'nullable', 'array']
        ]);

        try {
            DB::connection('sulutweb_simple')->beginTransaction();
            $statust = $validated['status'] == 'setuju' ? '2' : '3';
            $updateData = [
                'status' => $statust,
                'catatan' => $validated['catatan'] ?? null
            ];
            if ($validated['individual'] == true)
                LemburPegawai::whereIn('id', $validated['lembur_pegawai'])->update($updateData);
            else
                LemburPegawai::where('lembur_id', $validated['lembur_id'])->update($updateData);
            DB::connection('sulutweb_simple')->commit();
            return redirect()->route('simple.lembur.verify')->with('success', 'Berhasil mengubah status lembur');

        } catch (\Throwable $th) {
            DB::connection('sulutweb_simple')->rollBack();
            return redirect()->route('simple.lembur.verify')->with('error', 'Gagal mengubah status lembur, error: ' . $th->getMessage());
        }
    }

    public function verifyKabag(Request $request)
    {
        if ($request->paginated)
            $paginated = $request->paginated;
        else
            $paginated = 5;
        if ($request->currentPage)
            $currentPage = $request->currentPage;
        else
            $currentPage = 1;

        $query = Lembur::query();
        $query->join('sulutweb_man_management.timkerja as st', 'st.id', 'lembur.tim_id');

        $query->whereHas('pegawai', function ($q) {
            $q->whereIn('status', ['2', '4', '5']);
        });

        $query->with([
            'pegawai' => function ($q) {
                $q->whereIn('status', ['2', '4', '5'])->with(['pegawai', 'edited']);
            }
        ]);

        //search
        if ($request->sortOrder) {
            $fieldOrder = $request->sortField;
            if ($fieldOrder == 'tanggal') {
                $order = $request->sortOrder == 1 ? 'asc' : 'desc';
                $query->orderBy(
                    LemburPegawai::select('tanggal')
                        ->whereColumn('lembur_pegawai.lembur_id', 'lembur.id')
                        ->orderBy('tanggal', $order)
                        ->limit(1),
                    $order
                );
            } elseif ($fieldOrder == 'status_pengajuan') {
                $sub = LemburPegawai::selectRaw('COUNT(*)')
                    ->whereColumn('lembur_pegawai.lembur_id', 'lembur.id')
                    ->whereIn('status', [2, 4]);
                if ($request->sortOrder == 1) {
                    $query->orderByDesc($sub);
                } else {
                    $query->orderBy($sub);
                }
            } else {
                $order = $request->sortOrder == 1 ? 'asc' : 'desc';
                $query->orderBy($fieldOrder, $order);
            }
        } else {
            $query->orderByDesc(
                LemburPegawai::select('updated_at')
                    ->whereColumn('lembur_pegawai.lembur_id', 'lembur.id')
                    ->orderByDesc('updated_at')
                    ->limit(1)
            )->orderByDesc(
                    LemburPegawai::select('tanggal')
                        ->whereColumn('lembur_pegawai.lembur_id', 'lembur.id')
                        ->orderByDesc('tanggal')
                        ->limit(1)
                );
        }
        if ($request->searchField) {
            $search = $request->searchField;
            $pegawaiIds = Pegawai::where('name', 'like', '%' . $search . '%')
                ->orWhere('nip', 'like', '%' . $search . '%')
                ->pluck('id')
                ->toArray();
            $query->where(function ($q) use ($pegawaiIds) {
                $q->WhereHas('pegawai', function ($q2) use ($pegawaiIds) {
                    $q2->whereIn('pegawai_id', $pegawaiIds);
                });
            });
        }

        if ($request->has('filters')) {
            $filters = $request->filters;
            if (!empty($filters['tim_kerja'])) {
                $query->where('st.label', 'like', '%' . $filters['tim_kerja'] . '%');
            }
            if (!empty($filters['tanggal'])) {
                $monthMap = [
                    'januari' => '-01-',
                    'februari' => '-02-',
                    'maret' => '-03-',
                    'april' => '-04-',
                    'mei' => '-05-',
                    'juni' => '-06-',
                    'juli' => '-07-',
                    'agustus' => '-08-',
                    'september' => '-09-',
                    'oktober' => '-10-',
                    'november' => '-11-',
                    'desember' => '-12-'
                ];
                $dateSearch = str_ireplace(array_keys($monthMap), array_values($monthMap), $filters['tanggal']);
                $dateSearch = str_replace([' ', '--'], ['-', '-'], $dateSearch);
                $query->where(function ($q) use ($dateSearch) {
                    $q->WhereHas('pegawai', function ($q2) use ($dateSearch) {
                        $q2->WhereRaw("DATE_FORMAT(tanggal, '%e-%m-%Y') LIKE ?", ['%' . $dateSearch . '%']);
                    });
                });
            }
            if (!empty($filters['maksud_lembur'])) {
                $query->where('lembur.maksud_lembur', 'like', '%' . $filters['maksud_lembur'] . '%');
            }
        }

        $query->select(['lembur.*', 'st.label as tim_kerja']);
        $lembur = $query->paginate($paginated, ['*'], 'page', $currentPage);

        if ($request->paginated)
            return response()->json($lembur);
        return Inertia::render('Simple/VerifyKabag', [
            'lembur' => $lembur,
        ]);
    }

    public function verifyKabagPatch(Request $request)
    {
        $validated = $request->validate([
            'individual' => ['required', 'boolean'],
            'status' => ['required', 'string', 'in:setuju,ditolak'],
            'catatan' => ['required_if:status,ditolak', 'nullable', 'string'],
            'lembur_id' => ['required_if:individual,false', 'integer'],
            'lembur_pegawai' => ['required_if:individual,true', 'nullable', 'array']
        ]);

        try {
            DB::connection('sulutweb_simple')->beginTransaction();
            $statust = $validated['status'] == 'setuju' ? '4' : '5';
            $updateData = [
                'status' => $statust,
                'catatan' => $validated['catatan'] ?? null
            ];
            if ($validated['individual'] == true)
                LemburPegawai::whereIn('id', $validated['lembur_pegawai'])->update($updateData);
            else
                LemburPegawai::where('lembur_id', $validated['lembur_id'])->update($updateData);
            DB::connection('sulutweb_simple')->commit();
            return redirect()->route('simple.lembur.verify-kabag')->with('success', 'Berhasil mengubah status lembur');

        } catch (\Throwable $th) {
            DB::connection('sulutweb_simple')->rollBack();
            return redirect()->route('simple.lembur.verify-kabag')->with('error', 'Gagal mengubah status lembur, error: ' . $th->getMessage());
        }
    }

    public function myLembur(Request $request)
    {
        if ($request->paginated)
            $paginated = $request->paginated;
        else
            $paginated = 10;
        if ($request->currentPage)
            $currentPage = $request->currentPage;
        else
            $currentPage = 1;

        $query = LemburPegawai::query();
        $query->join('lembur', 'lembur.id', '=', 'lembur_pegawai.lembur_id')
            ->join('sulutweb_man_management.timkerja as st', 'st.id', '=', 'lembur.tim_id');
        $query->where('lembur_pegawai.pegawai_id', Auth::id());

        if ($request->sortOrder) {
            $fieldOrder = $request->sortField;
            $order = $request->sortOrder == 1 ? 'asc' : 'desc';
            $query->orderBy($fieldOrder, $order);
        } else
            $query->orderBy('updated_at', 'desc')->orderBy('tanggal', 'desc');
        if ($request->has('filters')) {
            $filters = $request->filters;
            if (!empty($filters['tim_kerja'])) {
                $query->where('st.label', 'like', '%' . $filters['tim_kerja'] . '%');
            }
            if (!empty($filters['tanggal'])) {
                $monthMap = [
                    'januari' => '-01-',
                    'februari' => '-02-',
                    'maret' => '-03-',
                    'april' => '-04-',
                    'mei' => '-05-',
                    'juni' => '-06-',
                    'juli' => '-07-',
                    'agustus' => '-08-',
                    'september' => '-09-',
                    'oktober' => '-10-',
                    'november' => '-11-',
                    'desember' => '-12-'
                ];
                $dateSearch = str_ireplace(array_keys($monthMap), array_values($monthMap), $filters['tanggal']);
                $dateSearch = str_replace([' ', '--'], ['-', '-'], $dateSearch);
                $query->whereRaw("DATE_FORMAT(lembur_pegawai.tanggal, '%e-%m-%Y') LIKE ?", ['%' . $dateSearch . '%']);
            }
            if (!empty($filters['maksud_lembur'])) {
                $query->where('lembur.maksud_lembur', 'like', '%' . $filters['maksud_lembur'] . '%');
            }
            if (!empty($filters['status'])) {
                $statusMapping = [
                    '1' => 'pending',
                    '2' => 'disetujui katim',
                    '3' => 'ditolak katim',
                    '4' => 'disetujui kabag',
                    '5' => 'ditolak kabag',
                ];
                $searchTerm = strtolower($filters['status']);
                $matchedStatuses = array_keys(array_filter($statusMapping, function ($label) use ($searchTerm) {
                    return str_contains($label, $searchTerm);
                }));

                if (!empty($matchedStatuses)) {
                    $query->whereIn('lembur_pegawai.status', $matchedStatuses);
                }
            }
        }
        $query->with(['lembur.tim', 'pegawai']);
        $query->select(['lembur_pegawai.*']);
        $lembur = $query->paginate($paginated, ['*'], 'page', $currentPage);
        if ($request->paginated)
            return response()->json($lembur);

        return Inertia::render('Simple/MyLembur', [
            'lembur' => $lembur,
        ]);
    }

    public function fetchLembur($lembur_id)
    {
        $lembur = Lembur::findOrFail($lembur_id)
            ->whereHas('pegawai', function ($q) {
                $q->where('status', 4);
            })
            ->with([
                'pegawai' => function ($q) {
                    $q->where('status', 4)->with(['pegawai']);
                },
                'tim'
            ])
            ->first();
        return response()->json($lembur);
    }

    public function fetchMaksud($tim_id)
    {
        $maksudLembur = Lembur::where('tim_id', $tim_id)->pluck('maksud_lembur')->toArray();
        return response()->json($maksudLembur);
    }
}
