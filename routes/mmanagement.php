<?php

use App\Http\Controllers\ManManagement\HomeController;
use App\Http\Controllers\ManManagement\PegawaiController;
use Illuminate\Support\Facades\Route;

Route::prefix('man-management')->name('man-management.')->middleware(['auth', 'use.vue.inertia'])->group(function () {
    Route::get('/index', [HomeController::class, 'index'])->name('index');

    Route::get('/check-pegawai/{nip}', [PegawaiController::class, 'checkPegawai'])->name('check-pegawai');
    Route::get('/get-current-pegawai', [PegawaiController::class, 'getCurrentPegawai'])->name('get-current-pegawai');
    Route::post('/upload-pegawai', [PegawaiController::class, 'uploadPegawai'])->name('upload-pegawai');
});
