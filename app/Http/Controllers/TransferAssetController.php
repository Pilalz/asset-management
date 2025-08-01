<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransferAsset;
use App\Models\Location;
use App\Models\Department;

class TransferAssetController extends Controller
{
    public function index()
    {
        $transferassets = TransferAsset::all();
        $locations = Location::all();
        $departments = Department::all();
        return view('transfer-asset.index', compact('locations', 'departments', 'transferassets'));
    }

    public function create()
    {
        $locations = Location::all();
        $departments = Department::all();
        
        return view('transfer-asset.create', compact('locations', 'departments'));
    }
}
