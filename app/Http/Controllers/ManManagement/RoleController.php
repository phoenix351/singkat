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
        if ($request->input('fileUpload')) {
            $fileData = $request->input('fileUpload');
            if (
                ($fileData[0][0] ?? null) != 'tipe' ||
                ($fileData[0][1] ?? null) != 'pengguna' ||
                ($fileData[0][2] ?? null) != 'prefix' ||
                ($fileData[0][3] ?? null) != 'role'
            ) {
                return redirect()->route('man-management.role-management.index')
                    ->with('error', 'File yang diupload tidak sesuai template');
            }

            $validRoles = ['viewer', 'operator', 'validator', 'kaprov', 'admin'];
            $validTipes = ['unit', 'tim', 'all'];
            $notification = [];
            $total = count($fileData) - 1;

            foreach ($fileData as $key => $value) {
                if ($key === 0)
                    continue;
                if (empty($value) || count($value) === 0)
                    continue;

                $row = $key + 1;
                $tipe = isset($value[0]) ? trim($value[0]) : null;
                $pengguna = isset($value[1]) ? trim($value[1]) : null;
                $prefix = isset($value[2]) ? trim($value[2]) : null;
                $roles = isset($value[3]) ? trim($value[3]) : null;
                $hasError = false;

                if (!$tipe) {
                    $notification[] = ['type' => 'error', 'message' => "Baris ke-{$row}: kolom tipe tidak boleh kosong"];
                    $hasError = true;
                }
                if (!$prefix) {
                    $notification[] = ['type' => 'error', 'message' => "Baris ke-{$row}: kolom prefix tidak boleh kosong"];
                    $hasError = true;
                }
                if (!$roles) {
                    $notification[] = ['type' => 'error', 'message' => "Baris ke-{$row}: kolom roles tidak boleh kosong"];
                    $hasError = true;
                }

                if ($hasError)
                    continue;

                if (!in_array($tipe, $validTipes)) {
                    $notification[] = ['type' => 'error', 'message' => "Baris ke-{$row}: tipe \"{$tipe}\" tidak valid (harus: unit, tim, all)"];
                    continue;
                }
                if (!in_array($roles, $validRoles)) {
                    $notification[] = ['type' => 'error', 'message' => "Baris ke-{$row}: roles \"{$roles}\" tidak valid (harus: viewer, operator, validator, kaprov, admin)"];
                    continue;
                }

                $app = AppManagement::where('prefix', $prefix)->first();
                if (!$app) {
                    $notification[] = ['type' => 'error', 'message' => "Baris ke-{$row}: Aplikasi dengan prefix \"{$prefix}\" tidak ditemukan"];
                    continue;
                }

                $to_role_id = null;
                if ($tipe !== 'all') {
                    if (!$pengguna) {
                        $notification[] = ['type' => 'error', 'message' => "Baris ke-{$row}: kolom pengguna tidak boleh kosong untuk tipe \"{$tipe}\""];
                        continue;
                    }
                    if ($tipe === 'tim') {
                        $selected_pengguna = TimKerja::where('label', $pengguna)->first();
                        if (!$selected_pengguna) {
                            $notification[] = ['type' => 'error', 'message' => "Baris ke-{$row}: Tim Kerja \"{$pengguna}\" tidak ditemukan"];
                            continue;
                        }
                    } elseif ($tipe === 'unit') {
                        $selected_pengguna = Pegawai::where('nip_lama', $pengguna)->orWhere('nip', $pengguna)->first();
                        if (!$selected_pengguna) {
                            $notification[] = ['type' => 'error', 'message' => "Baris ke-{$row}: Pegawai dengan NIP \"{$pengguna}\" tidak ditemukan"];
                            continue;
                        }
                    }
                    $to_role_id = $selected_pengguna->id;
                }
                $exists = Role::where('type', $tipe)
                    ->where('app_id', $app->id)
                    ->where('roles', $roles)
                    ->when($tipe !== 'all', fn($q) => $q->where('to_role_id', $to_role_id))
                    ->exists();

                if ($exists) {
                    $notification[] = ['type' => 'error', 'message' => "Baris ke-{$row}: Role \"{$roles}\" untuk pengguna \"{$pengguna}\" di aplikasi \"{$prefix}\" sudah ada"];
                    continue;
                }
                try {
                    DB::connection('sulutweb_man_management')->beginTransaction();
                    Role::create([
                        'type' => $tipe,
                        'to_role_id' => $to_role_id,
                        'app_id' => $app->id,
                        'roles' => $roles,
                    ]);
                    DB::connection('sulutweb_man_management')->commit();
                } catch (\Throwable $th) {
                    DB::connection('sulutweb_man_management')->rollBack();
                    $notification[] = ['type' => 'error', 'message' => "Baris ke-{$row}: Gagal menyimpan data, error: " . $th->getMessage()];
                }
            }

            if (count($notification) < $total)
                $notification[] = ['type' => 'success', 'message' => 'Role berhasil di-upload'];

            return redirect()->route('man-management.role-management.index')
                ->with('notification', $notification);
        }
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
