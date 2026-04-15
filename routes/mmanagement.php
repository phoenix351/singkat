<?php

use App\Exports\ManmentPegawaiExport;
use App\Exports\TimKerjaExport;
use App\Http\Controllers\ManManagement\HomeController;
use App\Http\Controllers\ManManagement\PegawaiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::prefix('man-management')->name('man-management.')->middleware(['auth', 'use.vue.inertia'])->group(function () {
    Route::get('/index', [HomeController::class, 'index'])->name('index');

    Route::get('/check-pegawai', [PegawaiController::class, 'checkPegawai'])->name('check-pegawai');
    Route::get('/get-current-pegawai', [PegawaiController::class, 'getCurrentPegawai'])->name('get-current-pegawai');
    Route::post('/upload-pegawai', [PegawaiController::class, 'uploadPegawai'])->name('upload-pegawai');

    Route::get('/tim-kerja', [HomeController::class, 'tkIndex'])->name('tim-kerja.index');
    Route::post('/tim-kerja/store', [PegawaiController::class, 'tkStore'])->name('tim-kerja.store');
    Route::patch('/tim-kerja/patch', [PegawaiController::class, 'tkStore'])->name('tim-kerja.patch');
    Route::delete('/tim-kerja/destroy/{id}', [PegawaiController::class, 'tkDestroy'])->name('tim-kerja.destroy');

    Route::get('/anggota-tim-kerja', [HomeController::class, 'atIndex'])->name('anggota.index');
    Route::post('/anggota-tim-kerja/store', [PegawaiController::class, 'atStore'])->name('anggota.store');
    Route::patch('/anggota-tim-kerja/patch', [PegawaiController::class, 'atStore'])->name('anggota.patch');
    Route::delete('/anggota-tim-kerja/destroy/{id}', [PegawaiController::class, 'atDestroy'])->name('anggota.destroy');

    Route::get('/download-template/tim-kerja', function () {
        $file_path = public_path('document/Tim Kerja Template.xlsx');
        return Response::download($file_path);
    });
    Route::get('/download-template/pegawai', function () {
        $file_path = public_path('document/Pegawai Template.xlsx');
        return Response::download($file_path);
    });
    Route::get('/download-template/keanggotaan', function () {
        $file_path = public_path('document/Keanggotaan Tim Template.xlsx');
        return Response::download($file_path);
    });

    Route::get('/export/pegawai', function (Request $request) {
        $kabupaten = $request->kabupaten;
        return Excel::download(new ManmentPegawaiExport($kabupaten), 'pegawai.xlsx');
    })->name('export-pegawai');
    Route::get('/export/tim-kerja', function (Request $request) {
        $tahun = $request->tahun;
        return Excel::download(new TimKerjaExport($tahun), 'tim-kerja.xlsx');
    })->name('export-tim-kerja');
});
