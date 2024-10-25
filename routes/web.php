<?php

use App\Http\Controllers\Singkat\AbkController;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Singkat\JabatanController;
use App\Http\Controllers\Singkat\PegawaiController;
use App\Http\Controllers\Singkat\CapaianController;
use App\Http\Controllers\Singkat\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Singkat\PredikatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UnitKerjaController;

Route::get('/', function () {
    $user = Auth::user();
    // dd($user);
    if (Auth::check()) {
        if ($user->role === 'viewer') {
            return redirect('/kelola-pak/' . $user->pegawai_id);
        } else {
            return redirect('/dashboard');
        }
    } else {
        return redirect('/login');
    }
});

// ROUTE APLIKASI SINGKAT 

Route::middleware('auth')->group(function () {

    Route::get('/', [LandingController::class, 'index'])->name('index');

    Route::get('/singkat/dashboard', [DashboardController::class, 'index'])->name('singkat.dashboard');
    Route::get('/singkat', [DashboardController::class, 'index'])->name('singkat.index');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::patch('/singkat/kelola-pak/{pegawai}', [PegawaiController::class, 'update']);

    // CKP
    Route::get("/singkat/kelola-ckp", [CapaianController::class, "index"])->name("kelola-ckp");
    Route::get('/kelola-ckp/{capaian}', [CapaianController::class, 'show']);
    Route::put('/kelola-ckp/{ckp}', [CapaianController::class, 'update']);
    Route::delete('/capaian/{capaian}', [CapaianController::class, 'destroy']);
    Route::get('/singkat/kelola-ckp/fetch', [CapaianController::class, 'fetch'])->name('kelola-ckp.fetch');
    
    Route::post('/capaian', [CapaianController::class, 'store'])->name('capaian.store');
    Route::patch('/capaian/{capaian}', [CapaianController::class, 'update'])->name('capaian.update');

    Route::patch('/jabatan/{jabatan}', [JabatanController::class, 'update'])->name('jabatan.update');
});

Route::get('/export-pegawai', [PegawaiController::class, 'export'])->middleware(['auth'])->name('pegawai.export');

Route::get("singkat/kelola-pak", [PegawaiController::class, "kelola_pak"])->middleware(['auth'])->name("kelola-pak");
Route::get('/kelola-pak/{pegawai}', [PegawaiController::class, 'show']);
Route::post('/kelola-pak', [PegawaiController::class, 'store'])->name('kelola-pak.store');
Route::put('/kelola-pak/{pegawai}', [PegawaiController::class, 'update']);
Route::delete('/kelola-pak/{pegawai}', [PegawaiController::class, 'destroy']);


// ABK
Route::get('/singkat/kelola-abk', [AbkController::class, 'index'])->middleware(['auth'])->name('abk.index');
Route::post('/kelola-abk', [AbkController::class, 'store'])->middleware(['auth'])->name('abk.store');
Route::put('/kelola-abk/{abk}', [AbkController::class, 'update'])->middleware(['auth'])->name('abk.update');
Route::delete('/kelola-abk/{abk}', [AbkController::class, 'destroy'])->middleware(['auth'])->name('abk.destroy');
Route::get('/kelola-abk/fetch', [AbkController::class, 'fetch'])->middleware(['auth'])->name('abk.fetch');

// Get Api Available Jabatan
Route::get('/api/get-available-jabatan', [AbkController::class, 'getAvailableJabatan']);
Route::get('/api/get-available-jabatan-edit', [AbkController::class, 'getAvailableJabatanEdit']);
Route::get('/api/predikats', [PredikatController::class, 'fetch']);
Route::get('/api/pegawais', [PegawaiController::class, 'fetch']);

Route::get('/api/abk-summary', [AbkController::class, 'getAbkSummary']);

// Ekspor ABK
Route::get('/export-abk', [AbkController::class, 'export'])->middleware(['auth'])->name('abk.export');

// User
Route::get('/users', [UserController::class, 'index'])->middleware(['auth'])->name('users.index');
Route::post('/users', [UserController::class, 'store'])->middleware(['auth'])->name('users.store');
Route::put('/users/{user}', [UserController::class, 'update'])->middleware(['auth'])->name('users.update');
Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware(['auth'])->name('users.destroy');

// Jabatan
Route::get('/jabatan', [JabatanController::class, 'index'])->middleware(['auth'])->name('jabatan.index');
Route::post('/jabatan', [JabatanController::class, 'store'])->middleware(['auth'])->name('jabatan.store');
Route::delete('/jabatan/{jabatan}', [JabatanController::class, 'destroy'])->middleware(['auth'])->name('jabatan.destroy');

// Unit Kerja
Route::get('/unit-kerja', [UnitKerjaController::class, 'index'])->middleware(['auth'])->name('unit-kerja.index');
Route::post('/unit-kerja', [UnitKerjaController::class, 'store'])->middleware(['auth'])->name('unit-kerja.store');
Route::put('/unit-kerja/{unitKerja}', [UnitKerjaController::class, 'update'])->middleware(['auth'])->name('unit-kerja.update');
Route::delete('/unit-kerja/{unitKerja}', [UnitKerjaController::class, 'destroy'])->middleware(['auth'])->name('unit-kerja.destroy');

//user area 
// capaianku
Route::get('/capaian-ku', [PegawaiController::class, 'capaian_ku'])->middleware(['auth'])->name('user-area.capaian-ku');


require __DIR__ . '/auth.php';
