<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssetClass;
use App\Models\AssetSubClass;
use App\Models\Department;
use App\Scopes\CompanyScope;
use Illuminate\Http\Request;

class RegisterDataController extends Controller
{
    public function getAssetSubClassesByClass($assetClassId)
    {
        $assetClass = AssetClass::withoutGlobalScope(CompanyScope::class)
                        ->with(['subClasses' => function ($query) {
                            $query->withoutGlobalScope(CompanyScope::class);
                        }])
                        ->find($assetClassId);

        if (!$assetClass) {
            return response()->json([], 404);
        }

        return response()->json($assetClass->subClasses);
    }

    public function getAssetNamesBySubClass($assetSubClassId)
    {
        $assetSubClass = AssetSubClass::withoutGlobalScope(CompanyScope::class)
                                      ->with(['assetNames' => function ($query) {
                                            $query->withoutGlobalScope(CompanyScope::class);
                                        }])
                                      ->find($assetSubClassId);

        if (!$assetSubClass) {
            return response()->json([], 404);
        }

        return response()->json($assetSubClass->assetNames);
    }
}