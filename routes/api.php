<?php

use App\Http\Controllers\Api\RegisterDataController;

Route::get('/asset-sub-classes-by-class/{assetClassId}', [RegisterDataController::class, 'getAssetSubClassesByClass']);
Route::get('/cost-codes-by-department/{departmentId}', [RegisterDataController::class, 'getCostCodesByDepartment']);