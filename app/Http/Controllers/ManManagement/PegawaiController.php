<?php

namespace App\Http\Controllers\ManManagement;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Controller;
use App\Models\ManManagement\AnggotaTimKerja;
use App\Models\ManManagement\Pegawai as ManManagementPegawai;
use App\Models\ManManagement\TimKerja;
use App\Models\Pegawai;
use Illuminate\Http\Request;
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
        $lc = app(LoginController::class);
        $result = $lc->ssoAPI($request->nip);
        $data = $result[0]['attributes'] ?? null;
        if (!$data) return redirect()->route('man-management.index')->with('error', 'Data tidak ditemukan');
        $payload = [
            'nip_lama'   => $data['attribute-nip-lama'][0] ?? null,
            'nip'        => $data['attribute-nip'][0] ?? null,
            'username'   => $data['attribute-username'][0] ?? null,
            'email'      => $data['attribute-email'][0] ?? null,
            'name'       => $data['attribute-nama'][0] ?? null,
            'golongan'   => $data['attribute-golongan'][0] ?? null,
            'jabatan'    => $data['attribute-jabatan'][0] ?? null,
            'organisasi' => $data['attribute-organisasi'][0] ?? null,
            'provinsi'   => $data['attribute-provinsi'][0] ?? null,
            'kabupaten'  => $data['attribute-kabupaten'][0] ?? null,
            'foto'       => $data['attribute-foto'][0] ?? null,
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
                if ($key === 0) continue;
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
            if (count($notification) < $total) $notification[] = ['type' => 'success', 'message' => 'Tim Kerja berhasil di upload'];
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
        if ($check) return redirect()->route('man-management.tim-kerja.index')->with('error', 'Tim Kerja sudah ada');

        if ($request->isMethod('patch')) {
            try {
                //code...
                DB::beginTransaction();
                $timkerja_to_updated = TimKerja::findOrFail($validated['id']);
                if ($timkerja_to_updated) $timkerja_to_updated->update($validated);
                DB::commit();
                return redirect()->route('man-management.tim-kerja.index')->with('success', 'Tim Kerja berhasil diedit');
            } catch (\Throwable $th) {
                //throw $th;
                DB::rollBack();
                return redirect()->route('man-management.tim-kerja.index')->with('error', 'Terjadi kesalahan dalam data');
            }
        }
        try {
            //code...
            DB::beginTransaction();
            TimKerja::create($validated);
            DB::commit();
            return redirect()->route('man-management.tim-kerja.index')->with('success', 'Tim Kerja berhasil ditambahkan');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return redirect()->route('man-management.tim-kerja.index')->with('error', 'Terjadi kesalahan dalam data');
        }
    }

    public function atStore(Request $request)
    {
        if ($request->input('fileUpload')) {
            $fileData = $request->input('fileUpload');
            if ($fileData[0][0] != 'nama_tim' && $fileData[0][1] != 'nama_pegawai' && $fileData[0][2] != 'keanggotaan_tim') {
                return redirect()->route('man-management.anggota.index')->with('error', 'File yang diupload tidak sesuai template');
            }
            $notification = [];
            $total = count($fileData) - 1;
            foreach ($fileData as $key => $value) {
                # code...
                if ($key === 0) continue;
                if (!empty($value) && count($value) > 0) {
                    $tim = TimKerja::where('label', $value[0])->value('id');
                    if (!$tim)  $notification[] = ['type' => 'error', 'message' => 'Tim Kerja ' . $value[0] . ' tidak ada'];
                    $pegawai = ManManagementPegawai::where('name', $value[1])->value('id');
                    if (!$pegawai) $notification[] = ['type' => 'error', 'message' => 'Pegawai ' . $value[1] . ' tidak ada'];
                    $check_keanggotaan = false;
                    if ($value[2] == 'anggota' || $value[2] == 'ketua') $check_keanggotaan = true;
                    if (!$check_keanggotaan) $notification[] = ['type' => 'error', 'message' => 'Pegawai ' . $value[1] . ' keanggotaannya tidak ada di format'];

                    $result = [
                        'tim_id' => $tim ?? null,
                        'pegawai_id' => $pegawai ?? null,
                        'keanggotaan' => $value[2]
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
                        $notification[] = ['type' => 'error', 'message' => 'Pegawai ' . $value[1] . ' sudah ada di tim tersebut'];
                    } else
                        AnggotaTimKerja::create($result);
                }
            }
            if (count($notification) < $total) $notification[] = ['type' => 'success', 'message' => 'Keanggotaan tim berhasil di upload'];
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
        if ($check) return redirect()->route('man-management.anggota.index')->with('error', 'Pegawai tersebut sudah ada di tim tersebut');

        if ($request->isMethod('patch')) {
            try {
                //code...
                DB::beginTransaction();
                $keanggotaan_tim_kerja_to_updated = AnggotaTimKerja::findOrFail($validated['id']);
                if ($keanggotaan_tim_kerja_to_updated) $keanggotaan_tim_kerja_to_updated->update($validated);
                DB::commit();
                return redirect()->route('man-management.anggota.index')->with('success', 'Keanggotaan pegawai ini di tim sudah diedit');
            } catch (\Throwable $th) {
                //throw $th;
                DB::rollBack();
                return redirect()->route('man-management.anggota.index')->with('error', 'Terjadi kesalahan dalam server');
            }
        }
        try {
            //code...
            DB::beginTransaction();
            AnggotaTimKerja::create($validated);
            DB::commit();
            return redirect()->route('man-management.anggota.index')->with('success', 'Pegawai sudah ditambahkan ke tim tersebut');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return redirect()->route('man-management.anggota.index')->with('error', 'Terjadi kesalahan di server');
        }
    }

    public function tkDestroy($id)
    {
        try {
            //code...
            DB::beginTransaction();
            $tim_kerja_to_destroy = TimKerja::findOrFail($id);
            $tim_kerja_to_destroy->delete();
            DB::commit();
            return redirect()->route('man-management.tim-kerja.index')
                ->with('success', 'Tim Kerja berhasil dihapus');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return redirect()->route('man-management.tim-kerja.index')
                ->with('error', 'Tim Kerja gagal dihapus');
        }
    }

    public function atDestroy($id)
    {
        try {
            //code...
            DB::beginTransaction();
            $keanggotaan_to_destroy = AnggotaTimKerja::findOrFail($id);
            $keanggotaan_to_destroy->delete();
            DB::commit();
            return redirect()->route('man-management.anggota.index')
                ->with('success', 'Keanggotaan berhasil dihapus');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return redirect()->route('man-management.anggota.index')
                ->with('error', 'Keanggotaan gagal dihapus');
        }
    }
}
