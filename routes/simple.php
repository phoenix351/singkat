<?php

use App\Http\Controllers\Simple\HomeController;
use App\Http\Controllers\Simple\LemburController;
use Illuminate\Support\Facades\Route;

Route::prefix('simple')->middleware('auth')->name('simple.')
    ->middleware(['auth', 'use.vue.inertia'])
    ->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('index');

        Route::get('/pengajuan-lembur', [LemburController::class, 'index'])->name('lembur');
    });
