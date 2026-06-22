<?php

use App\Exports\Se2026\PplExport;
use App\Http\Controllers\Se2026\DataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::prefix('se2026')->middleware(['auth', 'use.vue.inertia', 'permission'])->name('se2026.')->group(function () {
    // Route::get('/', function () {});
    Route::get('/', [DataController::class, 'index'])->name('data.index');
    Route::post('/upload-data', [DataController::class, 'uploadData'])->name('upload-data');
    Route::post('/upload-data-batch', [DataController::class, 'uploadDataBatch'])->name('upload-data-batch');

    Route::get('/fetch-log', [DataController::class, 'fetchLog'])->name('fetch-log');
    Route::get('/fetch-data-ppl', [DataController::class, 'fetchDataPpl'])->name('fetch-data-ppl');
    Route::get('/fetch-data-pml', [DataController::class, 'fetchDataPml'])->name('fetch-data-pml');
    Route::get('/update-petugas/{petugas}', [DataController::class, 'updatePetugas'])->name('update-petugas');
    Route::post('/update-petugas-batch', [DataController::class, 'uploadPetugasBatch'])->name('upload-petugas-batch');

    Route::get('/fetch-kec/{kabkot}', [DataController::class, 'fetchKec'])->name('fetch-kec');
    Route::get('/fetch-desa/{kec}', [DataController::class, 'fetchDesa'])->name('fetch-desa');
    Route::get('/fetch-sls/{desa}', [DataController::class, 'fetchSls'])->name('fetch-sls');

    Route::get('/download-petugas', function (Request $request) {
        $kabkot = $request->kabkot;
        $kec = $request->kec;
        $desa = $request->desa;
        $sls = $request->sls;
        $nama = $request->nama;
        $tab = $request->tab;

        return Excel::download(new PplExport($kabkot, $kec, $desa, $sls, $nama, $tab), 'Data petugas.xlsx');
    })->name('download-report-petugas');
});