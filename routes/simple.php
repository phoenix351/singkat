<?php

use App\Http\Controllers\Simple\HomeController;
use App\Http\Controllers\Simple\LemburController;
use Illuminate\Support\Facades\Route;

Route::prefix('simple')->middleware('auth')->name('simple.')
    ->middleware(['auth', 'use.vue.inertia'])
    ->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('index');

        Route::get('/pengajuan-lembur', [LemburController::class, 'index'])->name('lembur');
        Route::post('/pengajuan-lembur/store', [LemburController::class, 'store'])->name('lembur.store');
        Route::patch('/pengajuan-lembur/patch', [LemburController::class, 'store'])->name('lembur.patch');
        Route::delete('/pengajuan-lembur/destroy-pegawai/{id}', [LemburController::class, 'destroyPegawai'])->name('lembur.destroy-pegawai');
        Route::delete('/pengajuan-lembur/destroy/{id}', [LemburController::class, 'destroy'])->name('lembur.destroy');
        Route::get('/fetch-maksud/{tim_id}', [LemburController::class, 'fetchMaksud'])->name('fetch-maksud');
    });
