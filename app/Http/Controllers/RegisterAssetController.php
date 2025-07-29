<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegisterAsset;
use App\Models\Location;
use App\Models\Department;
use App\Models\AssetClass;
use App\Models\AssetSubClass;

class RegisterAssetController extends Controller
{
    public function index()
    {
        $registerassets = RegisterAsset::all();
        $locations = Location::all();
        $departments = Department::all();
        return view('register-asset.index', compact('locations', 'departments', 'registerassets'));
    }

    public function create()
    {
        $locations = Location::all();
        $departments = Department::all();
        $assetclasses = AssetClass::all();
        $assetsubclasses = AssetSubClass::all();
        
        return view('register-asset.create', compact('locations', 'departments', 'assetclasses', 'assetsubclasses'));
    }
}
