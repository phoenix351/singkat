<?php

namespace App\Http\Controllers\ManManagement;

use App\Http\Controllers\Controller;
use App\Models\ManManagement\Pegawai;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    //
    public function index(Request $request)
    {
        if ($request->paginated) $paginated = $request->paginated;
        else $paginated = 10;
        if ($request->currentPage) $currentPage = $request->currentPage;
        else $currentPage = 1;

        $query = Pegawai::query();
        if ($request->sortOrder) {
            $order = $request->sortOrder == 1 ? 'asc' : 'desc';
            if ($request->sortField == 'satker') $query->orderBy('organisasi',  $order);
            else $query->orderBy($request->sortField, $order);
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
        $pegawai = $query
            ->paginate($paginated, ['*'], 'page', $currentPage)
            ->through(function ($item) {
                $data = $item->attributesToArray(); // semua kolom model
                $kode_satker = substr($item->organisasi, 0, 4);
                $data['satker'] = $kode_satker . ' - ' . $data['kabupaten'];
                return $data;
            });
        return Inertia::render('ManManagement/Index', ['pegawai' => $pegawai]);
    }
}
