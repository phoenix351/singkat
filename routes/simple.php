<?php

use Illuminate\Support\Facades\Route;

Route::prefix('simple')->middleware('auth')->name('simple.')->group(function () {
    
});