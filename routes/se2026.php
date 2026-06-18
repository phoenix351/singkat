<?php

use App\Http\Controllers\Se2026\DataController;
use Illuminate\Support\Facades\Route;

Route::prefix('se2026')->middleware(['auth', 'use.vue.inertia'])->name('se2026.')->group(function () {
    // Route::get('/', function () {});
    Route::get('/', [DataController::class, 'index'])->name('data.index');
    Route::post('/upload-data', [DataController::class, 'uploadData'])->name('upload-data');
    Route::post('/upload-data-batch', [DataController::class, 'uploadDataBatch'])->name('upload-data-batch');

    Route::get('/fetch-data-ppl', [DataController::class, 'fetchDataPpl'])->name('fetch-data-ppl');
    Route::get('/fetch-kec/{kabkot}', [DataController::class, 'fetchKec'])->name('fetch-kec');
    Route::get('/fetch-desa/{kec}', [DataController::class, 'fetchDesa'])->name('fetch-desa');
    Route::get('/fetch-sls/{desa}', [DataController::class, 'fetchSls'])->name('fetch-sls');
});