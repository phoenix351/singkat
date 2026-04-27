<?php

namespace App\Http\Controllers\ManManagement;

use App\Http\Controllers\Controller;
use App\Models\ManManagement\AppManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AppController extends Controller
{
    //
    public function index(Request $request)
    {
        if ($request->paginated) $paginated = $request->paginated;
        else $paginated = 10;
        if ($request->currentPage) $currentPage = $request->currentPage;
        else $currentPage = 1;

        $query = AppManagement::query();
        if ($request->sortOrder) {
            $order = $request->sortOrder == 1 ? 'asc' : 'desc';
            $query->orderBy($request->sortField, $order);
        } else $query->orderBy('label', 'asc');
        if ($request->searchField) {
            $query->where(function ($q) use ($request) {
                $q->where('label', 'like', '%' . $request->searchField . '%')
                    ->orWhere('deskripsi', 'like', '%' . $request->searchField . '%')
                    ->orWhere('navigation', 'like', '%' . $request->searchField . '%')
                    ->orWhere('route_link', 'like', '%' . $request->searchField . '%');
            });
        }
        $apps = $query->paginate($paginated, ['*'], 'page', $currentPage);
        return Inertia::render('ManManagement/Aplikasi', ['apps' => $apps]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => ['sometimes', 'nullable'],
            'label' => ['required', 'string', 'max:30'],
            'deskripsi' => ['required', 'string', 'max:100'],
            'route_link' => ['required', 'string', 'max:100'],
            'navigation' => ['required', 'string', 'max:30'],
            'maintenance' => ['required', 'boolean'],
            'maintenance_message' => ['sometimes', 'nullable', 'max:100'],
            'image' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        ]);
        $image = $request->file('image');
        $id = $validated['id'] ?? null;
        $new_path = null;

        if ($request->isMethod('patch')) {
            try {
                //code...
                DB::beginTransaction();
                $old_path = $request->input('image_path');
                if ($image) {
                    $ext      = $image->getClientOriginalExtension();
                    $filename = Str::slug($validated['label'])  . '-logo-' . time() . '.' . $ext;
                    $disk   = 'public';
                    $folder = 'images/logo';
                    $new_path = $image->storeAs($folder, $filename, $disk);
                    $validated['image_path'] = $new_path;
                    if ($old_path != $new_path && $old_path != 'images/logo/logo-bps.png' && Storage::disk('public')->exists($old_path)) {
                        Storage::disk('public')->delete($old_path);
                    }
                }
                unset($validated['image']);
                if ($validated['maintenance'] == 0) $validated['maintenance_message'] = null;
                $app_to_update = AppManagement::findOrFail($id);
                if ($app_to_update) $app_to_update->update($validated);
                DB::commit();
                return redirect()->route('man-management.app-management.index')->with('success', 'Berhasil edit data');
            } catch (\Throwable $th) {
                //throw $th;
                DB::rollBack();
                return redirect()->route('man-management.app-management.index')->with('error', 'Gagal edit data, error : ' . $th->getMessage());
            }
        }
        try {
            //code...
            DB::beginTransaction();
            if ($image) {
                $ext      = $image->getClientOriginalExtension();
                $filename = Str::slug($validated['label'])  . '-logo-' . time() . '.' . $ext;
                $disk   = 'public';
                $folder = 'images/logo';
                $new_path = $image->storeAs($folder, $filename, $disk);
                $validated['image_path'] = $new_path;
            }
            unset($validated['image']);
            AppManagement::create($validated);
            DB::commit();
            return redirect()->route('man-management.app-management.index')->with('success', 'Informasi aplikasi berhasil ditambahkan');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            if (isset($new_path) && Storage::disk('public')->exists($new_path)) {
                Storage::disk('public')->delete($new_path);
            }
            return redirect()->route('man-management.app-management.index')->with('error', 'Terjadi kesalahan');
        }
    }

    public function destroy($id)
    {
        try {
            //code...
            DB::beginTransaction();
            $app_to_delete = AppManagement::findOrFail($id);
            if ($app_to_delete) {
                if (Storage::disk('public')->exists($app_to_delete->image_path)) Storage::disk('public')->delete($app_to_delete->image_path);
                $app_to_delete->delete();
            }
            DB::commit();
            return redirect()->route('man-management.app-management.index')->with('success', 'aplikasi berhasil dihapus');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return redirect()->route('man-management.app-management.index')->with('error', 'aplikasi gagal dihapus, error: ' . $th->getMessage());
        }
    }
}
