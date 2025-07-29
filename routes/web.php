<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetClassController;

Route::get('/', function () {
    return view('index');
});

Route::resource('asset-class', AssetClassController::class);