<?php

namespace App\Http\Controllers\ManManagement;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Controller;
use App\Models\ManManagement\AnggotaTimKerja;
use App\Models\ManManagement\Pegawai as ManManagementPegawai;
use App\Models\ManManagement\TimKerja;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PegawaiController extends Controller
{
    //
    public function getCurrentPegawai()
    {
        $pegawai = Pegawai::pluck('nip_bps')->unique()->toArray();
        return response()->json($pegawai);
    }

    public function checkPegawai()
    {
        $pegawai = ManManagementPegawai::pluck('nip_lama')->unique()->toArray();
        return response()->json($pegawai);
    }

    public function uploadPegawai(Request $request)
    {
        if ($request->isMethod('patch')) {
            $validated = $request->validate([
                'id' => ['required'],
                'nip_lama' => ['required'],
                'nip' => ['required'],
                'username' => ['required'],
                'email' => ['required'],
                'name' => ['required'],
                'golongan' => ['required'],
                'jabatan' => ['required'],
                'organisasi' => ['required'],
                'provinsi' => ['required'],
                'kabupaten' => ['required'],
                'foto' => ['required'],
            ]);
            if ($validated['provinsi'] != 'Sulawesi Utara')
                return redirect()->route('man-management.index')->with('error', 'Data NIP : ' . $validated['nip'] . ' bukan pegawai Sulawesi Utara');
            try {
                //code...
                DB::connection('sulutweb_man_management')->beginTransaction();
                $pegawai_to_update = ManManagementPegawai::findOrFail($validated['id']);
                if ($pegawai_to_update)
                    $pegawai_to_update->update($validated);
                DB::connection('sulutweb_man_management')->commit();
                return redirect()->route('man-management.index')->with('success', 'Data pegawai berhasil di-update');
            } catch (\Throwable $th) {
                //throw $th;
                DB::connection('sulutweb_man_management')->rollBack();
                return redirect()->route('man-management.index')->with('error', 'Data pegawai gagal di-update, error : ' . $th->getMessage());
            }
        }
        $lc = app(LoginController::class);
        $result = $lc->ssoAPI($request->nip);
        $data = $result[0]['attributes'] ?? null;
        if (!$data)
            return redirect()->route('man-management.index')->with('error', 'Data tidak ditemukan');
        $payload = [
            'nip_lama' => $data['attribute-nip-lama'][0] ?? null,
            'nip' => $data['attribute-nip'][0] ?? null,
            'username' => $data['attribute-username'][0] ?? null,
            'email' => $data['attribute-email'][0] ?? null,
            'name' => $data['attribute-nama'][0] ?? null,
            'golongan' => $data['attribute-golongan'][0] ?? null,
            'jabatan' => $data['attribute-jabatan'][0] ?? null,
            'organisasi' => $data['attribute-organisasi'][0] ?? null,
            'provinsi' => $data['attribute-provinsi'][0] ?? null,
            'kabupaten' => $data['attribute-kabupaten'][0] ?? null,
            'foto' => $data['attribute-foto'][0] ?? null,
        ];
        if (
            !$payload['nip_lama'] ||
            !$payload['nip'] ||
            !$payload['username'] ||
            !$payload['email'] ||
            !$payload['name'] ||
            !$payload['organisasi'] ||
            !$payload['provinsi'] ||
            !$payload['kabupaten']
        ) {
            return redirect()->route('man-management.index')->with('error', 'Data NIP : ' . $request->nip . ' tolong diperiksa lagi');
        }
        if ($payload['provinsi'] !== 'Sulawesi Utara') {
            return redirect()->route('man-management.index')->with('error', 'Data NIP : ' . $request->nip . ' bukan pegawai Sulawesi Utara');
        }
        ManManagementPegawai::updateOrCreate(
            ['nip_lama' => $payload['nip_lama']],
            $payload
        );

        return redirect()->route('man-management.index')->with('success', 'Pegawai NIP : ' . $request->nip . ' berhasil ditambahkan');
    }

    public function syncPegawai(Request $request)
    {
        $lc = app(LoginController::class);
        $result = $lc->ssoAPI($request->nip);
        $data = $result[0]['attributes'] ?? null;
        if (!$data)
            return redirect()->route('man-management.index')->with('error', 'Data tidak ditemukan');
        $payload = [
            'nip_lama' => $data['attribute-nip-lama'][0] ?? null,
            'nip' => $data['attribute-nip'][0] ?? null,
            'username' => $data['attribute-username'][0] ?? null,
            'email' => $data['attribute-email'][0] ?? null,
            'name' => $data['attribute-nama'][0] ?? null,
            'golongan' => $data['attribute-golongan'][0] ?? null,
            'jabatan' => $data['attribute-jabatan'][0] ?? null,
            'organisasi' => $data['attribute-organisasi'][0] ?? null,
            'provinsi' => $data['attribute-provinsi'][0] ?? null,
            'kabupaten' => $data['attribute-kabupaten'][0] ?? null,
            'foto' => $data['attribute-foto'][0] ?? null,
        ];
        if (
            !$payload['nip_lama'] ||
            !$payload['nip'] ||
            !$payload['username'] ||
            !$payload['email'] ||
            !$payload['name'] ||
            !$payload['organisasi'] ||
            !$payload['provinsi'] ||
            !$payload['kabupaten']
        ) {
            return redirect()->route('man-management.index')->with('error', 'Data NIP : ' . $request->nip . ' tolong diperiksa lagi');
        }
        ManManagementPegawai::where('nip_lama', $payload['nip_lama'])->update(
            $payload
        );
        return redirect()->route('man-management.index')->with('success', 'Pegawai NIP : ' . $request->nip . ' berhasil di-update');
    }

    public function tkStore(Request $request)
    {
        if ($request->input('fileUpload')) {
            $fileData = $request->input('fileUpload');
            if ($fileData[0][0] != 'tahun' && $fileData[0][0] != 'label') {
                return redirect()->route('man-management.tim-kerja.index')->with('error', 'File yang diupload tidak sesuai template');
            }
            $notification = [];
            $total = count($fileData) - 1;
            foreach ($fileData as $key => $value) {
                # code...
                if ($key === 0)
                    continue;
                if (!empty($value) && count($value) > 0) {
                    $result = [
                        'tahun' => $value[0] ?? null,
                        'label' => trim($value[1] ?? '') ?? null,
                    ];
                    $validator = Validator::make($result, [
                        'tahun' => ['required', 'date_format:Y'],
                        'label' => ['required', 'string'],
                    ], [
                        'tahun.required' => "Baris ke-" . ($key + 1) . " kolom tahun wajib diisi.",
                        'tahun.date_format' => "Baris ke-" . ($key + 1) . " tahun harus format YYYY.",
                        'label.required' => "Baris ke-" . ($key + 1) . " kolom label wajib diisi.",
                        'label.string' => "Baris ke-" . ($key + 1) . " label harus berupa teks.",
                    ]);
                    if ($validator->fails()) {
                        $notification[] = ['type' => 'error', 'message' => $validator->errors()->first()];
                        continue;
                    }
                    $check = TimKerja::where('tahun', $result['tahun'])
                        ->where('label', $result['label'])->first();
                    if ($check) {
                        $notification[] = ['type' => 'error', 'message' => 'Tim Kerja baris ke-' . $key + 1 . ' sudah ada di database'];
                    } else
                        TimKerja::create($result);
                }
            }
            if (count($notification) < $total)
                $notification[] = ['type' => 'success', 'message' => 'Tim Kerja berhasil di upload'];
            return redirect()->route('man-management.tim-kerja.index')->with('notification', $notification);
        }
        $validated = $request->validate([
            'id' => ['sometimes', 'nullable'],
            'tahun' => ['required', 'date_format:Y'],
            'label' => ['required', 'string']
        ]);
        $id = $validated['id'] ?? null;
        $check = TimKerja::where('tahun', $validated['tahun'])
            ->when($id, function ($query) use ($id) {
                $query->where('id', '!=', $id);
            })
            ->where('label', $validated['label'])
            ->first();
        if ($check)
            return redirect()->route('man-management.tim-kerja.index')->with('error', 'Tim Kerja sudah ada');

        if ($request->isMethod('patch')) {
            try {
                //code...
                DB::connection('sulutweb_man_management')->beginTransaction();
                $timkerja_to_updated = TimKerja::findOrFail($validated['id']);
                if ($timkerja_to_updated)
                    $timkerja_to_updated->update($validated);
                DB::connection('sulutweb_man_management')->commit();
                return redirect()->route('man-management.tim-kerja.index')->with('success', 'Tim Kerja berhasil diedit');
            } catch (\Throwable $th) {
                //throw $th;
                DB::connection('sulutweb_man_management')->rollBack();
                return redirect()->route('man-management.tim-kerja.index')->with('error', 'Terjadi kesalahan dalam data');
            }
        }
        try {
            //code...
            DB::connection('sulutweb_man_management')->beginTransaction();
            TimKerja::create($validated);
            DB::connection('sulutweb_man_management')->commit();
            return redirect()->route('man-management.tim-kerja.index')->with('success', 'Tim Kerja berhasil ditambahkan');
        } catch (\Throwable $th) {
            //throw $th;
            DB::connection('sulutweb_man_management')->rollBack();
            return redirect()->route('man-management.tim-kerja.index')->with('error', 'Terjadi kesalahan dalam data');
        }
    }

    public function atStore(Request $request)
    {
        if ($request->input('fileUpload')) {
            $fileData = $request->input('fileUpload');
            if ($fileData[0][0] != 'nama_tim' && $fileData[0][1] != 'nip_pegawai' && $fileData[0][2] != 'nama_pegawai' && $fileData[0][3] != 'keanggotaan_tim') {
                return redirect()->route('man-management.anggota.index')->with('error', 'File yang diupload tidak sesuai template');
            }
            $notification = [];
            $total = count($fileData) - 1;
            foreach ($fileData as $key => $value) {
                # code...
                if ($key === 0)
                    continue;
                if (!empty($value) && count($value) > 0) {
                    $tim = TimKerja::where('label', $value[0])->value('id');
                    $nip_pegawai = trim($value[1] ?? '');
                    $nama_pegawai = trim($value[2] ?? '');
                    $keanggotaan = trim($value[3] ?? '');

                    if (!$tim)
                        $notification[] = ['type' => 'error', 'message' => 'Baris ke-' . ($key + 1) . ': Tim Kerja "' . $value[0] . '" tidak ada'];
                    $pegawai_id = null;
                    $pegawai_by_nip = !empty($nip_pegawai)
                        ? ManManagementPegawai::where('nip_lama', $nip_pegawai)->orWhere('nip', $nip_pegawai)->first()
                        : null;

                    $pegawai_by_nama = !empty($nama_pegawai)
                        ? ManManagementPegawai::where('name', $nama_pegawai)->first()
                        : null;

                    if ($pegawai_by_nip && $pegawai_by_nama) {
                        if ($pegawai_by_nip->id !== $pegawai_by_nama->id) {
                            $notification[] = [
                                'type' => 'error',
                                'message' => 'Baris ke-' . ($key + 1) . ': NIP "' . $nip_pegawai . '" dan nama "' . $nama_pegawai . '" merujuk ke pegawai yang berbeda',
                            ];
                            continue;
                        }
                        $pegawai_id = $pegawai_by_nip->id;
                    } elseif ($pegawai_by_nip) {
                        $pegawai_id = $pegawai_by_nip->id;
                    } elseif ($pegawai_by_nama) {
                        $pegawai_id = $pegawai_by_nama->id;
                    } else {
                        $label = !empty($nip_pegawai) && !empty($nama_pegawai)
                            ? '"' . $nip_pegawai . '" / "' . $nama_pegawai . '"'
                            : ('"' . ($nip_pegawai ?: $nama_pegawai) . '"');
                        $notification[] = ['type' => 'error', 'message' => 'Baris ke-' . ($key + 1) . ': Pegawai ' . $label . ' tidak ditemukan'];
                    }
                    $check_keanggotaan = in_array($keanggotaan, ['anggota', 'ketua']);
                    if (!$check_keanggotaan)
                        $notification[] = [
                            'type' => 'error',
                            'message' => 'Baris ke-' . ($key + 1) . ': Keanggotaan "' . $keanggotaan . '" tidak valid (harus "anggota" atau "ketua")'
                        ];
                    $result = [
                        'tim_id' => $tim ?? null,
                        'pegawai_id' => $pegawai_id,
                        'keanggotaan' => $keanggotaan,
                    ];
                    $validator = Validator::make($result, [
                        'tim_id' => ['required'],
                        'pegawai_id' => ['required'],
                        'keanggotaan' => ['required']
                    ], [
                        'tim_id.required' => "Baris ke-" . ($key + 1) . " kolom tim wajib diisi.",
                        'pegawai_id.required' => "Baris ke-" . ($key + 1) . " kolom pegawai wajib diisi.",
                        'keanggotaan.required' => "Baris ke-" . ($key + 1) . " kolom keanggotaan wajib diisi.",
                    ]);
                    if ($validator->fails()) {
                        $notification[] = ['type' => 'error', 'message' => $validator->errors()->first()];
                        continue;
                    }

                    $check = AnggotaTimKerja::where('tim_id', $result['tim_id'])
                        ->where('pegawai_id', $result['pegawai_id'])
                        ->first();
                    if ($check) {
                        $identifier = !empty($nip_pegawai) ? $nip_pegawai : $nama_pegawai;
                        $notification[] = ['type' => 'error', 'message' => 'Baris ke-' . ($key + 1) . ': Pegawai "' . $identifier . '" sudah ada di tim tersebut'];
                    } else
                        AnggotaTimKerja::create($result);
                }
            }
            if (count($notification) < $total)
                $notification[] = ['type' => 'success', 'message' => 'Keanggotaan tim berhasil di upload'];
            return redirect()->route('man-management.anggota.index')->with('notification', $notification);
        }
        $validated = $request->validate([
            'id' => ['sometimes', 'nullable'],
            'tim_id' => ['required', 'integer'],
            'pegawai_id' => ['required', 'integer'],
            'keanggotaan' => ['required', 'string']
        ]);
        $id = $validated['id'] ?? null;
        $check = AnggotaTimKerja::where('tim_id', $validated['tim_id'])
            ->where('pegawai_id', $validated['pegawai_id'])
            ->when($id, function ($query) use ($id) {
                $query->where('id', '!=', $id);
            })
            ->first();
        if ($check)
            return redirect()->route('man-management.anggota.index')->with('error', 'Pegawai tersebut sudah ada di tim tersebut');

        if ($request->isMethod('patch')) {
            try {
                //code...
                DB::connection('sulutweb_man_management')->beginTransaction();
                $keanggotaan_tim_kerja_to_updated = AnggotaTimKerja::findOrFail($validated['id']);
                if ($keanggotaan_tim_kerja_to_updated)
                    $keanggotaan_tim_kerja_to_updated->update($validated);
                DB::connection('sulutweb_man_management')->commit();
                return redirect()->route('man-management.anggota.index')->with('success', 'Keanggotaan pegawai ini di tim sudah diedit');
            } catch (\Throwable $th) {
                //throw $th;
                DB::connection('sulutweb_man_management')->rollBack();
                return redirect()->route('man-management.anggota.index')->with('error', 'Terjadi kesalahan dalam server');
            }
        }
        try {
            //code...
            DB::connection('sulutweb_man_management')->beginTransaction();
            AnggotaTimKerja::create($validated);
            DB::connection('sulutweb_man_management')->commit();
            return redirect()->route('man-management.anggota.index')->with('success', 'Pegawai sudah ditambahkan ke tim tersebut');
        } catch (\Throwable $th) {
            //throw $th;
            DB::connection('sulutweb_man_management')->rollBack();
            return redirect()->route('man-management.anggota.index')->with('error', 'Terjadi kesalahan di server');
        }
    }

    public function pDestroy($id)
    {
        try {
            //code...
            DB::connection('sulutweb_man_management')->beginTransaction();
            $pegawai_to_destroy = ManManagementPegawai::findOrFail($id);
            $pegawai_to_destroy->delete();
            DB::connection('sulutweb_man_management')->commit();
            return redirect()->route('man-management.index')
                ->with('success', 'Pegawai berhasil dihapus');
        } catch (\Throwable $th) {
            //throw $th;
            DB::connection('sulutweb_man_management')->rollBack();
            return redirect()->route('man-management.index')
                ->with('error', 'Pegawai gagal dihapus');
        }
    }

    public function tkDestroy($id)
    {
        try {
            //code...
            DB::connection('sulutweb_man_management')->beginTransaction();
            $tim_kerja_to_destroy = TimKerja::findOrFail($id);
            $tim_kerja_to_destroy->delete();
            DB::connection('sulutweb_man_management')->commit();
            return redirect()->route('man-management.tim-kerja.index')
                ->with('success', 'Tim Kerja berhasil dihapus');
        } catch (\Throwable $th) {
            //throw $th;
            DB::connection('sulutweb_man_management')->rollBack();
            return redirect()->route('man-management.tim-kerja.index')
                ->with('error', 'Tim Kerja gagal dihapus');
        }
    }

    public function atDestroy($id)
    {
        try {
            //code...
            DB::connection('sulutweb_man_management')->beginTransaction();
            $keanggotaan_to_destroy = AnggotaTimKerja::findOrFail($id);
            $keanggotaan_to_destroy->delete();
            DB::connection('sulutweb_man_management')->commit();
            return redirect()->route('man-management.anggota.index')
                ->with('success', 'Keanggotaan berhasil dihapus');
        } catch (\Throwable $th) {
            //throw $th;
            DB::connection('sulutweb_man_management')->rollBack();
            return redirect()->route('man-management.anggota.index')
                ->with('error', 'Keanggotaan gagal dihapus');
        }
    }

    public function fetchAnggotaTim($id)
    {
        $tim = AnggotaTimKerja::where('tim_id', $id)->pluck('pegawai_id')->toArray();
        $anggota = ManManagementPegawai::whereIn('id', $tim)
            ->select(['id as value', 'name as label'])->get();
        return response()->json($anggota);
    }

    public function fetchMyTim()
    {
        $myTeam = AnggotaTimKerja::where('pegawai_id', Auth::id())->pluck('tim_id')->toArray();
        return response()->json($myTeam);
    }
}
