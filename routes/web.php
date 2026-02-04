<?php

use App\Http\Controllers\Index;
use App\Http\Middleware\isActive;
use Illuminate\Support\Facades\Route;

Route::get('/{any}', function () {
    return view('base');
})->where('any', '.*');