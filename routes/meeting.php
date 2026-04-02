<?php

use App\Http\Controllers\Meeting\ZoomController;
use Illuminate\Support\Facades\Route;

Route::prefix('meeting')->name('meeting.')->middleware(['auth', 'use.vue.inertia'])->group(function () {
    Route::get('/dashboard', [ZoomController::class, 'dashboard'])->name('dashboard');
    Route::get('/index/{id?}', [ZoomController::class, 'index'])->name('index');
    Route::get('/update/{id}', [ZoomController::class, 'update'])->name('update');
    Route::patch('/update/{id}', [ZoomController::class, 'update'])->name('update-meeting');
    Route::post('/store', [ZoomController::class, 'store'])->name('store');
    Route::delete('/meeting/{id}', [ZoomController::class, 'destroy'])->name('destroy');
});
