<?php

namespace App\Http\Controllers\Se2026;

use App\Http\Controllers\Controller;
use App\Models\Se2026\HistoryPml;
use App\Models\Se2026\HistoryPpl;
use App\Models\Se2026\MasterKabkot;
use App\Models\Se2026\Pml;
use App\Models\Se2026\Ppl;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PetugasController extends Controller
{
    public function index()
    {
        $kabkot = MasterKabkot::get()->map(function ($item) {
            return [
                'code' => $item->code,
                'label' => '[' . $item->code . '] ' . $item->label,
            ];
        });
        return Inertia::render('Se2026/Petugas', [
            'kabkot' => $kabkot,
        ]);
    }

    private function buildCapaian(
        string $historyModel,
        string $petugasModel,
        Request $request
    ) {
        $paginated = (int) ($request->paginated ?? 10);
        $currentPage = (int) ($request->currentPage ?? 1);
        $kabkot = $request->kabkot ?? null;
        $nama = $request->nama ?? null;
        $daysToShow = (int) ($request->days ?? 5);
        $sortField = $request->sortField ?? null;
        $sortOrder = (int) ($request->sortOrder ?? 1);

        $histInst = new $historyModel();
        $petugasInst = new $petugasModel();
        $histTable = $histInst->getTable();
        $petTable = $petugasInst->getTable();

        $globalFirstDate = $historyModel::min(\DB::raw('DATE(updated_at)'));

        $recentDates = $historyModel::selectRaw("DATE(updated_at) as date")
            ->when($kabkot, fn($q) => $q->where('kabkot_code', $kabkot))
            ->whereRaw("DATE(updated_at) > ?", [$globalFirstDate ?? '1970-01-01'])
            ->distinct()
            ->orderByRaw('date DESC')
            ->limit($daysToShow)
            ->pluck('date')
            ->toArray();
        sort($recentDates);
        $allEmails = $historyModel::query()
            ->from($histTable)
            ->select("$histTable.email")
            ->distinct()
            ->leftJoin($petTable, "$petTable.email", '=', "$histTable.email")
            ->when($kabkot, fn($q) => $q->where("$histTable.kabkot_code", $kabkot))
            ->when($nama, fn($q) => $q->where(function ($q2) use ($nama, $histTable, $petTable) {
                $q2->where("$petTable.nama", 'like', '%' . $nama . '%')
                    ->orWhere("$histTable.email", 'like', '%' . $nama . '%');
            }))
            ->pluck("$histTable.email")
            ->toArray();

        if (empty($allEmails)) {
            return response()->json([
                'paginator' => [
                    'data' => [],
                    'total' => 0,
                    'per_page' => $paginated,
                    'current_page' => $currentPage,
                    'last_page' => 1,
                ],
                'dates' => $recentDates,
            ]);
        }

        $allHistory = $historyModel::query()
            ->from($histTable)
            ->whereIn("$histTable.email", $allEmails)
            ->when($kabkot, fn($q) => $q->where("$histTable.kabkot_code", $kabkot))
            ->selectRaw("
                $histTable.email,
                DATE($histTable.updated_at) as date,
                SUM(COALESCE(submitted_p,0) + COALESCE(submitted_r,0) + COALESCE(approved,0) + COALESCE(rejected,0) + COALESCE(revoked,0) + COALESCE(completed,0)) as realisasi,
                SUM(COALESCE(open,0) + COALESCE(draft,0) + COALESCE(submitted_p,0) + COALESCE(submitted_r,0) + COALESCE(approved,0) + COALESCE(rejected,0) + COALESCE(revoked,0) + COALESCE(completed,0)) as total
            ")
            ->groupBy("$histTable.email", 'date')
            ->orderBy("$histTable.email")
            ->orderBy('date')
            ->get()
            ->groupBy('email');

        $kabkotMap = $historyModel::query()
            ->from($histTable)
            ->whereIn("$histTable.email", $allEmails)
            ->when($kabkot, fn($q) => $q->where("$histTable.kabkot_code", $kabkot))
            ->selectRaw("$histTable.email, $histTable.kabkot_code")
            ->groupBy("$histTable.email", "$histTable.kabkot_code")
            ->pluck("$histTable.kabkot_code", "$histTable.email");

        $namaMap = $petugasModel::whereIn('email', $allEmails)->pluck('nama', 'email');

        $allItems = collect($allEmails)->map(function ($email) use ($allHistory, $namaMap, $kabkotMap, $recentDates) {
            $history = $allHistory->get($email, collect())->sortBy('date')->values();
            $histByDate = $history->keyBy('date');

            $latest = $history->last();
            $totalNow = $latest ? (int) $latest->total : 0;
            $realisasiNow = $latest ? (int) $latest->realisasi : 0;

            $capaian = [];
            foreach ($recentDates as $date) {
                $dayRecord = $histByDate->get($date);
                if (!$dayRecord) {
                    $capaian[$date] = null;
                } else {
                    $prevRecord = $history->filter(fn($h) => $h->date < $date)->last();
                    if (!$prevRecord) {
                        $capaian[$date] = null;
                    } else {
                        $prevReal = (int) $prevRecord->realisasi;
                        $capaian[$date] = max(0, (int) $dayRecord->realisasi - $prevReal);
                    }
                }
            }

            $allDeltas = [];
            $prevReal = 0;
            $isFirst = true;
            foreach ($history as $h) {
                if ($isFirst) {
                    $prevReal = (int) $h->realisasi;
                    $isFirst = false;
                    continue;
                }
                $delta = max(0, (int) $h->realisasi - $prevReal);
                $allDeltas[] = $delta;
                $prevReal = (int) $h->realisasi;
            }
            $rataRata = count($allDeltas) > 0
                ? round(array_sum($allDeltas) / count($allDeltas), 2)
                : 0;

            return [
                'email' => $email,
                'nama' => $namaMap->get($email, '-'),
                'kabkot_code' => $kabkotMap->get($email, '-'),
                'total' => $totalNow,
                'realisasi' => $realisasiNow,
                'capaian' => $capaian,
                'rata_rata' => $rataRata,
            ];
        });

        if (!$sortField) {
            $sorted = $allItems
                ->sortBy([
                    fn($a, $b) => strcmp($a['kabkot_code'], $b['kabkot_code']),
                    fn($a, $b) => strcmp(strtolower($a['nama']), strtolower($b['nama'])),
                ]);
        } elseif (in_array($sortField, ['kabkot_code', 'nama', 'email', 'total', 'realisasi', 'rata_rata'])) {
            $sorted = $sortOrder === -1
                ? $allItems->sortByDesc(fn($i) => is_string($i[$sortField]) ? strtolower($i[$sortField]) : $i[$sortField])
                : $allItems->sortBy(fn($i) => is_string($i[$sortField]) ? strtolower($i[$sortField]) : $i[$sortField]);
        } else {
            $sorted = $sortOrder === -1
                ? $allItems->sortByDesc(fn($i) => $i['capaian'][$sortField] ?? -1)
                : $allItems->sortBy(fn($i) => $i['capaian'][$sortField] ?? -1);
        }

        $total = $sorted->count();
        $offset = ($currentPage - 1) * $paginated;
        $pageData = $sorted->values()->slice($offset, $paginated)->values();

        return response()->json([
            'paginator' => [
                'data' => $pageData,
                'total' => $total,
                'per_page' => $paginated,
                'current_page' => $currentPage,
                'last_page' => max(1, (int) ceil($total / $paginated)),
            ],
            'dates' => $recentDates,
        ]);
    }

    public function capaianPpl(Request $request)
    {
        return $this->buildCapaian(HistoryPpl::class, Ppl::class, $request);
    }

    public function capaianPml(Request $request)
    {
        return $this->buildCapaian(HistoryPml::class, Pml::class, $request);
    }
}
