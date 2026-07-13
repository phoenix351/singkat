<?php

namespace App\Http\Controllers\Se2026;

use App\Http\Controllers\Controller;
use App\Models\Se2026\DataFasih;
use App\Models\Se2026\DataFasihPml;
use App\Models\Se2026\HistoryPml;
use App\Models\Se2026\HistoryPpl;
use App\Models\Se2026\HistoryWilayah;
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
        $edited_a = 0;
        $rejected_a = 0;
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
            $edited_a = $edited_a + ($f->edited_a ?? 0);
            $rejected_a = $rejected_a + ($f->rejected_a ?? 0);
        }
        $total = $open + $draft + $submitted_p + $submitted_r + $approved + $rejected + $revoked + $completed + $edited_a + $rejected_a;

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
                'edited_a' => $group->sum('edited_a'),
                'rejected_a' => $group->sum('rejected_a'),
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
                        'edited_a' => $group->sum('edited_a'),
                        'rejected_a' => $group->sum('rejected_a'),
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
                        'edited_a' => $group->sum('edited_a'),
                        'rejected_a' => $group->sum('rejected_a'),
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
                        'edited_a' => $group->sum('edited_a'),
                        'rejected_a' => $group->sum('rejected_a'),
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
            'edited_a' => $edited_a,
            'rejected_a' => $rejected_a,
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
            'confirmed' => 'nullable|boolean',
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
        $cleanName = str_replace(['.csv', '.json'], '', $fileName);
        $exploded_fileName = explode('_', $cleanName);
        $totalElement = count($exploded_fileName);
        $tgl = $exploded_fileName[$totalElement - 2];
        $jam = $exploded_fileName[$totalElement - 1];
        $updatedAtReal = Carbon::createFromFormat('YmdHis', $tgl . $jam)->toDateTimeString();

        $pregged_check = '/^scraped_data_(.+?)_\d{8}_\d{6}\.(csv|json)$/';
        $expected_format = 'scraped_data_{kode}_{Ymd}_{His}.(csv|json)';

        if (isset($exploded_fileName[2]) && $exploded_fileName[2] === 'pml') {
            $pregged_check = '/^scraped_data_pml_(.+?)_\d{8}_\d{6}\.(csv|json)$/';
            $expected_format = 'scraped_data_pml_{kode}_{Ymd}_{His}.(csv|json)';
        }

        if (preg_match($pregged_check, $fileName, $matches)) {
            $kode = $matches[1];
            if (!in_array($kode, $array_kako)) {
                return response()->json([
                    'success' => false,
                    'message' => "Gagal upload {$fileName}: Kode tidak valid. Kode harus salah satu dari: " . implode(', ', $array_kako),
                    'file_name' => $fileName,
                ], 422);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => "Gagal upload {$fileName}: Format file tidak sesuai. Harus berupa {$expected_format}",
                'file_name' => $fileName,
            ], 422);
        }
        $data = array_slice($datas, 1);

        $field_ppl = (new DataFasih())->getFillable();
        $field_pml = (new DataFasihPml())->getFillable();
        $fields = $exploded_fileName[2] != 'pml' ? $field_ppl : $field_pml;
        $isPml = $exploded_fileName[2] === 'pml';
        $confirmed = filter_var($validated['confirmed'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $processedCount = 0;

        // ─── Pengecekan sum status sebelum proses ───
        if (!$confirmed) {
            $statusCols = ['open', 'draft', 'submitted_p', 'submitted_r', 'approved', 'rejected', 'revoked', 'completed', 'edited_a', 'rejected_a'];
            $sqlSum = implode(' + ', array_map(fn($col) => "COALESCE({$col}, 0)", $statusCols));

            $existingTotal = $isPml
                ? DataFasihPml::where('subsls_code', 'like', $kode . '%')->selectRaw("SUM({$sqlSum}) as total")->value('total') ?? 0
                : DataFasih::where('subsls_code', 'like', $kode . '%')->selectRaw("SUM({$sqlSum}) as total")->value('total') ?? 0;

            // Hitung sum dari data yang diupload
            $statusIndexes = array_flip($fields); // field => index
            $newTotal = 0;
            foreach ($data as $row) {
                foreach ($statusCols as $col) {
                    if (isset($statusIndexes[$col]) && array_key_exists($statusIndexes[$col], $row)) {
                        $newTotal += (int) $row[$statusIndexes[$col]];
                    }
                }
            }

            if ($existingTotal > 0 && $newTotal < $existingTotal) {
                return response()->json([
                    'success' => false,
                    'needs_confirmation' => true,
                    'message' => "Data yang diupload memiliki total status lebih sedikit dari data yang ada di database.",
                    'file_name' => $fileName,
                    'existing_total' => (int) $existingTotal,
                    'new_total' => $newTotal,
                    'kode' => $kode,
                ], 200);
            }
        }

        try {
            DB::connection('sulutweb_se2026')->beginTransaction();
            if (!$isPml) {
                DataFasih::where('subsls_code', 'like', $kode . '%')->delete();
            } else {
                DataFasihPml::where('subsls_code', 'like', $kode . '%')->delete();
            }

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
                        $new_data = DataFasih::updateOrCreate([
                            'email' => $mapped['email'] ?? null,
                            'subsls_code' => $mapped['subsls_code'] ?? null,
                        ], $mapped);
                    } else {
                        $new_data = DataFasihPml::updateOrCreate([
                            'email' => $mapped['email'] ?? null,
                            'subsls_code' => $mapped['subsls_code'] ?? null,
                        ], $mapped);
                    }
                    $processedCount++;
                }
            }

            $dataModel = $isPml ? DataFasihPml::class : DataFasih::class;
            $updatedAtDate = Carbon::parse($updatedAtReal)->toDateString();

            $subsls_code_list = $dataModel::where('subsls_code', 'like', $kode . '%')
                ->selectRaw('subsls_code,
                    SUM(COALESCE(open, 0)) as open,
                    SUM(COALESCE(draft, 0)) as draft,
                    SUM(COALESCE(submitted_p, 0)) as submitted_p,
                    SUM(COALESCE(submitted_r, 0)) as submitted_r,
                    SUM(COALESCE(approved, 0)) as approved,
                    SUM(COALESCE(rejected, 0)) as rejected,
                    SUM(COALESCE(revoked, 0)) as revoked,
                    SUM(COALESCE(completed, 0)) as completed,
                    SUM(COALESCE(edited_a, 0)) as edited_a,
                    SUM(COALESCE(rejected_a, 0)) as rejected_a')
                ->groupBy('subsls_code')
                ->get();

            foreach ($subsls_code_list as $value) {
                $existing = HistoryWilayah::where('subsls_code', $value->subsls_code)
                    ->whereDate('updated_at', $updatedAtDate)
                    ->first();

                $historyData = [
                    'open' => $value->open,
                    'draft' => $value->draft,
                    'submitted_p' => $value->submitted_p,
                    'submitted_r' => $value->submitted_r,
                    'approved' => $value->approved,
                    'rejected' => $value->rejected,
                    'revoked' => $value->revoked,
                    'completed' => $value->completed,
                    'edited_a' => $value->edited_a,
                    'rejected_a' => $value->rejected_a,
                    'updated_at' => $updatedAtReal,
                    'created_at' => $existing ? $existing->created_at : $updatedAtReal,
                ];

                if ($existing) {
                    $existing->update($historyData);
                } else {
                    HistoryWilayah::insert(array_merge(['subsls_code' => $value->subsls_code], $historyData));
                }
            }
            $historyEmailModel = $isPml ? HistoryPml::class : HistoryPpl::class;

            $email_list = $dataModel::where('subsls_code', 'like', $kode . '%')
                ->selectRaw('email,
                    SUBSTRING(subsls_code, 1, 4) as kabkot_code,
                    SUM(COALESCE(open, 0)) as open,
                    SUM(COALESCE(draft, 0)) as draft,
                    SUM(COALESCE(submitted_p, 0)) as submitted_p,
                    SUM(COALESCE(submitted_r, 0)) as submitted_r,
                    SUM(COALESCE(approved, 0)) as approved,
                    SUM(COALESCE(rejected, 0)) as rejected,
                    SUM(COALESCE(revoked, 0)) as revoked,
                    SUM(COALESCE(completed, 0)) as completed,
                    SUM(COALESCE(edited_a, 0)) as edited_a,
                    SUM(COALESCE(rejected_a, 0)) as rejected_a')
                ->groupBy('email', 'kabkot_code')
                ->get();

            foreach ($email_list as $value) {
                $existing = $historyEmailModel::where('email', $value->email)
                    ->whereDate('updated_at', $updatedAtDate)
                    ->first();

                $historyData = [
                    'kabkot_code' => $value->kabkot_code,
                    'open' => $value->open,
                    'draft' => $value->draft,
                    'submitted_p' => $value->submitted_p,
                    'submitted_r' => $value->submitted_r,
                    'approved' => $value->approved,
                    'rejected' => $value->rejected,
                    'revoked' => $value->revoked,
                    'completed' => $value->completed,
                    'edited_a' => $value->edited_a,
                    'rejected_a' => $value->rejected_a,
                    'updated_at' => $updatedAtReal,
                    'created_at' => $existing ? $existing->created_at : $updatedAtReal,
                ];

                if ($existing) {
                    $existing->update($historyData);
                } else {
                    $historyEmailModel::insert(array_merge(['email' => $value->email], $historyData));
                }
            }

            $logs = Logs::create([
                'kode' => $kode,
                'pegawai_id' => Auth::id(),
                'data_type' => $exploded_fileName[2] != 'pml' ? 'ppl' : 'pml',
                'updated_at' => $updatedAtReal
            ]);
            DB::connection('sulutweb_se2026')->commit();
            return response()->json([
                'success' => true,
                'message' => "Berhasil upload: {$fileName}",
                'file_name' => $fileName,
                'rows_processed' => $processedCount,
                'rows_total' => count($data),
            ]);
        } catch (\Throwable $th) {
            DB::connection('sulutweb_se2026')->rollBack();
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

        $fasihTable = (new DataFasih)->getTable();
        $pplTable = (new Ppl)->getTable();

        $sqlRealisasi = "SUM(COALESCE(submitted_p, 0) + COALESCE(submitted_r, 0) + COALESCE(approved, 0) + COALESCE(rejected, 0) + COALESCE(revoked, 0) + COALESCE(completed, 0) + COALESCE(edited_a, 0) + COALESCE(rejected_a, 0))";
        $sqlTotal = "SUM(COALESCE(open, 0) + COALESCE(draft, 0) + COALESCE(submitted_p, 0) + COALESCE(submitted_r, 0) + COALESCE(approved, 0) + COALESCE(rejected, 0) + COALESCE(revoked, 0) + COALESCE(completed, 0) + COALESCE(edited_a, 0) + COALESCE(rejected_a, 0))";

        $query = DataFasih::query()
            ->select([
                "$fasihTable.email",
                DB::raw("MAX($pplTable.nama) as nama"),
                DB::raw("$sqlRealisasi as realisasi"),
                DB::raw("$sqlTotal as total_target"),
                DB::raw("COALESCE(($sqlRealisasi / NULLIF($sqlTotal, 0)) * 100, 0) as persentase_realisasi"),
            ])
            ->leftJoin($pplTable, "$pplTable.email", '=', "$fasihTable.email")
            ->groupBy("$fasihTable.email");

        if ($sls)
            $query->where("$fasihTable.subsls_code", $sls);
        elseif ($desa)
            $query->where("$fasihTable.subsls_code", 'like', $desa . '%');
        elseif ($kec)
            $query->where("$fasihTable.subsls_code", 'like', $kec . '%');
        elseif ($kabkot)
            $query->where("$fasihTable.subsls_code", 'like', $kabkot . '%');

        if ($nama) {
            $query->where(function ($q) use ($nama, $fasihTable, $pplTable) {
                $q->where("$pplTable.nama", 'like', '%' . $nama . '%')
                    ->orWhere("$fasihTable.email", 'like', '%' . $nama . '%');
            });
        }

        if ($request->has('sortOrder') && $request->sortOrder) {
            $order = $request->sortOrder == 1 ? 'asc' : 'desc';
            $query->orderBy('persentase_realisasi', $order);
        } else {
            $query->orderByRaw("MIN($fasihTable.subsls_code) asc");
            $query->orderBy("$fasihTable.email");
        }

        $paginator = $query->paginate($paginated, ['*'], 'page', $currentPage);
        $emails = $paginator->pluck('email')->toArray();
        $fasihDetails = DataFasih::query()
            ->whereIn('email', $emails)
            ->when($sls, fn($q) => $q->where('subsls_code', $sls))
            ->when(!$sls && $desa, fn($q) => $q->where('subsls_code', 'like', $desa . '%'))
            ->when(!$sls && !$desa && $kec, fn($q) => $q->where('subsls_code', 'like', $kec . '%'))
            ->when(!$sls && !$desa && !$kec && $kabkot, fn($q) => $q->where('subsls_code', 'like', $kabkot . '%'))
            ->with(['subsls', 'subsls.desa', 'subsls.desa.kec', 'subsls.desa.kec.kabkot'])
            // ->orderByRaw("MIN(subsls_code) asc")
            ->orderBy("subsls_code", "asc")
            ->get()
            ->groupBy('email');

        $items = $paginator->getCollection()->map(function ($item) use ($fasihDetails) {
            $item->fasih = $fasihDetails->get($item->email, collect())->values();
            return $item;
        });
        $paginator->setCollection($items);

        return response()->json($paginator);
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

        $fasihTable = (new DataFasihPml)->getTable();
        $pmlTable = (new Pml)->getTable();

        $sqlRealisasi = "SUM(COALESCE(submitted_p, 0) + COALESCE(submitted_r, 0) + COALESCE(approved, 0) + COALESCE(rejected, 0) + COALESCE(revoked, 0) + COALESCE(completed, 0) + COALESCE(edited_a, 0) + COALESCE(rejected_a, 0))";
        $sqlTotal = "SUM(COALESCE(open, 0) + COALESCE(draft, 0) + COALESCE(submitted_p, 0) + COALESCE(submitted_r, 0) + COALESCE(approved, 0) + COALESCE(rejected, 0) + COALESCE(revoked, 0) + COALESCE(completed, 0) + COALESCE(edited_a, 0) + COALESCE(rejected_a, 0))";

        $query = DataFasihPml::query()
            ->select([
                "$fasihTable.email",
                DB::raw("MAX($pmlTable.nama) as nama"),
                DB::raw("$sqlRealisasi as realisasi"),
                DB::raw("$sqlTotal as total_target"),
                DB::raw("COALESCE(($sqlRealisasi / NULLIF($sqlTotal, 0)) * 100, 0) as persentase_realisasi"),
            ])
            ->leftJoin($pmlTable, "$pmlTable.email", '=', "$fasihTable.email")
            ->groupBy("$fasihTable.email");

        if ($sls)
            $query->where("$fasihTable.subsls_code", $sls);
        elseif ($desa)
            $query->where("$fasihTable.subsls_code", 'like', $desa . '%');
        elseif ($kec)
            $query->where("$fasihTable.subsls_code", 'like', $kec . '%');
        elseif ($kabkot)
            $query->where("$fasihTable.subsls_code", 'like', $kabkot . '%');

        if ($nama) {
            $query->where(function ($q) use ($nama, $fasihTable, $pmlTable) {
                $q->where("$pmlTable.nama", 'like', '%' . $nama . '%')
                    ->orWhere("$fasihTable.email", 'like', '%' . $nama . '%');
            });
        }

        if ($request->has('sortOrder') && $request->sortOrder) {
            $order = $request->sortOrder == 1 ? 'asc' : 'desc';
            $query->orderBy('persentase_realisasi', $order);
        } else {
            $query->orderByRaw("MIN($fasihTable.subsls_code) asc");
            $query->orderBy("$fasihTable.email");
        }

        $paginator = $query->paginate($paginated, ['*'], 'page', $currentPage);

        $emails = $paginator->pluck('email')->toArray();
        $fasihDetails = DataFasihPml::query()
            ->whereIn('email', $emails)
            ->when($sls, fn($q) => $q->where('subsls_code', $sls))
            ->when(!$sls && $desa, fn($q) => $q->where('subsls_code', 'like', $desa . '%'))
            ->when(!$sls && !$desa && $kec, fn($q) => $q->where('subsls_code', 'like', $kec . '%'))
            ->when(!$sls && !$desa && !$kec && $kabkot, fn($q) => $q->where('subsls_code', 'like', $kabkot . '%'))
            ->with(['subsls', 'subsls.desa', 'subsls.desa.kec', 'subsls.desa.kec.kabkot'])
            ->orderBy("subsls_code", "asc")
            ->get()
            ->groupBy('email');

        $items = $paginator->getCollection()->map(function ($item) use ($fasihDetails) {
            $item->fasih = $fasihDetails->get($item->email, collect())->values();
            return $item;
        });
        $paginator->setCollection($items);

        return response()->json($paginator);
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

        $emailsInFasih = $sourceModel::select('email')
            ->distinct()
            ->pluck('email')
            ->toArray();

        $deleted = $targetModel::whereNotIn('email', $emailsInFasih)->delete();

        $inserted = 0;
        foreach ($emailsInFasih as $email) {
            $created = $targetModel::firstOrCreate(['email' => $email]);
            if ($created->wasRecentlyCreated) {
                $inserted++;
            }
        }

        return response()->json([
            'message' => 'Berhasil sync petugas',
            'inserted' => $inserted,
            'deleted' => $deleted,
            'total' => count($emailsInFasih),
        ]);
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
                    'success' => false,
                    'message' => "Gagal upload {$fileName}: Kode tidak valid. Kode harus salah satu dari: " . implode(', ', $array_kako),
                    'file_name' => $fileName,
                ], 422);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => "Gagal upload {$fileName}: Format file tidak sesuai. Harus berupa {$expected_format}",
                'file_name' => $fileName,
            ], 422);
        }
        $data = array_slice($datas, 1);

        $isPml = (isset($exploded_fileName[0]) && $exploded_fileName[0] === 'pml');
        $sourceModel = $isPml ? Pml::class : Ppl::class;
        $fields = (new $sourceModel())->getFillable();

        $updateData = [];
        $processedCount = 0;
        $skippedCount = 0;

        try {
            foreach ($data as $row) {
                $mapped = [];
                foreach ($fields as $index => $field) {
                    if (array_key_exists($index, $row)) {
                        $mapped[$field] = $row[$index];
                    }
                }
                if (isset($mapped['nama']))
                    $mapped['nama'] = ucwords(strtolower($mapped['nama']));
                if (isset($mapped['email']))
                    $mapped['email'] = strtolower(trim($mapped['email']));
                if (!isset($mapped['email']) || empty($mapped['email'])) {
                    continue;
                }

                $updateData[] = $mapped;
            }

            foreach ($updateData as $record) {
                $updated = $sourceModel::where('email', $record['email'])
                    ->update(['nama' => $record['nama'] ?? null]);

                if ($updated > 0) {
                    $processedCount++;
                } else {
                    $skippedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil upload: {$fileName}. {$skippedCount} baris dilewati (email tidak ditemukan).",
                'file_name' => $fileName,
                'rows_processed' => $processedCount,
                'rows_skipped' => $skippedCount,
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

    /**
     * Fetch per-subsls and per-email totals from DB for a kode (kabkot),
     * so the frontend can diff against the newly uploaded file.
     */
    public function fetchDiffCheck(Request $request)
    {
        $kode   = $request->kode;
        $isPml  = filter_var($request->is_pml ?? false, FILTER_VALIDATE_BOOLEAN);

        if (!$kode) {
            return response()->json(['message' => 'Kode diperlukan'], 422);
        }

        $statusCols = ['open', 'draft', 'submitted_p', 'submitted_r', 'approved', 'rejected', 'revoked', 'completed', 'edited_a', 'rejected_a'];
        $sqlSum     = implode(' + ', array_map(fn($col) => "COALESCE({$col}, 0)", $statusCols));

        $dataModel = $isPml ? DataFasihPml::class : DataFasih::class;

        // Per subsls_code
        $bySubsls = $dataModel::where('subsls_code', 'like', $kode . '%')
            ->selectRaw("subsls_code, ({$sqlSum}) as total")
            ->get()
            ->map(fn($r) => ['subsls_code' => $r->subsls_code, 'total' => (int) $r->total]);

        // Per email (aggregated across all subsls for that kode)
        $byEmail = $dataModel::where('subsls_code', 'like', $kode . '%')
            ->selectRaw("email, SUM({$sqlSum}) as total")
            ->groupBy('email')
            ->get()
            ->map(fn($r) => ['email' => $r->email, 'total' => (int) $r->total]);

        return response()->json([
            'by_subsls' => $bySubsls,
            'by_email'  => $byEmail,
        ]);
    }
}
