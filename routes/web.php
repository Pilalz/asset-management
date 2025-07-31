<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetClassController;
use App\Http\Controllers\AssetSubClassController;
use App\Http\Controllers\AssetNameController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\RegisterAssetController;
use App\Http\Controllers\TransferAssetController;

Route::get('/', function () {return view('index');})->name('dashboard');

Route::resource('asset-class', AssetClassController::class);
Route::resource('asset-sub-class', AssetSubClassController::class);
Route::resource('asset-name', AssetNameController::class);
Route::resource('location', LocationController::class);
Route::resource('department', DepartmentController::class);
Route::resource('register-asset', RegisterAssetController::class);
Route::resource('transfer-asset', TransferAssetController::class);