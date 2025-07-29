<?php

use App\Http\Controllers\Api\AssetDataController;

Route::get('/asset-sub-classes-by-class/{assetClassId}', [AssetDataController::class, 'getAssetSubClassesByClass']);
Route::get('/cost-codes-by-department/{departmentId}', [AssetDataController::class, 'getCostCodesByDepartment']);