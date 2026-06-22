<?php

namespace App\Http\Controllers\Se2026;

use App\Http\Controllers\Controller;
use App\Models\Se2026\DataFasih;
use App\Models\Se2026\DataFasihPml;
use App\Models\Se2026\Logs;
use App\Models\Se2026\MasterDesa;
use App\Models\Se2026\MasterKabkot;
use App\Models\Se2026\MasterKec;
use App\Models\Se2026\MasterSubSls;
use App\Models\Se2026\Pml;
use App\Models\Se2026\Ppl;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DataController extends Controller
{
    //
    public function index(Request $request)
    {
        $fasih = DataFasih::with('subsls.desa.kec.kabkot')->get();
        $open = 0;
        $draft = 0;
        $submitted_p = 0;
        $submitted_r = 0;
        $approved = 0;
        $rejected = 0;
        $revoked = 0;
        $completed = 0;
        foreach ($fasih as $f) {
            # code...
            $open = $open + $f->open;
            $draft = $draft + $f->draft;
            $submitted_p = $submitted_p + $f->submitted_p;
            $submitted_r = $submitted_r + $f->submitted_r;
            $approved = $approved + $f->approved;
            $rejected = $rejected + $f->rejected;
            $revoked = $revoked + $f->revoked;
            $completed = $completed + $f->completed;
        }
        $total = $open + $draft + $submitted_p + $submitted_r + $approved + $rejected + $revoked + $completed;

        $groupKabkot = $fasih->groupBy(function ($item) {
            return $item->subsls?->desa?->kec?->kabkot?->code;
        })->map(function ($group) {
            $firstItem = $group->first();
            return [
                'name' => $firstItem->subsls?->desa?->kec?->kabkot?->label ?? 'Tidak Diketahui',
                'code' => $firstItem->subsls?->desa?->kec?->kabkot?->code ?? 'Unknown',
                'open' => $group->sum('open'),
                'draft' => $group->sum('draft'),
                'submitted_p' => $group->sum('submitted_p'),
                'submitted_r' => $group->sum('submitted_r'),
                'approved' => $group->sum('approved'),
                'rejected' => $group->sum('rejected'),
                'revoked' => $group->sum('revoked'),
                'completed' => $group->sum('completed'),
            ];
        })->values();
        $current_level = 'kabkot';
        $goToFilter = $request->code ?? null;
        if ($goToFilter) {
            $length_code = Str::length((string) $goToFilter);
            $data_progress = [];
            if ($length_code == 4) {
                $current_level = 'kec';
                $data_progress = $fasih->filter(function ($item) use ($goToFilter) {
                    return Str::startsWith((string) $item->subsls_code, (string) $goToFilter);
                })->groupBy(function ($item) {
                    return $item->subsls?->desa?->kec?->code;
                })->map(function ($group) {
                    $firstItem = $group->first();
                    return [
                        'name' => $firstItem->subsls?->desa?->kec?->label ?? 'Tidak Diketahui',
                        'code' => $firstItem->subsls?->desa?->kec?->code ?? 'Unknown',
                        'open' => $group->sum('open'),
                        'draft' => $group->sum('draft'),
                        'submitted_p' => $group->sum('submitted_p'),
                        'submitted_r' => $group->sum('submitted_r'),
                        'approved' => $group->sum('approved'),
                        'rejected' => $group->sum('rejected'),
                        'revoked' => $group->sum('revoked'),
                        'completed' => $group->sum('completed'),
                    ];
                })->values();
            } else if ($length_code == 7) {
                $current_level = 'desa';
                $data_progress = $fasih->filter(function ($item) use ($goToFilter) {
                    return Str::startsWith((string) $item->subsls_code, (string) $goToFilter);
                })->groupBy(function ($item) {
                    return $item->subsls?->desa?->code;
                })->map(function ($group) {
                    $firstItem = $group->first();
                    return [
                        'name' => $firstItem->subsls?->desa?->label ?? 'Tidak Diketahui',
                        'code' => $firstItem->subsls?->desa?->code ?? 'Unknown',
                        'open' => $group->sum('open'),
                        'draft' => $group->sum('draft'),
                        'submitted_p' => $group->sum('submitted_p'),
                        'submitted_r' => $group->sum('submitted_r'),
                        'approved' => $group->sum('approved'),
                        'rejected' => $group->sum('rejected'),
                        'revoked' => $group->sum('revoked'),
                        'completed' => $group->sum('completed'),
                    ];
                })->values();
            } else if ($length_code == 10) {
                $current_level = 'sls';
                $data_progress = $fasih->filter(function ($item) use ($goToFilter) {
                    return Str::startsWith((string) $item->subsls_code, (string) $goToFilter);
                })->groupBy(function ($item) {
                    return $item->subsls?->code;
                })->map(function ($group) {
                    $firstItem = $group->first();
                    return [
                        'name' => $firstItem->subsls?->code . ' - ' . $firstItem->subsls?->label ?? 'Tidak Diketahui',
                        'code' => $firstItem->subsls?->code ?? 'Unknown',
                        'open' => $group->sum('open'),
                        'draft' => $group->sum('draft'),
                        'submitted_p' => $group->sum('submitted_p'),
                        'submitted_r' => $group->sum('submitted_r'),
                        'approved' => $group->sum('approved'),
                        'rejected' => $group->sum('rejected'),
                        'revoked' => $group->sum('revoked'),
                        'completed' => $group->sum('completed'),
                    ];
                })->values();
            } else {
                $current_level = 'kabkot';
                $data_progress = $groupKabkot;
            }
            return response()->json(['data_progress' => $data_progress, 'current_level' => $current_level]);
        }
        $lastThreeUpdate = Logs::with(['pegawai', 'kabkot'])->orderBy('created_at', 'desc')->limit(3)->get()->map(function ($item) {
            $item->formatted_time = $item->updated_at->format('H:i d M Y');
            return $item;
        });
        $kabkot = MasterKabkot::get()->map(function ($item) {
            return [
                'code' => $item->code,
                'label' => '[' . $item->code . '] ' . $item->label,
            ];
        });
        return Inertia::render('Se2026/Dashboard', [
            'open' => $open,
            'draft' => $draft,
            'submitted_p' => $submitted_p,
            'submitted_r' => $submitted_r,
            'approved' => $approved,
            'rejected' => $rejected,
            'revoked' => $revoked,
            'completed' => $completed,
            'total' => $total,
            'data_progress' => $groupKabkot,
            'current_level' => $current_level,
            'lastThreeUpdate' => $lastThreeUpdate,
            'kabkot' => $kabkot,
        ]);
    }

    public function uploadData(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|array'
        ]);
        $datas = $validated['file'];

        // Slice to skip header row (index 0)
        $data = array_slice($datas, 1);

        // Fillable keys in order matching the array indices
        $fields = (new DataFasih())->getFillable();

        try {
            foreach ($data as $row) {
                // Map numeric keys to fillable field names
                $mapped = [];
                foreach ($fields as $index => $field) {
                    if (array_key_exists($index, $row)) {
                        $mapped[$field] = $row[$index];
                    }
                }

                $cek_subsls = MasterSubSls::where('code', $mapped['subsls_code'])->first();
                if ($cek_subsls) {
                    DataFasih::updateOrCreate(
                        [
                            'email' => $mapped['email'] ?? null,
                            'subsls_code' => $mapped['subsls_code'] ?? null,
                        ],
                        $mapped
                    );
                }
                // Upsert: match on email + subsls_code
            }

            return back()->with('success', 'Data berhasil diupload.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Gagal upload data: ' . $th->getMessage());
        }
    }

    public function uploadDataBatch(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|array',
            'file_name' => 'nullable|string',
        ]);
        $datas = $validated['file'];
        $fileName = $validated['file_name'] ?? 'unknown';
        $array_kako = [
            '7101',
            '7102',
            '7103',
            '7104',
            '7105',
            '7106',
            '7107',
            '7108',
            '7109',
            '7110',
            '7111',
            '7171',
            '7172',
            '7173',
            '7174'
        ];
        $kode = null;
        $cleanName = str_replace('.csv', '', $fileName);
        $exploded_fileName = explode('_', $cleanName);
        $totalElement = count($exploded_fileName);
        $tgl = $exploded_fileName[$totalElement - 2];
        $jam = $exploded_fileName[$totalElement - 1];
        $updatedAtReal = Carbon::createFromFormat('YmdHis', $tgl . $jam)->toDateTimeString();

        $pregged_check = '/^scraped_data_(.+?)_\d{8}_\d{6}\.csv$/';
        $expected_format = 'scraped_data_{kode}_{Ymd}_{His}.csv';

        if (isset($exploded_fileName[2]) && $exploded_fileName[2] === 'pml') {
            $pregged_check = '/^scraped_data_pml_(.+?)_\d{8}_\d{6}\.csv$/';
            $expected_format = 'scraped_data_pml_{kode}_{Ymd}_{His}.csv';
        }

        if (preg_match($pregged_check, $fileName, $matches)) {
            $kode = $matches[1];
            if (!in_array($kode, $array_kako)) {
                return response()->json([
                    'success'   => false,
                    'message'   => "Gagal upload {$fileName}: Kode tidak valid. Kode harus salah satu dari: " . implode(', ', $array_kako),
                    'file_name' => $fileName,
                ], 422);
            }
        } else {
            return response()->json([
                'success'   => false,
                'message'   => "Gagal upload {$fileName}: Format file tidak sesuai. Harus berupa {$expected_format}",
                'file_name' => $fileName,
            ], 422);
        }
        $data = array_slice($datas, 1);

        $field_ppl = (new DataFasih())->getFillable();
        $field_pml = (new DataFasihPml())->getFillable();
        $fields = $exploded_fileName[2] != 'pml' ? $field_ppl : $field_pml;
        $processedCount = 0;

        try {
            foreach ($data as $row) {
                $mapped = [];
                foreach ($fields as $index => $field) {
                    if (array_key_exists($index, $row)) {
                        $mapped[$field] = $row[$index];
                    }
                }
                $subsls_code = (string) ($mapped['subsls_code'] ?? '');
                $mapped['subsls_code'] = $subsls_code;
                $cek_subsls = MasterSubSls::where('code', (string) $mapped['subsls_code'])->first();
                if ($cek_subsls) {
                    if ($exploded_fileName[2] != 'pml') {
                        DataFasih::updateOrCreate(
                            [
                                'email' => $mapped['email'] ?? null,
                                'subsls_code' => (string) $mapped['subsls_code'] ?? null,
                            ],
                            $mapped
                        );
                    } else {
                        DataFasihPml::updateOrCreate(
                            [
                                'email' => $mapped['email'] ?? null,
                                'subsls_code' => (string) $mapped['subsls_code'] ?? null,
                            ],
                            $mapped
                        );
                    }
                    $processedCount++;
                }
            }

            $logs = Logs::create([
                'kode' => $kode,
                'pegawai_id' => Auth::id(),
                'data_type' => $exploded_fileName[2] != 'pml' ? 'ppl' : 'pml',
                'updated_at' => $updatedAtReal
            ]);

            return response()->json([
                'success' => true,
                'message' => "Berhasil upload: {$fileName}",
                'file_name' => $fileName,
                'rows_processed' => $processedCount,
                'rows_total' => count($data),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Gagal upload {$fileName}: " . $th->getMessage(),
                'file_name' => $fileName,
                'rows_processed' => $processedCount,
                'rows_total' => count($data),
            ], 422);
        }
    }

    public function fetchDataPpl(Request $request)
    {
        $paginated = $request->paginated ?? 10;
        $currentPage = $request->currentPage ?? 1;
        $kabkot = $request->kabkot ?? null;
        $kec = $request->kec ?? null;
        $desa = $request->desa ?? null;
        $sls = $request->sls ?? null;
        $nama = $request->nama ?? null;

        $query = Ppl::query();

        $pplTable = (new Ppl)->getTable();
        $fasihTable = (new DataFasih)->getTable();

        if ($nama) {
            $query->where(function ($q) use ($nama) {
                $q->where('nama', 'like', '%' . $nama . '%')
                    ->orWhere('email', 'like', '%' . $nama . '%');
            });
        }

        $fasihFilter = function ($q) use ($sls, $desa, $kec, $kabkot) {
            if ($sls) $q->where('subsls_code', $sls);
            else if ($desa) $q->where('subsls_code', 'like', $desa . '%');
            else if ($kec) $q->where('subsls_code', 'like', $kec . '%');
            else if ($kabkot) $q->where('subsls_code', 'like', $kabkot . '%');
        };

        $query->whereHas('fasih', $fasihFilter);

        // Default select
        $query->select("$pplTable.*");

        // if ($request->has('sortOrder') && $request->sortOrder) {
        //     $order = $request->sortOrder == 1 ? 'asc' : 'desc';

        //     $sqlRealisasi = "SUM(COALESCE(submitted_p, 0) + COALESCE(submitted_r, 0) + COALESCE(approved, 0) + COALESCE(rejected, 0) + COALESCE(revoked, 0) + COALESCE(completed, 0))";
        //     $sqlTotal = "SUM(COALESCE(open, 0) + COALESCE(draft, 0) + COALESCE(submitted_p, 0) + COALESCE(submitted_r, 0) + COALESCE(approved, 0) + COALESCE(rejected, 0) + COALESCE(revoked, 0) + COALESCE(completed, 0))";
        //     $query->addSelect([
        //         'persentase_realisasi' => DataFasih::selectRaw("COALESCE(($sqlRealisasi / NULLIF($sqlTotal, 0)) * 100, 0)")
        //             ->whereColumn("$fasihTable.email", "$pplTable.email")
        //             ->when($sls, fn($q) => $q->where('subsls_code', $sls))
        //             ->when($desa, fn($q) => $q->where('subsls_code', 'like', $desa . '%'))
        //             ->when($kec, fn($q) => $q->where('subsls_code', 'like', $kec . '%'))
        //             ->when($kabkot, fn($q) => $q->where('subsls_code', 'like', $kabkot . '%'))
        //     ])->orderBy('persentase_realisasi', $order);
        // }
        if ($request->has('sortOrder') && $request->sortOrder) {
            $order = $request->sortOrder == 1 ? 'asc' : 'desc';

            // Perbaikan: Hapus prefix agg_fasih. di dalam rumus fungsi agregat ini
            $sqlRealisasi = "SUM(COALESCE(submitted_p, 0) + COALESCE(submitted_r, 0) + COALESCE(approved, 0) + COALESCE(rejected, 0) + COALESCE(revoked, 0) + COALESCE(completed, 0))";
            $sqlTotal     = "SUM(COALESCE(open, 0) + COALESCE(draft, 0) + COALESCE(submitted_p, 0) + COALESCE(submitted_r, 0) + COALESCE(approved, 0) + COALESCE(rejected, 0) + COALESCE(revoked, 0) + COALESCE(completed, 0))";

            // Subquery untuk menghitung persentase per email
            $subQueryFasih = DataFasih::query()
                ->select('email')
                ->selectRaw("COALESCE(($sqlRealisasi / NULLIF($sqlTotal, 0)) * 100, 0) as persentase_realisasi")
                ->when($sls, fn($q) => $q->where('subsls_code', $sls))
                ->when($desa, fn($q) => $q->where('subsls_code', 'like', $desa . '%'))
                ->when($kec, fn($q) => $q->where('subsls_code', 'like', $kec . '%'))
                ->when($kabkot, fn($q) => $q->where('subsls_code', 'like', $kabkot . '%'))
                ->groupBy('email');

            // Gabungkan ke query utama menggunakan leftJoinSub
            $query->leftJoinSub($subQueryFasih, 'agg_fasih', function ($join) use ($pplTable) {
                $join->on("agg_fasih.email", "=", "$pplTable.email");
            })
                ->addSelect(DB::raw("COALESCE(agg_fasih.persentase_realisasi, 0) as persentase_realisasi"))
                ->orderBy('persentase_realisasi', $order);
        }

        $dataPpl = $query->with(['fasih' => $fasihFilter])
            ->paginate($paginated, ['*'], 'page', $currentPage);

        return response()->json($dataPpl);
    }

    public function fetchDataPml(Request $request)
    {
        $paginated = $request->paginated ?? 10;
        $currentPage = $request->currentPage ?? 1;
        $kabkot = $request->kabkot ?? null;
        $kec = $request->kec ?? null;
        $desa = $request->desa ?? null;
        $sls = $request->sls ?? null;
        $nama = $request->nama ?? null;

        $query = Pml::query();

        $pmlTable = (new Pml)->getTable();
        $fasihTable = (new DataFasihPml)->getTable();

        if ($nama) {
            $query->where(function ($q) use ($nama) {
                $q->where('nama', 'like', '%' . $nama . '%')
                    ->orWhere('email', 'like', '%' . $nama . '%');
            });
        }

        $fasihFilter = function ($q) use ($sls, $desa, $kec, $kabkot) {
            if ($sls) $q->where('subsls_code', $sls);
            else if ($desa) $q->where('subsls_code', 'like', $desa . '%');
            else if ($kec) $q->where('subsls_code', 'like', $kec . '%');
            else if ($kabkot) $q->where('subsls_code', 'like', $kabkot . '%');
        };

        $query->whereHas('fasih', $fasihFilter);

        // Default select
        $query->select("$pmlTable.*");

        // if ($request->has('sortOrder') && $request->sortOrder) {
        //     $order = $request->sortOrder == 1 ? 'asc' : 'desc';

        //     $sqlRealisasi = "SUM(COALESCE(submitted_p, 0) + COALESCE(submitted_r, 0) + COALESCE(approved, 0) + COALESCE(rejected, 0) + COALESCE(revoked, 0) + COALESCE(completed, 0))";
        //     $sqlTotal = "SUM(COALESCE(open, 0) + COALESCE(draft, 0) + COALESCE(submitted_p, 0) + COALESCE(submitted_r, 0) + COALESCE(approved, 0) + COALESCE(rejected, 0) + COALESCE(revoked, 0) + COALESCE(completed, 0))";
        //     $query->addSelect([
        //         'persentase_realisasi' => DataFasih::selectRaw("COALESCE(($sqlRealisasi / NULLIF($sqlTotal, 0)) * 100, 0)")
        //             ->whereColumn("$fasihTable.email", "$pmlTable.email")
        //             ->when($sls, fn($q) => $q->where('subsls_code', $sls))
        //             ->when($desa, fn($q) => $q->where('subsls_code', 'like', $desa . '%'))
        //             ->when($kec, fn($q) => $q->where('subsls_code', 'like', $kec . '%'))
        //             ->when($kabkot, fn($q) => $q->where('subsls_code', 'like', $kabkot . '%'))
        //     ])->orderBy('persentase_realisasi', $order);
        // }
        if ($request->has('sortOrder') && $request->sortOrder) {
            $order = $request->sortOrder == 1 ? 'asc' : 'desc';

            // Perbaikan: Hapus prefix agg_fasih. di dalam rumus fungsi agregat ini
            $sqlRealisasi = "SUM(COALESCE(submitted_p, 0) + COALESCE(submitted_r, 0) + COALESCE(approved, 0) + COALESCE(rejected, 0) + COALESCE(revoked, 0) + COALESCE(completed, 0))";
            $sqlTotal     = "SUM(COALESCE(open, 0) + COALESCE(draft, 0) + COALESCE(submitted_p, 0) + COALESCE(submitted_r, 0) + COALESCE(approved, 0) + COALESCE(rejected, 0) + COALESCE(revoked, 0) + COALESCE(completed, 0))";

            // Subquery untuk menghitung persentase per email
            $subQueryFasih = DataFasihPml::query()
                ->select('email')
                ->selectRaw("COALESCE(($sqlRealisasi / NULLIF($sqlTotal, 0)) * 100, 0) as persentase_realisasi")
                ->when($sls, fn($q) => $q->where('subsls_code', $sls))
                ->when($desa, fn($q) => $q->where('subsls_code', 'like', $desa . '%'))
                ->when($kec, fn($q) => $q->where('subsls_code', 'like', $kec . '%'))
                ->when($kabkot, fn($q) => $q->where('subsls_code', 'like', $kabkot . '%'))
                ->groupBy('email');

            // Gabungkan ke query utama menggunakan leftJoinSub
            $query->leftJoinSub($subQueryFasih, 'agg_fasih', function ($join) use ($pmlTable) {
                $join->on("agg_fasih.email", "=", "$pmlTable.email");
            })
                ->addSelect(DB::raw("COALESCE(agg_fasih.persentase_realisasi, 0) as persentase_realisasi"))
                ->orderBy('persentase_realisasi', $order);
        }

        $dataPml = $query->with(['fasih' => $fasihFilter])
            ->paginate($paginated, ['*'], 'page', $currentPage);

        return response()->json($dataPml);
    }

    public function updatePetugas($petugas)
    {
        if ($petugas === 'ppl') {
            $sourceModel = DataFasih::class;
            $targetModel = Ppl::class;
        } else if ($petugas === 'pml') {
            $sourceModel = DataFasihPml::class;
            $targetModel = Pml::class;
        } else {
            return response()->json(['message' => 'Tipe petugas tidak valid'], 400);
        }

        $current_petugas = $sourceModel::select(['email'])
            ->distinct()
            ->pluck('email')
            ->toArray();

        foreach ($current_petugas as $email) {
            $targetModel::firstOrCreate(
                ['email' => $email],
                ['email' => $email]
            );
        }
        return response()->json('Berhasil');
    }

    public function uploadPetugasBatch(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|array',
            'file_name' => 'nullable|string',
        ]);
        $datas = $validated['file'];
        $fileName = $validated['file_name'] ?? 'unknown';
        $array_kako = [
            '7101',
            '7102',
            '7103',
            '7104',
            '7105',
            '7106',
            '7107',
            '7108',
            '7109',
            '7110',
            '7111',
            '7171',
            '7172',
            '7173',
            '7174'
        ];
        $kode = null;
        $exploded_fileName = explode('_', $fileName);
        $pregged_check = '/^ppl_(.+?)\.xlsx$/';
        $expected_format = 'ppl_{kode}.xlsx';

        if (isset($exploded_fileName[0]) && $exploded_fileName[0] === 'pml') {
            $pregged_check = '/^pml_(.+?)\.xlsx$/';
            $expected_format = 'pml_{kode}.xlsx';
        }

        if (preg_match($pregged_check, $fileName, $matches)) {
            $kode = $matches[1];
            if (!in_array($kode, $array_kako)) {
                return response()->json([
                    'success'   => false,
                    'message'   => "Gagal upload {$fileName}: Kode tidak valid. Kode harus salah satu dari: " . implode(', ', $array_kako),
                    'file_name' => $fileName,
                ], 422);
            }
        } else {
            return response()->json([
                'success'   => false,
                'message'   => "Gagal upload {$fileName}: Format file tidak sesuai. Harus berupa {$expected_format}",
                'file_name' => $fileName,
            ], 422);
        }
        $data = array_slice($datas, 1);

        $isPml = (isset($exploded_fileName[0]) && $exploded_fileName[0] === 'pml');
        $sourceModel = $isPml ? Pml::class : Ppl::class;
        $fields = (new $sourceModel())->getFillable();

        $upsertData = [];
        $processedCount = 0;

        try {
            foreach ($data as $row) {
                $mapped = [];
                foreach ($fields as $index => $field) {
                    if (array_key_exists($index, $row)) {
                        $mapped[$field] = $row[$index];
                    }
                }
                if (isset($mapped['nama'])) $mapped['nama'] = ucwords(strtolower($mapped['nama']));
                if (!isset($mapped['email']) || empty($mapped['email'])) {
                    continue;
                }

                $upsertData[] = $mapped;
                $processedCount++;
            }
            if (!empty($upsertData)) {
                $columnsToUpdate = array_keys($upsertData[0]);
                $sourceModel::upsert(
                    $upsertData,
                    ['email'],
                    $columnsToUpdate
                );
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil upload: {$fileName}",
                'file_name' => $fileName,
                'rows_processed' => $processedCount,
                'rows_total' => count($data),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Gagal upload {$fileName}: " . $th->getMessage(),
                'file_name' => $fileName,
                'rows_processed' => $processedCount,
                'rows_total' => count($data),
            ], 422);
        }
    }

    public function fetchKec($kabkot)
    {
        $kec = MasterKec::where('kabkot_code', $kabkot)->get()->map(function ($item) {
            return [
                'code' => $item->code,
                'label' => '[' . $item->code . '] ' . $item->label,
            ];
        });
        return response()->json($kec);
    }

    public function fetchDesa($kec)
    {
        $desa = MasterDesa::where('kec_code', $kec)->get()->map(function ($item) {
            return [
                'code' => $item->code,
                'label' => '[' . $item->code . '] ' . $item->label,
            ];
        });
        return response()->json($desa);
    }

    public function fetchSls($desa)
    {
        $sls = MasterSubSls::where('desa_code', $desa)->get()->map(function ($item) {
            return [
                'code' => $item->code,
                'label' => '[' . $item->code . '] ' . $item->label,
            ];
        });
        return response()->json($sls);
    }

    public function fetchLog(Request $request)
    {
        $paginated = $request->paginated ?? 10;
        $currentPage = $request->currentPage ?? 1;
        $logs = Logs::with(['pegawai', 'kabkot'])->orderBy('created_at', 'desc')
            ->paginate($paginated, ['*'], 'page', $currentPage);

        $mappedCollection = $logs->getCollection()->map(function ($item) {
            $item->formatted_time = $item->updated_at->format('H:i d M Y');
            return $item;
        });
        return response()->json($logs->setCollection($mappedCollection));
    }
}
