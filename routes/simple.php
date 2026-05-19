<?php

use App\Http\Controllers\Simple\HomeController;
use App\Http\Controllers\Simple\LemburController;
use App\Http\Controllers\Simple\PresensiController;
use App\Http\Controllers\Simple\SpklController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::prefix('simple')->middleware('auth')->name('simple.')
    ->middleware(['auth', 'use.vue.inertia'])
    ->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('index');

        Route::get('/my-lembur', [LemburController::class, 'myLembur'])->name('my-lembur');

        Route::get('/pengajuan-lembur', [LemburController::class, 'index'])->name('lembur');
        Route::post('/pengajuan-lembur/store', [LemburController::class, 'store'])->name('lembur.store');
        Route::patch('/pengajuan-lembur/patch', [LemburController::class, 'store'])->name('lembur.patch');
        Route::delete('/pengajuan-lembur/destroy-pegawai/{id}', [LemburController::class, 'destroyPegawai'])->name('lembur.destroy-pegawai');
        Route::delete('/pengajuan-lembur/destroy/{id}', [LemburController::class, 'destroy'])->name('lembur.destroy');
        Route::get('/fetch-maksud/{tim_id}', [LemburController::class, 'fetchMaksud'])->name('fetch-maksud');

        //ketua tim
        Route::get('/pengajuan-lembur/verifikasi', [LemburController::class, 'verify'])->name('lembur.verify');
        Route::patch('/pengajuan-lembur/verifikasi/patch', [LemburController::class, 'verifyPatch'])->name('lembur.verify-patch');
        //kabag
        Route::get('/pengajuan-lembur/verifikasi-kabag', [LemburController::class, 'verifyKabag'])->name('lembur.verify-kabag');
        Route::patch('/pengajuan-lembur/verifikasi-kabag/patch', [LemburController::class, 'verifyKabagPatch'])->name('lembur.verify-kabag-patch');

        //spkl
        Route::get('/daftar-spkl', [SpklController::class, 'index'])->name('spkl');
        Route::post('/daftar-spkl/store', [SpklController::class, 'store'])->name('spkl.store');
        Route::get('/daftar-spkl/print', [SpklController::class, 'print'])->name('spkl.print');

        //laporan lembur
        Route::get('/laporan-lembur', [SpklController::class, 'laporan'])->name('laporan-lembur');

        //presensi-lembur
        Route::patch('/presensi/patch', [SpklController::class, 'patch'])->name('presensi.patch');
        Route::get('/download-template/presensi', function () {
            $file_path = public_path('document/template_presensi_lembur.xlsx');
            return Response::download($file_path);
        });

        //fetching
        Route::get('/fetch-lembur/{lembur_id}', [LemburController::class, 'fetchLembur'])->name('fetch-lembur');
    });
