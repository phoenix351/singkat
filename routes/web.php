<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Singkat\AbkController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Singkat\JabatanController;
use App\Http\Controllers\Singkat\PegawaiController;
use App\Http\Controllers\Singkat\PredikatController;
use App\Http\Controllers\SKController;
use App\Http\Controllers\TokenController;

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
Route::get('/token', function () {
    return csrf_token();
})->name('token');
// Ekspor ABK

require __DIR__ . '/auth.php';
require __DIR__ .'/singkat.php';
require __DIR__ .'/simple.php';
require __DIR__ . '/meeting.php';
require __DIR__ . '/mmanagement.php';
