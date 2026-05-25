<?php

namespace App\Http\Controllers\ManManagement;

use App\Http\Controllers\Controller;
use App\Models\ManManagement\AppManagement;
use App\Models\ManManagement\Pegawai;
use App\Models\ManManagement\Role;
use App\Models\ManManagement\TimKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class RoleController extends Controller
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

        $query = Role::query()->from('sulutweb_man_management.roles as mmr');
        $query->join('sulutweb_man_management.application_management as mma', 'mma.id', '=', 'mmr.app_id');
        $query->leftJoin('sulutweb_man_management.pegawai as mmp', function ($join) {
            $join->on('mmp.id', '=', 'mmr.to_role_id')
                ->where('mmr.type', '=', 'unit');
        });
        $query->leftJoin('sulutweb_man_management.timkerja as mmtk', function ($join) {
            $join->on('mmtk.id', '=', 'mmr.to_role_id')
                ->where('mmr.type', '=', 'tim');
        });

        if ($request->sortOrder) {
            $order = $request->sortOrder == 1 ? 'asc' : 'desc';
            if ($request->sortField == 'pengguna') {
                $query->orderBy(DB::raw('COALESCE(mmp.name, mmtk.label)'), $order);
            } elseif ($request->sortField == 'app') {
                $query->orderBy('mma.label', $order);
            } else {
                $query->orderBy('mmr.' . $request->sortField, $order);
            }
        } else {
            $query->orderBy('mma.label', 'asc');
        }

        if ($request->searchField) {
            $query->where(function ($q) use ($request) {
                $q->where('mma.label', 'like', '%' . $request->searchField . '%')
                    ->orWhere('mmr.type', 'like', '%' . $request->searchField . '%')
                    ->orWhere('mmr.roles', 'like', '%' . $request->searchField . '%')
                    ->orWhere('mmp.name', 'like', '%' . $request->searchField . '%')
                    ->orWhere('mmtk.label', 'like', '%' . $request->searchField . '%');
            });
        }
        $query->select(['mmr.*', 'mma.label as app', DB::raw('COALESCE(mmp.name, mmtk.label) as pengguna')]);
        $roles = $query->paginate($paginated, ['*'], 'page', $currentPage)
            ->through(function ($item) {
                $data = $item->attributesToArray();
                return $data;
            });
        $apps = AppManagement::select(['id as value', 'label'])->get();
        return Inertia::render('ManManagement/Role', [
            'roles' => $roles,
            'apps' => $apps
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => ['sometimes', 'nullable'],
            'type' => ['required', 'string', 'max:4'],
            'to_role_id' => ['required_unless:type,all', 'nullable', 'integer'],
            'app_id' => ['required', 'integer'],
            'roles' => ['required', 'string', 'max:20'],
        ]);
        $id = $validated['id'] ?? null;

        if ($request->isMethod('patch')) {
            try {
                //code...
                DB::connection('sulutweb_man_management')->beginTransaction();
                $role_to_update = Role::findOrFail($id);
                if ($role_to_update)
                    $role_to_update->update($validated);
                DB::connection('sulutweb_man_management')->commit();
                return redirect()->route('man-management.role-management.index')->with('success', 'Berhasil edit data');
            } catch (\Throwable $th) {
                //throw $th;
                DB::connection('sulutweb_man_management')->rollBack();
                return redirect()->route('man-management.role-management.index')->with('error', 'Gagal edit data, error : ' . $th->getMessage());
            }
        }
        try {
            //code...
            DB::connection('sulutweb_man_management')->beginTransaction();
            Role::create($validated);
            DB::connection('sulutweb_man_management')->commit();
            return redirect()->route('man-management.role-management.index')->with('success', 'Data berhasil ditambahkan');
        } catch (\Throwable $th) {
            //throw $th;
            DB::connection('sulutweb_man_management')->rollBack();
            return redirect()->route('man-management.role-management.index')->with('error', 'Terjadi kesalahan, error : ' . $th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            //code...
            DB::connection('sulutweb_man_management')->beginTransaction();
            $role_to_delete = Role::findOrFail($id);
            if ($role_to_delete)
                $role_to_delete->delete();
            DB::connection('sulutweb_man_management')->commit();
            return redirect()->route('man-management.role-management.index')->with('success', 'data berhasil dihapus');
        } catch (\Throwable $th) {
            //throw $th;
            DB::connection('sulutweb_man_management')->rollBack();
            return redirect()->route('man-management.role-management.index')->with('error', 'data gagal dihapus, error: ' . $th->getMessage());
        }
    }

    public function fetchPegawai()
    {
        $this_pegawai = Pegawai::select(['name as label', 'id as value'])->get();
        return response()->json($this_pegawai);
    }

    public function fetchTim()
    {
        $this_tim = TimKerja::select(['tahun', 'label', 'id as value'])->get()
            ->map(function ($item) {
                return [
                    'label' => $item->tahun . ' - ' . $item->label,
                    'value' => $item->value,
                ];
            });
        return response()->json($this_tim);
    }
}
