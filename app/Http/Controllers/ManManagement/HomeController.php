<?php

namespace App\Http\Controllers\ManManagement;

use App\Http\Controllers\Controller;
use App\Models\ManManagement\AnggotaTimKerja;
use App\Models\ManManagement\Golongan;
use App\Models\ManManagement\Pegawai;
use App\Models\ManManagement\Satker;
use App\Models\ManManagement\TimKerja;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
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

        $query = Pegawai::query();
        if ($request->sortOrder) {
            $order = $request->sortOrder == 1 ? 'asc' : 'desc';
            if ($request->sortField == 'satker')
                $query->orderBy('organisasi', $order);
            else
                $query->orderBy($request->sortField, $order);
        } else
            $query->orderBy('organisasi', 'asc')
                ->orderBy('name', 'asc');
        if ($request->searchField) {
            $columns = $request->input('listColumn', []);
            $query->where(function ($q) use ($request, $columns) {
                foreach ($columns as $key => $value) {
                    # code...
                    if ($key === 0) {
                        if ($value == 'satker') {
                            $q->where('organisasi', 'like', '%' . $request->searchField . '%')
                                ->orWhere('kabupaten', 'like', '%' . $request->searchField . '%');
                        } else
                            $q->where($value, 'like', '%' . $request->searchField . '%');
                    } else {
                        if ($value == 'satker') {
                            $q->orWhere('organisasi', 'like', '%' . $request->searchField . '%')
                                ->orWhere('kabupaten', 'like', '%' . $request->searchField . '%');
                        } else
                            $q->orWhere($value, 'like', '%' . $request->searchField . '%');
                    }
                }
            });
        }
        $satker = Satker::select(['kode_satker', 'nama_satker'])
            ->orderBy('kode_satker', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->kode_satker . ' - ' . $item->nama_satker,
                    'value' => $item->nama_satker,
                ];
            })
            ->toArray();
        $golongan = Golongan::get()->toArray();
        $pegawai = $query
            ->paginate($paginated, ['*'], 'page', $currentPage)
            ->through(function ($item) {
                $data = $item->attributesToArray(); // semua kolom model
                $kode_satker = substr($item->organisasi, 0, 4);
                $data['satker'] = $kode_satker . ' - ' . $data['kabupaten'];
                return $data;
            });
        return Inertia::render('ManManagement/Index', [
            'pegawai' => $pegawai,
            'satker' => $satker,
            'golongan' => $golongan,
        ]);
    }

    public function tkIndex(Request $request)
    {
        if ($request->paginated)
            $paginated = $request->paginated;
        else
            $paginated = 10;
        if ($request->currentPage)
            $currentPage = $request->currentPage;
        else
            $currentPage = 1;

        $query = TimKerja::query();
        if ($request->sortOrder) {
            $order = $request->sortOrder == 1 ? 'asc' : 'desc';
            $query->orderBy($request->sortField, $order);
        } else
            $query->orderBy('tahun', 'desc')
                ->orderBy('label', 'asc');
        if ($request->searchField) {
            $query->where(function ($q) use ($request) {
                $q->where('tahun', 'like', '%' . $request->searchField . '%')
                    ->orWhere('label', 'like', '%' . $request->searchField . '%');
            });
        }
        $tim_kerja = $query->paginate($paginated, ['*'], 'page', $currentPage);
        return Inertia::render('ManManagement/TimKerja', ['tim_kerja' => $tim_kerja]);
    }

    public function atIndex(Request $request)
    {
        if ($request->paginated)
            $paginated = $request->paginated;
        else
            $paginated = 10;
        if ($request->currentPage)
            $currentPage = $request->currentPage;
        else
            $currentPage = 1;

        $query = AnggotaTimKerja::query()->from('sulutweb_man_management.keanggotaan_timkerja as mmktk');
        $query->join('sulutweb_man_management.pegawai as mmp', 'mmp.id', '=', 'mmktk.pegawai_id');
        $query->join('sulutweb_man_management.timkerja as mmtk', 'mmtk.id', '=', 'mmktk.tim_id');

        if ($request->sortOrder) {
            $order = $request->sortOrder == 1 ? 'asc' : 'desc';
            $query->orderBy($request->sortField, $order);
        } else {
            $query->orderBy('mmtk.label', 'asc')->orderBy('mmktk.keanggotaan', 'desc')->orderBy('mmp.name', 'asc');
        }
        if ($request->searchField) {
            $query->where(function ($q) use ($request) {
                $q->where('mmp.name', 'like', '%' . $request->searchField . '%')
                    ->orWhere('mmktk.keanggotaan', 'like', '%' . $request->searchField . '%')
                    ->orWhere('mmtk.label', 'like', '%' . $request->searchField . '%');
            });
        }
        $query->with(['tim', 'pegawai']);
        $query->select(['mmktk.*']);
        $anggota = $query->paginate($paginated, ['*'], 'page', $currentPage);

        $tim = TimKerja::select(['id as value', 'label'])->orderBy('label', 'asc')->get()->toArray();
        $pegawai = Pegawai::select(['id as value', 'name as label'])->orderBy('name', 'asc')->get()->toArray();
        return Inertia::render('ManManagement/Anggota', [
            'anggota' => $anggota,
            'tim' => $tim,
            'pegawai' => $pegawai
        ]);
    }
}
