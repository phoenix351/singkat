<?php

use App\Http\Controllers\Meeting\ZoomController;
use Illuminate\Support\Facades\Route;

Route::prefix('meeting')->name('meeting.')->middleware(['auth', 'use.vue.inertia'])->group(function () {
    Route::get('/dashboard', [ZoomController::class, 'dashboard'])->name('dashboard');
});
