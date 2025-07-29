<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetClassController;
use App\Http\Controllers\AssetSubClassController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\RegisterAssetController;

Route::get('/', function () {
    return view('index');
});

Route::resource('asset-class', AssetClassController::class);
Route::resource('asset-sub-class', AssetSubClassController::class);
Route::resource('location', LocationController::class);
Route::resource('department', DepartmentController::class);
Route::resource('register-asset', RegisterAssetController::class);