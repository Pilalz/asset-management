<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\RegisterDataController;
use App\Http\Controllers\Api\StockOpnameDataController;

Route::get('/asset-sub-classes-by-class/{assetClassId}', [RegisterDataController::class, 'getAssetSubClassesByClass']);
Route::get('/asset-names-by-sub-class/{assetSubClassId}', [RegisterDataController::class, 'getAssetNamesBySubClass']);
Route::get('/asset-by-code/{assetCode}', [StockOpnameDataController::class, 'getAssetByCode']);
Route::get('/found-asset/{id}', [StockOpnameDataController::class, 'foundAsset']);