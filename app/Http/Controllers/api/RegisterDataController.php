<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssetClass;
use App\Models\AssetSubClass;
use App\Models\Department;
use Illuminate\Http\Request;

class AssetDataController extends Controller
{
    public function getAssetSubClassesByClass($assetClassId)
    {
        // Pastikan relasi 'subClasses' ada di model AssetClass
        // dan foreign key 'asset_class_id' ada di tabel asset_sub_classes
        $assetClass = AssetClass::with('subClasses')->find($assetClassId);

        if (!$assetClass) {
            return response()->json([], 404); // Mengembalikan array kosong jika tidak ditemukan
        }

        return response()->json($assetClass->subClasses);
    }

    public function getCostCodesByDepartment($departmentId)
    {
        // Pastikan relasi 'costCodes' ada di model Department
        // dan foreign key 'department_id' ada di tabel cost_codes
        $department = Department::with('costCodes')->find($departmentId);

        if (!$department) {
            return response()->json([], 404); // Mengembalikan array kosong jika tidak ditemukan
        }

        return response()->json($department->costCodes);
    }
}