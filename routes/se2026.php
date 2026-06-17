<?php

use App\Http\Controllers\Se2026\DataController;
use Illuminate\Support\Facades\Route;

Route::prefix('se2026')->middleware(['auth', 'use.vue.inertia'])->name('se2026.')->group(function () {
    // Route::get('/', function () {});
    Route::get('/', [DataController::class, 'index'])->name('data.index');
}); 