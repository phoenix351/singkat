<?php

namespace App\Http\Controllers\ManManagement;

use App\Http\Controllers\Controller;
use App\Models\ManManagement\AppManagement;
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
        if ($request->paginated) $paginated = $request->paginated;
        else $paginated = 10;
        if ($request->currentPage) $currentPage = $request->currentPage;
        else $currentPage = 1;

        $query = AppManagement::query();
        $apps = $query->paginate($paginated, ['*'], 'page', $currentPage);
        return Inertia::render('ManManagement/Aplikasi', ['apps' => $apps]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:30'],
            'deskripsi' => ['required', 'string', 'max:100'],
            'route_link' => ['required', 'string', 'max:30'],
            'navigation' => ['required', 'string', 'max:30'],
            'maintenance' => ['required', 'boolean'],
            'maintenance_message' => ['sometimes', 'nullable', 'max:100'],
            'image' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        ]);
        $image = $request->file('image');

        $new_path = null;
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
}
