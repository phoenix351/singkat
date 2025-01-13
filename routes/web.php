<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Singkat\AbkController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Singkat\JabatanController;
use App\Http\Controllers\Singkat\PegawaiController;
use App\Http\Controllers\Singkat\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Singkat\PredikatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Singkat\PAKController;
use App\Http\Controllers\SKController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\UnitKerjaController;

// ROute SSO 
Route::get('/sso-login', [LoginController::class, 'sso_redirect'])->name('sso-login');
Route::get('/sso-callback', [LoginController::class, 'sso_callback'])->name('sso-callback');



Route::get('/', function () {
    $user = Auth::user();
    // dd($user);
    if (Auth::check()) {

        return redirect('/home');
    } else {
        return redirect('/login');
    }
})->name("index");

Route::get('/token/csrf', [TokenController::class, 'get_csrf_token'])->name('api.token.csrf');




// ROUTE APLIKASI SINGKAT 

Route::middleware('auth')->group(function () {

    Route::get('/home', [LandingController::class, 'index'])->name('home');

    Route::get('/singkat/dashboard', [DashboardController::class, 'index'])->name('singkat.dashboard');
    Route::get('/singkat', [DashboardController::class, 'index'])->name('singkat');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::patch('/singkat/kelola-pak/{pegawai}', [PegawaiController::class, 'update']);

    // CKP
    Route::get("/singkat/admin/pak", [PAKController::class, "index"])->name("singkat.admin.pak");
    Route::get('/singkat/admin/pak/{pak}', [PAKController::class,'show'])->name('singkat.admin.pak.show');
    Route::post('/singkat/admin/pak', [PAKController::class,'store'])->name('singkat.admin.pak.store');
    Route::post('/singkat/admin/pak/{pak}', [PAKController::class,'update'])->name('singkat.admin.pak.update');
    Route::delete('/singkat/admin/pak/{pak}', [PAKController::class,'destroy'])->name('singkat.admin.pak.destroy');
    Route::get('/singkat/admin/pak/fetch', [PAKController::class, 'fetch'])->name('singkat.admin.pak.fetch');

    // SK
    Route::get('/singkat/admin/jenis-sk', [SKController::class, 'index'])->name('singkat.admin.jenis-sk');
    Route::post('/singkat/admin/jenis-sk', [SKController::class, 'store'])->name('singkat.admin.jenis-sk.store');
    Route::put('/singkat/admin/jenis-sk', [SKController::class, 'update'])->name('singkat.admin.jenis-sk.update');
    Route::delete('/singkat/admin/jenis-sk/{sk}', [SKController::class, 'destroy'])->name('singkat.admin.jenis-sk.destroy');

    // Pegawai
    Route::get("/singkat/admin/pegawai", [PegawaiController::class, "kelola_pak"])->name("singkat.admin.pegawai");
    Route::get('/singkat/admin/pegawai/{pegawai}', [PegawaiController::class, 'singkat.admin.pegawai.show']);
    Route::post('/singkat/admin/pegawai', [PegawaiController::class, 'store'])->name('singkat.admin.pegawai.store');
    Route::put('/singkat/admin/pegawai/{pegawai}', [PegawaiController::class, 'update'])->name('singkat.admin.pegawai.update');
    Route::delete('/singkat/admin/pegawai/{pegawai}', [PegawaiController::class, 'destroy'])->name('singkat.admin.pegawai.destroy');
    Route::get('/singkat/admin/pegawai/export', [PegawaiController::class, 'export'])->name('singkat.admin.pegawai.export');


    // ABK
    Route::get('/singkat/admin/abk', [AbkController::class, 'index'])->name('singkat.admin.abk');
    Route::post('/singkat/admin/abk', [AbkController::class, 'store'])->name('singkat.admin.abk.store');
    Route::put('/singkat/admin/abk/{abk}', [AbkController::class, 'update'])->name('singkat.admin.abk.update');
    Route::delete('/singkat/admin/abk/{abk}', [AbkController::class, 'destroy'])->name('singkat.admin.abk.destroy');
    Route::get('/singkat/admin/abk/fetch', [AbkController::class, 'fetch'])->name('singkat.admin.abk.fetch');
    Route::get('/singkat/admin/abk/export', [AbkController::class, 'export'])->name('singkat.admin.abk.export');

    // User
    Route::get('/singkat/admin/users', [UserController::class, 'index'])->name('singkat.admin.users');
    Route::post('/singkat/admin/users', [UserController::class, 'store'])->name('singkat.admin.users.store');
    Route::put('/singkat/admin/users/{user}', [UserController::class, 'update'])->name('singkat.admin.users.update');
    Route::delete('/singkat/admin/users/{user}', [UserController::class, 'destroy'])->name('singkat.admin.users.destroy');

    // Jabatan
    Route::get('/singkat/admin/jabatan', [JabatanController::class, 'index'])->name('singkat.admin.jabatan');
    Route::post('/singkat/admin/jabatan', [JabatanController::class, 'store'])->name('singkat.admin.jabatan.store');
    Route::patch('/singkat/admin/jabatan/{jabatan}', [JabatanController::class, 'update'])->name('singkat.admin.jabatan.update');
    Route::delete('/singkat/admin/jabatan/{jabatan}', [JabatanController::class, 'destroy'])->name('singkat.admin.jabatan.destroy');
    
    // Unit Kerja
    Route::get('/singkat/admin/unit-kerja', [UnitKerjaController::class, 'index'])->name('singkat.admin.unit-kerja');
    Route::post('/singkat/admin/unit-kerja', [UnitKerjaController::class, 'store'])->name('singkat.admin.unit-kerja.store');
    Route::put('/singkat/admin/unit-kerja/{unitKerja}', [UnitKerjaController::class, 'update'])->name('singkat.admin.unit-kerja.update');
    Route::delete('/singkat/admin/unit-kerja/{unitKerja}', [UnitKerjaController::class, 'destroy'])->name('singkat.admin.unit-kerja.destroy');

    //user area 
    // capaianku
    Route::get('/singkat/capaian-ku', [PegawaiController::class, 'capaian_ku'])->name('singkat.user.capaian-ku');
});



// Get Api Available Jabatan
Route::get('/api/get-available-jabatan', [AbkController::class, 'getAvailableJabatan']);
Route::get('/api/get-available-jabatan-edit', [AbkController::class, 'getAvailableJabatanEdit']);
Route::get('/api/predikats', [PredikatController::class, 'fetch']);
Route::get('/api/pegawais', [PegawaiController::class, 'fetch']);
Route::get('/api/jabatans/{jabatan}', [JabatanController::class, 'fetch'])->name('api.jabatans');
Route::get('/api/pegawai/histories', [PegawaiController::class, 'get_histories'])->name('pegawai.histories');

Route::get('/api/jenis-sk', [SKController::class, 'fetchAll']);
Route::get('/api/jenis-sk/{sk}', [SKController::class, 'fetchOne']);

Route::get('/api/abk-summary', [AbkController::class, 'getAbkSummary']);

// Ekspor ABK




require __DIR__ . '/auth.php';
