<?php

namespace App\Http\Controllers\ManManagement;

use App\Http\Controllers\Controller;
use App\Models\ManManagement\AppManagement;
use Illuminate\Http\Request;
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
}
