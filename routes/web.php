<?php

use App\Http\Controllers\AbkController;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\CapaianController;
use App\Http\Controllers\PredikatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UnitKerjaController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    } else {
        return redirect('/login');
    }
});

Route::get('/dashboard', function () {
    // Jumlah pegawai
    $jumlahPegawai = Pegawai::count();
    $totalUsers = User::count();
    $unitKerja = UnitKerja::count();

    return Inertia::render('Dashboard/Dashboard', [
        "jumlahPegawai" => $jumlahPegawai,
        "users" => $totalUsers,
        "unitKerja" => $unitKerja,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // CKP
    Route::get("kelola-ckp", [CapaianController::class, "index"])->name("kelola-ckp");
    Route::get('/kelola-ckp/{ckp}', [CapaianController::class, 'show']);
    Route::post('/capaian', [CapaianController::class, 'store'])->name('capaian.store');
    Route::put('/kelola-ckp/{ckp}', [CapaianController::class, 'update']);
    Route::delete('/kelola-ckp/{ckp}', [CapaianController::class, 'destroy']);
    
});

Route::get('/export-pegawai', [PegawaiController::class, 'export'])->middleware(['auth'])->name('pegawai.export');

Route::get("kelola-pak", [PegawaiController::class, "kelola_pak"])->middleware(['auth'])->name("kelola-pak");
Route::get('/kelola-pak/{pegawai}', [PegawaiController::class, 'show']);
Route::post('/kelola-pak', [PegawaiController::class, 'store'])->name('kelola-pak.store');
Route::put('/kelola-pak/{pegawai}', [PegawaiController::class, 'update']);
Route::delete('/kelola-pak/{pegawai}', [PegawaiController::class, 'destroy']);


// ABK
Route::get('/kelola-abk', [AbkController::class, 'index'])->middleware(['auth'])->name('abk.index');
Route::post('/kelola-abk', [AbkController::class, 'store'])->middleware(['auth'])->name('abk.store');
Route::put('/kelola-abk/{abk}', [AbkController::class, 'update'])->middleware(['auth'])->name('abk.update');
Route::delete('/kelola-abk/{abk}', [AbkController::class, 'destroy'])->middleware(['auth'])->name('abk.destroy');

// Get Api Available Jabatan
Route::get('/api/get-available-jabatan', [AbkController::class, 'getAvailableJabatan']);
Route::get('/api/get-available-jabatan-edit', [AbkController::class, 'getAvailableJabatanEdit']);
Route::get('/api/predikats',[PredikatController::class,'fetch']);
Route::get('/api/pegawais',[PegawaiController::class,'fetch']);

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
Route::put('/jabatan/{jabatan}', [JabatanController::class, 'update'])->middleware(['auth'])->name('jabatan.update');
Route::delete('/jabatan/{jabatan}', [JabatanController::class, 'destroy'])->middleware(['auth'])->name('jabatan.destroy');

// Unit Kerja
Route::get('/unit-kerja', [UnitKerjaController::class, 'index'])->middleware(['auth'])->name('unit-kerja.index');
Route::post('/unit-kerja', [UnitKerjaController::class, 'store'])->middleware(['auth'])->name('unit-kerja.store');
Route::put('/unit-kerja/{unitKerja}', [UnitKerjaController::class, 'update'])->middleware(['auth'])->name('unit-kerja.update');
Route::delete('/unit-kerja/{unitKerja}', [UnitKerjaController::class, 'destroy'])->middleware(['auth'])->name('unit-kerja.destroy');

//Capaian

require __DIR__ . '/auth.php';
